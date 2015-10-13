<?php
/**
 * User: kit
 * Date: 11/10/15
 * Time: 19:18
 */

namespace AppBundle\Repository\Facebook;

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
}