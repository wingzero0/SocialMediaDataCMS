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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use AppBundle\Document\Post;
use AppBundle\Document\Comment;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
/**
 * @Route("/api/v{vNumber}")
 */
class PostsController extends Controller{
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
        $comments = $this->container->get('fos_comment.manager.comment')->findCommentsByThread($post);
        if ($comments->count() > 0){
            return new Response($this->serialize(iterator_to_array($comments, false), $vNumber));
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        if ($dm instanceof DocumentManager){
            $repo = $dm->getRepository('AppBundle\Document\Comment');
            $comments = $repo->createQueryBuilder()
                ->field('thread.$id')->equals(new \MongoId($post->getId()))
                ->getQuery()
                ->execute();
        }else{
            throw new \InvalidArgumentException();
        }
        return new Response($this->serialize(iterator_to_array($comments, false), $vNumber));

    }

    /**
     * @param array $objs
     * @param string $version
     * @return string JSON string
     */
    private function serialize($objs, $version){
        $data = array('data' => $objs);
        $serializer = $this->get('jms_serializer');
        return $serializer->serialize($data, 'json', SerializationContext::create()->setVersion($version));
    }
}