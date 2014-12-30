<?php

namespace Acme\UserBundle\Controller;

use Acme\UserBundle\Form\Type\LoginFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Model\UserInterface;

class ResettingController extends BaseController
{

    public function requestAction()
    {
        $form = $this->container->get('form.factory')->createBuilder()
            ->add('username', 'email', array('label' => 'form.email'))
            ->getForm();
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                /** @var $user UserInterface */
                $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($form->getData()['username']);
                try {
                    if (null === $user) {
                        throw new \Exception('form.email_doesnt_exists');
                    }

                    if (null === $user->getConfirmationToken()) {
                        /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                        $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                        $user->setConfirmationToken($tokenGenerator->generateToken());
                    }

                    $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
                    $user->setPasswordRequestedAt(new \DateTime());
                    $this->container->get('fos_user.user_manager')->updateUser($user);
//                    return $this->container->get('templating')->renderResponse('AcmeUserBundle:Resetting:check_email.html.twig', array('email' => $user->getEmail()));
                    return new JsonResponse(array('success' => true));
                } catch (\Exception $e) {
//                    $error = new FormError($e->getMessage());
//                    $form->get('username')->addError($error);
                    return new JsonResponse(array('success' => false));

                }
            }
        }
        return new JsonResponse(array('success' => false));
//        return $this->container->get('templating')->renderResponse('AcmeUserBundle:Resetting:request.html.twig', array('form' => $form->createView()));
    }

    public function resetAction($token){
    	
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);
		if (null === $user) {
			echo 'The user with confirmation token does not exist for value '.$token;
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));            
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
        	return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
           $this->setFlash('fos_user_success', 'resetting.flash.success');
        //    $response = new RedirectResponse($this->getRedirectionUrl($user));
         //   $this->authenticateUser($user, $response);
            Header("Location:/user/login");
            die();
          //  return $response;
        }

        return $this->container->get('templating')->renderResponse('AcmeUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

    protected function getRedirectionUrl(UserInterface $user)
    {
        return new RedirectResponse($this->container->get('router')->generate('app'));
    }

}
