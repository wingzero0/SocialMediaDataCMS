<?php
/**
 * User: kit
 * Date: 03/01/16
 * Time: 10:30 AM
 */

namespace AppBundle\Document\OAuth;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\User;
use FOS\OAuthServerBundle\Document\RefreshToken as BaseRefreshToken;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * @MongoDB\Document(collection="RefreshToken")
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;
    /**
     * @MongoDB\ReferenceOne(targetDocument="Document\OAuth\Client")
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