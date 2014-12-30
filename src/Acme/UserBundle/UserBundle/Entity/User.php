<?php

namespace Acme\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="Acme\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="ed_users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="user.fullname.not_blank", groups={"Registration", "Profile"})
     * @Assert\Length(min="3", max="50", minMessage="The name is too short.", maxMessage="The name is too long.", groups={"Registration", "Profile"})
     */
    protected $fullname;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\Range(min="-12", max="12", groups={"Registration", "Profile"})
     */
    protected $timezone;

    /**
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     * @Serializer\AccessType("public_method")
     */
    protected $photo;

    /**
     * @ORM\Column(name="language", type="string")
     */
    protected $language;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Goal", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $goals;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Task", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $tasks;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserTool", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $userTools;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Action", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $actions;

    /**
     * @ORM\OneToMany(targetEntity="UserSettings", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $settings;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Action", mappedBy="owner")
     * @Serializer\Exclude
     */
    protected $owner_comments;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Action", mappedBy="sender")
     * @Serializer\Exclude
     */
    protected $action_comments;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Dayoff", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $dayoffs;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Transaction", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $transactions;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\ProgressStatistic", mappedBy="user")
     * @ORM\OrderBy({"calculatedAt" = "ASC"})
     * @Serializer\Exclude
     */
    protected $progressStatistics;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserRelation", mappedBy="sender")
     * @Serializer\Exclude
     */
    protected $sentUserRelations;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserRelation", mappedBy="receiver")
     * @Serializer\Exclude
     */
    protected $receivedUserRelations;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\ChatMessage", mappedBy="sender")
     * @Serializer\Exclude
     */
    protected $sentMessages;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\ChatMessage", mappedBy="receiver")
     * @Serializer\Exclude
     */
    protected $receivedMessages;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\ActionInvite", mappedBy="sender")
     * @Serializer\Exclude
     */
    protected $sentActionInvites;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\ActionInvite", mappedBy="receiver")
     * @Serializer\Exclude
     */
    protected $receivedActionInvites;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\Notification", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $notifications;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserAchievement", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $userAchievements;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserAction", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $userActions;

    /**
     * @ORM\OneToMany(targetEntity="Acme\EdelaBundle\Entity\UserSubactionProgress", mappedBy="user")
     * @Serializer\Exclude
     */
    protected $userSubactionProgresses;

    /**
     * @var string
     *
     * @ORM\Column(name="vkontakte_id", type="string", nullable=true)
     * @Serializer\Exclude
     */
    protected $vkontakteId;

    /**
     * @var string
     *
     * @ORM\Column(name="vkontakte_access_token", type="string", nullable=true)
     * @Serializer\Exclude
     */
    protected $vkontakteAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", nullable=true)
     * @Serializer\Exclude
     */
    protected $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_access_token", type="string", nullable=true)
     * @Serializer\Exclude
     */
    protected $facebookAccessToken;

    /**
     * @ORM\Column(name="exp_total", type="integer")
     * @Serializer\Exclude
     */
    protected $exp_total;

    /**
     * @ORM\Column(name="exp_bill", type="integer")
     * @Serializer\Exclude
     */
    protected $exp_bill;

    /**
     * @ORM\Column(name="bill", type="integer")
     * @Serializer\Exclude
     */
    protected $bill;

    /**
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    protected $birthday;

    /**
     * @ORM\Column(name="is_subscribed", type="boolean")
     */
    protected $isSubscribed;

    /**
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="15M", mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"})
     */
    private $file;

    /**
     * @ORM\Column(name="paid_sms", type="integer")
     */
    private $paid_sms;

    /**
     * @ORM\Column(name="paid_dayoffs", type="integer")
     */
    private $paid_dayoffs;

    /**
     * @ORM\Column(name="is_online", type="boolean")
     */
    private $is_online;

    public function __construct()
    {
        parent::__construct();
        $this->language = 'ru';
        $this->timezone = 0;
        $this->exp_total = 0;
        $this->exp_bill = 0;
        $this->userAchievements = new ArrayCollection();
        $this->isSubscribed = true;
        $this->paid_sms = 0;
        $this->paid_dayoffs = 0;
        $this->is_online = false;
        $this->bill = 0;
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
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set timezone
     *
     * @param integer $timezone
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return integer 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Add goals
     *
     * @param \Acme\EdelaBundle\Entity\Goal $goals
     * @return User
     */
    public function addGoal(\Acme\EdelaBundle\Entity\Goal $goals)
    {
        $this->goals[] = $goals;

        return $this;
    }

    /**
     * Remove goals
     *
     * @param \Acme\EdelaBundle\Entity\Goal $goals
     */
    public function removeGoal(\Acme\EdelaBundle\Entity\Goal $goals)
    {
        $this->goals->removeElement($goals);
    }

    /**
     * Get goals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add tasks
     *
     * @param \Acme\EdelaBundle\Entity\Task $tasks
     * @return User
     */
    public function addTask(\Acme\EdelaBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Acme\EdelaBundle\Entity\Task $tasks
     */
    public function removeTask(\Acme\EdelaBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set vkontakteId
     *
     * @param string $vkontakteId
     * @return User
     */
    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakteId = $vkontakteId;

        return $this;
    }

    /**
     * Get vkontakteId
     *
     * @return string 
     */
    public function getVkontakteId()
    {
        return $this->vkontakteId;
    }

    /**
     * Set vkontakteAccessToken
     *
     * @param string $vkontakteAccessToken
     * @return User
     */
    public function setVkontakteAccessToken($vkontakteAccessToken)
    {
        $this->vkontakteAccessToken = $vkontakteAccessToken;

        return $this;
    }

    /**
     * Get vkontakteAccessToken
     *
     * @return string 
     */
    public function getVkontakteAccessToken()
    {
        return $this->vkontakteAccessToken;
    }

    /**
     * Add actions
     *
     * @param \Acme\EdelaBundle\Entity\Action $actions
     * @return User
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

    /**
     * Add userActions
     *
     * @param \Acme\EdelaBundle\Entity\UserAction $userActions
     * @return User
     */
    public function addUserAction(\Acme\EdelaBundle\Entity\UserAction $userActions)
    {
        $this->userActions[] = $userActions;

        return $this;
    }

    /**
     * Remove userActions
     *
     * @param \Acme\EdelaBundle\Entity\UserAction $userActions
     */
    public function removeUserAction(\Acme\EdelaBundle\Entity\UserAction $userActions)
    {
        $this->userActions->removeElement($userActions);
    }

    /**
     * Get userActions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserActions()
    {
        return $this->userActions;
    }

    /**
     * Add userSubactionProgresses
     *
     * @param \Acme\EdelaBundle\Entity\UserSubactionProgress $userSubactionProgresses
     * @return User
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

    /**
     * Set exp_total
     *
     * @param integer $expTotal
     * @return User
     */
    public function setExpTotal($expTotal)
    {
        $this->exp_total = $expTotal;

        return $this;
    }

    /**
     * Get exp_total
     *
     * @return integer 
     */
    public function getExpTotal()
    {
        return $this->exp_total;
    }

    /**
     * Set exp_bill
     *
     * @param integer $expBill
     * @return User
     */
    public function setExpBill($expBill)
    {
        $this->exp_bill = $expBill;

        return $this;
    }

    /**
     * Get exp_bill
     *
     * @return integer 
     */
    public function getExpBill()
    {
        return $this->exp_bill;
    }

    /**
     * Add userAchievements
     *
     * @param \Acme\EdelaBundle\Entity\UserAchievement $userAchievements
     * @return User
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
     * Add notifications
     *
     * @param \Acme\EdelaBundle\Entity\Notification $notifications
     * @return User
     */
    public function addNotification(\Acme\EdelaBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;

        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Acme\EdelaBundle\Entity\Notification $notifications
     */
    public function removeNotification(\Acme\EdelaBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add userTools
     *
     * @param \Acme\EdelaBundle\Entity\UserTool $userTools
     * @return User
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

    /**
     * @deprecated
     * @return float
     */
    public function getLevel(){
        return false;
        return floor($this->getExpTotal() / 500);
    }


    /**
     * Add sentUserRelations
     *
     * @param \Acme\EdelaBundle\Entity\UserRelation $sentUserRelations
     * @return User
     */
    public function addSentUserRelation(\Acme\EdelaBundle\Entity\UserRelation $sentUserRelations)
    {
        $this->sentUserRelations[] = $sentUserRelations;

        return $this;
    }

    /**
     * Remove sentUserRelations
     *
     * @param \Acme\EdelaBundle\Entity\UserRelation $sentUserRelations
     */
    public function removeSentUserRelation(\Acme\EdelaBundle\Entity\UserRelation $sentUserRelations)
    {
        $this->sentUserRelations->removeElement($sentUserRelations);
    }

    /**
     * Get sentUserRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSentUserRelations()
    {
        return $this->sentUserRelations;
    }

    /**
     * Add receivedUserRelations
     *
     * @param \Acme\EdelaBundle\Entity\UserRelation $receivedUserRelations
     * @return User
     */
    public function addReceivedUserRelation(\Acme\EdelaBundle\Entity\UserRelation $receivedUserRelations)
    {
        $this->receivedUserRelations[] = $receivedUserRelations;

        return $this;
    }

    /**
     * Remove receivedUserRelations
     *
     * @param \Acme\EdelaBundle\Entity\UserRelation $receivedUserRelations
     */
    public function removeReceivedUserRelation(\Acme\EdelaBundle\Entity\UserRelation $receivedUserRelations)
    {
        $this->receivedUserRelations->removeElement($receivedUserRelations);
    }

    /**
     * Get receivedUserRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReceivedUserRelations()
    {
        return $this->receivedUserRelations;
    }

    /**
     * Add sentActionInvites
     *
     * @param \Acme\EdelaBundle\Entity\ActionInvite $sentActionInvites
     * @return User
     */
    public function addSentActionInvite(\Acme\EdelaBundle\Entity\ActionInvite $sentActionInvites)
    {
        $this->sentActionInvites[] = $sentActionInvites;

        return $this;
    }

    /**
     * Remove sentActionInvites
     *
     * @param \Acme\EdelaBundle\Entity\ActionInvite $sentActionInvites
     */
    public function removeSentActionInvite(\Acme\EdelaBundle\Entity\ActionInvite $sentActionInvites)
    {
        $this->sentActionInvites->removeElement($sentActionInvites);
    }

    /**
     * Get sentActionInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSentActionInvites()
    {
        return $this->sentActionInvites;
    }

    /**
     * Add receivedActionInvites
     *
     * @param \Acme\EdelaBundle\Entity\ActionInvite $receivedActionInvites
     * @return User
     */
    public function addReceivedActionInvite(\Acme\EdelaBundle\Entity\ActionInvite $receivedActionInvites)
    {
        $this->receivedActionInvites[] = $receivedActionInvites;

        return $this;
    }

    /**
     * Remove receivedActionInvites
     *
     * @param \Acme\EdelaBundle\Entity\ActionInvite $receivedActionInvites
     */
    public function removeReceivedActionInvite(\Acme\EdelaBundle\Entity\ActionInvite $receivedActionInvites)
    {
        $this->receivedActionInvites->removeElement($receivedActionInvites);
    }

    /**
     * Get receivedActionInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReceivedActionInvites()
    {
        return $this->receivedActionInvites;
    }

    /**
     * Add progressStatistics
     *
     * @param \Acme\EdelaBundle\Entity\ProgressStatistic $progressStatistics
     * @return User
     */
    public function addProgressStatistic(\Acme\EdelaBundle\Entity\ProgressStatistic $progressStatistics)
    {
        $this->progressStatistics[] = $progressStatistics;

        return $this;
    }

    /**
     * Remove progressStatistics
     *
     * @param \Acme\EdelaBundle\Entity\ProgressStatistic $progressStatistics
     */
    public function removeProgressStatistic(\Acme\EdelaBundle\Entity\ProgressStatistic $progressStatistics)
    {
        $this->progressStatistics->removeElement($progressStatistics);
    }

    /**
     * Get progressStatistics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProgressStatistics()
    {
        return $this->progressStatistics;
    }

    /**
     * Set isSubscribed
     *
     * @param boolean $isSubscribed
     * @return User
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }

    /**
     * Get isSubscribed
     *
     * @return boolean 
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo && $this->photo[0] == 'i' ? $this->getPhotoWebPath() : $this->photo;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function getAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadRootDir() . '/' . $this->photo;
    }

    public function getPhotoWebPath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDir() . '/' . $this->photo;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/profilephotos';
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

        $this->photo = $newFilename;

        $this->file = null;
    }

    /**
     * Set paidSms
     *
     * @param integer $paidSms
     * @return User
     */
    public function setPaidSms($paidSms)
    {
        $this->paid_sms = $paidSms;

        return $this;
    }

    /**
     * Get paidSms
     *
     * @return integer 
     */
    public function getPaidSms()
    {
        return $this->paid_sms;
    }

    /**
     * Set is_online
     *
     * @param boolean $isOnline
     * @return User
     */
    public function setIsOnline($isOnline)
    {
        $this->is_online = $isOnline;

        return $this;
    }

    /**
     * Get is_online
     *
     * @return boolean 
     */
    public function getIsOnline()
    {
        return $this->is_online;
    }

    /**
     * Add sentMessages
     *
     * @param \Acme\EdelaBundle\Entity\ChatMessage $sentMessages
     * @return User
     */
    public function addSentMessage(\Acme\EdelaBundle\Entity\ChatMessage $sentMessages)
    {
        $this->sentMessages[] = $sentMessages;

        return $this;
    }

    /**
     * Remove sentMessages
     *
     * @param \Acme\EdelaBundle\Entity\ChatMessage $sentMessages
     */
    public function removeSentMessage(\Acme\EdelaBundle\Entity\ChatMessage $sentMessages)
    {
        $this->sentMessages->removeElement($sentMessages);
    }

    /**
     * Get sentMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSentMessages()
    {
        return $this->sentMessages;
    }

    /**
     * Add receivedMessages
     *
     * @param \Acme\EdelaBundle\Entity\ChatMessage $receivedMessages
     * @return User
     */
    public function addReceivedMessage(\Acme\EdelaBundle\Entity\ChatMessage $receivedMessages)
    {
        $this->receivedMessages[] = $receivedMessages;

        return $this;
    }

    /**
     * Remove receivedMessages
     *
     * @param \Acme\EdelaBundle\Entity\ChatMessage $receivedMessages
     */
    public function removeReceivedMessage(\Acme\EdelaBundle\Entity\ChatMessage $receivedMessages)
    {
        $this->receivedMessages->removeElement($receivedMessages);
    }

    /**
     * Get receivedMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReceivedMessages()
    {
        return $this->receivedMessages;
    }

    /**
     * Add transactions
     *
     * @param \Acme\EdelaBundle\Entity\Transaction $transactions
     * @return User
     */
    public function addTransaction(\Acme\EdelaBundle\Entity\Transaction $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \Acme\EdelaBundle\Entity\Transaction $transactions
     */
    public function removeTransaction(\Acme\EdelaBundle\Entity\Transaction $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Set bill
     *
     * @param integer $bill
     * @return User
     */
    public function setBill($bill)
    {
        $this->bill = $bill;

        return $this;
    }

    /**
     * Get bill
     *
     * @return integer 
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * Add dayoffs
     *
     * @param \Acme\EdelaBundle\Entity\Dayoff $dayoffs
     * @return User
     */
    public function addDayoff(\Acme\EdelaBundle\Entity\Dayoff $dayoffs)
    {
        $this->dayoffs[] = $dayoffs;

        return $this;
    }

    /**
     * Remove dayoffs
     *
     * @param \Acme\EdelaBundle\Entity\Dayoff $dayoffs
     */
    public function removeDayoff(\Acme\EdelaBundle\Entity\Dayoff $dayoffs)
    {
        $this->dayoffs->removeElement($dayoffs);
    }

    /**
     * Get dayoffs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDayoffs()
    {
        return $this->dayoffs;
    }

    /**
     * Set paid_dayoffs
     *
     * @param integer $paidDayoffs
     * @return User
     */
    public function setPaidDayoffs($paidDayoffs)
    {
        $this->paid_dayoffs = $paidDayoffs;

        return $this;
    }

    /**
     * Get paid_dayoffs
     *
     * @return integer 
     */
    public function getPaidDayoffs()
    {
        return $this->paid_dayoffs;
    }

    /**
     * Add owner_comments
     *
     * @param \Acme\EdelaBundle\Entity\Action $ownerComments
     * @return User
     */
    public function addOwnerComment(\Acme\EdelaBundle\Entity\Action $ownerComments)
    {
        $this->owner_comments[] = $ownerComments;

        return $this;
    }

    /**
     * Remove owner_comments
     *
     * @param \Acme\EdelaBundle\Entity\Action $ownerComments
     */
    public function removeOwnerComment(\Acme\EdelaBundle\Entity\Action $ownerComments)
    {
        $this->owner_comments->removeElement($ownerComments);
    }

    /**
     * Get owner_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnerComments()
    {
        return $this->owner_comments;
    }

    /**
     * Add action_comments
     *
     * @param \Acme\EdelaBundle\Entity\Action $actionComments
     * @return User
     */
    public function addActionComment(\Acme\EdelaBundle\Entity\Action $actionComments)
    {
        $this->action_comments[] = $actionComments;

        return $this;
    }

    /**
     * Remove action_comments
     *
     * @param \Acme\EdelaBundle\Entity\Action $actionComments
     */
    public function removeActionComment(\Acme\EdelaBundle\Entity\Action $actionComments)
    {
        $this->action_comments->removeElement($actionComments);
    }

    /**
     * Get action_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActionComments()
    {
        return $this->action_comments;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    public function getCurrentDateTime(){
        return (new \DateTime())->setTimezone(new \DateTimeZone('+' . $this->getTimezone()));
    }


    /**
     * Add settings
     *
     * @param \Acme\UserBundle\Entity\UserSettings $settings
     * @return User
     */
    public function addSetting(\Acme\UserBundle\Entity\UserSettings $settings)
    {
        $this->settings[] = $settings;

        return $this;
    }

    /**
     * Remove settings
     *
     * @param \Acme\UserBundle\Entity\UserSettings $settings
     */
    public function removeSetting(\Acme\UserBundle\Entity\UserSettings $settings)
    {
        $this->settings->removeElement($settings);
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
