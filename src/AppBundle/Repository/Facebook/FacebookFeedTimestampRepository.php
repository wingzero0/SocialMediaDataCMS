<?php

namespace AppBundle\Repository\Facebook;

use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookFeedTimestamp;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

class FacebookFeedTimestampRepository extends DocumentRepository
{
    /**
     * @param FacebookFeed $feed
     * @param int $limit
     * @return FacebookFeedTimestamp[]|null
     */
    public function findAllByFeed(FacebookFeed $feed, $limit=25){
        $facebookFeedTimestamp = $this->getQueryBuilderFindAllByFeed($feed)
            ->limit($limit)
            ->sort("id", "desc")
            ->getQuery()->execute();
        return $facebookFeedTimestamp;
    }

    /**
     * @param FacebookFeed $feed
     * @return Builder
     */
    public function getQueryBuilderFindAllByFeed(FacebookFeed $feed){
        $qb = $this->createQueryBuilder()
            ->field("fbFeed")->references($feed);
        return $qb;
    }

    /**
     * @param FacebookFeed $feed
     * @param \DateTime $start
     * @param \DateTime $end
     * @return FacebookFeedTimestamp[]|null
     */
    public function findAllByFeedAndTimeRange(FacebookFeed $feed,
                                              \DateTime $start,
                                              \DateTime $end)
    {
        return $this->getQueryBuilderFindAllByFeed($feed)
            ->field("updateTime")->gte($start)
            ->field("updateTime")->lte($end)
            ->sort('id', 'desc')
            ->getQuery()
            ->execute();
    }
}
