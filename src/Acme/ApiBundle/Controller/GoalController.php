<?php

namespace Acme\ApiBundle\Controller;

use Acme\EdelaBundle\Entity\Goal;
use Acme\EdelaBundle\Entity\GoalImage;
use Acme\EdelaBundle\Form\Type\GoalEditFormType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GoalController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Post("/goals")
     */
    public function postGoalAction(Request $request)
    {
         $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->get('form.factory')->createNamedBuilder('', 'form', null, array('csrf_protection' => false))
            ->add('name', 'text', array('label' => 'goal.new.short_label'))
            ->add('images', 'text', array('label' => 'goal.new.short_label'))
            ->getForm();
            
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $existingGoal = $this->getDoctrine()->getRepository('AcmeEdelaBundle:Goal')->findOneBy(array(
                'name' => $form->getData()['name'],
                'user' => $currentUser,
            	'isDeleted'=>0
            ));

            if (!$existingGoal) {
                $newGoal = new Goal();
                $newGoal->setName($form->getData()['name'])
                    ->setUser($this->container->get('security.context')->getToken()->getUser());
                    
		        $em->persist($newGoal);
                $em->flush();
                if(isset($form->getData()['images']) && count($form->getData()['images'])>0){
                 	$image = $this->getDoctrine()->getRepository('AcmeEdelaBundle:GoalImage')->find($form->getData()['images'][0]['id']);            			
            		$image->setGoal($newGoal);
            		$em->persist($image);
            		$em->flush();
                }
                return $newGoal;
            }
            return ['error' => ['message' => 'existing goal']];
        }
        return ['errors' => $form->getErrors()];
    }

    /**
     * @Rest\View
     * @Rest\Get("/goals")
     */
    public function getGoalsAction(Request $request)
    {    	
        $user_id = $request->get('user_id');
        if ($user_id == 0) {
            $user = $this->container->get('security.context')->getToken()->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('AcmeUserBundle:User')->find($user_id);
        }

        if (!$user) {
            throw $this->createNotFoundException('user.not_found');
        }

        /** @var $user User */
        $goals = $user->getGoals()->matching(Criteria::create()->where(Criteria::expr()->eq('isDeleted', 0)));
        if ($user_id != 0 && $user != $this->container->get('security.context')->getToken()->getUser()) {
            $goals = $goals->matching(Criteria::create()->where(Criteria::expr()->eq('isPrivate', 0)));
        }
        $response = [];
        foreach($goals as $goal){
            $oneGoal = [
                'title' => $goal->getName(),
                'id' => $goal->getId()
            ];
            $oneGoal['actions'] = [];
            $oneGoal['images'] = [];
            foreach ($goal->getImages() as $image) {
                $oneGoal['images'][] = ['webPath' => $image->getWebPath()];
            }
            foreach ($goal->getActions() as $action) {
                $oneGoal['actions'][] = ['title' => $action->getTitle()];
            }

            $response[] = $oneGoal;
        }
        return $response;
    }

    /**
     * @Rest\View
     * @Rest\Get("/goals/{goal_id}")
     */
    public function getGoalAction($goal_id){

        $goal = $this->getDoctrine()->getRepository('AcmeEdelaBundle:Goal')
            ->createQueryBuilder('g')
            ->select('g.id')
            ->addSelect('g.isPrivate')
            ->addSelect('g.priority')
            ->addSelect('g.isSaved')
            ->addSelect('g.isSlideshow')
            ->addSelect('g.isImportant')
            ->addSelect('g.slideshowInterval')
            ->addSelect('g.name')
            ->where('g.id=:goal_id')
            ->setParameter('goal_id', $goal_id)
            ->getQuery()->getOneOrNullResult();

        $images = $this->getDoctrine()->getRepository('AcmeEdelaBundle:GoalImage')
            ->createQueryBuilder('gi')
            ->select("CONCAT('uploads/goalimages/', gi.path) as webPath")
            ->where('gi.goal=:goal_id')
            ->setParameter('goal_id', $goal['id'])
            ->getQuery()->getArrayResult();
        $goal['images'] = $images;
        return $goal;
    }

    /**
     * @Rest\View
     * @Rest\Patch("/goals/{goal_id}")
     */
    public function patchGoalAction(Request $request, $goal_id){
        $em = $this->getDoctrine()->getManager();
        $goal = $em->getRepository('AcmeEdelaBundle:Goal')->find($goal_id);
    	if (!$goal || $goal->getUser() != $this->container->get('security.context')->getToken()->getUser()) {
            throw $this->createNotFoundException('goal.not_found');
        }        
        $form = $this->createForm(new GoalEditFormType(), $goal, array('csrf_protection'   => false, 'method' => 'PATCH'));
        $form->handleRequest($request);
        if ($form->isValid()){
            $goal->setIsSaved(true);
            $em->persist($goal);
            $em->flush();
            return "success";
        }
        return $form->getErrors();
    }

    /**
     * @Rest\View
     * @Rest\Post("goals/images")
     */
    public function postGoalImageAction(Request $request){
        $goalImage = new GoalImage();
        $form = $this->get('form.factory')->createNamedBuilder('', 'form', $goalImage, array('csrf_protection' => false, 'method' => 'POST'))
            ->add('goal')
            ->add('file')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $goalImage->upload();
            $em->persist($goalImage);
            $em->flush();
            return ['webPath' => $goalImage->getWebPath(),'id'=>$goalImage->getId()];
        }
        return $form->isSubmitted();
    }

    /**
     * @Rest\View
     * @Rest\Delete("/goals/{goal_id}")
     */
    public function deleteGoalAction($goal_id){
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $goal = $em->find('AcmeEdelaBundle:Goal', $goal_id);
        if ($goal->getUser() != $currentUser){
            return ['success' => false];
        }

        $goal->setIsDeleted(true);
        $em->persist($goal);
        $em->flush();

        return ['success' => true];
    }
}
