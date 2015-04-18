<?php

namespace Acme\UserBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Acme\UserBundle\Form\DataTransformer\StringToCityTransformer;

class CityType extends AbstractType
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languageTransformer = new StringToCityTransformer($this->entityManager);
        $builder->addModelTransformer($languageTransformer);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'city_entity';
    }

} 