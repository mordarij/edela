<?php

namespace Acme\ApiBundle\Controller;

use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\EventStore;
use Acme\EdelaBundle\Entity\Dayoff;
use Acme\EdelaBundle\Form\Type\ProfileEditFormType;
use Acme\UserBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Get("/profile/short")
     */
    public function getShortProfileAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        return [
            'id' => $currentUser->getId(),
            'name' => $currentUser->getFullname(),
            'exp' => $currentUser->getExpBill(),
            'total_exp' => $currentUser->getExpTotal(),
            'bill' => $currentUser->getBill(),
            'available_dayoffs' => $em->getRepository('AcmeUserBundle:User')->getAvailableDayoffs($currentUser),
            'notifications' => $currentUser->getNotifications()->matching(
                    Criteria::create()
                        ->where(Criteria::expr()->isNull('readAt'))
                )
        ];

    }

    /**
     * @Rest\View
     * @Rest\Get("/profile/achievements")
     */
    public function getProfileAchievementsAction(Request $request)
    {

//        return [
//            ['title' => 1, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer1.png', 'progress' => 100, 'exp_reward' => 650],
//            ['title' => 2, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer2.png', 'progress' => 10, 'exp_reward' => 650],
//            ['title' => 3, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer3.png', 'progress' => 100, 'exp_reward' => 650],
//            ['title' => 4, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer4.png', 'progress' => 10, 'exp_reward' => 650],
//            ['title' => 5, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer5.png', 'progress' => 10, 'exp_reward' => 650],
//            ['title' => 6, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer6.png', 'progress' => 10, 'exp_reward' => 650],
//            ['title' => 7, 'description' => 2, 'image' => '/bundles/acmeedela/images/honer7.png', 'progress' => 100, 'exp_reward' => 650],
//        ];

        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        $achievements = $this->getDoctrine()->getRepository('AcmeEdelaBundle:Achievement')->getAll($currentUser);

        return $achievements;

    }

    /**
     * @Rest\View
     * @Rest\Get("/profile")
     */
    public function getProfileAction()
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AcmeUserBundle:User')->getProfileData($currentUser);
    }

    /**
     * @Rest\View
     * @Rest\Patch("/profile")
     */
    public function patchProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new ProfileEditFormType(), $currentUser, ['csrf_protection' => false]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($currentUser);
            $em->flush();
            return $currentUser;
        }
        return $form->isSubmitted() ? $form->getErrors() : 'not submitted';
    }

    /**
     * @Rest\View
     * @Rest\Patch("/profile/settings")
     */
    public function patchSettingsAction(Request $request){
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $this->getDoctrine()->getConnection();
        $stmt = $conn->prepare('INSERT INTO ed_users_settings(user_id, tkey, `value`) VALUES(:user, :key, :val) ON DUPLICATE KEY UPDATE `value`=:val');
        $stmt->bindValue('user', $currentUser->getId());
        $stmt->bindValue('key', 'lk_'.$request->get('key').'_disabled');
        $stmt->bindValue('val', !$request->get('enabled'));

        $stmt->execute();

    }


    /**
     * @Rest\View
     * @Rest\Patch("/profile/password")
     */
    public function patchProfilePasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $encoder_service = $this->get('security.encoder_factory');
        $encoder = $encoder_service->getEncoder($currentUser);
        $encoded_pass = $encoder->encodePassword($request->get('old'), $currentUser->getSalt());
        if ($encoded_pass != $currentUser->getPassword()) {
            return ['success' => false, 'message' => 'old'];
        }
        if (!$request->get('new_one') || trim(strlen($request->get('new_one'))) == 0 || $request->get('new_one') != $request->get('new_two')) {
            return ['success' => false, 'message' => 'new'];
        }
        $userManager = $this->get('fos_user.user_manager');
        $currentUser->setPlainPassword(trim($request->get('new_one')));
        $userManager->updateUser($currentUser);
        return ['success' => true];
    }

    /**
     * @Rest\View
     * @Rest\Post("profile/photo")
     */
    public function postProfilePhotoAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->get('form.factory')->createNamedBuilder('', 'form', $currentUser, array('csrf_protection' => false, 'method' => 'POST'))
            ->add('file')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $currentUser->upload();
            $em->persist($currentUser);
            $em->flush();
            return ['webPath' => $currentUser->getPhotoWebPath()];
        }
        return $form->isSubmitted();
    }

    /**
     * @Rest\View
     * @Rest\Post("/profile/dayoff")
     */
    public function postDayoffAction(Request $request){

        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $date = new \DateTime($request->get('date'));
        $userTime = (new \DateTime())->setTimeZone(new \DateTimeZone('+' . $currentUser->getTimezone()->getTimezone()));
        $firstMonthDay = clone $userTime;
        $firstMonthDay->modify('first day of this month');
        $lastMonthDay = clone $userTime;
        $lastMonthDay->modify('last day of this month')->setTime(23,59,59);
        if (!$date || $date < $firstMonthDay || $date > $lastMonthDay){
            return ['success' => false, 'reason' => 'no date'];
        }

        $em = $this->getDoctrine()->getManager();
        $existing = $em->getRepository('AcmeEdelaBundle:Dayoff')->findOneBy(['user' => $currentUser, 'dateAt' => $date]);
        $reason = 'existed';

        if (!$existing){
            if ($em->getRepository('AcmeUserBundle:User')->getAvailableDayoffs($currentUser) < 1){
                return ['success' => false, 'reason' => 'no-dayoffs'];
            }
            $dayoff = new Dayoff();
            $dayoff->setUser($currentUser)
                ->setDateAt($date);

            $em->persist($dayoff);
            $em->flush();
            $reason = 'created';
        }
        return ['success' => true, 'reason' => $reason];
    }

}
