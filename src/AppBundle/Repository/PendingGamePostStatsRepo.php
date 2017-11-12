<?php

namespace AppBundle\Repository;
use AppBundle\Document\PendingGamePost;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PendingGamePostStatsRepo extends DocumentRepository
{
    /**
     * @return PendingGamePost[]
     */
    public function findAllWithSortedKey()
    {
        return $this->createQueryBuilder()
            ->sort('id', 'desc')
            ->getQuery()
            ->execute();
    }
}
