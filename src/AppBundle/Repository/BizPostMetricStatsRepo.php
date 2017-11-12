<?php

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;

class BizPostMetricStatsRepo extends DocumentRepository
{
    public function findAllByTimeRange(\MongoId $refId,
                                       \DateTime $start,
                                       \DateTime $end)
    {
        $startKey = $start->format('Y-m-d');
        $endKey = $end->format('Y-m-d');
        return $this->createQueryBuilder()
            ->field('id.ref_page_id')->equals($refId)
            ->field('id.date')->gte($startKey)
            ->field('id.date')->lte($endKey)
            ->sort('id.date', 'desc')
            ->getQuery()
            ->execute();
    }
}
