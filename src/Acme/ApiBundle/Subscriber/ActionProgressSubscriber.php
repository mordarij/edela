<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:33 AM
 */

namespace Acme\ApiBundle\Subscriber;


use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\ActionInviteEvent;
use Acme\ApiBundle\Event\SubactionProgressEvent;
use Acme\EdelaBundle\Entity\Notification;
use Acme\EdelaBundle\Entity\UserActionProgress;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActionProgressSubscriber
{

    private $em;
    private $completeEvent;

    function __construct(EntityManager $em, $completeEvent)
    {
        $this->em = $em;
        $this->completeEvent = $completeEvent;
    }

    public function subactionComplete(SubactionProgressEvent $event, $name, EventDispatcher $dispatcher)
    {
        $action = $event->getSubaction()->getAction();
        $user = $event->getUser();
        $userAction = $this->em->getRepository('AcmeEdelaBundle:UserAction')->findOneBy(array('user' => $user, 'action' => $action));
        $userTime = $user->getCurrentDateTime();
        $dayStart = clone $userTime;
        $dayFinish = clone $userTime;
        $dayStart->modify('today midnight');
        $dayFinish->modify('tomorrow midnight');
        $existingProgress = $this->em->getRepository('AcmeEdelaBundle:UserActionProgress')->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('userAction', $userAction))
                ->andWhere(Criteria::expr()->gte('createdAt', $dayStart))
                ->andWhere(Criteria::expr()->lte('createdAt', $dayFinish))
        );
        $dispatch = null;
        if (!$existingProgress->count() && $action->getSubactions()->count() == count($this->em->getRepository('AcmeEdelaBundle:Action')->getSubactions($action->getId(), $user, null, true))) {
            $progress = new UserActionProgress();
            $progress->setUserAction($userAction);
            $progress->setResult(1);
            $this->em->persist($progress);
            $dispatch = true;
        } elseif($existingProgress->count()){
            $this->em->remove($existingProgress->first());
            $dispatch = false;
        }
        $this->em->flush();
        if ($dispatch !== null){
            $event = new ActionEvent($action, $user, $dispatch);
            $dispatcher->dispatch($this->completeEvent, $event);
        }
    }

}