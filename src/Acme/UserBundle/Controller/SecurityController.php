<?php

namespace Acme\UserBundle\Controller;

use Acme\UserBundle\Form\Type\LoginFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController{

    public function renderLogin(array $data)
    {
        	 
        $data['_csrf_token'] = $data['csrf_token'];
        $data['_username'] = $data['last_username'];
        
        if(isset($_GET["authenticated"])){ 			
       		$data['token'] = "http://e-dela.com/user/snlogin/check-".$_GET["redirect"]."?code=".$_GET["code"]; 			
	        $resetForm = $this->container->get('form.factory')->createNamedBuilder(null, 'form', $data)
            ->add('username', 'email', array('label' => 'form.email'))
            ->add('token', 'hidden')
            ->getForm();

            return $this->container->get('templating')->renderResponse('AcmeUserBundle:Security:email_input.html.twig', array(
        		'error' => $data['error'],
        	 	'resetForm' => $resetForm->createView()
       	 	));
        }else{
        
        	$form = $this->container->get('form.factory')->createNamedBuilder(null, 'form', $data)
            ->add('_username', 'text', array('label' => 'form.email'))
            ->add('_password', 'password', array('label' => 'form.password'))
            ->add('_remember_me', 'checkbox', array('label' => 'form.remember_me', 'required' => false))
            ->add('_csrf_token', 'hidden')
            ->getForm();

        	$resetForm = $this->container->get('form.factory')->createBuilder()
            ->add('username', 'email', array('label' => 'form.email'))
            ->getForm();

        	return $this->container->get('templating')->renderResponse('AcmeUserBundle:Security:login.html.twig', array(
        	    'form' => $form->createView(),
        	    'error' => $data['error'],
        	    'resetForm' => $resetForm->createView()
       	 	));
    	}
    }

}
