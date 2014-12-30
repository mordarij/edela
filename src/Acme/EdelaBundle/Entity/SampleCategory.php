<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SampleCategory
 *
 * @ORM\Table(name="ed_sample_categories")
 * @ORM\Entity
 */
class SampleCategory
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
     * @ORM\ManyToMany(targetEntity="Sample", mappedBy="categories")
     */
    private $samples;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->samples = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return SampleCategory
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
     * Add samples
     *
     * @param \Acme\EdelaBundle\Entity\Sample $samples
     * @return SampleCategory
     */
    public function addSample(\Acme\EdelaBundle\Entity\Sample $samples)
    {
        $this->samples[] = $samples;

        return $this;
    }

    /**
     * Remove samples
     *
     * @param \Acme\EdelaBundle\Entity\Sample $samples
     */
    public function removeSample(\Acme\EdelaBundle\Entity\Sample $samples)
    {
        $this->samples->removeElement($samples);
    }

    /**
     * Get samples
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSamples()
    {
        return $this->samples;
    }

    function __toString()
    {
        return $this->title;
    }
}
