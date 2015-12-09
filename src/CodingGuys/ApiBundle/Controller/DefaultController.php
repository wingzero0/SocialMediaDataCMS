<?php

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AppBaseController
{
    /**
     * @Route("/home/", name="@apiHome")
     */
    public function indexAction()
    {
        $posts = $this->getPostRepo()->getQueryBuilderSortWithRank()
            ->limit(100)
            ->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post->getId();
        }
        return new Response(json_encode($ret));
    }
}
