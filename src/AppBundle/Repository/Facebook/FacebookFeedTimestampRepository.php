<?php

namespace AppBundle\Repository\Facebook;

use AppBundle\Document\Facebook\FacebookFeed;
use Doctrine\ODM\MongoDB\DocumentRepository;

class FacebookFeedTimestampRepository extends DocumentRepository
{
    public function findAllByFeed(FacebookFeed $feed, $limit=25){
        $facebookFeedTimestamp = $this->createQueryBuilder()
            ->field("fbFeed")->references($feed)
            ->limit($limit)
            ->sort("id", "desc")
            ->getQuery()->execute();
        return $facebookFeedTimestamp;
    }
}
