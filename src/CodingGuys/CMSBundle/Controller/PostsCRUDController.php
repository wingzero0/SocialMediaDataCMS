<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/17/15
 * Time: 11:18 AM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Document\Facebook\FacebookFeed;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Post;
use CodingGuys\CMSBundle\Form\PostType;

/**
 * @Route("/dashboard/posts")
 */
class PostsCRUDController extends CMSBaseController{
    /**
     * Displays all the Posts in DB
     *
     * @Route("/", name="posts_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        $limit = 15;
        $page = intval($request->get('page', 1));

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('AppBundle:Post');
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
     * @Route("/search", name="posts_search")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:PostsCRUD:index.html.twig")
     */
    public function searchAction(Request $request){

        $limit = 15;
        $page = intval($request->get('page', 1));

        $keywords = explode(' ', $request->get('query'));
        $regex = array();
        $postsID = array();
        $i = 0;
        foreach($keywords as $keyword){
            try {
                $postsID[$i] = new \MongoId($keyword);
            } catch (\MongoException $me){

            }
            $keyword = '/' . $keyword . '/i';
            $regex[$i] = new \MongoRegex($keyword);
            $i++;
        }

        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $qbMnemonobiz = $dm->createQueryBuilder('AppBundle:MnemonoBiz');
        $qbMnemonobiz->addOr($qbMnemonobiz->expr()->field('name')->all($regex));
        $products = $qbMnemonobiz->getQuery()->toArray();
        $mnemonobizID = array();
        $i = 0;
        foreach($products as $product){
            $mnemonobizID[$i] = $product->getId();
        }

        $qbPost = $dm->createQueryBuilder('AppBundle:Post');
        $qbPost->addOr($qbPost->expr()->field('id')->in($postsID));
        $qbPost->addOr($qbPost->expr()->field('content')->all($regex));
        $qbPost->addOr($qbPost->expr()->field('mnemonoBiz.id')->in($mnemonobizID));
        $query = $qbPost->getQuery();

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
     * Finds and displays a Post document.
     *
     * @Route("/{id}", name="posts_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id){
        $document = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AppBundle:Post')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find MnemonoBiz document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'header' => "Post Detail",
            'document'      => $document,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Post document.
     *
     * @Route("/{id}/edit", name="posts_edit")
     * @Method({"GET","PUT"})
     * @Template("CodingGuysCMSBundle:PostsCRUD:new.html.twig")
     */
    public function editAction(Request $request, $id){
        $document = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AppBundle:Post')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);
        if($editForm->isValid()){
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('posts_edit',array('id' => $id)));
        }

        return array(
            'header' => "Edit Post",
            'form' => $editForm->createView(),
        );

    }

    /**
     * set or unset a post as a spotlight post
     *
     * @Route("/{id}/spotlight", name="posts_spotlight")
     * @Method({"PUT"})
     */
    public function spotlightAction(Request $request, $id){
        $document = $this->getPostRepo()->find($id);

        if (!$document instanceof Post) {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $setFlag = intval($request->get("set"));

        $ret = array();
        if ($setFlag > 0){
            $document->setSpotlight(true);
            $ret = array("spotlight" => true);
        }else{
            $document->setSpotlight(false);
            $ret = array("spotlight" => false);
        }

        $this->getDM()->persist($document);
        $this->getDM()->flush();

        return new JsonResponse($ret);
    }

    /**
     * Deletes a Post document.
     *
     * @Route("/{id}", name="posts_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if($form->isValid()){
            $dm = $this->get('doctrine_mongodb')->getManager();
            $document = $dm->getRepository('AppBundle:Post')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Post document.');
            }

            $dm->remove($document);
            $dm->flush();
        }
    }

    /**
     * show a raw data of Post source.
     *
     * @Route("/{id}/sourceRaw", name="posts_source_raw")
     * @Method("GET")
     * @Template()
     */
    public function sourceRawAction(Request $request, $id)
    {
        $post = $this->getPostRepo()->find($id);

        if (! $post instanceof Post) {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $sourceObj = $post->getImportFromRef();
        $rawData = $this->queryRawData($sourceObj);
        $strOutput = print_r($rawData, true);
        return array("strOutput" => $strOutput);
    }

    private function queryRawData($obj){
        if ($obj instanceof FacebookFeed){
            return $this->getFacebookFeedRepo()->getRawById($obj->getId());
        }
    }

    /**
     * Creates a form to edit a Post document.
     *
     * @param Post $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Post $document)
    {
        $form = $this->createForm(new PostType(), $document, array(
            'action' => $this->generateUrl('posts_edit', array('id' => $document->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a Post document by id.
     *
     * @param mixed $id The document id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('posts_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }


}