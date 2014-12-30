<?php

namespace Acme\EdelaBundle\Entity;

use Acme\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserAchievement
 *
 * @ORM\Table(name="ed_users_achievements")
 * @ORM\Entity
 */
class UserAchievement
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
     * @var User
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="userAchievements")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="achievement_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\EdelaBundle\Entity\Achievement", inversedBy="userAchievements")
     */
    private $achievement;

    /**
     * @var integer
     *
     * @ORM\Column(name="progress", type="integer")
     */
    private $progress;

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
     * Set progress
     *
     * @param integer $progress
     * @return UserAchievement
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return integer 
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserAchievement
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
     * @return UserAchievement
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
     * Set achievement
     *
     * @param \Acme\EdelaBundle\Entity\Achievement $achievement
     * @return UserAchievement
     */
    public function setAchievement(\Acme\EdelaBundle\Entity\Achievement $achievement = null)
    {
        $this->achievement = $achievement;

        return $this;
    }

    /**
     * Get achievement
     *
     * @return \Acme\EdelaBundle\Entity\Achievement 
     */
    public function getAchievement()
    {
        return $this->achievement;
    }

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
