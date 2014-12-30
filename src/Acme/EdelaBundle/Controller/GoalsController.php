<?php

namespace Acme\EdelaBundle\Controller;

use Acme\EdelaBundle\Entity\Goal;
use Acme\EdelaBundle\Entity\GoalImage;
use Acme\EdelaBundle\Entity\Task;
use Acme\EdelaBundle\Form\Type\GoalEditFormType;
use Acme\EdelaBundle\Form\Type\TaskCreateShortFormType;
use Acme\UserBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GoalsController extends Controller
{
    public function listAction($user_id)
    {
        if ($user_id == 0) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            return new RedirectResponse($this->container->get('router')->generate('goals_list', array('user_id' => $user->getId())));
        }
        $user = $this->getDoctrine()->getRepository('AcmeUserBundle:User')->find($user_id);
        if (!$user) {
            throw $this->createNotFoundException('user.not_found');
        }
        $formView = null;
        if ($user == $this->container->get('security.context')->getToken()->getUser()) {
            $form = $this->createFormBuilder()
                ->add('name', 'text', array('label' => 'goal.new.short_label'))
                ->getForm();

            $request = $this->container->get('request_stack')->getCurrentRequest();
            $form->handleRequest($request);

            if ($form->isValid()) {

                $existingGoal = $this->getDoctrine()->getRepository('AcmeEdelaBundle:Goal')->findOneBy(array(
                    'name' => $form->getData()['name'],
                    'user' => $user
                ));

                if (!$existingGoal) {
                    $newGoal = new Goal();
                    $newGoal->setName($form->getData()['name'])
                        ->setUser($this->container->get('security.context')->getToken()->getUser());
                    $this->getDoctrine()->getManager()->persist($newGoal);
                    $this->getDoctrine()->getManager()->flush();
                    return new RedirectResponse($this->container->get('router')->generate('goals_edit', array('goal_id' => $newGoal->getId())));
                }
                $form->get('name')->addError(new FormError('goal.new.existing'));
            }
            $formView = $form->createView();
        }

        /** @var $user User */
        $goals = $user->getGoals();
        if ($user_id != 0 && $user != $this->container->get('security.context')->getToken()->getUser()) {
            $goals = $goals->matching(Criteria::create()->where(Criteria::expr()->eq('isPrivate', 0)));
        }

        return $this->render('AcmeEdelaBundle:Goals:list.html.twig', array('goals' => $goals, 'form' => $formView));
    }

    public function editAction($goal_id)
    {
        $em = $this->getDoctrine()->getManager();
        $goal = $em->getRepository('AcmeEdelaBundle:Goal')->find($goal_id);

        if (!$goal || $goal->getUser() != $this->container->get('security.context')->getToken()->getUser()) {
            throw $this->createNotFoundException('goal.not_found');
        }

        $form = $this->createForm(new GoalEditFormType(), $goal);
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if (!$goal->getIsSaved()) {
            $submitButtonLabel = 'goal.edit.add_tasks';
            $firstSaveAttempt = true;
        } else {
            $submitButtonLabel = 'goal.edit.save';
            $firstSaveAttempt = false;
        }
        $form->add('save', 'submit', array('label' => $submitButtonLabel, 'attr' => array('class' => 'btn-primary')));
        $form->add('cancel', 'button', array('label' => 'goal.edit.cancel'));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $goal->setIsSaved(true);
            $em->persist($goal);
            $em->flush();
            if ($firstSaveAttempt) {
                return new RedirectResponse($this->container->get('router')->generate('goals_actions', array('goal_id' => $goal->getId())));
            }
        }

        $images = $goal->getImages();

        return $this->render('AcmeEdelaBundle:Goals:edit.html.twig', array('form' => $form->createView(), 'images' => $images, 'goal' => $goal));
    }

    public function imageUploadAction()
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $goalImage = new GoalImage();
        $form = $this->createFormBuilder($goalImage, array('csrf_protection' => false, 'method' => 'POST'))
            ->add('goal')
            ->add('file')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $goalImage->upload();
            $em->persist($goalImage);
            $em->flush();
            return new JsonResponse(['status' => 'ok', 'path' => $goalImage->getWebPath()]);
        }
        return new JsonResponse('Error');
    }

    public function actionsAction($goal_id)
    {
        $em = $this->getDoctrine()->getManager();
        $goal = $em->getRepository('AcmeEdelaBundle:Goal')->find($goal_id);

        if (!$goal || $goal->getUser() != $this->container->get('security.context')->getToken()->getUser()) {
            throw $this->createNotFoundException('goal.not_found');
        }

        $tasks = $goal->getTasks()->matching(
            Criteria::create()->where(
                Criteria::expr()->orX(
                    Criteria::expr()->eq('dateAt', new \DateTime('today midnight')),
                    Criteria::expr()->isNull('dateAt')
                )
            )->andWhere(Criteria::expr()->isNull('parent'))
        );

        $task = new Task();
        $task->setGoal($goal);
        $addTaskForm = $this->createForm(new TaskCreateShortFormType(), $task, array('action' => $this->generateUrl('tasks_create_short'), 'em' => $em));
        return $this->render('AcmeEdelaBundle:Actions:list.html.twig', array('goal' => $goal, 'addTaskForm' => $addTaskForm->createView(), 'tasks' => $tasks));
    }
}
