<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 8/19/14
 * Time: 1:39 PM
 */

namespace Acme\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;


class StaticPagesAdmin extends Admin {

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('tkey', null, ['label' => 'Ключ'])
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('text', 'textarea', ['label' => 'Текст', 'attr' => ['class' => 'editor', 'id' => uniqid('te')]]);
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('tkey')
            ->add('title')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('tkey')
            ->addIdentifier('title')
        ;
    }

} 