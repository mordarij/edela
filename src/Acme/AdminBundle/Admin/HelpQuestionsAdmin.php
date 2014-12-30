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


class HelpQuestionsAdmin extends Admin {

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('question', 'text', array('label' => 'Вопрос'))
            ->add('answer', 'text', array('label' => 'Ответ'))
            ->add('isHot', 'checkbox', array('label' => 'Показывать в популярных?', 'required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('question')
            ->add('isHot')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('question')
            ->add('isHot')
        ;
    }

} 