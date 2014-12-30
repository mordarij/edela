<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Tool
 *
 * @ORM\Table(name="ed_tools")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\ToolRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Tool
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_level", type="integer")
     */
    private $minLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="cost", type="integer")
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="text")
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255)
     * @Serializer\Accessor(getter="getPictureWebPath")
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="big_picture", type="string", length=255)
     * @Serializer\Accessor(getter="getBigPictureWebPath")
     */
    private $bigPicture;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255)
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserTool", mappedBy="tool")
     * @Serializer\Exclude
     */
    protected $userTools;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="15M", mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"})
     */
    private $file;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="15M", mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"})
     */
    private $bigFile;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload() {
        $this->upload();
        $this->bigUpload();
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
     * @return Tool
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
     * Set description
     *
     * @param string $description
     * @return Tool
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set minLevel
     *
     * @param integer $minLevel
     * @return Tool
     */
    public function setMinLevel($minLevel)
    {
        $this->minLevel = $minLevel;

        return $this;
    }

    /**
     * Get minLevel
     *
     * @return integer 
     */
    public function getMinLevel()
    {
        return $this->minLevel;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     * @return Tool
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return integer 
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Tool
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return Tool
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }


    /**
     * Set class
     *
     * @param string $class
     * @return Tool
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userTools = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userTools
     *
     * @param \Acme\EdelaBundle\Entity\UserTool $userTools
     * @return Tool
     */
    public function addUserTool(\Acme\EdelaBundle\Entity\UserTool $userTools)
    {
        $this->userTools[] = $userTools;

        return $this;
    }

    /**
     * Remove userTools
     *
     * @param \Acme\EdelaBundle\Entity\UserTool $userTools
     */
    public function removeUserTool(\Acme\EdelaBundle\Entity\UserTool $userTools)
    {
        $this->userTools->removeElement($userTools);
    }

    /**
     * Get userTools
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserTools()
    {
        return $this->userTools;
    }

    public function getAbsolutePath()
    {
        return null === $this->picture
            ? null
            : $this->getUploadRootDir() . '/' . $this->picture;
    }

    public function getPictureWebPath()
    {
        return null === $this->picture
            ? null
            : $this->getUploadDir() . '/' . $this->picture;
    }

    public function getBigPictureWebPath()
    {
        return null === $this->bigPicture
            ? null
            : $this->getUploadDir() . '/' . $this->bigPicture;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/toolimages';
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

        $this->picture = $newFilename;

        $this->file = null;
    }

    public function bigUpload()
    {
        if (null === $this->getBigFile()) {
            return;
        }

        if (!in_array(strtolower($this->getBigFile()->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif'])){
            $extension = '';
        } else {
            $extension = strtolower($this->getBigFile()->getClientOriginalExtension());
        }

        $newFilename = uniqid('im') . '.' . $extension;
        $this->getBigFile()->move(
            $this->getUploadRootDir(),
            $newFilename
        );

        $this->bigPicture = $newFilename;

        $this->bigFile = null;
    }

    /**
     * @return string
     */
    public function getBigPicture()
    {
        return $this->bigPicture;
    }

    /**
     * @param string $bigPicture
     */
    public function setBigPicture($bigPicture)
    {
        $this->bigPicture = $bigPicture;
        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getBigFile()
    {
        return $this->bigFile;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $bigFile
     */
    public function setBigFile($bigFile)
    {
        $this->bigFile = $bigFile;
    }
}
