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
use AppBundle\Document\DeviceInfo;

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

    /**
     * @param string $imei
     * @return DeviceInfo|null
     */
    public function findOneByIMEI($imei){
        return $this->createQueryBuilder()
                    ->field("imei")->equals($imei)
                    ->getQuery()
                    ->getSingleResult();
    }

    /**
     * @param string $registrationId
     * @param User $user
     * @return DeviceInfo|null
     */
    public function findOneByRegistrationIdAndUser($registrationId,$user){
        return $this->createQueryBuilder()
            ->field("registrationId")->equals($registrationId)
            ->field("user")->references($user)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param string $registrationId
     * @return DeviceInfo|null
     */
    public function findOneByRegistrationId($registrationId){
        return $this->createQueryBuilder()
            ->field("registrationId")->equals($registrationId)
            ->getQuery()
            ->getSingleResult();
    }
}