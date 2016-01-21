<?php
/**
 * User: kit
 * Date: 21/01/16
 * Time: 11:20 PM
 */

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use AppBundle\Document\User;


class DeviceInfoRepository extends DocumentRepository{

    /**
     * @param User $user
     * @return Builder
     */
    public function getQueryBuilderDeviceInfoAndroidByUser(User $user){
        $qb = $this->createQueryBuilder()
            ->field("pushEnabled")->equals(true)
            ->field("androidOriOS")->equals("android")
            ->field("user")->references($user);
        return $qb;
    }
}