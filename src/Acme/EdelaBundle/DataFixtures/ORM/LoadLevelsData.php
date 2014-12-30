<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 6/27/14
 * Time: 10:14 AM
 */

namespace Acme\EdelaBundle\DataFixtures\ORM;


use Acme\EdelaBundle\Entity\Achievement;
use Acme\EdelaBundle\Entity\ActionDynamicType;
use Acme\EdelaBundle\Entity\ActionType;
use Acme\EdelaBundle\Entity\Level;
use Acme\EdelaBundle\Entity\Tool;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLevelsData implements FixtureInterface{

    function load(ObjectManager $manager)
    {

        $level = new Level();
        $level->setNumber(1)->setExp(0);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(2)->setExp(50);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(3)->setExp(170);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(4)->setExp(400);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(5)->setExp(850);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(6)->setExp(1700);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(7)->setExp(3600);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(8)->setExp(7500);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(9)->setExp(16000);
        $manager->persist($level);

        $level = new Level();
        $level->setNumber(10)->setExp(34000);
        $manager->persist($level);

        $manager->flush();
    }
}