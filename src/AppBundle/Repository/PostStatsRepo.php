<?php

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\PostStats;

class PostStatsRepo extends DocumentRepository
{
    /**
     * @param \MongoId $refId
     * @return PostStats[]
     */
    public function findAllByRefId(\MongoId $refId)
    {
        return $this->createQueryBuilder()
            ->field('id.ref_post_id')->equals($refId)
            ->sort('id.date', 'desc')
            ->getQuery()
            ->execute();
    }
}
