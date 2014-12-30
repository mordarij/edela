<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Action
 *
 * @ORM\Table(name="ed_actions")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\ActionRepository")
 */
class Action
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="actions")
     * @Serializer\Exclude
     */
    private $user;

    /**
     * @var Goal
     *
     * @ORM\JoinColumn(name="goal_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Goal", inversedBy="actions")
     */
    private $goal;

    /**
     * @ORM\JoinColumn(name="action_type_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="ActionType", inversedBy="actions")
     */
    private $actionType;

    /**
     * @ORM\JoinColumn(name="action_dynamic_type_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="ActionDynamicType", inversedBy="actions")
     */
    private $actionDynamicType;

    /**
     * @var string
     *
     * @ORM\Column(name="action_type_title", type="string", length=255, nullable=true)
     */
    private $actionTypeTitle;

    /**
     * @var integer
     *
     * @ORM\Column(name="repeat_amount", type="integer")
     */
    private $repeatAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="action_time", type="time", nullable=true)
     */
    private $actionTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="action_time_start", type="time", nullable=true)
     */
    private $actionTimeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="action_time_finish", type="time", nullable=true)
     */
    private $actionTimeFinish;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;



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
     * @ORM\OneToMany(targetEntity="UserAction", mappedBy="action", cascade={"persist"})
     */
    protected $userActions;

    /**
     * @ORM\OneToMany(targetEntity="ActionInvite", mappedBy="action", cascade={"persist"})
     */
    protected $actionInvites;

    /**
     * @ORM\OneToMany(targetEntity="Subaction", mappedBy="action", cascade={"persist"})
     */
    protected $subactions;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="actions", cascade={"persist"})
     * @ORM\JoinTable(name="ed_actions_tags",
     *                  joinColumns={@ORM\JoinColumn(name="action_id", referencedColumnName="id")},
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
     * Set title
     *
     * @param string $title
     * @return Action
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set actionTypeTitle
     *
     * @param string $actionTypeTitle
     * @return Action
     */
    public function setActionTypeTitle($actionTypeTitle)
    {
        $this->actionTypeTitle = $actionTypeTitle;

        return $this;
    }

    /**
     * Get actionTypeTitle
     *
     * @return string 
     */
    public function getActionTypeTitle()
    {
        return $this->actionTypeTitle;
    }

    /**
     * Set repeatAmount
     *
     * @param integer $repeatAmount
     * @return Action
     */
    public function setRepeatAmount($repeatAmount)
    {
        $this->repeatAmount = $repeatAmount;

        return $this;
    }

    /**
     * Get repeatAmount
     *
     * @return integer 
     */
    public function getRepeatAmount()
    {
        return $this->repeatAmount;
    }

    /**
     * Set actionTime
     *
     * @param \DateTime $actionTime
     * @return Action
     */
    public function setActionTime($actionTime)
    {
        $this->actionTime = $actionTime;

        return $this;
    }

    /**
     * Get actionTime
     *
     * @return \DateTime 
     */
    public function getActionTime()
    {
        return $this->actionTime;
    }

    /**
     * Set actionTimeStart
     *
     * @param \DateTime $actionTimeStart
     * @return Action
     */
    public function setActionTimeStart($actionTimeStart)
    {
        $this->actionTimeStart = $actionTimeStart;

        return $this;
    }

    /**
     * Get actionTimeStart
     *
     * @return \DateTime 
     */
    public function getActionTimeStart()
    {
        return $this->actionTimeStart;
    }

    /**
     * Set actionTimeFinish
     *
     * @param \DateTime $actionTimeFinish
     * @return Action
     */
    public function setActionTimeFinish($actionTimeFinish)
    {
        $this->actionTimeFinish = $actionTimeFinish;

        return $this;
    }

    /**
     * Get actionTimeFinish
     *
     * @return \DateTime 
     */
    public function getActionTimeFinish()
    {
        return $this->actionTimeFinish;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Action
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Action
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
     * @return Action
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
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return Action
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
     * Set goal
     *
     * @param \Acme\EdelaBundle\Entity\Goal $goal
     * @return Action
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
     * Set actionType
     *
     * @param \Acme\EdelaBundle\Entity\ActionType $actionType
     * @return Action
     */
    public function setActionType(\Acme\EdelaBundle\Entity\ActionType $actionType = null)
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * Get actionType
     *
     * @return \Acme\EdelaBundle\Entity\ActionType 
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Set actionDynamicType
     *
     * @param \Acme\EdelaBundle\Entity\ActionDynamicType $actionDynamicType
     * @return Action
     */
    public function setActionDynamicType(\Acme\EdelaBundle\Entity\ActionDynamicType $actionDynamicType = null)
    {
        $this->actionDynamicType = $actionDynamicType;

        return $this;
    }

    /**
     * Get actionDynamicType
     *
     * @return \Acme\EdelaBundle\Entity\ActionDynamicType 
     */
    public function getActionDynamicType()
    {
        return $this->actionDynamicType;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userActions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->repeatAmount = 40;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->isPrivate = false;
    }

    /**
     * Add userActions
     *
     * @param \Acme\EdelaBundle\Entity\UserAction $userActions
     * @return Action
     */
    public function addUserAction(\Acme\EdelaBundle\Entity\UserAction $userActions)
    {
        $userActions->setAction($this);
        $this->userActions[] = $userActions;

        return $this;
    }

    /**
     * Remove userActions
     *
     * @param \Acme\EdelaBundle\Entity\UserAction $userActions
     */
    public function removeUserAction(\Acme\EdelaBundle\Entity\UserAction $userActions)
    {
        $this->userActions->removeElement($userActions);
    }

    /**
     * Get userActions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserActions()
    {
        return $this->userActions;
    }

    /**
     * Add subactions
     *
     * @param \Acme\EdelaBundle\Entity\Subaction $subactions
     * @return Action
     */
    public function addSubaction(\Acme\EdelaBundle\Entity\Subaction $subactions)
    {
        $subactions->setAction($this);
        $this->subactions[] = $subactions;

        return $this;
    }

    /**
     * Remove subactions
     *
     * @param \Acme\EdelaBundle\Entity\Subaction $subactions
     */
    public function removeSubaction(\Acme\EdelaBundle\Entity\Subaction $subactions)
    {
        $this->subactions->removeElement($subactions);
    }

    /**
     * Get subactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubactions()
    {
        return $this->subactions;
    }


    /**
     * Add tags
     *
     * @param \Acme\EdelaBundle\Entity\Tag $tags
     * @return Action
     */
    public function addTag(\Acme\EdelaBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

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

    /**
     * Add actionInvites
     *
     * @param \Acme\EdelaBundle\Entity\ActionInvite $actionInvites
     * @return Action
     */
    public function addActionInvite(\Acme\EdelaBundle\Entity\ActionInvite $actionInvites)
    {
        $this->actionInvites[] = $actionInvites;

        return $this;
    }

    /**
     * Remove actionInvites
     *
     * @param \Acme\EdelaBundle\Entity\ActionInvite $actionInvites
     */
    public function removeActionInvite(\Acme\EdelaBundle\Entity\ActionInvite $actionInvites)
    {
        $this->actionInvites->removeElement($actionInvites);
    }

    /**
     * Get actionInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActionInvites()
    {
        return $this->actionInvites;
    }
}
