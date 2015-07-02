<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 01/07/15
 * Time: 22:23
 */

namespace AppBundle\Controller;

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
     */
    public function getPostsAction(Request $request, $vNumber)
    {
        $bizs = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('AppBundle:MnemonoBiz')
            ->getQuery()
            ->execute();

        return new Response($this->serialize(iterator_to_array($bizs, false), $vNumber));
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

        $dm = $this->get('doctrine_mongodb')->getManager();
        $comments = $dm->createQueryBuilder('AppBundle:Comment')
            ->field('thread')->references($post)
            ->getQuery()
            ->execute();
        return new Response($this->serialize(iterator_to_array($comments, false), $vNumber));

    }


}