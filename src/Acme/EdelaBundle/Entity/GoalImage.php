<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GoalImage
 *
 * @ORM\Table(name="ed_goals_images")
 * @ORM\Entity
 */
class GoalImage
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
     * @var Goal
     *
     * @ORM\JoinColumn(name="goal_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Goal", inversedBy="goals")
     */
    private $goal;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="15M", mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"})
     */
    private $file;


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
     * Set path
     *
     * @param string $path
     * @return GoalImage
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return GoalImage
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return GoalImage
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
     * Set goal
     *
     * @param \Acme\EdelaBundle\Entity\Goal $goal
     * @return GoalImage
     */
    public function setGoal(\Acme\EdelaBundle\Entity\Goal $goal = null)
    {
        $this->goal = $goal;

        return $this;
    }

    function __construct()
    {
        $this->isActive = true;
        $this->createdAt = new \DateTime();
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

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/goalimages';
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        if (!in_array(strtolower($this->getFile()->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif'])){
            $extension = '';
        } else {
            $extension = strtolower($this->getFile()->getClientOriginalExtension());
        }

        $newFilename = uniqid('im') . '.' . $extension;
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $newFilename
        );

        $this->path = $newFilename;

        $this->file = null;
    }


}
