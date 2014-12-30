<?php

namespace Acme\EdelaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AppController extends Controller
{
    public function appAction($user_id)
    {
        $response = new Response();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        if (!$user_id){
            return new RedirectResponse($this->generateUrl('app', ['user_id' => $currentUser->getId()]));
        }
        $csrf = $this->get('form.csrf_provider');
        $token = $csrf->generateCsrfToken('');
        $response->headers->setCookie(new Cookie('XSRF-TOKEN', $token, 0, '/', null, false, false));
        return $this->render('AcmeEdelaBundle:App:app.html.twig', array('currentUser' => $currentUser, 'displayUser' => $user_id), $response);
    }

}
