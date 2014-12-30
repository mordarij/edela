<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSettings
 *
 * @ORM\Table(name="ed_users_settings",uniqueConstraints={@ORM\UniqueConstraint(name="settings_idx", columns={"user_id", "tkey"})})
 * @ORM\Entity
 */
class UserSettings
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
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="settings")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="tkey", type="string", length=255)
     */
    private $tkey;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


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
     * Set tkey
     *
     * @param string $tkey
     * @return UserSettings
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
     * Set value
     *
     * @param string $value
     * @return UserSettings
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return UserSettings
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
}
