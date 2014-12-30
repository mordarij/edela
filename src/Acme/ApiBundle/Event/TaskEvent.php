<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:32 AM
 */

namespace Acme\ApiBundle\Event;


use Acme\EdelaBundle\Entity\Task;
use Acme\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class TaskEvent extends Event{

    private $task;

    function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return \Acme\EdelaBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

} 