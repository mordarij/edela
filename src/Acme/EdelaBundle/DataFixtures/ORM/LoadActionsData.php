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
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadActionsData  implements FixtureInterface{

    function load(ObjectManager $manager)
    {

        $done = new ActionType();
        $done->setTitle('Выполнено/Не выполнено')
            ->setTkey('done');
        $manager->persist($done);

        $done = new ActionType();
        $done->setTitle('Текст')
            ->setTkey('text');
        $manager->persist($done);

        $done = new ActionType();
        $done->setTitle('Числовой показатель')
            ->setTkey('number');
        $manager->persist($done);

        $done = new ActionType();
        $done->setTitle('Времянной показатель')
            ->setTkey('time');
        $manager->persist($done);

        $dynamic = new ActionDynamicType();
        $dynamic->setTitle('Стремиться к увеличению')
            ->setTkey('up');
        $manager->persist($dynamic);

        $dynamic = new ActionDynamicType();
        $dynamic->setTitle('Стремиться к уменьшению')
            ->setTkey('down');
        $manager->persist($dynamic);

        $manager->flush();
    }
}