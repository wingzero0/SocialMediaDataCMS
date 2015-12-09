<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/9/15
 * Time: 1:57 PM
 */

namespace CodingGuys\CMSBundle\Controller;

/*use Nelmio\ApiDocBundle\Annotation\ApiDoc;*/

use AppBundle\Controller\AppBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\MnemonoBiz;
use CodingGuys\CMSBundle\Form\MnemonoBizType;

/**
 * @Route("/dashboard/mnemonobiz")
 */
class MnemonoBizController extends AppBaseController {
    /**
     * @Route("/", name="mnemonobiz_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        $limit = 15;
        $page = intval($request->get('page', 1));

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('AppBundle:MnemonoBiz');
        $query = $qb->getQuery();

        $paginator  = $this->get('knp_paginator');
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
     * Search MnemonoBiz documents by query.
     *
     * @Route("/search", name="mnemonobiz_search")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:index.html.twig")
     */
    public function searchAction(Request $request){

        $limit = 15;
        $page = intval($request->get('page', 1));

        $keywords = explode(' ', $request->get('query'));
        $regex = array();
        $i = 0;
        foreach($keywords as $keyword){
            $keyword = '/' . $keyword . '/i';
            $regex[$i] = new \MongoRegex($keyword);
            $i++;
        }

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('AppBundle:MnemonoBiz');

        $qb->addOr($qb->expr()->field('name')->all($regex));
        $qb->addOr($qb->expr()->field('shortDesc')->all($regex));
        $qb->addOr($qb->expr()->field('longDesc')->all($regex));

        $query = $qb->getQuery();

        $paginator  = $this->get('knp_paginator');
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
     * Displays a form to create a new MnemonoBiz document.
     *
     * @Route("/new", name="mnemonobiz_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $document = new MnemonoBiz();
        $form   = $this->createCreateForm($document);

        return array(
            'header' => "MnemonoBiz Create",
            'document' => $document,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a MnemonoBiz document.
     *
     * @param MnemonoBiz $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MnemonoBiz $document)
    {
        $form = $this->createForm(new MnemonoBizType(), $document, array(
            'action' => $this->generateUrl('mnemonobiz_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a new MnemonoBiz document.
     *
     * @Route("/", name="mnemonobiz_create")
     * @Method("POST")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $document = new MnemonoBiz();
        $form = $this->createCreateForm($document);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em =  $this->get('doctrine_mongodb')->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('mnemonobiz_show', array('id' => $document->getId())));
        }

        return array(
            'header' => "MnemonoBiz Create Failed!",
            'document' => $document,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a MnemonoBiz document.
     *
     * @Route("/{id}", name="mnemonobiz_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $document =  $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AppBundle:MnemonoBiz')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find MnemonoBiz document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'header' => "MmemonoBiz Detail",
            'document'      => $document,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to delete a MnemonoBiz document by id.
     *
     * @param mixed $id The document id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mnemonobiz_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Deletes a MnemonoBiz document.
     *
     * @Route("/{id}", name="mnemonobiz_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine_mongodb')->getManager();
            $document = $em->getRepository('AppBundle:MnemonoBiz')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find MnemonoBiz document.');
            }

            $em->remove($document);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mnemonobiz_home'));
    }

    /**
     * Displays a form to edit an existing MnemonoBiz document.
     *
     * @Route("/{id}/edit", name="mnemonobiz_edit")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:new.html.twig")
     */
    public function editAction($id)
    {
        $document = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AppBundle:MnemonoBiz')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find User document.');
        }

        $editForm = $this->createEditForm($document);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'header' => "Edit MmemonoBiz",
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a MnemonoBiz document.
     *
     * @param MnemonoBiz $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MnemonoBiz $document)
    {
        $form = $this->createForm(new MnemonoBizType(), $document, array(
            'action' => $this->generateUrl('mnemonobiz_update', array('id' => $document->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing MnemonoBiz document.
     *
     * @Route("/{id}", name="mnemonobiz_update")
     * @Method("PUT")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->get('doctrine_mongodb')->getManager();

        $document = $em->getRepository('AppBundle:MnemonoBiz')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find MnemonoBiz document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em = $this->get('doctrine_mongodb')->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('mnemonobiz_edit', array('id' => $id)));
        }

        return array(
            'header' => "Update MmemonoBiz Failed!",
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }



}