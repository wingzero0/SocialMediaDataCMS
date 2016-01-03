<?php

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Api Login controller.
 *
 * @Route("/mobile")
 */
class DefaultController extends AppBaseController
{
    /**
     * @ApiDoc(
     *  description="home page feed",
     *  parameters={
     *      {"name"="tags", "dataType"="string", "required"=false, "description"="filter by tags"},
     *  }
     * )
     * @Route("/home/", name="@apiHome")
     * @Method("GET")
     */
    public function indexAction()
    {
        $posts = $this->getPostRepo()->getPublicQueryBuilderSortWithRank()
            ->limit(100)
            ->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post->getId();
        }
        return new Response(json_encode($ret));
    }
}
