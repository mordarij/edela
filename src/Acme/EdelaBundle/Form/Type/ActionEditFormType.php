<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 5/16/14
 * Time: 6:31 PM
 */

namespace Acme\EdelaBundle\Form\Type;


use Acme\EdelaBundle\Form\DataTransformer\GoalToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionEditFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'action_edit';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('goal')
            ->add('title')
            ->add('repeat_amount')
            ->add('note')
            ->add('subactions', 'collection', array('allow_add' => true, 'allow_delete' => true, 'type' => new SubactionFormType(), 'by_reference' => false))
            ->add('action_type', 'entity', [ 'class' => 'AcmeEdelaBundle:ActionType', 'property' => 'id' ])
            ->add('action_dynamic_type', 'entity', [ 'class' => 'AcmeEdelaBundle:ActionDynamicType', 'property' => 'id' ])
            ->add('action_type_title')
            ->add('action_time', 'time', array('required' => false, 'widget' => 'text'))
            ->add('action_time_start', 'time', array('required' => false, 'widget' => 'text'))
            ->add('action_time_finish', 'time', array('required' => false, 'widget' => 'text'))
            ->add('tags', 'collection', array('type' => new TagType(), 'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\EdelaBundle\Entity\Action',
        ));
    }

}