<?php

namespace Acme\ApiBundle\Controller;

use Acme\UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Patch("/notifications/{id}")
     */
    public function patchNotificationAction(Request $request, $id)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $notification = $em->find('AcmeEdelaBundle:Notification', $id);

        if (!$notification || $notification->getUser() != $currentUser){
            throw $this->createAccessDeniedException();
        }

        if ($request->get('is_read') && !$notification->getReadAt()){
            $notification->setReadAt(new \DateTime());
            $em->persist($notification);
            $em->flush();
        }
        return true;
    }


}
