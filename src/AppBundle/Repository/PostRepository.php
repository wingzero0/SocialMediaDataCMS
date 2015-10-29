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

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindAllFeedByDateRange(\DateTime $startDate, \DateTime $endDate, $limit = 100, $skip = 0){
        $qb = $this->createQueryBuilder()
            ->field("updateAt")->gte($startDate)
            ->field("updateAt")->lte($endDate)
            ->skip($skip)->limit($limit);
        return $qb;
    }
}