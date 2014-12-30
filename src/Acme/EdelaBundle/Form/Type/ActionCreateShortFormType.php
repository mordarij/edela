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

class ActionCreateShortFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'action_create_short';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $em = $options['em'];
        $goalTransformer = new GoalToIdTransformer($em);
        $builder
            ->add('start_at', 'date', array(
                "mapped" => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ))
            ->add('title', 'text', array('label' => 'action.daily_action.create_new'))
            ->add('goal', null, array('required' => false))
            ->add('position', null, array('required' => false, 'mapped' => false))
            ->add('add', 'submit', array('label' => 'common.add'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Acme\EdelaBundle\Entity\Action',
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