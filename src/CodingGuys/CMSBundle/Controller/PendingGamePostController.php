<?php

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Document\PendingGamePost;
use Doctrine\ODM\MongoDB\Query\Query;

/**
 * @Route("/dashboard/pending-game-posts")
 */
class PendingGamePostController extends AppBaseController
{
    /**
     * @Route("/", name="pending_game_posts_home")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @return array variables for template engine
     */
    public function indexAction(Request $request)
    {
        // TODO merge index.html.twig, nb.html.twig, knb.html.twig
        $limit = 10;
        $page = intval($request->get('page', 1));
        $query = $this->getPendingGamePostRepo()
            ->getFindByKQuery();
        return $this->getPostsByPendingGamePostQuery($query, $page, $limit);
    }

    /**
     * @Route("/nb", name="pending_game_posts_nb")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @return array variables for template engine
     */
    public function nbAction(Request $request)
    {
        $limit = 10;
        $page = intval($request->get('page', 1));
        $query = $this->getPendingGamePostRepo()
            ->getFindByNBQuery();
        return $this->getPostsByPendingGamePostQuery($query, $page, $limit);
    }

    /**
     * @Route("/knb", name="pending_game_posts_knb")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @return array variables for template engine
     */
    public function knbAction(Request $request)
    {
        $limit = 10;
        $page = intval($request->get('page', 1));
        $query = $this->getPendingGamePostRepo()
            ->getFindByKNBQuery();
        return $this->getPostsByPendingGamePostQuery($query, $page, $limit);
    }

    /**
     * @param Query $pendingGamePostQuery
     * @param int $limit
     * @param int $page
     *
     * @return array variables for template engine
     */
    private function getPostsByPendingGamePostQuery(Query $pendingGamePostQuery, $page, $limit){
        $paginator = $this->getKnpPaginator();
        /* @var PendingGamePost[] $items */
        $items = $paginator->paginate($pendingGamePostQuery, $page, $limit);
        $this->getPendingGamePostRepo()
            ->primeReferences($items, ['importFromRef']);
        $mnPostMongoIds = [];
        foreach ($items as $item)
        {
            $mnPostMongoIds[] = $item->getId();
        }
        $mnPosts = $this->getPostRepo()
            ->findByIdsSelectTagsAndExpireDate($mnPostMongoIds);
        $mnPostsInfo = [];
        foreach ($mnPosts as $mnPost)
        {
            $mnPostsInfo[$mnPost->getId()] = $mnPost;
        }
        return [
            'items' => $items,
            'mnPostsInfo' => $mnPostsInfo,
        ];
    }

    /**
     * @Route("/stats", name="pending_game_posts_stats")
     * @Template()
     *
     * @return array variables for template engine
     */
    public function statsAction()
    {
        $items = $this->getPendingGamePostStatsRepo()
            ->findAllWithSortedKey();
        return [
            'items' => $items,
        ];
    }
}
