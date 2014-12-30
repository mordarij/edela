<?php

namespace Acme\ApiBundle\Controller;

use Acme\ApiBundle\Event\EventStore;
use Acme\EdelaBundle\Entity\UserRelation;
use Acme\UserBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class FriendController extends FOSRestController
{

    /**
     * @Rest\View
     * @Rest\Get("/friends")
     */
    public function getFriendsAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        return $this->getDoctrine()->getManager()->getRepository('AcmeEdelaBundle:UserRelation')->getRelations($currentUser);
    }

    /**
     * @Rest\View
     * @Rest\Post("/friends/invite")
     */
    public function sendInviteAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        if ($request->get('email')) {
            $emailSender = $this->get('api.sender.email');
            return ['success' => $emailSender->send()];
        }
        return ['success' => false];
    }


    /**
     * @Rest\View
     * @Rest\Get("/friends/find")
     */
    public function findFriendsAction(Request $request)
    {

        $name = $request->get('name');
        $city = $request->get('city');
        $goal = $request->get('goal');
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        return $this->getDoctrine()->getManager()->getRepository('AcmeUserBundle:User')->findFriends($name, $city, $goal, $currentUser);

    }

    /**
     * @Rest\View
     * @Rest\Post("/friends/add/{id}")
     */
    public function addFriendAction($id)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $user = $em->find('AcmeUserBundle:User', $id);
        if (!$user || $user == $currentUser) {
            return $this->createNotFoundException();
        }

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->andX($expr->eq('sender', $currentUser), $expr->eq('receiver', $user)))
            ->orWhere($expr->andX($expr->eq('receiver', $currentUser), $expr->eq('sender', $user)));
        $relation = $em->getRepository('AcmeEdelaBundle:UserRelation')->matching($criteria);

        if ($relation->count()) {
            return ['success' => true];
        }

        $relation = new UserRelation();
        $relation->setIsAccepted(true)
            ->setSender($currentUser)
            ->setReceiver($user);
        $em->persist($relation);
        $em->flush();

        return ['success' => true];
    }


}
