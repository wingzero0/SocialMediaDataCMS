<?php
/**
 * User: kit
 * Date: 17/01/16
 * Time: 5:58 PM
 */

namespace AppBundle\Repository\Utility;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use AppBundle\Document\Utility\LogRecord;

class LogRecordRepository extends DocumentRepository
{
    /**
     * @return LogRecord
     */
    public function findLastPostReportLogRecord()
    {
        $logRecord = $this->createQueryBuilder()
            ->field("category")->equals("postReview")
            ->sort(["_id" => -1])
            ->getQuery()->getSingleResult();
        return $logRecord;
    }
}
