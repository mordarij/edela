<?php

namespace Acme\EdelaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Achievement
 *
 * @ORM\Table(name="ed_achievements")
 * @ORM\Entity(repositoryClass="Acme\EdelaBundle\Repository\AchievementRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Achievement
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
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     * @Serializer\Accessor(getter="getImageWebPath")
     */
    private $image;

    /**
     * @var integer
     *
     * @ORM\Column(name="exp_reward", type="integer")
     */
    private $expReward;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserAchievement", mappedBy="achievement")
     */
    protected $userAchievements;

    /**
     * @ORM\Column(name="tkey", type="string")
     */
    private $tkey;

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
     * @return Achievement
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
     * @return Achievement
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
     * Set image
     *
     * @param string $image
     * @return Achievement
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set expReward
     *
     * @param integer $expReward
     * @return Achievement
     */
    public function setExpReward($expReward)
    {
        $this->expReward = $expReward;

        return $this;
    }

    /**
     * Get expReward
     *
     * @return integer 
     */
    public function getExpReward()
    {
        return $this->expReward;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userAchievements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userAchievements
     *
     * @param \Acme\EdelaBundle\Entity\UserAchievement $userAchievements
     * @return Achievement
     */
    public function addUserAchievement(\Acme\EdelaBundle\Entity\UserAchievement $userAchievements)
    {
        $this->userAchievements[] = $userAchievements;

        return $this;
    }

    /**
     * Remove userAchievements
     *
     * @param \Acme\EdelaBundle\Entity\UserAchievement $userAchievements
     */
    public function removeUserAchievement(\Acme\EdelaBundle\Entity\UserAchievement $userAchievements)
    {
        $this->userAchievements->removeElement($userAchievements);
    }

    /**
     * Get userAchievements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserAchievements()
    {
        return $this->userAchievements;
    }

    /**
     * Set tkey
     *
     * @param string $tkey
     * @return Achievement
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

    public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir() . '/' . $this->image;
    }

    public function getImageWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir() . '/' . $this->image;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/achievementimages';
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

        $this->image = $newFilename;

        $this->file = null;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload() {
        $this->upload();
    }
}
