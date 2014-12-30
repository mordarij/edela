<?php

namespace Acme\EdelaBundle\Controller;

use Acme\EdelaBundle\Entity\Task;
use Acme\EdelaBundle\Entity\TaskNotification;
use Acme\EdelaBundle\Form\Type\TaskCreateFormType;
use Acme\EdelaBundle\Form\Type\TaskCreateShortFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function createShortAction()
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $task = new Task();
        $task->setUser($this->container->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new TaskCreateShortFormType(), $task, array('em' => $this->getDoctrine()->getManager()));

        $form->handleRequest($request);
        $success = false;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            $success = true;
        }

        if ($success) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['status' => 'OK', 'text' => $task->getName()]);
            } else {
                return new RedirectResponse($this->container->get('router')->generate('tasks_edit', array('task_id' => $task->getId())));
            }
        } else {
            return new JsonResponse(['status' => 'Error']);
        }
    }

    public function editAction($task_id)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();
        if ($task_id) {
            $task = $em->getRepository('AcmeEdelaBundle:Task')->find($task_id);
        } else {
            $task = new Task();
            $task->setUser($this->container->get('security.context')->getToken()->getUser());
            if (($parent_id = $request->get('parent_id')) &&
                ($parent = $em->getRepository('AcmeEdelaBundle:Task')->find($parent_id)) &&
                    $parent->getUser() == $this->container->get('security.context')->getToken()->getUser()
            ) {
                $task->setParent($parent)
                    ->setGoal($parent->getGoal())
                    ->setIsPrivate($parent->getIsPrivate());
            }
        }
        $form = $this->createForm(new TaskCreateFormType(), $task);
        $originalNotifications = new ArrayCollection();
        foreach ($task->getNotifications() as $notification) {
            $originalNotifications->add($notification);
        }

        $form->handleRequest($request);
        if ($form->isValid()) {


            foreach ($originalNotifications as $notification) {
                if (false === $task->getNotifications()->contains($notification)) {
                    $em->remove($notification);
                }
            }
            $em->persist($task);
            $em->flush();
        }
        return $this->render('AcmeEdelaBundle:Task:edit.html.twig', array('form' => $form->createView()));
    }

    public function setDoneAction($task_id){
        $em = $this->getDoctrine()->getManager();
        /** @var Task $task */
        $task = $em->getRepository('AcmeEdelaBundle:Task')->find($task_id);

        if ($this->container->get('security.context')->getToken()->getUser() != $task->getUser()){
            $success = false;
        } else {
            $task->setIsDone(true)
                ->setDoneAt(new \DateTime());
            $em->persist($task);
            $em->flush();
            $success = true;
        }
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => $success]);
        } else {
            return new RedirectResponse($request->headers->get('referer'));
        }

    }

}
