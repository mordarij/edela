<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:33 AM
 */

namespace Acme\ApiBundle\Subscriber;


use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\EventStore;
use Acme\ApiBundle\Event\TaskEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExperienceSubscriber{

    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onActionComplete(ActionEvent $event){
        if ($event->getProgress() === null) return;
        $exp = $event->getProgress() ? 2 : -2;
        $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getUser(), $exp);
    }

    public function onTaskComplete(TaskEvent $event){
        $exp = 1;
        $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getTask()->getUser(), $exp);
    }
}