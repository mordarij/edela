<?php

namespace Acme\ApiBundle\Controller;

use Acme\ApiBundle\Event\TaskEvent;
use Acme\EdelaBundle\Entity\Task;
use Acme\EdelaBundle\Form\Type\TaskCreateShortFormType;
use Acme\EdelaBundle\Form\Type\TaskEditFormType;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class
TaskController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Get("/tasks")
     */
    public function getTasksAction(Request $request)
    {
        $date = $request->get('date');
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $user_id = $request->get('user_id', 0);
        $goal_id = $request->get('goal_id', 0);
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

        $tasks = $em->getRepository('AcmeEdelaBundle:Task')->getTasks($user, null, $goal);
        return $tasks;
    }

    /**
     * @Rest\View
     * @Rest\Get("/tasks/done")
     */
    public function getDoneTasksAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $user_id = $request->get('user_id', 0);
        if ($currentUser->getId() == $user_id || $user_id == 0) {
            $user = $currentUser;
        } else {
            $user = $em->find('AcmeUserBundle:User', $user_id);
        }
        $tasks = $em->getRepository('AcmeEdelaBundle:Task')->findBy(array('user' => $user, 'isDone' => true));
        return $tasks;
    }

    /**
     * @Rest\View
     * @Rest\Post("/tasks")
     */
    public function postTaskAction(Request $request)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $task = new Task();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $task->setUser($user);
        $form = $this->createForm(new TaskCreateShortFormType(), $task, array('em' => $this->getDoctrine()->getManager(), 'csrf_protection' => false));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return [
                'id' => $task->getId(),
                'created_at' => $task->getCreatedAt(),
                'title' => $task->getName(),
                'is_important' => false,
                'parent' => $task->getParent() ? $task->getParent()->getId() : 0,
                'goal_title' => $task->getGoal() ? $task->getGoal()->getName() : null,
                'goal_id' => $task->getGoal() ? $task->getGoal()->getId() : null,
                'done' => false,
                'is_urgent' => false,
                'is_sms_notification' => false,
                'notification_time' => $task->getNotificationTime() ? $task->getNotificationTime()->format('H:i') : null,
                'date' => $task->getDateAt() ? $task->getDateAt()->format('Y-m-d') : null,
                'note' => $task->getNote()
            ];
        }

        return $form->getErrors();
    }

    /**
     * @Rest\View
     * @Rest\Post("/tasks/{task_id}/execute")
     */
    public function executeAction($task_id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Task $task */
        $task = $em->getRepository('AcmeEdelaBundle:Task')->find($task_id);

        if ($this->container->get('security.context')->getToken()->getUser() != $task->getUser()) {
            $success = false;
            $done = false;
        } else {
            if ($task->getIsDone()) {
                $task->setIsDone(false);
            } else {
                $task->setIsDone(true);

            }
            $task->setDoneAt(new \DateTime());
            $em->persist($task);
            $em->flush();
            $success = true;
            $done = $task->getIsDone();
            $event = new TaskEvent($task);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch($this->container->getParameter('events.task_complete'), $event);
        }
        return ['success' => $success, 'done' => $done];

    }

    /**
     * @Rest\View
     * @Rest\Patch("/tasks/{task_id}")
     */
    public function editTaskAction($task_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Task $task */
        $task = $em->getRepository('AcmeEdelaBundle:Task')->find($task_id);
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        if (!$task || $task->getUser() != $currentUser) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new TaskEditFormType(), $task, array('csrf_protection' => false));
        $goal = $task->getGoal();
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($goal && $goal != $task->getGoal()){
                $task->setGoal($goal);
            }
            $em->persist($task);
            $em->flush();
            return [
                'id' => $task->getId(),
                'created_at' => $task->getCreatedAt(),
                'title' => $task->getName(),
                'is_important' => $task->getIsImportant(),
                'parent' => $task->getParent() ? $task->getParent()->getId() : 0,
                'goal_title' => $task->getGoal() ? $task->getGoal()->getName() : null,
                'goal_id' => $task->getGoal() ? $task->getGoal()->getId() : null,
                'done' => $task->getIsDone(),
                'is_urgent' => $task->getIsUrgent(),
                'is_sms_notification' => $task->getIsSmsNotification(),
                'notification_time' => $task->getNotificationTime() ? $task->getNotificationTime()->format('H:i') : null,
                'date' => $task->getDateAt() ? $task->getDateAt()->format('Y-m-d') : null,
                'note' => $task->getNote()
            ];
        }

        return $form->getErrors();

    }
}
