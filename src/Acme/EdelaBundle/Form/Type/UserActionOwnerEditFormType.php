<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 5/16/14
 * Time: 6:31 PM
 */

namespace Acme\EdelaBundle\Form\Type;


use Acme\EdelaBundle\Form\DataTransformer\GoalToIdTransformer;
use Acme\EdelaBundle\Form\DataTransformer\PeriodicityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserActionOwnerEditFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_action_edit';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('action', new ActionEditFormType())
            ->add('periodicity', null, array('required' => false))
            ->add('periodicity_interval', null, array('required' => false))
            ->add('is_private', null, array('required' => false))
            ->add('is_time_report')
            ->add('is_important')
            ->add('is_sms_notification')
            ->add('is_email_notification')
            ->add('position')
            ->add('notification_time', 'time', array('required' => false, 'widget' => 'text'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\EdelaBundle\Entity\UserAction',
            'cascade_validation' => true,
            'method' => 'PATCH'
        ));
    }

}