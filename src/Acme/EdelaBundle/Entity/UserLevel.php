<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserLevel
 *
 * @ORM\Table(name="ed_user_levels")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UserLevel
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
     * @ORM\Column(name="start_exp", type="integer")
     */
    private $startExp;

    /**
     * @var integer
     *
     * @ORM\Column(name="tools_num", type="integer")
     */
    private $toolsNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255)
     * @Serializer\Accessor(getter="getPictureWebPath")
     */
    private $picture;

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
     * Set title
     *
     * @param string $title
     * @return UserLevel
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
     * Set startExp
     *
     * @param string $startExp
     * @return UserLevel
     */
    public function setStartExp($startExp)
    {
        $this->startExp = $startExp;

        return $this;
    }

    /**
     * Get startExp
     *
     * @return string 
     */
    public function getStartExp()
    {
        return $this->startExp;
    }

    /**
     * Set toolsNum
     *
     * @param integer $toolsNum
     * @return UserLevel
     */
    public function setToolsNum($toolsNum)
    {
        $this->toolsNum = $toolsNum;

        return $this;
    }

    /**
     * Get toolsNum
     *
     * @return integer 
     */
    public function getToolsNum()
    {
        return $this->toolsNum;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return UserLevel
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload() {
        $this->upload();
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

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
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
        touch('/tmp/upl1');
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

    protected function getUploadDir()
    {
        return '/uploads/levelimages';
    }
}
