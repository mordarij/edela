<?php

namespace Acme\EdelaBundle\Controller;

use Acme\EdelaBundle\Entity\Action;
use Acme\EdelaBundle\Entity\Task;
use Acme\EdelaBundle\Entity\UserAction;
use Acme\EdelaBundle\Entity\UserActionProgress;
use Acme\EdelaBundle\Entity\UserSubactionProgress;
use Acme\EdelaBundle\Form\Type\ActionCreateShortFormType;
use Acme\EdelaBundle\Form\Type\UserActionOwnerEditFormType;
use Acme\EdelaBundle\Form\Type\TaskCreateShortFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ActionsController extends Controller
{
    public function listAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $task = new Task();
        $addTaskForm = $this->createForm(new TaskCreateShortFormType(), $task, array('action' => $this->generateUrl('tasks_create_short'), 'em' => $em));
        $action = new Action();
        $addActionForm = $this->createForm(new ActionCreateShortFormType(), $action, array('action' => $this->generateUrl('actions_create_short'), 'em' => $em));

        $tasks = $em->getRepository('AcmeEdelaBundle:Task')->matching(
            Criteria::create()
                ->where(
                    Criteria::expr()->orX(
                        Criteria::expr()->eq('dateAt', new \DateTime('today midnight')),
                        Criteria::expr()->isNull('dateAt')
                    ))
                ->andWhere(Criteria::expr()->isNull('parent'))
                ->andWhere(Criteria::expr()->eq('user', $user))
//                ->andWhere(Criteria::expr()->neq('isDone', true))
        );

        $actions = $em->getRepository('AcmeEdelaBundle:Action')->getActions($user);
        foreach($actions as $key => $action){
            $actions[$key]['subactions'] = $em->getRepository('AcmeEdelaBundle:Action')->getSubactions($action[0], $user);
        }
        return $this->render("AcmeEdelaBundle:Actions:list.html.twig", array(
            'addTaskForm' => $addTaskForm->createView(),
            'addActionForm' => $addActionForm->createView(),
            'tasks' => $tasks,
            'actions' => $actions
        ));

    }

    public function createShortAction()
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $action = new Action();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $action->setUser($user);
        $form = $this->createForm(new ActionCreateShortFormType(), $action, array('em' => $this->getDoctrine()->getManager()));

        $form->handleRequest($request);
        $success = false;
        if ($form->isValid()) {
            $userAction = new UserAction();
            $userAction->setUser($user);
            $userAction->setIsDeleted(false);
            $action->addUserAction($userAction);
            $em = $this->getDoctrine()->getManager();
            $em->persist($action);
            $em->flush();
            $success = true;
        }        
        if ($success) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'text' => $this->renderView('AcmeEdelaBundle:Actions:_one_block.html.twig', array('action' => $action, 'progress' => null))]);
            } else {
                return new RedirectResponse($this->container->get('router')->generate('tasks_edit', array('task_id' => $action->getId())));
            }
        } else {
            return new JsonResponse(['success' => false]);
        }
    }  

    public function executeAction($action_id)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $action = $em->find('AcmeEdelaBundle:Action', $action_id);
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));

        try {
            if (!$userAction || !$userAction->getIsDayIncluded(new \DateTime()) || $action->getSubactions()->count()) {
                throw new \Exception();
            }
            $existingProgress = $em->getRepository('AcmeEdelaBundle:UserActionProgress')->matching(
                Criteria::create()
                    ->where(Criteria::expr()->eq('userAction', $userAction))
                    ->andWhere(Criteria::expr()->gte('createdAt', new \DateTime('today midnight')))
                    ->andWhere(Criteria::expr()->lte('createdAt', new \DateTime('tomorrow midnight')))
            );
            if ($existingProgress->count() > 0) {
                throw new \Exception();
            }

            $progress = new UserActionProgress();
            $progress->setUserAction($userAction);
            $progress->setResult($request->get('result', 1));
            if ($request->get('note')) {
                $progress->setNote($request->get('note'));
                $progress->setResult(strlen($request->get('note')));
            }
            $em->persist($progress);
            $em->flush();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
        }

        if ($success) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'text' => $this->renderView('AcmeEdelaBundle:Actions:_one_block.html.twig', array('action' => $action, 'progress' => $progress))]);
            } else {
                return new RedirectResponse($this->container->get('router')->generate('tasks_edit', array('task_id' => $action->getId())));
            }
        } else {
            return new JsonResponse(['success' => false]);
        }
    }


    public function executesubAction($subaction_id){
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $subaction = $em->find('AcmeEdelaBundle:Subaction', $subaction_id);
        $action = $subaction->getAction();
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));
        $success = false;

        try{
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
            if ($existingSubprogress){
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
            if ($action->getSubactions()->count() == count($em->getRepository('AcmeEdelaBundle:Action')->getSubactions($action, $user, null, true))){
                $progress = new UserActionProgress();
                $progress->setUserAction($userAction);
                $progress->setResult($request->get('result', 1));
                $em->persist($progress);
            }

            $em->flush();
            $success = true;

        } catch(\Exception $e){
            $success = false;
        }

        if ($success) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'text' => $this->renderView('AcmeEdelaBundle:Actions:_one_block.html.twig', array('action' => $action, 'progress' => $progress))]);
            } else {
                return new RedirectResponse($this->container->get('router')->generate('tasks_edit', array('task_id' => $action->getId())));
            }
        } else {
            return new JsonResponse(['success' => false]);
        }
    }

    public function editAction($action_id)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        /** @var Action $action */
        $action = $em->getRepository('AcmeEdelaBundle:Action')->find($action_id);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $userAction = $em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));
        if (!$action || $user != $action->getUser()) {
            return $this->createNotFoundException('Not found');
        }

        $originalSubactions = new ArrayCollection();
        foreach ($action->getSubactions() as $subaction) {
            $originalSubactions->add($subaction);
        }
        $form = $this->createForm(new UserActionOwnerEditFormType(), $userAction);
        $form->handleRequest($request);
        if ($form->isValid()) {

            foreach ($originalSubactions as $subaction) {
                if (false === $action->getSubactions()->contains($subaction)) {
                    $em->remove($subaction);
                }
            }

            $em->persist($userAction);
            $em->flush();
        }

        return $this->render('AcmeEdelaBundle:Actions:edit.html.twig', array('form' => $form->createView()));
    }

}
