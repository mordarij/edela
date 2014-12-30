<?php

namespace Acme\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SamplesController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AcmeEdelaBundle:SampleCategory')->findAll();
        $samples = $em->getRepository('AcmeEdelaBundle:Sample')->findAll();
        return $this->render('AcmeAdminBundle:Samples:index.html.twig', ['categories' => $categories, 'samples' => $samples]);
    }
}
