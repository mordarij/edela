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


class LevelsAdmin extends Admin {

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('number', null, ['label' => 'Номер'])
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('startExp', null, ['label' => 'Количество опыта'])
            ->add('toolsNum', null, ['label' => 'Количество инструментов'])
            ->add('file', 'file', array('label' => 'Картинка', 'required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('number')
            ->add('title')
            ->add('startExp')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('number')
            ->addIdentifier('title')
            ->addIdentifier('startExp')
        ;
    }

} 