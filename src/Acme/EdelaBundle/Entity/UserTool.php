<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * UserTool
 *
 * @ORM\Table(name="ed_users_tools")
 * @ORM\Entity
 */
class UserTool
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Exclude
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="userTools")
     * @Serializer\Exclude
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Tool", inversedBy="userTools")
     * @Serializer\Exclude
     */
    private $tool;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    private $isEnabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_available", type="boolean")
     */
    private $isAvailable;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;


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
     * Set userId
     *
     * @param integer $userId
     * @return UserTool
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set toolId
     *
     * @param integer $toolId
     * @return UserTool
     */
    public function setToolId($toolId)
    {
        $this->toolId = $toolId;

        return $this;
    }

    /**
     * Get toolId
     *
     * @return integer 
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return UserTool
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean 
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return UserTool
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return UserTool
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
     * Set tool
     *
     * @param \Acme\EdelaBundle\Entity\Tool $tool
     * @return UserTool
     */
    public function setTool(\Acme\EdelaBundle\Entity\Tool $tool = null)
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * Get tool
     *
     * @return \Acme\EdelaBundle\Entity\Tool 
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * Set isAvailable
     *
     * @param boolean $isAvailable
     * @return UserTool
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * Get isAvailable
     *
     * @return boolean 
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    function __construct()
    {
        $this->isAvailable = false;
        $this->isEnabled = false;
        $this->weight = 1;
    }
}
