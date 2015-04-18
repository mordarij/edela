<?php

namespace Acme\UserBundle\Form\Type;

use FOS\UserBundle\Model\UserManager;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

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
      	
      /*  $builder->add('timezone', 'city_entity',array(
                'class'         => 'AcmeEdelaBundle:City',
                'property'      => 'title',
                'label'         => 'form.timezone'               
            )        
            Acme\EdelaBundle\Entity\City
        );*/
        
       $builder->add('timezone', 'shtumi_ajax_autocomplete', array('entity_alias'=>'cities'));

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