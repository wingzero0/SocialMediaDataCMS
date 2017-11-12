<?php

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends AppBaseController
{
    /**
     * @Route("/dashboard/", name="@BackendHome")
     * @Template()
     */
    public function dashboardAction()
    {
        $bizCount = $this->getMnemenoBizRepo()
            ->getHomeCount();
        $postItems = $this->getPostOverallStatsRepo()
            ->findAllWithSortedKey();
        return [
            'bizCount' => $bizCount,
            'postItems' => $postItems,
        ];
    }

    /**
     * @Route("/", name="@DummyHomePage")
     * @Template()
     */
    public function dummyHomeAction(Request $request)
    {
        return $this->redirect($this->generateUrl("@BackendHome"));
    }

    /**
     * @Route("/login", name="@loginPage")
     * @Template("CodingGuysCMSBundle:Default:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $helper = $this->get('security.authentication_utils');
        return [
            'lastUsername' => $helper->getLastUsername(),
        ];
    }
}
