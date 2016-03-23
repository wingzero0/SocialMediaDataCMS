<?php
/**
 * User: kit
 * Date: 23/03/16
 * Time: 6:49 PM
 */

namespace AppBundle\Repository\Weibo;

use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\Weibo\WeiboFeed;

class WeiboFeedRepository extends DocumentRepository{
    /**
     * @param $mid
     * @return WeiboFeed|null
     */
    public function findOneByMid($mid){
        return $this->createQueryBuilder()
            ->field("mid")->equals($mid)->getQuery()->getSingleResult();
    }
}