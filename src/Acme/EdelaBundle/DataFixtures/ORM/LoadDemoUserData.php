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
use Acme\EdelaBundle\Entity\ProgressStatistic;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class LoadDemoUserData  implements FixtureInterface, ContainerAwareInterface{

    private $container;

    function load(ObjectManager $manager)
    {
        // Get our userManager, you must implement `ContainerAwareInterface`
        $userManager = $this->container->get('fos_user.user_manager');

        // Create our user and set details
        $user = $userManager->createUser();
        $user->setUsername('demo');
        $user->setFullname('Demo User');
        $user->setEmail('email@domain.com');
        $user->setPlainPassword('password');
        //$user->setPassword('3NCRYPT3D-V3R51ON');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_DEMO'));

        // Update the user
        $userManager->updateUser($user, true);

        $dates = $this->createDateRangeArray('2014-08-01', '2014-09-05');
        foreach ($dates as $date) {
            $progress = new ProgressStatistic();
            $progress->setTotalActions(15)
                ->setProgressedActions(rand(0,15))
                ->setEfficiency(!$progress->getProgressedActions()? -4 : $progress->getProgressedActions() * 3)
                ->setCalculatedAt(new \DateTime($date))
                ->setUser($user);
            $manager->persist($progress);
        }



        $manager->flush();
    }

    private function createDateRangeArray($strDateFrom,$strDateTo)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.

        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}