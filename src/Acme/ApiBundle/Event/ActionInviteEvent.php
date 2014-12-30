<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:32 AM
 */

namespace Acme\ApiBundle\Event;


use Acme\EdelaBundle\Entity\Action;
use Acme\EdelaBundle\Entity\ActionInvite;
use Acme\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class ActionInviteEvent extends Event{

    private $action;
    private $sender;
    private $receiver;
    private $invite;

    function __construct(Action $action, User $sender, User $receiver, ActionInvite $invite)
    {
        $this->action = $action;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->invite = $invite;
    }

    /**
     * @return \Acme\EdelaBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return \Acme\UserBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return \Acme\UserBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return mixed
     */
    public function getInvite()
    {
        return $this->invite;
    }


} 