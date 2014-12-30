<?php

namespace Acme\ApiBundle\Controller;

use Acme\EdelaBundle\Entity\Transaction;
use Acme\EdelaBundle\Form\Type\ProfileEditFormType;
use Acme\UserBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends FOSRestController
{
    /**
     * @Rest\View
     * @Rest\Get("/payment/robokassagen")
     */
    public function generateRobokassaAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $transaction = new Transaction();
        $transaction->setUser($currentUser)
            ->setType($request->get('type'))
            ->setSum($request->get('sum'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();

        return [
            'login' => 'iSolovey',
            'sum' => $request->get('sum'),
            'invId' => $transaction->getId(),
            'desc' => $request->get('type'),
            'sign' => md5('iSolovey:' . $request->get('sum') . ':' . $transaction->getId() . ':i73yxbdm')
        ];
    }

    /**
     * @Rest\View
     * @Rest\Get("/payment/yagen")
     */
    public function generateYandexAction(Request $request){

        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $transaction = new Transaction();
        $transaction->setUser($currentUser)
            ->setType($request->get('type'))
            ->setSum($request->get('sum'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();

        return [
            'label' => 'ya' . $transaction->getId(),
            'receiver' => '41001433432610',
            'method' => 'AC',
            'id' => $transaction->getId()
        ];
    }

    /**
     * @Rest\View
     * @Rest\Get("/payment/details/{id}")
     */
    public function getTransactionDetailsAction($id, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $transaction = $this->getDoctrine()->getManager()->find('AcmeEdelaBundle:Transaction', $id);
        if ($transaction->getUser() == $currentUser) {
            return $transaction;
        }
        return $this->createNotFoundException();

    }

    /**
     * @Rest\View
     * @Rest\Get("/payment/history")
     */
    public function getPaymentsHistoryAction(Request $request){
        /** @var User $currentUser */
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        $transactions = $currentUser->getTransactions()->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('isProcessed', 1))
        );

        return $transactions;
    }

}
