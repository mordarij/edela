<?php

namespace Acme\EdelaBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LandingController extends Controller
{
    public function IndexAction()
    {
        $currentUser=1;
        $user=2;
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->andX($expr->eq('isAccepted', $currentUser), $expr->eq('isAccepted', $user)))
            ->orWhere($expr->andX($expr->eq('isAccepted', $currentUser), $expr->eq('isAccepted', $user)));
        $relation = $this->getDoctrine()->getManager()->getRepository('AcmeEdelaBundle:UserRelation')->matching($criteria);
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') || $securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->generateUrl('app', ['user_id' => $securityContext->getToken()->getUser()->getId()]));
        }
        return $this->render('AcmeEdelaBundle:Landing:Index.html.twig');
    }

    public function loginDemoAction(){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AcmeUserBundle:User')->findOneBy(['username' => 'demo']);

        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->get("security.context")->setToken($token); //now the user is logged in

        //now dispatch the login event
        $request = $this->get("request");
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        return new RedirectResponse($this->generateUrl('landing'));
    }

}
