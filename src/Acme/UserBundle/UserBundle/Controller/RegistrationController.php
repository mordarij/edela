<?php

namespace Acme\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseController{

    public function registerAction()
    {

        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);

        if ($process) {        	
            $user = $form->getData();
            $this->setFlash('success', 'registration.check_email');
           // print_r($user);
            //$url = $this->container->get('router')->generate('app');
            //$response = new RedirectResponse($url);
          //  $this->authenticateUser($user, $response);
          return $this->container->get('templating')->renderResponse('AcmeUserBundle:Registration:check_email.html.twig');
            return $response;
        }

        return $this->container->get('templating')->renderResponse('AcmeUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function confirmedAction(){
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $this->setFlash('success', 'registration.email_confirmed');
		//return $this->container->get('templating')->renderResponse('AcmeUserBundle:Registration:check_email.html.twig');
        return new RedirectResponse($this->container->get('router')->generate('app'));
    }

    public function checkEmailAction(){
        return $this->container->get('templating')->renderResponse('AcmeUserBundle:Registration:check_email.html.twig');
    }

}
