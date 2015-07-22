<?php

namespace AppBundle\Repository;

use AppBundle\Document\Facebook\FacebookFeed;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PostRepository extends DocumentRepository
{
    public function findAllByFeed(FacebookFeed $feed, $limit){
        $feedTimestamps = $this->createQueryBuilder()
            ->field("fbFeed")->references($feed)
            ->limit($limit)
            ->sort("id", "desc")
            ->getQuery()->execute();

        return $feedTimestamps;
    }
}