<?php
/**
 * User: kit
 * Date: 10/29/2015
 * Time: 9:26 AM
 */

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\PostForReview;

class PostForReviewRepository extends DocumentRepository{

    /**
     * @param int $batch
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindByBatch($batch){
        return $this->createQueryBuilder()
            ->field("batch")->equals($batch);
    }

    /**
     * @return int
     */
    public function findLastBatchNum(){
        $lastBatchRecord = $this->createQueryBuilder()
            ->sort("batch", "desc")->limit(1)->getQuery()->getSingleResult();

        if ($lastBatchRecord instanceof PostForReview){
            return $lastBatchRecord->getBatch();
        }
        return 0;
    }
}