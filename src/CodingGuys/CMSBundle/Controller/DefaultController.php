<?php

namespace CodingGuys\CMSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard/", name="@BackendHome")
     * @Template()
     */
    public function dashboardAction()
    {

        return array();
    }

    /**
     * @Route("/", name="homepage")
     * @Template("CodingGuysCMSBundle:layout:layout.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
}
