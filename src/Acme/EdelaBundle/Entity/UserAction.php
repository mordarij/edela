<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserAction
 *
 * @ORM\Table(name="ed_users_actions")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\UserActionRepository")
 */
class UserAction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="userActions")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="userActions", cascade={"persist"})
     */
    private $action;

    /**
     * @var integer
     *
     * @ORM\Column(name="periodicity", type="integer")
     * @Assert\Range(min=0, max=127)
     */
    private $periodicity;

    /**
     * @ORM\Column(name="periodicity_interval", type="integer")
     */
    private $periodicityInterval;

    /**
     * @ORM\OneToMany(targetEntity="UserActionProgress", mappedBy="userAction", cascade={"persist"})
     */
    private $userActionProgresses;

    /**
     * @ORM\Column(name="is_finished", type="boolean")
     */
    private $isFinished;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_time_report", type="boolean")
     */
    private $isTimeReport;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_important", type="boolean")
     */
    private $isImportant;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sms_notification", type="boolean")
     */
    private $isSmsNotification;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_email_notification", type="boolean")
     */
    private $isEmailNotification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="notification_time", type="time", nullable=true)
     */
    private $notificationTime;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer");
     */
    private $position;

     /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set periodicity
     *
     * @param integer $periodicity
     * @return UserAction
     */
    public function setPeriodicity($periodicity)
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    /**
     * Get periodicity
     *
     * @return integer
     */
    public function getPeriodicity()
    {
        return $this->periodicity;
    }

    /**
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return UserAction
     */
    public function setUser(\Acme\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Acme\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set action
     *
     * @param \Acme\EdelaBundle\Entity\Action $action
     * @return UserAction
     */
    public function setAction(\Acme\EdelaBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \Acme\EdelaBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }

    function __construct()
    {
        $this->position = 0;
        $this->createdAt = new \DateTime();
        $this->periodicity = 0;
        foreach (range(0, 6) as $day) {
            $this->addDayOfWeek($day);
        }
        $this->isFinished = false;
        $this->periodicityInterval = 0;
        $this->isPrivate = false;
        $this->startAt = new \DateTime();
        $this->isTimeReport = false;
        $this->isImportant = false;
        $this->isSmsNotification = false;
        $this->isEmailNotification = false;
    }

    public function addDayOfWeek($dayOfWeek)
    {
        $this->periodicity |= (1 << $dayOfWeek);
    }

    public function removeDayOfWeek($dayOfWeek)
    {
        $this->periodicity &= ~(1 << $dayOfWeek);
    }

    public function getIsDayIncluded(\DateTime $date)
    {
        return ($this->periodicity & (1 << $date->format('w'))) ||
        ($this->periodicityInterval && $this->createdAt->diff($date)->days % $this->periodicityInterval == 0);
    }

    /**
     * Add userActionProgresses
     *
     * @param \Acme\EdelaBundle\Entity\UserActionProgress $userActionProgresses
     * @return UserAction
     */
    public function addUserActionProgress(\Acme\EdelaBundle\Entity\UserActionProgress $userActionProgresses)
    {
        $this->userActionProgresses[] = $userActionProgresses;

        return $this;
    }

    /**
     * Remove userActionProgresses
     *
     * @param \Acme\EdelaBundle\Entity\UserActionProgress $userActionProgresses
     */
    public function removeUserActionProgress(\Acme\EdelaBundle\Entity\UserActionProgress $userActionProgresses)
    {
        $this->userActionProgresses->removeElement($userActionProgresses);
    }

    /**
     * Get userActionProgresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserActionProgresses()
    {
        return $this->userActionProgresses;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     * @return UserAction
     */
    public function setIsFinished($isFinished)
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    /**
     * Get isFinished
     *
     * @return boolean 
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }

    /**
     * Set periodicityInterval
     *
     * @param integer $periodicityInterval
     * @return UserAction
     */
    public function setPeriodicityInterval($periodicityInterval)
    {
        $this->periodicityInterval = $periodicityInterval;

        return $this;
    }

    /**
     * Get periodicityInterval
     *
     * @return integer 
     */
    public function getPeriodicityInterval()
    {
        return $this->periodicityInterval;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserAction
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     * @return UserAction
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return boolean 
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return UserAction
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set isTimeReport
     *
     * @param boolean $isTimeReport
     * @return UserAction
     */
    public function setIsTimeReport($isTimeReport)
    {
        $this->isTimeReport = $isTimeReport;

        return $this;
    }

    /**
     * Get isTimeReport
     *
     * @return boolean 
     */
    public function getIsTimeReport()
    {
        return $this->isTimeReport;
    }

    /**
     * Set isImportant
     *
     * @param boolean $isImportant
     * @return UserAction
     */
    public function setIsImportant($isImportant)
    {
        $this->isImportant = $isImportant;

        return $this;
    }

    /**
     * Get isImportant
     *
     * @return boolean 
     */
    public function getIsImportant()
    {
        return $this->isImportant;
    }

    /**
     * Set isSmsNotification
     *
     * @param boolean $isSmsNotification
     * @return UserAction
     */
    public function setIsSmsNotification($isSmsNotification)
    {
        $this->isSmsNotification = $isSmsNotification;

        return $this;
    }

    /**
     * Get isSmsNotification
     *
     * @return boolean 
     */
    public function getIsSmsNotification()
    {
        return $this->isSmsNotification;
    }

    /**
     * Set isEmailNotification
     *
     * @param boolean $isEmailNotification
     * @return UserAction
     */
    public function setIsEmailNotification($isEmailNotification)
    {
        $this->isEmailNotification = $isEmailNotification;

        return $this;
    }

    /**
     * Get isEmailNotification
     *
     * @return boolean 
     */
    public function getIsEmailNotification()
    {
        return $this->isEmailNotification;
    }

    /**
     * Set notificationTime
     *
     * @param \DateTime $notificationTime
     * @return UserAction
     */
    public function setNotificationTime($notificationTime)
    {
        $this->notificationTime = $notificationTime;

        return $this;
    }

    /**
     * Get notificationTime
     *
     * @return \DateTime 
     */
    public function getNotificationTime()
    {
        return $this->notificationTime;
    }


    /**
     * Set position
     *
     * @param integer $position
     * @return UserAction
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
    
 /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return UserAction
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
}
