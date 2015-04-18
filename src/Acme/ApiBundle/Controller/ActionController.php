<?php

namespace Acme\ApiBundle\Controller;

use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\ActionInviteEvent;
use Acme\ApiBundle\Event\SubactionProgressEvent;
use Acme\EdelaBundle\Entity\Action;
use Acme\EdelaBundle\Entity\ActionInvite;
use Acme\EdelaBundle\Entity\UserAction;
use Acme\EdelaBundle\Entity\UserActionProgress;
use Acme\EdelaBundle\Entity\UserSubactionProgress;
use Acme\EdelaBundle\Form\Type\ActionCreateShortFormType;
use Acme\EdelaBundle\Form\Type\UserActionOwnerEditFormType;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class ActionController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Get("/actions")
     */
    public function getActionsAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $user_id = $request->get('user_id', 0);
        $goal_id = $request->get('goal_id', 0);
        $date = $request->get('date');
        if ($currentUser->getId() == $user_id || $user_id == 0) {
            $user = $currentUser;
        } else {
            $user = $em->find('AcmeUserBundle:User', $user_id);
        }

        if ($goal_id) {
            $goal = $em->find('AcmeEdelaBundle:Goal', $goal_id);
            if (!$goal) {
                throw $this->createNotFoundException('goals.not_found');
            }
        } else {
            $goal = null;
        }

        if ($date) {
            $date = new \DateTime($date);
        }

        $actions = $em->getRepository('AcmeEdelaBundle:Action')->getActions($user, $date, $goal);
        $progress = $em->getRepository('AcmeEdelaBundle:Action')->getProgress($actions, $user);
        $progressById = [];
        foreach ($progress as $action) {
            $progressById[$action['id']] = $action['progress'];
        }
        foreach ($actions as $key => $action) {
            $actions[$key]['subactions'] = $em->getRepository('AcmeEdelaBundle:Action')->getSubactions($action['id'], $user, $date);
            $actions[$key]['done'] = isset($progressById[$action['id']]) ? $progressById[$action['id']] : 0;
            $actions[$key]['tags'] = [];
        }


        return $actions;
    }

    /**
     * @Rest\View
     * @Rest\Get("/actions/delayed")
     */
    public function getDelayedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $user_id = $request->get('user_id', 0);
        if ($currentUser->getId() == $user_id || $user_id == 0) {
            $user = $currentUser;
        } else {
            $user = $em->find('AcmeUserBundle:User', $user_id);
        }

        $actions = $em->getRepository('AcmeEdelaBundle:Action')->getDelayedActions($user);

        return $actions;

    }

    /**
     * @Rest\View
     * @Rest\Post("/actions")
     */
    public function postActionAction(Request $request)
    {
        $action = new Action();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $action->setUser($user);
        $form = $this->createForm(new ActionCreateShortFormType(), $action, array('em' => $this->getDoctrine()->getManager(), 'csrf_protection' => false));
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $userAction = new UserAction();
            $userAction->setUser($user);
            $userAction->setStartAt($form->get('start_at')->getData());
            $userAction->setPosition($form->get('position')->getData());
            $userAction->setIsDeleted(0);           
            $action->addUserAction($userAction);

            $actionType = $em->getRepository('AcmeEdelaBundle:ActionType')->findOneBy(['tkey' => 'done']);
            $action->setActionType($actionType);

            $dynamicType = $em->getRepository('AcmeEdelaBundle:ActionDynamicType')->findOneBy(['tkey' => 'up']);
            $action->setActionDynamicType($dynamicType);

            $em->persist($action);
            $em->flush();

            $event = new ActionEvent($action, $user, false);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch('event.action.add', $event);

            return [
                'id' => $action->getId(),
                'title' => $action->getTitle(),
                'progress' => 0,
                'repeat_amount' => $action->getRepeatAmount(),
                'start_time' => $userAction->getStartAt(),
                'position' => $userAction->getPosition(),
                'periodicity' => $userAction->getPeriodicity(),
                'periodicity_interval' => $userAction->getPeriodicityInterval(),
                'is_private' => $userAction->getIsPrivate(),
                'done' => 0,
                'action_type_id' => $actionType->getId(),
                'action_type_title' => $action->getActionTypeTitle(),
                'dynamic_type_id' => $dynamicType->getId(),
                'goal' => [
                    'id' => $action->getGoal() ? $action->getGoal()->getId() : null,
                    'title' => $action->getGoal() ? $action->getGoal()->getName() : null
                ]
            ];
        }
        return $form->getErrors();
    }

    /**
     * @Rest\View
     * @Rest\Get("/samples")
     */
    public function getSamplesAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AcmeEdelaBundle:Sample')->findAll();
    }

    /**
     * @Rest\View
     * @Rest\Get("/actions/{id}/joint")
     */
    public function getJointInfoAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $action = $em->find('AcmeEdelaBundle:Action', $id);

        $jointInfo = $em->getRepository('AcmeEdelaBundle:Action')->getJointInfo($action);

        return $jointInfo;

    }

    /**
     * @Rest\View
     * @Rest\Get("/actions/{id}/invite/{user_id}")
     */
    public function postInviteJointAction($id, $user_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $action = $em->find('AcmeEdelaBundle:Action', $id);
        $user = $em->find('AcmeUserBundle:User', $user_id);

        if (!$action || !$user) {
            return $this->createNotFoundException();
        }

        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(['action' => $action, 'user' => $user]);
        if ($userAction) {
            return ['success' => true];
        }
        $userInvite = $em->getRepository('AcmeEdelaBundle:ActionInvite')->findOneBy(['action' => $action, 'receiver' => $user]);
        if ($userInvite) {
            return ['success' => true];
        }
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $userInvite = new ActionInvite();
        $userInvite->setSender($currentUser)
            ->setReceiver($user)
            ->setAction($action);
        $em->persist($userInvite);
        $em->flush();

        $event = new ActionInviteEvent($action, $currentUser, $user, $userInvite);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch($this->container->getParameter('events.action_invite'), $event);

        return ['success' => true];

    }

    /**
     * @Rest\View
     * @Rest\Post("/actions/invites/{id}/{action}")
     */
    public function acceptInviteAction($id, $action, Request $request)
    {
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $invite = $em->find('AcmeEdelaBundle:ActionInvite', $id);
        if ($invite->getIsAccepted() !== null) {
            return ['success' => true];
        }

        if ($action == 'accept') {
            $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(['action' => $invite->getAction(), 'user' => $currentUser]);

            if ($userAction) {
                return ['success' => true];
            }

            $userAction = new UserAction();
            $userAction->setUser($currentUser);
            $userAction->setStartAt(new \DateTime());
            $invite->getAction()->addUserAction($userAction);
            $em->persist($invite->getAction());
            $invite->setIsAccepted(true);
        } elseif ($action == 'reject') {
            $invite->setIsAccepted(false);
        }
        $invite->setAcceptedAt(new \DateTime());
        $em->persist($invite);
        $em->flush();

        return ['success' => true];

    }


    /**
     * @Rest\View
     * @Rest\Post("/actions/{id}/join")
     */
    public function postJoinAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $action = $em->find('AcmeEdelaBundle:Action', $id);
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $currentUser, 'action' => $action));

        if ($userAction) {
            return ['success' => true];
        }

        $userAction = new UserAction();
        $userAction->setUser($currentUser);
        $userAction->setStartAt(new \DateTime());
        $action->addUserAction($userAction);
        $em = $this->getDoctrine()->getManager();
        $em->persist($action);
        $em->flush();

        return ['success' => true];


    }

    /**
     * @Rest\View
     * @Rest\Post("/samples/{id}")
     */
    public function addFromSamplesAction(Request $request, $id)
    {
        $sample = $this->getDoctrine()->getManager()->find('AcmeEdelaBundle:Sample', $id);
        if (!$sample) {
            return $this->createNotFoundException();
        }

        $action = new Action();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $action->setUser($user);

        $action->setTitle($sample->getTitle());

        $em = $this->getDoctrine()->getManager();
        if ($request->get('goal') && $goal = $em->find('AcmeEdelaBundle:Goal', $request->get('goal'))) {
            $action->setGoal($goal);
        }

        $userAction = new UserAction();
        $userAction->setUser($user);
        $userAction->setStartAt(new \DateTime());

        $actionType = $em->getRepository('AcmeEdelaBundle:ActionType')->findOneBy(['tkey' => 'done']);
        $action->setActionType($actionType);

        $dynamicType = $em->getRepository('AcmeEdelaBundle:ActionDynamicType')->findOneBy(['tkey' => 'up']);
        $action->setActionDynamicType($dynamicType);

        $action->addUserAction($userAction);
        $em = $this->getDoctrine()->getManager();
        $em->persist($action);
        $em->flush();
        return ['id' => $action->getId(), 'title' => $action->getTitle(), 'progress' => false];
    }

    /**
     * @Rest\View
     * @Rest\Delete("/actions/{id}")
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $action = $em->find('AcmeEdelaBundle:Action', $id);
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $currentUser, 'action' => $action));

        if (!$userAction) {
             return ['success' => false];
        }
        $userAction->setIsDeleted(true);
        $em->persist($userAction);
        $em->flush();

        return ['success' => true];

    }

    /**
     * @Rest\View
     * @Rest\Post("/actions/{id}/execute")
     */
    public function executeAction($id)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        /** @var $action Action */
        $action = $em->find('AcmeEdelaBundle:Action', $id);
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));
        $result = $request->get('result', 1);

        try {
            if (!$userAction || !$userAction->getIsDayIncluded(new \DateTime()) || ($action->getSubactions()->count() && $action->getActionType() == $em->getRepository('AcmeEdelaBundle:ActionType')->findOneByTkey('done'))) {
                throw new \Exception();
            }
            $existingProgress = $em->getRepository('AcmeEdelaBundle:UserActionProgress')->matching(
                Criteria::create()
                    ->where(Criteria::expr()->eq('userAction', $userAction))
                    ->andWhere(Criteria::expr()->gte('createdAt', new \DateTime('today midnight')))
                    ->andWhere(Criteria::expr()->lte('createdAt', new \DateTime('tomorrow midnight')))
            );
            $progressDone = null;
            if ($existingProgress->count() > 0) {
                if ($result < 1) {
                    $progressDone = 0;
                    $em->remove($existingProgress->first());
                } else {
                    $progress = $existingProgress->first();
                    $progress->setResult($result);
                    if ($request->get('note')) {
                        $progress->setNote($request->get('note'));
                        $progress->setResult(strlen($request->get('note')));
                    }
                }
            } elseif ($result > 0) {
                $progressDone = 1;
                $progress = new UserActionProgress();
                $progress->setUserAction($userAction);
                $progress->setResult($result);
                if ($request->get('note')) {
                    $progress->setNote($request->get('note'));
                    $progress->setResult(strlen($request->get('note')));
                }
                $em->persist($progress);
            }

            $em->flush();

            $event = new ActionEvent($action, $user, $progressDone);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch($this->container->getParameter('events.action_complete'), $event);


            return ['success' => true, 'progress' => $progressDone];
        } catch (\Exception $e) {
            return ['success' => false, 'progress' => 0, 'message' => $e->getMessage()];
        }
    }

    /**
     * @Rest\View
     * @Rest\Get("/actions/{id}")
     */
    public function getAction($id, Request $request)
    {
        $action = $this->getDoctrine()->getManager()->find('AcmeEdelaBundle:Action', $id);

        return $action;
    }

    /**
     * @Rest\View
     * @Rest\Patch("/actions/{id}")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $action = $em->find('AcmeEdelaBundle:Action', $id);
        if (!$action) {
            return $this->createNotFoundException();
        }
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(['action' => $action, 'user' => $currentUser]);        
        if (!$userAction) {
            return $this->createNotFoundException();
        }
		$originalSubactions = new ArrayCollection();
        foreach ($action->getSubactions() as $subaction) {
            $originalSubactions->add($subaction);
        }
        
        $form = $this->createForm(new UserActionOwnerEditFormType(), $userAction, ['csrf_protection' => false]);

        $goal = $action->getGoal();
        $form->handleRequest($request);
        if ($form->isValid()) {
        	
            if ($goal && $goal != $action->getGoal()) {
                $action->setGoal($goal);
            }
            foreach ($originalSubactions as $subaction) {
                if (false === $action->getSubactions()->contains($subaction)) {
                    $em->remove($subaction);
                }
            }
            if($userAction->getPosition() > $form->get('position')->getData())
            	$userAction->setPosition($form->get('position')->getData()+1);
            else
            	$userAction->setPosition($form->get('position')->getData());
            $em->persist($userAction->getAction());
            $em->persist($userAction);
            $em->flush();
			
            return [
                'subactions' => $em->getRepository('AcmeEdelaBundle:Action')->getSubactions($action->getId(), $currentUser)
            ];


        }

        return $form->isSubmitted() ? $form->getErrors() : 'not submitted';
    }

    /**
     * @Rest\View
     * @Rest\Post("/subactions/{id}/execute")
     */
    public function executeSubactionAction($id)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $subaction = $em->find('AcmeEdelaBundle:Subaction', $id);
        if (!$subaction) {
            return ['success' => false, 'reason' => 1];
        }
        $action = $subaction->getAction();
        /** @var UserAction $userAction */
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));
        $userTime = $user->getCurrentDateTime();
        if (!$userAction || !$userAction->getIsDayIncluded($userTime)) {
            return ['success' => false, 'reason' => 2];
        }
        $progress = $em->getRepository('AcmeEdelaBundle:Action')->executeSubaction($subaction, $user);
        $event = new SubactionProgressEvent($subaction, $user, $progress);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch($this->container->getParameter('events.subaction_complete'), $event);
        return ['success' => true, 'progress' => $progress];
    }

    public function executesubAction2($id)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $subaction = $em->find('AcmeEdelaBundle:Subaction', $id);
        $action = $subaction->getAction();
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));
        $success = false;

        try {
            if (!$userAction || !$userAction->getIsDayIncluded(new \DateTime())) {
                throw new \Exception();
            }

            $existingSubprogress = $em->getRepository('AcmeEdelaBundle:UserSubactionProgress')->matching(
                Criteria::create()
                    ->where(Criteria::expr()->eq('user', $user))
                    ->andWhere(Criteria::expr()->eq('subaction', $subaction))
                    ->andWhere(Criteria::expr()->gte('createdAt', new \DateTime('today midnight')))
                    ->andWhere(Criteria::expr()->lte('createdAt', new \DateTime('tomorrow midnight')))
            )->count();
            if ($existingSubprogress) {
                throw new \Exception();
            }

            $existingProgress = $em->getRepository('AcmeEdelaBundle:UserActionProgress')->matching(
                Criteria::create()
                    ->where(Criteria::expr()->eq('userAction', $userAction))
                    ->andWhere(Criteria::expr()->gte('createdAt', new \DateTime('today midnight')))
                    ->andWhere(Criteria::expr()->lte('createdAt', new \DateTime('tomorrow midnight')))
            )->count();
            if ($existingProgress > 0) {
                throw new \Exception();
            }

            $subprogress = new UserSubactionProgress();
            $subprogress->setUser($user)->setSubaction($subaction);
            $em->persist($subprogress);
            $em->flush();
            if ($action->getSubactions()->count() == count($em->getRepository('AcmeEdelaBundle:Action')->getSubactions($action, $user, null, true))) {
                $progress = new UserActionProgress();
                $progress->setUserAction($userAction);
                $progress->setResult($request->get('result', 1));
                $em->persist($progress);
            }
            $em->flush();
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }


}
