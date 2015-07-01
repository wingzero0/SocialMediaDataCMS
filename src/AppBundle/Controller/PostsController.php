<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 01/07/15
 * Time: 22:23
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/v{vNumber}")
 */
class PostsController extends Controller{
    /**
     * @Route("/posts/{bizName}", name="retrival")
     */
    public function getPostsAction(Request $request, $vNumber, $bizName)
    {
        $bizs = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('AppBundle:MnemonoBiz')
            ->getQuery()
            ->execute();

        return new Response($this->serialize(iterator_to_array($bizs, false), $vNumber));
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