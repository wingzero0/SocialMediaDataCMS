<?php
/**
 * User: kit
 * Date: 11/10/15
 * Time: 19:18
 */

namespace AppBundle\Repository\Facebook;

use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\Facebook\FacebookFeed;
use Doctrine\ODM\MongoDB\DocumentRepository;

class FacebookFeedRepository extends DocumentRepository{
    /**
     * @param string $fromDate
     * @param string $toDate
     * @param int $limit
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderByDateRange($fromDate, $toDate, $limit = 100){
        $qb = $this->createQueryBuilder()
            ->field("createdTime")->gte($fromDate)
            ->field("createdTime")->lte($toDate)
            ->limit($limit)->sort("id")
        ;
        return $qb;
    }

    /**
     * @param FacebookPage $fbPage
     * @param string $fromDate
     * @param string $toDate
     * @param int $limit
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderByPageAndDateRange(FacebookPage $fbPage, $fromDate, $toDate, $limit = 100){
        $qb = $this->getQueryBuilderByDateRange($fromDate, $toDate, $limit)
            ->field("fbPage")->references($fbPage)
        ;
        return $qb;
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getRawById($id){
        return $this->createQueryBuilder()->hydrate(false)->field("id")->equals($id)->getQuery()->getSingleResult();
    }

    /**
     * @param $fbId
     * @return FacebookFeed|null
     */
    public function findOneByFbId($fbId){
        return $this->createQueryBuilder()
            ->field("fbId")->equals($fbId)->getQuery()->getSingleResult();
    }
}