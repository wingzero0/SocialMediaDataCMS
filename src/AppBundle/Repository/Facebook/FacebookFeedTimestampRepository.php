<?php

namespace AppBundle\Repository\Facebook;

use AppBundle\Document\Facebook\FacebookFeed;
use Doctrine\ODM\MongoDB\DocumentRepository;

class FacebookFeedTimestampRepository extends DocumentRepository
{
    public function findAllByFeed(FacebookFeed $feed){
        $facebookFeedTimestamp = $this->createQueryBuilder()
            ->field("fbFeed")->references($feed)
            ->getQuery()->execute();
        return $facebookFeedTimestamp;
    }
}