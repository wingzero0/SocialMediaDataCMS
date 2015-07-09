<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Document\User;
use AppBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/dashboard/member")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="member_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em =$this->get('doctrine.odm.mongodb.document_manager');

        $documents = $em->getRepository('AppBundle:User')->findAll();

        return array(
            'documents' => $documents,
        );
    }
    /**
     * Creates a new User document.
     *
     * @Route("/", name="member_create")
     * @Method("POST")
     * @Template("AppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $document = new User();
        $form = $this->createCreateForm($document);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em =  $this->get('doctrine_mongodb')->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('member_show', array('id' => $document->getId())));
        }

        return array(
            'document' => $document,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a User document.
    *
    * @param User $document The document
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(User $document)
    {
        $form = $this->createForm(new UserType(), $document, array(
            'action' => $this->generateUrl('member_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User document.
     *
     * @Route("/new", name="member_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $document = new User();
        $form   = $this->createCreateForm($document);

        return array(
            'document' => $document,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User document.
     *
     * @Route("/{id}", name="member_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
//        $em = $this->getDoctrine()->getManager();

        $document =  $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AppBundle:User')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find User document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'      => $document,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User document.
     *
     * @Route("/{id}/edit", name="member_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
//        $em = $this->getDoctrine()->getManager();

        $document = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AppBundle:User')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find User document.');
        }

        $editForm = $this->createEditForm($document);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'      => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User document.
    *
    * @param User $document The document
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $document)
    {
        $form = $this->createForm(new UserType(), $document, array(
            'action' => $this->generateUrl('member_update', array('id' => $document->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User document.
     *
     * @Route("/{id}", name="member_update")
     * @Method("PUT")
     * @Template("AppBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->get('doctrine_mongodb')->getManager();

        $document = $em->getRepository('AppBundle:User')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find User document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($document);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($document);
            $em->flush();

            return $this->redirect($this->generateUrl('member_edit', array('id' => $id)));
        }

        return array(
            'document'      => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User document.
     *
     * @Route("/{id}", name="member_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine_mongodb')->getManager();
            $document = $em->getRepository('AppBundle:User')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find User document.');
            }

            $em->remove($document);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('member_home'));
    }

    /**
     * Creates a form to delete a User document by id.
     *
     * @param mixed $id The document id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('member_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

}
