<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserActionProgress
 *
 * @ORM\Table(name="ed_users_actions_progresses")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\UserActionProgressRepository")
 */
class UserActionProgress
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
     * @ORM\JoinColumn(name="user_action_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="UserAction", inversedBy="userActionProgresses")
     */
    private $userAction;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer")
     */
    private $result;

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

    function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->result = 1;
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
     * Set result
     *
     * @param integer $result
     * @return UserActionProgress
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return integer
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return UserActionProgress
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
     * @return UserActionProgress
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
     * Set userAction
     *
     * @param \Acme\EdelaBundle\Entity\UserAction $userAction
     * @return UserActionProgress
     */
    public function setUserAction(\Acme\EdelaBundle\Entity\UserAction $userAction = null)
    {
        $this->userAction = $userAction;

        return $this;
    }

    /**
     * Get userAction
     *
     * @return \Acme\EdelaBundle\Entity\UserAction
     */
    public function getUserAction()
    {
        return $this->userAction;
    }
}
