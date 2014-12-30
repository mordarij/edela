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


class ToolsAdmin extends Admin {

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('description', 'textarea', array('label' => 'Описание', 'attr' => ['class' => 'editor', 'id' => uniqid('te')]))
            ->add('shortDescription', 'textarea', array('label' => 'Короткое описание'))
            ->add('minLevel', 'number', array('label' => 'Минимальный уровень'))
            ->add('cost', 'number', array('label' => 'Стоимость(опыт)'))
            ->add('class', 'text', array('label' => 'Класс'))
            ->add('file', 'file', array('label' => 'Картинка', 'required' => false))
            ->add('bigFile', 'file', array('label' => 'Большая картинка', 'required' => false))
//            ->add('image', 'file')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
        ;
    }

} 