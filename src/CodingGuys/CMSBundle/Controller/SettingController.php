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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/dashboard/settings")
 */
class SettingController extends AppBaseController
{
    /**
     * @Route("/weighting", name="weighting_home")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:Settings:Weighting/index.html.twig")
     */
    public function weightingIndexAction(Request $request)
    {
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
     * @Template("CodingGuysCMSBundle:Settings:Weighting/form.html.twig")
     */
    public function weightingCreateAction(Request $request)
    {
        $weighting = new Weighting();
        $form = $this->createWeightingCreateForm($weighting);

        $form->handleRequest($request);
        if ($form->isValid())
        {
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
     * @Route("/weighting/{id}/edit", name="weighting_edit")
     * @Method({"PUT","GET"})
     * @Template("CodingGuysCMSBundle:Settings:Weighting/form.html.twig")
     */
    public function weightingEditAction(Request $request, $id)
    {
        $document = $this->getWeightingRepo()->find($id);

        if (!($document instanceof Weighting))
        {
            throw $this->createNotFoundException('Unable to find Weighting document.');
        }
        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);

        if ($editForm->isValid())
        {
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('weighting_home'));
        }

        return array(
            'header' => "Edit Weighting",
            'form' => $editForm->createView(),
        );
    }

        /**
     * Deletes a Weighting document.
     *
     * @Route("/{id}", name="weighting_delete")
     * @Method({"GET","DELETE"})
     * @Template("CodingGuysCMSBundle:Settings:Weighting/delete.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $document = $this->getWeightingRepo()->find($id);

        if (!$document)
        {
            throw $this->createNotFoundException('Unable to find Weighting document.');
        }
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $dm = $this->getDM();
            $dm->remove($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('weighting_home'));
        }
        return array("deleteForm" => $form->createView(), "document" => $document);
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
        $form = $this->createForm(WeightingType::class, $document, array(
            'action' => $this->generateUrl('weighting_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to edit a SpotlightAds document.
     *
     * @param Weighting $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Weighting $document)
    {
        $form = $this->createForm(WeightingType::class, $document, array(
            'action' => $this->generateUrl('weighting_edit', array('id' => $document->getId())),
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
            ->setAction($this->generateUrl('weighting_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Hard Delete'))
            ->getForm()
            ;
    }
}
