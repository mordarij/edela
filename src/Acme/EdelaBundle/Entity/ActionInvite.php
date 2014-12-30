<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActionInvite
 *
 * @ORM\Table(name="ed_actions_invites")
 * @ORM\Entity
 */
class ActionInvite
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
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="sentActionInvites")
     */
    private $sender;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="receivedActionInvites")
     */
    private $receiver;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="actionInvites", cascade={"persist"})
     */
    private $action;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_accepted", type="boolean", nullable = true)
     */
    private $isAccepted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="accepted_at", type="datetime", nullable = true)
     */
    private $acceptedAt;


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
     * Set isAccepted
     *
     * @param boolean $isAccepted
     * @return ActionInvite
     */
    public function setIsAccepted($isAccepted)
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get isAccepted
     *
     * @return boolean
     */
    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ActionInvite
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
     * Set acceptedAt
     *
     * @param \DateTime $acceptedAt
     * @return ActionInvite
     */
    public function setAcceptedAt($acceptedAt)
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }

    /**
     * Get acceptedAt
     *
     * @return \DateTime
     */
    public function getAcceptedAt()
    {
        return $this->acceptedAt;
    }

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set sender
     *
     * @param \Acme\UserBundle\Entity\User $sender
     * @return ActionInvite
     */
    public function setSender(\Acme\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Acme\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver
     *
     * @param \Acme\UserBundle\Entity\User $receiver
     * @return ActionInvite
     */
    public function setReceiver(\Acme\UserBundle\Entity\User $receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Acme\UserBundle\Entity\User 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set action
     *
     * @param \Acme\EdelaBundle\Entity\Action $action
     * @return ActionInvite
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
}
