<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 8/12/14
 * Time: 12:20 PM
 */

namespace Acme\EdelaBundle\Command;

use Acme\ApiBundle\Sender\EmailSender;
use Acme\EdelaBundle\Entity\ProgressStatistic;
use Acme\EdelaBundle\Entity\UserAction;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('notifications:send')
            ->setDescription('Sends actions and tasks notifications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ObjectManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $currentUtc = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
        $str = '
        SELECT ua.id
        FROM ed_users_actions ua
        LEFT JOIN ed_users u ON ua.user_id=u.id
        WHERE
          ((ua.is_sms_notification = 1 AND u.paid_sms > 0 AND LENGTH(u.phone) > 5) OR ua.is_email_notification = 1)
          AND
          (ua.periodicity & (1 << DATE_FORMAT(DATE_ADD(\'' . $currentUtc->format('Y-m-d H:i') . '\', INTERVAL u.timezone HOUR), \'%w\'))
          OR
          DATEDIFF(ua.created_at, DATE_ADD(\'' . $currentUtc->format('Y-m-d H:i') . '\', INTERVAL u.timezone HOUR)) % ua.periodicity_interval = 0
          )
          AND
          TIME(ua.notification_time) < TIME(\'' . $currentUtc->format('H:i') . '\') + INTERVAL u.timezone HOUR
          AND
          TIME(ua.notification_time) + INTERVAL 15 MINUTE > TIME(\'' . $currentUtc->format('H:i') . '\') + INTERVAL u.timezone HOUR
        ';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $actions = $em->createNativeQuery($str, $rsm)->getResult();
        /** @var UserAction[] $actions */
        $actions = $em->getRepository('AcmeEdelaBundle:UserAction')->findById(array_column($actions, 'id'));
        foreach ($actions as $action) {
            if ($action->getIsSmsNotification()){
                $smsSender = $this->getContainer()->get('api.sender.sms');
                $smsSent = $smsSender->send($action->getUser()->getPhone(), 'Напоминание о ' . $action->getAction()->getTitle());
                if ($smsSent){
                    $em->getRepository('AcmeUserBundle:User')->increasePaidSms($action->getUser(), -1);
                }
            }
            if ($action->getIsEmailNotification()){
                /** @var $emailSender EmailSender */
                $emailSender = $this->getContainer()->get('api.sender.email');
                $emailSender->send($action->getUser()->getEmail(), 'Напоминание e-dela.com', 'Напоминание о ' . $action->getAction()->getTitle());
            }
        }


        $em->flush();
        $output->writeln('Done');
    }


}