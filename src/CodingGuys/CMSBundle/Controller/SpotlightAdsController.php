<?php
/**
 * User: kit
 * Date: 14/01/16
 * Time: 8:36 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\SpotlightAds;
use CodingGuys\CMSBundle\Form\SpotlightAdsType;
use AppBundle\Controller\AppBaseController;

/**
 * @Route("/dashboard/spotlight")
 */
class SpotlightAdsController extends AppBaseController {
    /**
     * @Route("/", name="spotlightAds_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        $limit = 15;
        $page = intval($request->get('page', 1));

        $query = $this->getSpotlightAdsRepo()->getFindAllQueryBuilder()->getQuery();
        $paginator = $this->getKnpPaginator();
        $pagination = $paginator->paginate($query,$page,$limit);
        return array(
            'pagination' => $pagination,
        );
    }
    /**
     * Create a ManagedTag
     *
     * @Route("/create", name="spotlightAds_create")
     * @Method({"GET","POST"})
     * @Template("CodingGuysCMSBundle:SpotlightAds:form.html.twig")
     */
    public function createAction(Request $request){
        $document = new SpotlightAds();
        $newForm = $this->createNewForm($document);

        $newForm->handleRequest($request);

        if($newForm->isValid()){
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('spotlightAds_home'));
        }

        return array(
            'header' => "Create Ads",
            'form' => $newForm->createView(),
        );
    }


    /**
     * Creates a form to generate a ManagedTag document.
     *
     * @param SpotlightAds $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm(SpotlightAds $document)
    {
        $form = $this->createForm(new SpotlightAdsType(), $document, array(
            'action' => $this->generateUrl('spotlightAds_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
}