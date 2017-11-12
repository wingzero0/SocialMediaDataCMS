<?php

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Document\TrendingPost;

/**
 * @Route("/dashboard/trending-posts")
 */
class TrendingPostController extends AppBaseController
{
    /**
     * @Route("/", name="trending_posts_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $limit = 8;
        $page = intval($request->get('page', 1));
        $query = $this->getTrendingPostRepo()
            ->createQueryBuilder()
            ->sort(['key' => -1])
            ->getQuery();
        $paginator  = $this->getKnpPaginator();
        $items = $paginator->paginate($query, $page, $limit);
        return [
            'items' => $items,
        ];
    }
}
