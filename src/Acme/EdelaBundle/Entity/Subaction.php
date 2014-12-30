<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subaction
 *
 * @ORM\Table(name="ed_subactions")
 * @ORM\Entity
 */
class Subaction
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
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="subactions")
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserSubactionProgress", mappedBy="subaction")
     */
    protected $userSubactionProgresses;

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
     * Set title
     *
     * @param string $title
     * @return Subaction
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Subaction
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
     * Set action
     *
     * @param \Acme\EdelaBundle\Entity\Action $action
     * @return Subaction
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

    /**
     * Add userSubactionProgresses
     *
     * @param \Acme\EdelaBundle\Entity\UserSubactionProgress $userSubactionProgresses
     * @return Subaction
     */
    public function addUserSubactionProgress(\Acme\EdelaBundle\Entity\UserSubactionProgress $userSubactionProgresses)
    {
        $this->userSubactionProgresses[] = $userSubactionProgresses;

        return $this;
    }

    /**
     * Remove userSubactionProgresses
     *
     * @param \Acme\EdelaBundle\Entity\UserSubactionProgress $userSubactionProgresses
     */
    public function removeUserSubactionProgress(\Acme\EdelaBundle\Entity\UserSubactionProgress $userSubactionProgresses)
    {
        $this->userSubactionProgresses->removeElement($userSubactionProgresses);
    }

    /**
     * Get userSubactionProgresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserSubactionProgresses()
    {
        return $this->userSubactionProgresses;
    }
}
