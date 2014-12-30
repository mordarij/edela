<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:32 AM
 */

namespace Acme\ApiBundle\Event;


use Acme\EdelaBundle\Entity\Action;
use Acme\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class ActionEvent extends Event{

    private $action;
    private $user;
    private $progress;

    function __construct(Action $action, User $user, $progress = true)
    {
        $this->action = $action;
        $this->user = $user;
        $this->progress = $progress;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function getProgress()
    {
        return $this->progress;
    }

} 