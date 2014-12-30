<?php

namespace Acme\EdelaBundle\Controller;

use Acme\EdelaBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class PayController extends Controller
{
    public function resultAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $sum = $request->get('OutSum');
        $invId = $request->get('InvId');
        $sign = $request->get('SignatureValue');

        if ($sign == md5($sum . ':' . $invId . ':' . 'apx7qqhf')) {


            return new Response('OK' . $invId);
        }
        return new Response('Fail');
    }

    public function yaresultAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        return new Response('OK');

    }

    public function successAction()
    {

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $sum = $request->get('OutSum');
        $invId = $request->get('InvId');
        $sign = $request->get('SignatureValue');

        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($sign == md5($sum . ':' . $invId . ':' . 'i73yxbdm')) {

            $em = $this->getDoctrine()->getManager();
            /** @var Transaction $transaction */
            $transaction = $em->find('AcmeEdelaBundle:Transaction', $invId);
            if ($transaction->getSum() != $sum) {
                return $this->redirect($this->generateUrl('app', ['user_id' => $user->getId()]) . '#/payment/result/fail/' . $invId);
            }
            $em->getRepository('AcmeUserBundle:User')->increaseBill($user, $sum);
            $transaction->setIsProcessed(true)->setProcessedAt(new \DateTime());
            $em->persist($transaction);
            $em->flush();

            return $this->redirect($this->generateUrl('app', ['user_id' => $user->getId()]) . '#/payment/result/success/' . $invId);
        }
        return $this->redirect($this->generateUrl('app', ['user_id' => $user->getId()]) . '#/payment/result/fail/' . $invId);
    }

    public function yasuccessAction($id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        /** @var Transaction $transaction */
        $transaction = $em->find('AcmeEdelaBundle:Transaction', $id);
        if ($transaction->getUser() != $user) {
            return $this->redirect($this->generateUrl('app', ['user_id' => $user->getId()]) . '#/payment/result/fail/' . $id);
        }
        $em->getRepository('AcmeUserBundle:User')->increaseBill($user, $transaction->getSum());
        $transaction->setIsProcessed(true)->setProcessedAt(new \DateTime());
        $em->persist($transaction);
        $em->flush();

        return $this->redirect($this->generateUrl('app', ['user_id' => $user->getId()]) . '#/payment/result/success/' . $id);
    }

    public
    function failAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $invId = $request->get('InvId');

        $em = $this->getDoctrine()->getManager();
        /** @var Transaction $transaction */
        $transaction = $em->find('AcmeEdelaBundle:Transaction', $invId);

        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        if ($transaction && $transaction->getUser() == $currentUser) {
            $transaction->setIsProcessed(false)->setProcessedAt(new \DateTime());
            $em->persist($transaction);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('app', ['user_id' => $this->container->get('security.context')->getToken()->getUser()->getId()]) . '#/payment/result/fail/' . $invId);
    }

}
