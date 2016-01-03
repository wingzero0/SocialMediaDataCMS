<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/17/15
 * Time: 11:18 AM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\Facebook\FacebookFeed;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Post;
use CodingGuys\CMSBundle\Form\PostType;
use AppBundle\Document\MnemonoBiz;

/**
 * @Route("/dashboard/posts")
 */
class PostsCRUDController extends AppBaseController{
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

        $query = $this->getPostRepo()->createQueryBuilder()
            ->sort(array("id"=>-1))->getQuery();

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
     * Create a Post manually
     *
     * @Route("/create", name="posts_create")
     * @Method({"GET","POST"})
     * @Template("CodingGuysCMSBundle:PostsCRUD:form.html.twig")
     */
    public function createAction(Request $request){
        $document = new Post();
        $newForm = $this->createNewForm($document);

        $newForm->handleRequest($request);

        if($newForm->isValid()){
            $this->updatePostFinalScore($document);
            $createTime = new \DateTime();
            $document->setCreateAt($createTime);
            $document->setUpdateAt($createTime);
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('posts_home'));
        }

        return array(
            'header' => "Create Post",
            'form' => $newForm->createView(),
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
        $document = $this->getPostRepo()->find($id);

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
     * @Template("CodingGuysCMSBundle:PostsCRUD:form.html.twig")
     */
    public function editAction(Request $request, $id){
        $document = $this->getPostRepo()->find($id);

        if (!($document instanceof Post)) {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $backupBiz = $document->getMnemonoBiz();

        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);
        if($editForm->isValid()){
            $this->updatePostFinalScore($document);
            if ($backupBiz instanceof MnemonoBiz){
                $document->setMnemonoBiz($backupBiz);
            }

            $document->setUpdateAt(new \DateTime());
            $dm = $this->getDM();
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
            return $this->redirect($this->generateUrl('posts_home'));
        }
        return new JsonResponse(array("ret" => "delete fail"));
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
     * set or unset a post as a homepage post
     *
     * @Route("/{id}/homepage", name="posts_set_homepage")
     * @Method({"PUT"})
     */
    public function setHomepageAction(Request $request, $id){
        $document = $this->getPostRepo()->find($id);

        if (!$document instanceof Post) {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $setFlag = intval($request->get("set"));

        if ($setFlag > 0){
            $document->setShowAtHomepage(true);
            $ret = array("homepage" => true);
        }else{
            $document->setShowAtHomepage(false);
            $ret = array("homepage" => false);
        }

        $this->getDM()->persist($document);
        $this->getDM()->flush();

        return new JsonResponse($ret);
    }

    /**
     * set a post publishStatus as published or not (means reviewed)
     *
     * @Route("/{id}/publish", name="posts_publish")
     * @Method({"PUT"})
     */
    public function publishAction(Request $request, $id){
        $document = $this->getPostRepo()->find($id);

        if (!$document instanceof Post) {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $setFlag = intval($request->get("set"));

        $status = "review";
        if ($setFlag > 0){
            $status = "published";
        }
        $document->setPublishStatus($status);

        $this->getDM()->persist($document);
        $this->getDM()->flush();
        $ret = array("status" => $status);

        return new JsonResponse($ret);
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
        $ret = $this->queryRawData($sourceObj);
        $strOutput = print_r($ret["rawData"], true);
        return array("possibleLinks" => $ret["possibleLinks"], "strOutput" => $strOutput);
    }

    private function queryRawData($obj){
        $possibleLinks = array();
        if ($obj instanceof FacebookFeed){
            $rawData = $this->getFacebookFeedRepo()->getRawById($obj->getId());

            if (isset($rawData["link"])){
                $possibleLinks[] = $rawData["link"];
            }
            $possibleLinks[] = $obj->getShortLink();
            return array("possibleLinks" => $possibleLinks, "rawData" => $rawData);
        }
        return array("possibleLinks" => $possibleLinks, "rawData" => null);
    }

    /**
     * Creates a form to edit a Post document.
     *
     * @param Post $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm(Post $document)
    {
        $defaultExpireDate = new \DateTime();
        $defaultExpireDate->add(new \DateInterval("P7D"));
        $document->setExpireDate($defaultExpireDate);
        $form = $this->createForm(new PostType(), $document, array(
            'action' => $this->generateUrl('posts_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
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
            ->add('submit', 'submit', array('label' => 'Hard Delete'))
            ->getForm()
            ;
    }

    private function updatePostFinalScore(Post $post){
        $localWeight = $this->getWeighting("localWeight");
        $globalWeight = $this->getWeighting("globalWeight");
        $adminWeight = $this->getWeighting("adminWeight");

        $finalScore = $post->updateFinalScore($localWeight , $globalWeight , $adminWeight );
        return $finalScore;
    }
}