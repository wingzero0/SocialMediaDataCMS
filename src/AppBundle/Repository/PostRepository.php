<?php

namespace AppBundle\Repository;

use AppBundle\Document\Facebook\FacebookFeed;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PostRepository extends DocumentRepository
{
    /**
     * @param FacebookFeed $feed
     * @return AppBundle\Document\Post|null
     */
    public function findOneByFeed(FacebookFeed $feed){
        $post = $this->createQueryBuilder()
            ->field("importFrom")->equals("facebookFeed")
            ->field("importFromRef")->references($feed)
            ->sort("id", "desc")
            ->getQuery()->getSingleResult();

        return $post;
    }
}