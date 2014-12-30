<?php

namespace Acme\ApiBundle\Controller;

use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\EventStore;
use Acme\EdelaBundle\Entity\Action;
use Acme\EdelaBundle\Entity\UserAction;
use Acme\EdelaBundle\Entity\UserActionProgress;
use Acme\EdelaBundle\Entity\UserSubactionProgress;
use Acme\EdelaBundle\Form\Type\ActionCreateShortFormType;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Get("/statistics/progress")
     */
    public function getProgressStatiticsAction(Request $request){
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        return $currentUser->getProgressStatistics();
    }

    /**
     * @Rest\View
     * @Rest\Get("/statistics/actions")
     */
    public function getActionsStatisticsAction(Request $request){
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AcmeEdelaBundle:Action')->getUserStats($currentUser);
    }
}
