<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *   collection="PendingGamePost",
 *   repositoryClass="AppBundle\Repository\PendingGamePostRepository"
 * )
 * @MongoDB\Indexes(
 *   @MongoDB\Index(keys={"createdAt"="desc"}),
 * )
 */
class PendingGamePost
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="date", name="created_at")
     */
    private $createdAt;

    /**
     * @MongoDB\Field(type="string", name="import_from")
     */
    private $importFrom;

    /**
     * @MongoDB\ReferenceOne(
     *   discriminatorField="import_from",
     *   discriminatorMap={
     *     "facebookFeed"="AppBundle\Document\Facebook\FacebookFeed",
     *     "weiboFeed"="AppBundle\Document\Weibo\WeiboFeed"
     *   },
     *   defaultDiscriminatorValue="facebookFeed",
     *   name="import_from_ref",
     *   storeAs="dbRef"
     * )
     */
    private $importFromRef;

    /**
     * @MongoDB\Field(type="boolean", name="by_k")
     */
    private $byK;

    /**
     * @MongoDB\Field(type="boolean", name="by_nb")
     */
    private $byNB;

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getImportFrom()
    {
        return $this->importFrom;
    }

    public function getImportFromRef()
    {
        return $this->importFromRef;
    }

    public function getByK()
    {
        return $this->byK;
    }

    public function getByNB()
    {
        return $this->byNB;
    }
}
