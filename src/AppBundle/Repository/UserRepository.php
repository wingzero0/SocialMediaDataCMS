<?php
/**
 * User: kit
 * Date: 21/01/16
 * Time: 11:05 PM
 */

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use AppBundle\Document\User;

class UserRepository extends DocumentRepository{

    /**
     * @param $username
     * @return User|null
     */
    public function findOneByUsername($username){
        $user = $this->createQueryBuilder()
            ->field("username")->equals($username)
            ->getQuery()
            ->getSingleResult();
        return $user;
    }
}