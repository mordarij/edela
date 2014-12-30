<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 8/12/14
 * Time: 12:20 PM
 */

namespace Acme\EdelaBundle\Command;

use Acme\EdelaBundle\Entity\ProgressStatistic;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatisticsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('statistics:update')
            ->setDescription('Updates statistics');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ObjectManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $currentUtc = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
        $calculationDate = clone $currentUtc;
        $calculationDate->modify('midnight');
        $systemOffset = (new \DateTime())->getOffset();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('AcmeUserBundle:User', 'u');
        $rsm->addFieldResult('u', 'id', 'id');
        $rsm->addFieldResult('u', 'timezone', 'timezone');

        $str = 'select u.id as id,
        u.timezone
        from ed_users u
         WHERE DATE_ADD("' . $currentUtc->format('Y-m-d H:i') . '", INTERVAL u.timezone HOUR) > "' . $calculationDate->format('Y-m-d H:i') . '" AND
         NOT EXISTS(SELECT * FROM ed_progress_statistics ps WHERE ps.user_id=u.id AND DATE_ADD(ps.calculated_at, INTERVAL (u.timezone - ' . $systemOffset / 3600 . ') HOUR) > "' . $calculationDate->format('Y-m-d H:i') . '")
        ';
        $query = $em->createNativeQuery($str, $rsm);
        $users = $query->getResult();
        $dayStart = clone $calculationDate;
        $dayStart->sub(new \DateInterval('P1D'));
        $dayFinish = clone $calculationDate;

        foreach ($users as $user) {
//            $user = $em->find('AcmeUserBundle:User', $user->getId());
            $progress = new ProgressStatistic();
            $progress->setUser($user);
            $queryBuilder = $em->createQueryBuilder();
            $actions = $queryBuilder->from('AcmeEdelaBundle:UserAction', 'ua')
                ->addSelect('uap.result as progress')
                ->addSelect('IDENTITY(a.goal) as goal_id')
                ->leftJoin('AcmeEdelaBundle:Action', 'a', Join::WITH, 'ua.action=a')
                ->leftJoin('AcmeUserBundle:User', 'u', Join::WITH, 'ua.user=u')
                ->leftJoin('AcmeEdelaBundle:UserActionProgress', 'uap', Join::WITH, 'uap.userAction=ua AND
                DATE_ADD2(uap.createdAt, INTERVAL (u.timezone - ' . $systemOffset / 3600 . ')  HOUR) > :dayStart AND
                DATE_ADD2(uap.createdAt, INTERVAL (u.timezone - ' . $systemOffset / 3600 . ')  HOUR) < :dayFinish
                ')
                ->setParameter('dayFinish', $dayFinish)
                ->setParameter('dayStart', $dayStart)
                ->where('ua.user = :user')
                ->setParameter('user', $user);

            $orX = $queryBuilder->expr()->orX();
            $orX->add($queryBuilder->expr()->gt('BIT_AND(ua.periodicity, :dayOfWeek)', '0'));
            $orX->add($queryBuilder->expr()->eq('MOD(DATE_DIFF(ua.createdAt, :date), ua.periodicityInterval)', '0'));

            $actions->andWhere($orX)
                ->setParameter('date', $dayStart)
                ->setParameter('dayOfWeek', (int)(1 << $dayStart->format('w')));
            $actions->andWhere('ua.startAt < :dayFinish');
            var_dump($actions->getQuery()->getSQL(), $dayFinish, $dayStart);
            $actions = $actions->getQuery()->getArrayResult();
            var_dump($actions);
            $progress->setTotalActions(count($actions));
            $progress->setProgressedActions(count(array_filter($actions, function ($item) {
                return (bool)$item['progress'];
            })));

            $goaledActions = count(array_filter($actions, function ($item) {
                return (bool)$item['goal_id'];
            }));
            $progress->setEfficiency(($progress->getProgressedActions() * 3) + $goaledActions);
            $em->persist($progress);
        }
        $em->flush();
        $output->writeln('Done');
    }


}