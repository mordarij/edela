<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Goal
 *
 * @ORM\Table(name="ed_goals")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\GoalRepository")
 */
class Goal
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=1023)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="goals")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_slideshow", type="boolean")
     */
    private $isSlideshow;

    /**
     * @var integer
     *
     * @ORM\Column(name="slideshow_interval", type="integer")
     */
    private $slideshowInterval;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="GoalImage", mappedBy="goal", cascade={"remove"})
     */
    private $images;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="goal")
     */
    private $tasks;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Action", mappedBy="goal")
     */
    private $actions;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_saved", type="boolean")
     */
    private $isSaved;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;

    /**
     * @var boolean
     * @ORM\Column(name="is_important", type="boolean")
     */
    private $isImportant;


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
     * Set name
     *
     * @param string $name
     * @return Goal
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     * @return Goal
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Goal
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Goal
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set isSlideshow
     *
     * @param boolean $isSlideshow
     * @return Goal
     */
    public function setIsSlideshow($isSlideshow)
    {
        $this->isSlideshow = $isSlideshow;

        return $this;
    }

    /**
     * Get isSlideshow
     *
     * @return boolean 
     */
    public function getIsSlideshow()
    {
        return $this->isSlideshow;
    }

    /**
     * Set slideshowInterval
     *
     * @param integer $slideshowInterval
     * @return Goal
     */
    public function setSlideshowInterval($slideshowInterval)
    {
        $this->slideshowInterval = $slideshowInterval;

        return $this;
    }

    /**
     * Get slideshowInterval
     *
     * @return integer 
     */
    public function getSlideshowInterval()
    {
        return $this->slideshowInterval;
    }

    /**
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return Goal
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

    function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->isPrivate = false;
        $this->isSlideshow = false;
        $this->slideshowInterval = 10;
        $this->priority = 5;
        $this->isSaved = false;
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isDeleted = false;
        $this->isImportant = false;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Goal
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Add images
     *
     * @param \Acme\EdelaBundle\Entity\GoalImage $images
     * @return Goal
     */
    public function addImage(\Acme\EdelaBundle\Entity\GoalImage $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Acme\EdelaBundle\Entity\GoalImage $images
     */
    public function removeImage(\Acme\EdelaBundle\Entity\GoalImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    function __toString()
    {
        return $this->name;
    }

    /**
     * Set isSaved
     *
     * @param boolean $isSaved
     * @return Goal
     */
    public function setIsSaved($isSaved)
    {
        $this->isSaved = $isSaved;

        return $this;
    }

    /**
     * Get isSaved
     *
     * @return boolean 
     */
    public function getIsSaved()
    {
        return $this->isSaved;
    }

    /**
     * Add tasks
     *
     * @param \Acme\EdelaBundle\Entity\Task $tasks
     * @return Goal
     */
    public function addTask(\Acme\EdelaBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Acme\EdelaBundle\Entity\Task $tasks
     */
    public function removeTask(\Acme\EdelaBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add actions
     *
     * @param \Acme\EdelaBundle\Entity\Action $actions
     * @return Goal
     */
    public function addAction(\Acme\EdelaBundle\Entity\Action $actions)
    {
        $this->actions[] = $actions;

        return $this;
    }

    /**
     * Remove actions
     *
     * @param \Acme\EdelaBundle\Entity\Action $actions
     */
    public function removeAction(\Acme\EdelaBundle\Entity\Action $actions)
    {
        $this->actions->removeElement($actions);
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Goal
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

    /**
     * Set isImportant
     *
     * @param boolean $isImportant
     * @return Goal
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
}
