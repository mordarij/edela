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
use Acme\EdelaBundle\Entity\Notification;
use Acme\EdelaBundle\Entity\UserAchievement;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActionInviteSubscriber
{

    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function inviteSent(ActionInviteEvent $event)
    {
        $notification = new Notification();
        $notification->setUser($event->getReceiver())
            ->setActions(json_encode(['accept' => '$scope.post("api/actions/invites/' . $event->getInvite()->getId() . '/accept")',
                'reject' => '$scope.post("api/actions/invites/' . $event->getInvite()->getId() . '/reject")']))
            ->setText('Вас пригласили в дело')
            ->setIcon('abc');
        $this->em->persist($notification);
        $this->em->flush();
    }

}