<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Task
 *
 * @ORM\Table(name="ed_tasks")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\TaskRepository")
 */
class Task
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\SerializedName("title")
     */
    private $name;

    /**
     * @var Goal
     *
     * @ORM\JoinColumn(name="goal_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Goal", inversedBy="tasks")
     * @Serializer\Exclude
     */
    private $goal;

    /**
     * @var Task
     *
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="childs")
     */
    private $parent;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="parent")
     * @Serializer\Exclude
     */
    private $childs;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_done", type="boolean")
     */
    private $isDone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="done_at", type="datetime", nullable=true)
     */
    private $doneAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_at", type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $dateAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="tasks")
     * @Serializer\Exclude
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    protected $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_urgent", type="boolean")
     */
    private $isUrgent;

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
     * @var \DateTime
     *
     * @ORM\Column(name="notification_time", type="time", nullable=true)
     * @Serializer\Type("DateTime<'H:i'>")
     */
    private $notificationTime;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinTable(name="ed_tasks_tags",
     *                  joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *                )
     */
    private $tags;

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
     * @return Task
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
     * Set isDone
     *
     * @param boolean $isDone
     * @return Task
     */
    public function setIsDone($isDone)
    {
        $this->isDone = $isDone;

        return $this;
    }

    /**
     * Get isDone
     *
     * @return boolean 
     */
    public function getIsDone()
    {
        return $this->isDone;
    }

    /**
     * Set dateAt
     *
     * @param \DateTime $dateAt
     * @return Task
     */
    public function setDateAt($dateAt)
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    /**
     * Get dateAt
     *
     * @return \DateTime 
     */
    public function getDateAt()
    {
        return $this->dateAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Task
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

    function __construct()
    {
        $this->isDone = false;
        $this->createdAt = new \DateTime();
        $this->priority = 1;
        $this->isPrivate = 0;
        $this->childs = new ArrayCollection();
        $this->isImportant = false;
        $this->isUrgent = false;
        $this->isSmsNotification = false;
        $this->tags = new ArrayCollection();
    }

    function __toString()
    {
        return $this->name;
    }

    /**
     * Set goal
     *
     * @param \Acme\EdelaBundle\Entity\Goal $goal
     * @return Task
     */
    public function setGoal(\Acme\EdelaBundle\Entity\Goal $goal = null)
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * Get goal
     *
     * @return \Acme\EdelaBundle\Entity\Goal 
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * Set parent
     *
     * @param \Acme\EdelaBundle\Entity\Task $parent
     * @return Task
     */
    public function setParent(\Acme\EdelaBundle\Entity\Task $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Acme\EdelaBundle\Entity\Task 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add childs
     *
     * @param \Acme\EdelaBundle\Entity\Task $childs
     * @return Task
     */
    public function addChild(\Acme\EdelaBundle\Entity\Task $childs)
    {
        $this->childs[] = $childs;

        return $this;
    }

    /**
     * Remove childs
     *
     * @param \Acme\EdelaBundle\Entity\Task $childs
     */
    public function removeChild(\Acme\EdelaBundle\Entity\Task $childs)
    {
        $this->childs->removeElement($childs);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return Task
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
     * Set note
     *
     * @param string $note
     * @return Task
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     * @return Task
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
     * Set doneAt
     *
     * @param \DateTime $doneAt
     * @return Task
     */
    public function setDoneAt($doneAt)
    {
        $this->doneAt = $doneAt;

        return $this;
    }

    /**
     * Get doneAt
     *
     * @return \DateTime 
     */
    public function getDoneAt()
    {
        return $this->doneAt;
    }

    /**
     * Set isUrgent
     *
     * @param boolean $isUrgent
     * @return Task
     */
    public function setIsUrgent($isUrgent)
    {
        $this->isUrgent = $isUrgent;

        return $this;
    }

    /**
     * Get isUrgent
     *
     * @return boolean 
     */
    public function getIsUrgent()
    {
        return $this->isUrgent;
    }

    /**
     * Set isImportant
     *
     * @param boolean $isImportant
     * @return Task
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
     * Set notificationTime
     *
     * @param \DateTime $notificationTime
     * @return Task
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
     * Set isSmsNotification
     *
     * @param boolean $isSmsNotification
     * @return Task
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
     * Add tags
     *
     * @param \Acme\EdelaBundle\Entity\Tag $tags
     * @return Task
     */
    public function addTag(\Acme\EdelaBundle\Entity\Tag $tags)
    {
        if (!$this->tags->contains($tags)){
            $this->tags[] = $tags;
        }

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Acme\EdelaBundle\Entity\Tag $tags
     */
    public function removeTag(\Acme\EdelaBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
}
