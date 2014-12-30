<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 2:33 AM
 */

namespace Acme\ApiBundle\Subscriber;


use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\EventStore;
use Acme\ApiBundle\Event\TaskEvent;
use Acme\EdelaBundle\Entity\Notification;
use Acme\EdelaBundle\Entity\UserAchievement;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class AchievementsSubscriber
{

    private $em;
    private $doneCount = 0;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function firstActionDone(ActionEvent $event)
    {
        $this->doneCount = $this->em->getRepository('AcmeEdelaBundle:Action')->getDoneCount($event->getUser());
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'first-action-done']);
        if (!$achievement) return;
        if (!$userAchievement = $event->getUser()->getUserAchievements()->matching(Criteria::create()->where(Criteria::expr()->eq('achievement', $achievement)))->first()) {
            $userAchievement = new UserAchievement();
            $userAchievement->setProgress(100)->setUser($event->getUser())->setAchievement($achievement);
            $this->em->persist($userAchievement);
            $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getUser(), $achievement->getExpReward());
            $this->em->persist($event->getUser());

            $notification = new Notification();
            $notification->setUser($event->getUser())
                ->setActions(json_encode(['default' => '$scope.goto("achievements")']))
                ->setText('Вы заработали достижение')
                ->setIcon($achievement->getImage());
            $this->em->persist($notification);

        }

        $this->em->flush();
    }

    public function tenActionDone(ActionEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'ten-action-done']);
        if (!$achievement) return;
        if (!$userAchievement = $event->getUser()->getUserAchievements()->matching(Criteria::create()->where(Criteria::expr()->eq('achievement', $achievement)))->first()) {
            $userAchievement = new UserAchievement();
            $userAchievement->setProgress(0)->setUser($event->getUser())->setAchievement($achievement);
        }

        $previousProgress = $userAchievement->getProgress();
        if ($this->doneCount < 10) {
            $userAchievement->setProgress($this->doneCount * 10);
        } else {
            $userAchievement->setProgress(100);
        }

        $this->em->persist($userAchievement);

        if ($userAchievement->getProgress() == 100 && $event->getProgress() && $previousProgress != $userAchievement->getProgress()) {
            $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getUser(), $achievement->getExpReward());
            $this->em->persist($event->getUser());
        }

        $this->em->flush();

    }

    public function hundredActionDone(ActionEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'hundred-action-done']);
        if (!$achievement) return;
        if (!$userAchievement = $event->getUser()->getUserAchievements()->matching(Criteria::create()->where(Criteria::expr()->eq('achievement', $achievement)))->first()) {
            $userAchievement = new UserAchievement();
            $userAchievement->setProgress(0)->setUser($event->getUser())->setAchievement($achievement);
        }

        $previousProgress = $userAchievement->getProgress();
        if ($this->doneCount < 100) {
            $userAchievement->setProgress($this->doneCount);
        } else {
            $userAchievement->setProgress(100);
        }

        $this->em->persist($userAchievement);

        if ($userAchievement->getProgress() == 100 && $event->getProgress() && $previousProgress != $userAchievement->getProgress()) {
            $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getUser(), $achievement->getExpReward());
            $this->em->persist($event->getUser());
        }

        $this->em->flush();
    }

    public function firstActionAdd(ActionEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'first-action-add']);
        if (!$achievement) return;
        if (!$userAchievement = $event->getUser()->getUserAchievements()->matching(Criteria::create()->where(Criteria::expr()->eq('achievement', $achievement)))->first()) {
            $userAchievement = new UserAchievement();
            $userAchievement->setProgress(100)->setUser($event->getUser())->setAchievement($achievement);
            $this->em->persist($userAchievement);
            $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getUser(), $achievement->getExpReward());
            $this->em->persist($event->getUser());
            $this->em->flush();
        }
    }
    public function firstTaskAdd(TaskEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'first-task-add']);
        if (!$achievement) return;
        if (!$userAchievement = $event->getUser()->getUserAchievements()->matching(Criteria::create()->where(Criteria::expr()->eq('achievement', $achievement)))->first()) {
            $userAchievement = new UserAchievement();
            $userAchievement->setProgress(100)->setUser($event->getUser())->setAchievement($achievement);
            $this->em->persist($userAchievement);
            $this->em->getRepository('AcmeUserBundle:User')->increaseExp($event->getUser(), $achievement->getExpReward());
            $this->em->persist($event->getUser());
            $this->em->persist($notification);
            $this->em->flush();
        }
    }
    public function tasks10Complete(TaskEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'tasks-10-complete']);
        if (!$achievement) return;
    }
    public function tasks50Complete(TaskEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'tasks-50-complete']);
        if (!$achievement) return;
    }
    public function tasks200Complete(TaskEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'tasks-200-complete']);
        if (!$achievement) return;
    }
    public function tasks1000Complete(TaskEvent $event)
    {
        $achievement = $this->em->getRepository('AcmeEdelaBundle:Achievement')->findOneBy(['tkey' => 'tasks-1000-complete']);
        if (!$achievement) return;
    }
}