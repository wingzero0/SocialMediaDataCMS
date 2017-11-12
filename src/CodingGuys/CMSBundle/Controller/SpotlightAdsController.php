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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/dashboard/spotlight")
 */
class SpotlightAdsController extends AppBaseController
{
    /**
     * @Route("/", name="spotlightAds_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
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
     * Create a SpotlightAds
     *
     * @Route("/create", name="spotlightAds_create")
     * @Method({"GET","POST"})
     * @Template("CodingGuysCMSBundle:SpotlightAds:form.html.twig")
     */
    public function createAction(Request $request)
    {
        $document = new SpotlightAds();
        $newForm = $this->createNewForm($document);

        $newForm->handleRequest($request);

        if ($newForm->isValid())
        {
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
     * Edit a SpotlightAds
     *
     * @Route("/{id}/edit", name="spotlightAds_edit")
     * @Method({"GET","PUT"})
     * @Template("CodingGuysCMSBundle:SpotlightAds:form.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $document = $this->getSpotlightAdsRepo()->find($id);
        if (!($document instanceof SpotlightAds))
        {
            throw $this->createNotFoundException('Unable to find SpotlightAds document.');
        }
        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);

        if ($editForm->isValid())
        {
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('spotlightAds_home'));
        }

        return array(
            'header' => "Create Tag",
            'form' => $editForm->createView(),
        );
    }


    /**
     * Creates a form to generate a SpotlightAds document.
     *
     * @param SpotlightAds $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm(SpotlightAds $document)
    {
        $form = $this->createForm(SpotlightAdsType::class, $document, array(
            'action' => $this->generateUrl('spotlightAds_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Deletes a SpotlightAds document.
     *
     * @Route("/{id}", name="spotlightAds_delete")
     * @Method({"GET","DELETE"})
     * @Template("CodingGuysCMSBundle:SpotlightAds:delete.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $document = $this->getSpotlightAdsRepo()->find($id);

        if (!$document)
        {
            throw $this->createNotFoundException('Unable to find SpotlightAds document.');
        }
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $dm = $this->getDM();
            $dm->remove($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('spotlightAds_home'));
        }
        return array("deleteForm" => $form->createView(), "document" => $document);
    }

    /**
     * Creates a form to edit a SpotlightAds document.
     *
     * @param SpotlightAds $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SpotlightAds $document)
    {
        $form = $this->createForm(SpotlightAdsType::class, $document, array(
            'action' => $this->generateUrl('spotlightAds_edit', array('id' => $document->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('spotlightAds_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Hard Delete'))
            ->getForm()
            ;
    }
}
