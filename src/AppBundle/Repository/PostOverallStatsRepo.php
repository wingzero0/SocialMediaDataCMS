<?php

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PostOverallStatsRepo extends DocumentRepository
{
    public function findAllWithSortedKey()
    {
        return $this->createQueryBuilder()
            ->sort('_id', 'desc')
            ->getQuery()
            ->execute();
    }
}
