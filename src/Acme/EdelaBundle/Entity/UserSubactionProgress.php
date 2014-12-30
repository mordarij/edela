<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSubactionProgress
 *
 * @ORM\Table(name="ed_users_subactions_progresses")
 * @ORM\Entity
 */
class UserSubactionProgress
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
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="userSubactionProgresses")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="subaction_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Subaction", inversedBy="userSubactionProgresses")
     */
    private $subaction;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserSubactionProgress
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
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return UserSubactionProgress
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
     * Set subaction
     *
     * @param \Acme\EdelaBundle\Entity\Subaction $subaction
     * @return UserSubactionProgress
     */
    public function setSubaction(\Acme\EdelaBundle\Entity\Subaction $subaction = null)
    {
        $this->subaction = $subaction;

        return $this;
    }

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get subaction
     *
     * @return \Acme\EdelaBundle\Entity\Subaction 
     */
    public function getSubaction()
    {
        return $this->subaction;
    }
}
