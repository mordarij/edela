<?php

namespace Acme\EdelaBundle\Repository;

use Acme\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ToolRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ToolRepository extends EntityRepository
{

    public function getEnabled(User $user){

        $tools = $this->createQueryBuilder('t')
            ->select('t.id as id')
            ->addSelect('t.title')
            ->addSelect('t.description')
            ->addSelect('t.cost')
            ->addSelect('t.class as class_name')
            ->addSelect('t.minLevel as min_level')
            ->addSelect('ut.isEnabled as is_enabled')
            ->addSelect('ut.isAvailable as is_available')
            ->leftJoin('AcmeEdelaBundle:UserTool', 'ut', Join::WITH, 'ut.tool=t AND ut.user=:user')
            ->setParameter('user', $user)
            ->where('ut.isEnabled=:true')
            ->setParameter('true', 1);

        return $tools->getQuery()->getArrayResult();

    }

    public function getAvailable(User $user){

        $tools = $this->createQueryBuilder('t')
            ->select('t.id as id')
            ->addSelect('t.title')
            ->addSelect('t.description')
            ->addSelect('t.shortDescription')
            ->addSelect('t.cost')
            ->addSelect('t.class')
            ->addSelect('t.picture')
            ->addSelect('t.bigPicture')
            ->addSelect('t.minLevel as min_level')
            ->addSelect('ut.isEnabled as is_enabled')
            ->addSelect('ut.isAvailable as is_available')
            ->leftJoin('AcmeEdelaBundle:UserTool', 'ut', Join::WITH, 'ut.tool=t AND ut.user=:user')
            ->setParameter('user', $user);

        return $tools->getQuery()->getArrayResult();

    }

}
