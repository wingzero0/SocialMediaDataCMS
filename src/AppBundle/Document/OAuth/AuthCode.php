<?php
/**
 * User: kit
 * Date: 03/01/16
 * Time: 2:14 AM
 */

namespace AppBundle\Document\OAuth;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use FOS\OAuthServerBundle\Document\AuthCode as BaseAuthCode;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * @MongoDB\Document(collection="oauthAuthCode")
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\OAuth\Client")
     */
    protected $client;

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }
}
