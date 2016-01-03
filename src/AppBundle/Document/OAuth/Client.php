<?php
/**
 * User: kit
 * Date: 03/01/16
 * Time: 2:10 AM
 */

namespace AppBundle\Document\OAuth;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use FOS\OAuthServerBundle\Document\Client as BaseClient;
/**
 * @MongoDB\Document(collection="oauthClient")
 */
class Client extends BaseClient
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;
}