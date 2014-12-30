<?php

namespace Acme\UserBundle\Form\Type;

use FOS\UserBundle\Model\UserManager;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RegistrationFormType extends BaseType
{

    protected $userManager;

    // Add constructor with UserManager
    public function __construct($class, UserManager $userManager)
    {
        parent::__construct($class);
        $this->userManager = $userManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('fullname', null, array('label' => 'form.fullname'));
        parent::buildForm($builder, $options);
        $builder->remove('username');

        $builder->add('timezone', 'choice', array('label' => 'form.timezone', 'choices' => array(
            3 => 'Москва',
            6 => 'Екатеринбург',
            7 => 'Новосибирск',
            8 => 'Красноярск',
            9 => 'Иркутск',
            11 => 'Владивосток',
            12 => 'Магадан',
        )));

        // Add hook on submitted data in order to copy email into username
        $um = $this->userManager;
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($um) {
            $user = $event->getData();
            /** @var User $user */
            if ($user) {
                // Set username like email and canonicalize both fields
                $user->setUsername($user->getEmail());
                $um->updateCanonicalFields($user);
            }
        });

    }

    public function getName()
    {
        return 'acme_user_registration';
    }
}