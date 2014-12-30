<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * UserActionComment
 *
 * @ORM\Table(name="ed_user_actions_comments")
 * @ORM\Entity
 */
class UserActionComment
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
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="owner_comments")
     * @Serializer\Exclude
     */
    private $owner;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="action_comments")
     * @Serializer\Exclude
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }


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
     * Set text
     *
     * @param string $text
     * @return UserActionComment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserActionComment
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
     * Set owner
     *
     * @param \Acme\UserBundle\Entity\User $owner
     * @return UserActionComment
     */
    public function setOwner(\Acme\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Acme\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set sender
     *
     * @param \Acme\UserBundle\Entity\User $sender
     * @return UserActionComment
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
}
