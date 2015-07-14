<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 01/07/15
 * Time: 22:23
 */

namespace AppBundle\Controller;

use AppBundle\Document\MnemonoBiz;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Post;
use AppBundle\Document\Comment;
use FOS\CommentBundle\Model\ThreadInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Controller\BaseController;

/**
 * @Route("/api/v{vNumber}")
 */
class PostsController extends BaseController{
    /**
     * @Route("/posts", name="get_all_post")
     * @Method("GET")
     */
    public function getPostsAction(Request $request, $vNumber)
    {
        // TODO implement default ranking
        $idGt = $request->get("idGt", null);
        $dm = $this->getDM();
        $qb = $dm->createQueryBuilder($this->postQueryPath);
        if ($idGt){
            $qb->field("id")->gt($idGt);
        }
        $posts = $qb->limit(25)
            ->getQuery()
            ->execute();

        return new Response($this->serialize(iterator_to_array($posts, false), $vNumber));
    }

    /**
     * @Route("/posts", name="create_post")
     * @Method("POST")
     */
    public function createPostsAction(Request $request, $vNumber)
    {
        $dm = $this->getDM();
        $mnemonoBizId = $request->get("mnemonoBizId", null);
        $post = new Post();
        if ($mnemonoBizId != null){
            $businesses = $dm->createQueryBuilder($this->mnemonoBizQueryPath)
                ->field("id")->equals($mnemonoBizId)->getQuery()->execute();
            foreach($businesses as $business){
                if ($business instanceof MnemonoBiz){
                    $post->setContent($request->get("content", ""));
                    $post->setMnemonoBiz($business);
                    $dm->persist($post);
                    $dm->flush();
                    break;
                }else{
                    return new Response("{\"mnemonoBizId is not biz object\"}");
                }
            }
        }else{
            return new Response("{\"mnemonoBizId can't be null\"}");
        }

        return new Response($this->serialize(array(0 => $post), $vNumber));
    }

    /**
     * @Route("/posts/{id}/comments", name="create_comment")
     * @Method({"POST"})
     */
    public function createCommentAction(Request $request, $vNumber, $id){
        $post = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!($post instanceof Post) || !($post instanceof ThreadInterface)){
            throw new \InvalidArgumentException();
        }
        $comment = $this->container->get('fos_comment.manager.comment')->createComment($post);
        if ($comment instanceof Comment){
            $comment->setBody("Test Body");
            $comment->setCreatedAt(new \DateTime());
            $this->container->get('fos_comment.manager.comment')->saveComment($comment);
            return new Response($this->serialize(array('ret' => 1), $vNumber));
        }
        return new Response($this->serialize(array('ret' => 0), $vNumber));

    }

    /**
     * @Route("/posts/{id}/comments", name="get_all_comments")
     * @Method({"GET"})
     */
    public function getCommentAction(Request $request, $vNumber, $id){
        $post = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!($post instanceof Post) || !($post instanceof ThreadInterface)){
            throw new \InvalidArgumentException();
        }

        $dm = $this->getDM();
        $comments = $dm->createQueryBuilder($this->commentQueryPath)
            ->field('thread')->references($post)
            ->getQuery()
            ->execute();
        return new Response($this->serialize(iterator_to_array($comments, false), $vNumber));

    }


}