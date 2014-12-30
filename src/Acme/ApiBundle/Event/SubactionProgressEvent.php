<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:32 AM
 */

namespace Acme\ApiBundle\Event;


use Acme\EdelaBundle\Entity\Subaction;
use Acme\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class SubactionProgressEvent extends Event{

    private $subaction;
    private $user;
    private $progress;

    function __construct(Subaction $subaction, User $user, $progress)
    {
        $this->subaction = $subaction;
        $this->user = $user;
        $this->progress = $progress;
    }

    /**
     * @return \Acme\EdelaBundle\Entity\Subaction
     */
    public function getSubaction()
    {
        return $this->subaction;
    }

    /**
     * @return \Acme\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

} 