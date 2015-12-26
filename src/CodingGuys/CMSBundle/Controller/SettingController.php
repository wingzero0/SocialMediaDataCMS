<?php
/**
 * User: kit
 * Date: 17/10/15
 * Time: 4:11 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use CodingGuys\CMSBundle\Controller\CMSBaseController;
use CodingGuys\CMSBundle\Form\Settings\WeightingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Settings\Weighting;

/**
 * @Route("/dashboard/settings")
 */
class SettingController extends AppBaseController{
    /**
     * @Route("/weighting", name="weighting_home")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:Settings:Weighting/index.html.twig")
     */
    public function weightingIndexAction(Request $request){
        $limit = 15;
        $page = intval($request->get('page', 1));
        $query = $this->getWeightingRepo()->getBaseQueryBuilder()->getQuery();

        $paginator  = $this->getKnpPaginator();

        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );

        return array(
            'pagination' => $pagination,
        );
    }

    /**
     * @Route("/weighting/new", name="weighting_create")
     * @Method({"POST","GET"})
     * @Template("CodingGuysCMSBundle:Settings:Weighting/new.html.twig")
     */
    public function weightingCreateAction(Request $request){
        $weighting = new Weighting();
        $form = $this->createWeightingCreateForm($weighting);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $dm = $this->getDM();
            $dm->persist($weighting);
            $dm->flush();
            return $this->redirect($this->generateUrl('weighting_home'));
        }
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create document.
     *
     * @param Weighting $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createWeightingCreateForm(Weighting $document)
    {
        $form = $this->createForm(new WeightingType(), $document, array(
            'action' => $this->generateUrl('weighting_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
}