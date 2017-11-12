<?php
/**
 * User: kit
 * Date: 8/1/2016
 * Time: 13:51
 */

namespace CodingGuys\CMSBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Controller\AppBaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\ManagedTag;
use CodingGuys\CMSBundle\Form\ManagedTagType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/dashboard/managedTag")
 */
class ManagedTagController extends AppBaseController
{
    /**
     * @Route("/", name="managedTag_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $limit = 15;
        $page = intval($request->get('page', 1));

        $query = $this->getManagedTagRepo()->getFindAllQueryBuilder()->getQuery();
        $paginator = $this->getKnpPaginator();
        $pagination = $paginator->paginate($query,$page,$limit);
        return array(
            'pagination' => $pagination,
        );
    }
    /**
     * Create a ManagedTag
     *
     * @Route("/create", name="managedTag_create")
     * @Method({"GET","POST"})
     * @Template("CodingGuysCMSBundle:ManagedTag:form.html.twig")
     */
    public function createAction(Request $request){
        $document = new ManagedTag();
        $newForm = $this->createNewForm($document);

        $newForm->handleRequest($request);

        if($newForm->isValid()){
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('managedTag_home'));
        }

        return array(
            'header' => "Create Tag",
            'form' => $newForm->createView(),
        );
    }

    /**
     * Edit a ManagedTag
     *
     * @Route("/{id}/edit", name="managedTag_edit")
     * @Method({"GET","PUT"})
     * @Template("CodingGuysCMSBundle:ManagedTag:form.html.twig")
     */
    public function editAction(Request $request, $id){
        $document = $this->getManagedTagRepo()->find($id);
        if ( !($document instanceof ManagedTag)){
            throw $this->createNotFoundException('Unable to find ManagedTag document.');
        }
        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);

        if($editForm->isValid()){
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('managedTag_home'));
        }

        return array(
            'header' => "Create Tag",
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ManagedTag document.
     *
     * @Route("/{id}", name="managedTag_delete")
     * @Method({"GET","DELETE"})
     * @Template("CodingGuysCMSBundle:ManagedTag:delete.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $document = $this->getManagedTagRepo()->find($id);

        if (!$document)
        {
            throw $this->createNotFoundException('Unable to find ManagedTag document.');
        }
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $dm = $this->getDM();
            $dm->remove($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('managedTag_home'));
        }
        return array("deleteForm" => $form->createView(), "document" => $document);
    }

    /**
     * Creates a form to generate a ManagedTag document.
     *
     * @param ManagedTag $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm(ManagedTag $document)
    {
        $form = $this->createForm(ManagedTagType::class, $document, array(
            'action' => $this->generateUrl('managedTag_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to edit a ManagedTag document.
     *
     * @param ManagedTag $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ManagedTag $document)
    {
        $form = $this->createForm(ManagedTagType::class, $document, array(
            'action' => $this->generateUrl('managedTag_edit', array('id' => $document->getId())),
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
            ->setAction($this->generateUrl('managedTag_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Hard Delete'))
            ->getForm()
            ;
    }
}
