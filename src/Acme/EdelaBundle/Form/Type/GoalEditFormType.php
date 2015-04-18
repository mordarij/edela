<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 5/16/14
 * Time: 6:31 PM
 */

namespace Acme\EdelaBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GoalEditFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'edit_goal';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name')
           // ->add('isPrivate')
            //->add('priority', 'choice', array('label' => 'goal.edit.priority.label', 'choices' => array(5 => 5, 4 => 4, 3 => 3, 2 => 2, 1 => 1)))
       //     ->add('isSlideshow', 'checkbox', array('label' => 'goal.edit.is_slideshow', 'required' => false))
        //    ->add('isImportant', 'checkbox', array('label' => 'goal.edit.is_slideshow', 'required' => false))
            ->add('images', 'text', ['mapped' => false]);
            //->add('isSaved', 'text', ['mapped' => false]));
      //      ->add('slideshowInterval', 'choice', array('choices' => array(10 => 10, 30 => 30, 60 => 60, 120 => 120)));

    }


}