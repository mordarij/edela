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


class SamplesAdmin extends Admin {

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('categories', 'sonata_type_model', array('label' => 'Категории', 'expanded' => true, 'by_reference' => false, 'multiple' => true))
            ->add('info', null, array('label' => 'Информация'))
            ->add('goal', null, array('label' => 'Цель'))
            ->add('note', null, array('label' => 'Заметка'))
            ->add('file', 'file', array('label' => 'Картинка'))
//            ->add('image', 'file')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('categories')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
        ->add('image')
//            ->add('categories')
        ;
    }

} 