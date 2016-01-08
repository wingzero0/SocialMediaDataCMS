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
use AppBundle\Document\ManagedTag;
use CodingGuys\CMSBundle\Form\ManagedTagType;
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
    /**
     * Create a Post manually
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
     * Creates a form to edit a Post document.
     *
     * @param ManagedTag $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm(ManagedTag $document)
    {
        $form = $this->createForm(new ManagedTagType(), $document, array(
            'action' => $this->generateUrl('managedTag_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
}