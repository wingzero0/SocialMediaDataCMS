<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/9/15
 * Time: 1:57 PM
 */

namespace AppBundle\Controller;

/*use Nelmio\ApiDocBundle\Annotation\ApiDoc;*/

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Controller\CMSBaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Form\MnemonoBizType;
use Doctrine\ODM\MongoDB\Query\Builder;


/**
 * @Route("/dashboard/mnemonobiz")
 */
class MnemonoBizController extends CMSBaseController {
    /**
     * @Route("/", name="mnemonobiz_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        $limit = 15;
        $page = intval($request->get('page', 1));

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $documents = $dm->getRepository('AppBundle:MnemonoBiz')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $documents,
            $page,
            $limit
        );

        return array(
            'documents' => $documents,
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
     * @Template("AppBundle:MnemonoBiz:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $document = new User();
        $form = $this->createCreateForm($document);
        /*$form->handleRequest($request);

        if ($form->isValid()) {
            $em =  $this->get('doctrine_mongodb')->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('MnemonoBiz_show', array('id' => $document->getId())));
        }*/

        return array(
            'document' => $document,
            'form'   => $form->createView(),
        );
    }


}