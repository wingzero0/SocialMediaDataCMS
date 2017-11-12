<?php

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Query;

class PendingGamePostRepository extends DocumentRepository
{
    use Prime;

    /**
     * @return Query
     */
    public function getFindByKQuery()
    {
        return $this->createQueryBuilder()
            ->field('byK')->equals(true)
            ->sort(['createdAt' => -1])
            ->getQuery();
    }

    /**
     * @return Query
     */
    public function getFindByNBQuery()
    {
        return $this->createQueryBuilder()
            ->field('byNB')->equals(true)
            ->sort(['createdAt' => -1])
            ->getQuery();
    }

    /**
     * @return Query
     */
    public function getFindByKNBQuery()
    {
        return $this->createQueryBuilder()
            ->field('byK')->equals(true)
            ->field('byNB')->equals(true)
            ->sort(['createdAt' => -1])
            ->getQuery();
    }
}
