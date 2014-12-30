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

class TaskCreateShortFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'task_create_short';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $em = $options['em'];
        $goalTransformer = new GoalToIdTransformer($em);
        $builder->add($builder->create('goal', 'hidden', array('required' => false))->addModelTransformer($goalTransformer))
            ->add('parent', null, array('required' => false))
            ->add('name', 'text', array('label' => 'action.task.create_new'))
            ->add('add', 'submit', array('label' => 'common.add'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Acme\EdelaBundle\Entity\Task',
            ))
            ->setRequired(array(
                'em',
            ))
            ->setAllowedTypes(array(
                'em' => 'Doctrine\Common\Persistence\ObjectManager',
            ));

        // ...
    }


}