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

class CommonController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Get("/variables")
     */
    public function getVariablesAction(Request $request)
    {
        $variables = [];

        $em = $this->getDoctrine()->getManager();

        $actionTypes = $em->getRepository('AcmeEdelaBundle:ActionType')->findAll();
        $variables['action_types'] = [];
        foreach ($actionTypes as $actionType) {
            $variables['action_types'][$actionType->getId()] = ['id' => $actionType->getId(), 'title' => $actionType->getTitle(), 'tkey' => $actionType->getTkey()];
        }

        $dytnamicTypes = $em->getRepository('AcmeEdelaBundle:ActionDynamicType')->findAll();
        $variables['dynamic_types'] = [];
        foreach ($dytnamicTypes as $dynamicType) {
            $variables['dynamic_types'][] = ['id' => $dynamicType->getId(), 'title' => $dynamicType->getTitle()];
        }

        $levels = $em->getRepository('AcmeEdelaBundle:UserLevel')->findBy([], ['number' => 'ASC']);
//        $variables['levels'] = [];
//        foreach ($levels as $level) {
//            $variables['levels'][$level->getNumber()] = $level;
//        }
        $variables['levels'] = $levels;

        return $variables;
    }
}
