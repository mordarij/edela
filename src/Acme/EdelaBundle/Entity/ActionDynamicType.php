<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActionDynamicType
 *
 * @ORM\Table(name="ed_action_dynamic_types")
 * @ORM\Entity
 */
class ActionDynamicType
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
     * @var string
     *
     * @ORM\Column(name="tkey", type="string", length=255)
     */
    private $tkey;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="actionDynamicType")
     */
    private $actions;


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
     * @return ActionDynamicType
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
     * Set tkey
     *
     * @param string $tkey
     * @return ActionDynamicType
     */
    public function setTkey($tkey)
    {
        $this->tkey = $tkey;

        return $this;
    }

    /**
     * Get tkey
     *
     * @return string 
     */
    public function getTkey()
    {
        return $this->tkey;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add actions
     *
     * @param \Acme\EdelaBundle\Entity\Action $actions
     * @return ActionDynamicType
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
}
