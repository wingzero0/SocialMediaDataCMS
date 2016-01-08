<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 8/1/2016
 * Time: 13:51
 */

namespace CodingGuys\CMSBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Controller\AppBaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/dashboard/managedTag")
 */
class ManagedTagController extends AppBaseController{
    /**
     * @Route("/", name="managedTag_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        return array();
    }
}