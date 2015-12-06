<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 06/12/15
 * Time: 10:00 AM
 */

namespace AppBundle\Utility;
use Doctrine\ODM\MongoDB\Query\Builder;

class LoopCollectionStrategy {
    /**
     * @param $queryBuilderCallback
     * @param $reducerCallBack
     *
     * it will reset the dm in the loop
     */
    public function loopCollectionWithQueryBuilder($queryBuilderCallback, $reducerCallBack, $resetDMCallBack){
        $limit = 100;
        $lastFeedId = null;
        $firstRun = true;

        do{
            $resetDMCallBack();
            $qb = $queryBuilderCallback($limit);

            if (!$qb instanceof Builder){
                break;
            }

            if (!$firstRun){
                $qb->field("id")->gt($lastFeedId);
            }
            $cursor = $qb->getQuery()->execute();

            $recordCount = $cursor->count(true);
            foreach($cursor as $record){
                $reducerCallBack($record);
                $lastFeedId = $record->getId();
            }
            $firstRun = false;
        }while($recordCount > 0);
    }

    /**
     * @param $queryBuilderCallback
     * @param $reducerCallBack
     * @param $resetDMCallBack
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function loopCollectionWithSkipParam($queryBuilderCallback, $reducerCallBack, $resetDMCallBack){
        $limit = 100;
        $skip = 0;

        do{
            $resetDMCallBack();
            $qb = $queryBuilderCallback($limit, $skip);

            if (!$qb instanceof Builder){
                break;
            }

            $cursor = $qb->getQuery()->execute();

            $recordCount = $cursor->count(true);
            foreach($cursor as $record){
                $reducerCallBack($record);
            }
            $skip += $recordCount;
        }while($recordCount > 0);
    }
}