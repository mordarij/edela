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

class TaskNotificationFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'task_notification';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('notificationType')
            ->add('timeAt', 'time');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\EdelaBundle\Entity\TaskNotification',
        ));
    }

}