<?php

namespace Acme\EdelaBundle\Repository;

use Acme\EdelaBundle\Entity\Action;
use Acme\EdelaBundle\Entity\Goal;
use Acme\EdelaBundle\Entity\Subaction;
use Acme\EdelaBundle\Entity\UserSubactionProgress;
use Acme\UserBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ActionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActionRepository extends EntityRepository
{

    public function getActions(User $user, \DateTime $date = null, Goal $goal = null)
    {

        if (!$date) {
            $date = new \DateTime();
        }
        $date->modify('midnight');
        $dateFinish = clone $date;
        $dateFinish->modify('tomorrow midnight');
        $builder = $this->getEntityManager()->createQueryBuilder();
        $actions = $builder->from('AcmeEdelaBundle:UserAction', 'ua')
            ->select('a.id as id')
            ->addSelect('a.title as title')
            ->addSelect('uap.result as progress')
            ->addSelect('uap.note as progress_note')
            ->addSelect('a.repeatAmount as repeat_amount')
            ->addSelect('ua.startAt as start_time')
            ->addSelect('ua.position as position')
            ->addSelect('ua.periodicity as periodicity')
            ->addSelect('ua.periodicityInterval as periodicity_interval')
            ->addSelect('ua.isPrivate as is_private')
            ->addSelect('IDENTITY(a.actionType) as action_type_id')
            ->addSelect('a.actionTypeTitle as action_type_title')
            ->addSelect('IDENTITY(a.actionDynamicType) as dynamic_type_id')
            ->addSelect('a.actionTime as action_time')
            ->addSelect('a.actionTimeStart as action_time_start')
            ->addSelect('a.actionTimeFinish as action_time_finish')
            ->addSelect('ua.isTimeReport as is_time_report')
            ->addSelect('ua.isImportant as is_important')
            ->addSelect('ua.isSmsNotification as is_sms_notification')
            ->addSelect('ua.isEmailNotification as is_email_notification')
            ->addSelect('ua.notificationTime as notification_time')
            ->addSelect('a.note as note')
            ->addSelect('IDENTITY(a.goal) as goal_id')
            ->addSelect('g.name as goal_title')
            ->leftJoin('AcmeEdelaBundle:Action', 'a', Join::WITH, 'ua.action = a')
            ->leftJoin('AcmeEdelaBundle:UserActionProgress', 'uap', Join::WITH, 'uap.userAction=ua AND uap.createdAt > :dayStart AND uap.createdAt < :dayFinish')
            ->leftJoin('AcmeEdelaBundle:Goal', 'g', Join::WITH, 'g=a.goal')
            ->setParameter('dayStart', $date)
            ->setParameter('dayFinish', $dateFinish)
            ->where('ua.user = :user')
            ->setParameter('user', $user);
        //->andWhere('ua.startAt < :dayFinish');
        if ($goal) {
            $actions->andWhere('a.goal=:goal')
                ->setParameter('goal', $goal);
        }

        $orX = $builder->expr()->orX();
        $orX->add($builder->expr()->gt('BIT_AND(ua.periodicity, :dayOfWeek)', '0'));
        $orX->add($builder->expr()->eq('MOD(DATE_DIFF(ua.createdAt, :date), ua.periodicityInterval)', '0'));

        $actions->andWhere($orX)
            ->setParameter('date', $date)
            ->setParameter('dayOfWeek', (int)(1 << $date->format('w')));
        $actions->orderBy('ua.position');
        $result = $actions->getQuery()->getArrayResult();
        foreach ($result as $key => $action) {
            $result[$key]['action_time'] = $result[$key]['action_time'] ? $result[$key]['action_time']->format('H:i') : null;
            $result[$key]['action_time_start'] = $result[$key]['action_time_start'] ? $result[$key]['action_time_start']->format('H:i') : null;
            $result[$key]['action_time_finish'] = $result[$key]['action_time_finish'] ? $result[$key]['action_time_finish']->format('H:i') : null;
            $result[$key]['notification_time'] = $result[$key]['notification_time'] ? $result[$key]['notification_time']->format('H:i') : null;
            $result[$key]['goal'] = ['id' => $action['goal_id'], 'title' => $action['goal_title']];
        }

        return $result;
    }

    public function getProgress($actions, User $user)
    {
        if (isset($actions['id'])) {
            $actions = [$actions];
        }
        $aids = [];
        foreach ($actions as $action) {
            $aids[] = $action['id'];
        }

        $builder = $this->getEntityManager()->createQueryBuilder()->from('AcmeEdelaBundle:UserActionProgress', 'uap');
        $progress = $builder->select('COUNT(uap) as progress')->addSelect('IDENTITY(ua.action) as id')
            ->leftJoin('AcmeEdelaBundle:UserAction', 'ua', Join::WITH, 'ua=uap.userAction')
            ->where('IDENTITY(ua.action) IN (:actions)')
            ->setParameter('actions', $aids)
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->groupBy('ua.action');

        return $progress->getQuery()->getArrayResult();
    }

    public function getUserAction($user, $action)
    {

        $action = $this->getEntityManager()->createQueryBuilder()->from('AcmeEdelaBundle:UserAction', 'ua')
            ->select('ua')
            ->addSelect('a')
            ->leftJoin('AcmeEdelaBundle:Action', 'a', Join::WITH, 'ua.action = a')
            ->where('ua.user = :user')
            ->setParameter('user', $user)
            ->andWhere('ua.action = :action')
            ->setParameter('action', $action)
            ->setMaxResults(1);

        return $action->getQuery()->getResult();
    }


    public function getSubactions($action_id, User $user, \DateTime $date = null, $doneOnly = false)
    {

        if (!$date) {
            $date = new \DateTime();
        }
        $date->modify('midnight');
        $dateFinish = clone $date;
        $dateFinish->modify('tomorrow midnight');

        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT sa.id, sa.title, EXISTS(
        SELECT * FROM ed_users_subactions_progresses sp WHERE sp.subaction_id=sa.id AND sp.user_id=:user AND sp.created_at > :dayStart AND sp.created_at < :dayFinish
        ) as progress FROM ed_subactions sa WHERE ' .
            'sa.action_id=:action';
        if ($doneOnly) {
            $sql .= ' HAVING progress';
        }
        $result = $connection->fetchAll($sql, [
            'user' => $user->getId(),
            'action' => $action_id,
            'dayStart' => $date,
            'dayFinish' => $dateFinish
        ], ['dayStart' => 'datetime', 'dayFinish' => 'datetime']);

        foreach($result as &$row){
            $row['progress'] = (bool)$row['progress'];
        }

        return $result;

        $subactions = $this->getEntityManager()->createQueryBuilder()->from('AcmeEdelaBundle:Subaction', 'sa')
            ->select('sa.id as id')
            ->addSelect('sa.title as title')
            ->addSelect('sp.createdAt as progress')
            ->leftJoin('AcmeEdelaBundle:UserSubactionProgress', 'sp', Join::WITH, 'sp.subaction=sa AND sp.user=:user AND sp.createdAt > :dayStart AND sp.createdAt < :dayFinish')
            ->setParameter('user', $user)
            ->setParameter('dayStart', $date)
            ->setParameter('dayFinish', $dateFinish)
            ->where('sa.action=:action')
            ->setParameter('action', $action_id);
        if ($doneOnly) {
            $subactions->andWhere('sp.createdAt IS NOT NULL');
        }

        return $subactions->getQuery()->getResult();

    }

    public function getDelayedActions(User $user, \DateTime $date = null)
    {

        if (!$date) {
            $date = new \DateTime();
        }
        $date->modify('midnight');
        $builder = $this->getEntityManager()->createQueryBuilder();
        $actions = $builder->from('AcmeEdelaBundle:UserAction', 'ua')
            ->select('a.id as id')
            ->addSelect('a.title as title')
            ->addSelect('ua.startAt as start_at')
            ->leftJoin('AcmeEdelaBundle:Action', 'a', Join::WITH, 'ua.action=a')
            ->where('ua.user=:user')
            ->setParameter('user', $user);

        $orX = $builder->expr()->orX();
        $orX->add($builder->expr()->isNull('ua.startAt'));
        $orX->add($builder->expr()->gt('ua.startAt', ':date'));

        $actions->andWhere($orX)
            ->setParameter('date', $date);

        return $actions->getQuery()->getArrayResult();

    }

    public function getDoneCount(User $user)
    {

        $builder = $this->getEntityManager()->createQueryBuilder();
        $actions = $builder->from('AcmeEdelaBundle:UserActionProgress', 'uap')
            ->select('COUNT(ua) as cnt')
            ->leftJoin('AcmeEdelaBundle:UserAction', 'ua', Join::WITH, 'uap.userAction=ua')
            ->where('ua.user=:user')
            ->setParameter('user', $user);

        return $actions->getQuery()->getArrayResult()[0]['cnt'];

    }

    public function getJointInfo(Action $action, \DateTime $date = null)
    {

        if (!$date) {
            $date = new \DateTime();
        }
        $date->modify('midnight');
        $dateFinish = clone $date;
        $dateFinish->modify('tomorrow midnight');

        $builder = $this->getEntityManager()->createQueryBuilder();

        $users = $builder->from('AcmeEdelaBundle:UserAction', 'ua')
            ->addSelect('u as user')
            ->addSelect('uap.result as progress')
            ->leftJoin('AcmeUserBundle:User', 'u', Join::WITH, 'ua.user=u')
            ->leftJoin('AcmeEdelaBundle:UserActionProgress', 'uap', Join::WITH, 'uap.userAction=ua AND uap.createdAt > :dayStart AND uap.createdAt < :dayFinish')
            ->where('ua.action=:action')
            ->setParameter('action', $action)
            ->setParameter('dayStart', $date)
            ->setParameter('dayFinish', $dateFinish);

        return $users->getQuery()->getArrayResult();

    }

    public function getUserStats(User $user)
    {

        $builder = $this->getEntityManager()->createQueryBuilder();

        $stats = $builder->from('AcmeEdelaBundle:UserAction', 'ua')
            ->select('COUNT(uap) as done_count')
            ->addSelect('a.repeatAmount as total_count')
            ->addSelect('a.title as title')
            ->addSelect('ua.createdAt as start_at')
            ->leftJoin('AcmeEdelaBundle:UserActionProgress', 'uap', Join::WITH, 'ua=uap.userAction')
            ->leftJoin('AcmeEdelaBundle:Action', 'a', Join::WITH, 'a=ua.action')
            ->where('ua.user=:user')
            ->setParameter('user', $user)
            ->groupBy('ua');

        return $stats->getQuery()->getArrayResult();

    }

    public function executeSubaction(Subaction $subaction, User $user)
    {
        $em = $this->getEntityManager();
        $userTime = $user->getCurrentDateTime();
        $dayStart = clone $userTime;
        $dayFinish = clone $userTime;
        $dayStart->modify('today midnight');
        $dayFinish->modify('tomorrow midnight');

        $existingSubprogress = $em->getRepository('AcmeEdelaBundle:UserSubactionProgress')->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->eq('subaction', $subaction))
                ->andWhere(Criteria::expr()->gte('createdAt', $dayStart))
                ->andWhere(Criteria::expr()->lte('createdAt', $dayFinish))
        );
        if ($existingSubprogress->count()) {
            $em->remove($existingSubprogress->first());
            $progress = false;
        } else {
            $subprogress = new UserSubactionProgress();
            $subprogress->setUser($user)->setSubaction($subaction);
            $em->persist($subprogress);
            $progress = true;
        }
        $em->flush();
        return $progress;
    }
}