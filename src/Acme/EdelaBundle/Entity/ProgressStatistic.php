<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * ProgressStatistic
 *
 * @ORM\Table(name="ed_progress_statistics")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\ProgressStatisticRepository")
 */
class ProgressStatistic
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
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="progressStatistics")
     * @Serializer\Exclude
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_actions", type="integer")
     */
    private $totalActions;

    /**
     * @var integer
     *
     * @ORM\Column(name="progressed_actions", type="integer")
     */
    private $progressedActions;

    /**
     * @ORM\Column(name="efficiency")
     */
    private $efficiency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="calculated_at", type="datetime")
     */
    private $calculatedAt;


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
     * Set totalActions
     *
     * @param integer $totalActions
     * @return ProgressStatistic
     */
    public function setTotalActions($totalActions)
    {
        $this->totalActions = $totalActions;

        return $this;
    }

    /**
     * Get totalActions
     *
     * @return integer 
     */
    public function getTotalActions()
    {
        return $this->totalActions;
    }

    /**
     * Set progressedActions
     *
     * @param integer $progressedActions
     * @return ProgressStatistic
     */
    public function setProgressedActions($progressedActions)
    {
        $this->progressedActions = $progressedActions;

        return $this;
    }

    /**
     * Get progressedActions
     *
     * @return integer 
     */
    public function getProgressedActions()
    {
        return $this->progressedActions;
    }

    /**
     * Set calculatedAt
     *
     * @param \DateTime $calculatedAt
     * @return ProgressStatistic
     */
    public function setCalculatedAt($calculatedAt)
    {
        $this->calculatedAt = $calculatedAt;

        return $this;
    }

    /**
     * Get calculatedAt
     *
     * @return \DateTime 
     */
    public function getCalculatedAt()
    {
        return $this->calculatedAt;
    }

    /**
     * Set user
     *
     * @param \Acme\UserBundle\Entity\User $user
     * @return ProgressStatistic
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
     * Set efficiency
     *
     * @param string $efficiency
     * @return ProgressStatistic
     */
    public function setEfficiency($efficiency)
    {
        $this->efficiency = $efficiency;

        return $this;
    }

    /**
     * Get efficiency
     *
     * @return string 
     */
    public function getEfficiency()
    {
        return $this->efficiency;
    }

    function __construct()
    {
        $this->calculatedAt = new \DateTime();
    }
}
