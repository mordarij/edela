<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 10:14 AM
 */

namespace Acme\EdelaBundle\DataFixtures\ORM;


use Acme\EdelaBundle\Entity\Achievement;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAchievementsData  implements FixtureInterface{

    function load(ObjectManager $manager)
    {
        $achievement = new Achievement();
        $achievement->setTitle('Первое задание выполнено')
            ->setDescription('Выполнено первое задание')
            ->setExpReward(650)
            ->setTkey('first-action-done')
            ->setImage('/bundles/acmeedela/images/honer1.png');
        $manager->persist($achievement);

        $achievement = new Achievement();
        $achievement->setTitle('10 заданий выполнено')
            ->setDescription('Выполнено 10 заданий')
            ->setExpReward(1250)
            ->setTkey('ten-action-done')
            ->setImage('/bundles/acmeedela/images/honer2.png');
        $manager->persist($achievement);

        $achievement = new Achievement();
        $achievement->setTitle('100 заданий выполнено')
            ->setDescription('Выполнено 100 заданий')
            ->setExpReward(1550)
            ->setTkey('hundred-action-done')
            ->setImage('/bundles/acmeedela/images/honer3.png');
        $manager->persist($achievement);

        $manager->flush();
    }
}