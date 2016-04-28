<?php
/**
 * User: kit
 * Date: 14/03/16
 * Time: 7:25 PM
 */

namespace AppBundle\Repository\Weibo;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\Weibo\WeiboPage;

class WeiboPageRepository extends DocumentRepository{

    /**
     * @param int $limit
     * @return Builder
     */
    public function getQueryBuilder($limit = 100){
        $qb = $this->createQueryBuilder()
            ->field("exception")->notEqual(true)
            ->limit($limit)->sort("id");
        return $qb;
    }
    /**
     * @param string $id mongo id in string
     * @return WeiboPage|null
     */
    public function findOneById($id){
        $qb = $this->getQueryBuilder()
            ->field("id")->equals($id);
        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $uid
     * @return WeiboPage|null
     */
    public function findOneByUid($uid){
        $qb = $this->getQueryBuilder()
            ->field("uid")->equals($uid);
        return $qb->getQuery()->getSingleResult();
    }
}