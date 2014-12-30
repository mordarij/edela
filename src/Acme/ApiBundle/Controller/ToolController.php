<?php

namespace Acme\ApiBundle\Controller;

use Acme\EdelaBundle\Entity\Tool;
use Acme\EdelaBundle\Entity\UserTool;
use Acme\UserBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class ToolController extends Controller
{
    /**
     * @Rest\View
     * @Rest\Get("/tools/enabled")
     */
    public function getEnabledAction()
    {
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        return $this->getDoctrine()->getManager()->getRepository('AcmeEdelaBundle:Tool')->getEnabled($currentUser);
    }

    /**
     * @Rest\View
     * @Rest\Get("/tools")
     */
    public function getAvailableAction()
    {
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        return $this->getDoctrine()->getManager()->getRepository('AcmeEdelaBundle:Tool')->getAvailable($currentUser);
    }

    /**
     * @Rest\View
     * @Rest\Patch("/tools/{id}")
     */
    public function updateToolAction($id, Request $request){
        /** @var $currentUser User */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        /** @var $tool Tool */
        $tool = $em->find('AcmeEdelaBundle:Tool', $id);
        if (!$tool){
            return $this->createNotFoundException();
        }

        $userTool = $tool->getUserTools()->matching(Criteria::create()->where(Criteria::expr()->eq('user', $currentUser)))->first();
        if (!$userTool){
            $userTool = new UserTool();
            $userTool->setUser($currentUser)
                ->setTool($tool);
        }
        if ($request->get('is_enabled')){
            if ($userTool->getIsAvailable() || (!$tool->getCost() && $currentUser->getLevel() > $tool->getMinLevel())){
                $userTool->setIsAvailable(true);
                $currentEnable = $userTool->getIsEnabled();
                $userTool->setIsEnabled(!$currentEnable);
            }
        }
        if ($request->get('buy_exp')){
            if (!$userTool->getIsAvailable() && $currentUser->getExpBill() >= $tool->getCost() && $currentUser->getLevel() >= $tool->getMinLevel()){
                $em->getRepository('AcmeUserBundle:User')->spendExp($currentUser, $tool->getCost());
                $userTool->setIsAvailable(true);
            }
        }

        $em->persist($userTool);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $toolArray = json_decode($serializer->serialize($tool, 'json'), true);
        $userToolArray = json_decode($serializer->serialize($userTool, 'json'), true);
        return ['success' => true, 'data' => array_merge($toolArray, $userToolArray)];




    }
}
