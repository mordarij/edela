<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 7/29/14
 * Time: 12:39 PM
 */

namespace Acme\ApiBundle\Sender;


class EmailSender
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($email, $subject, $body){

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('send@e-dela.com')
            ->setTo($email)
            ->setBody(
                    $body
//                $this->renderView(
//                    'HelloBundle:Hello:email.txt.twig',
//                    array('name' => $name)
//                )
            )
        ;
        $this->mailer->send($message);

        return true;
    }

}