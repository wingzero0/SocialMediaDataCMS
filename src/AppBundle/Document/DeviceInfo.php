<?php
/**
 * User: kit
 * Date: 21/01/16
 * Time: 11:00 PM
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ODM\Document(collection="deviceInfo",repositoryClass="AppBundle\Repository\DeviceInfoRepository")
 */
class DeviceInfo
{
    /**
     * @ODM\ReferenceOne(targetDocument="AppBundle\Document\User")
     */
    protected $user;
    /**
     * @ODM\Field(type="string")
     *
     */
    protected $imei;
    /**
     * @ODM\Id(strategy="auto")
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     */
    protected  $androidOriOS;

    /**
     * @ODM\Field(type="string")
     */
    protected $registrationId;

    /**
     * @ODM\Field(type="boolean")
     */
    protected $pushEnabled;
    /**
     * @ODM\Field(type="string")
     */
    protected $phoneOsVersion;


    /**
     * @ODM\Field(type="date")
     */
    protected $mobileLastUpdateTime;

    /**
     * @ODM\Field(type="date")
     */
    protected $registerTime;

    /**
     * Set imei
     *
     * @param string $imei
     * @return string
     */
    public function setIMEI($imei)
    {
        $this->imei = $imei;

        return $this;
    }

    /**
     * Get imei
     *
     * @return string
     */
    public function getIMEI()
    {
        return $this->imei;
    }
    /**
     * @ODM\Field(type="string")
     */
    protected $phoneModel;

    public function setUser($user){
        $this->user=$user;
    }
    public function getUser(){
        return $this->user;
    }
    public function setPushEnabled($pushEnabled){
        $this->pushEnabled=$pushEnabled;
    }

    public function setRegisterTime($registerTime){
        $this->registerTime=$registerTime;
    }
    public function setLastUpdateTime($mobileLastUpdateTime){
        $this->mobileLastUpdateTime=$mobileLastUpdateTime;
    }
    public function setPhoneOsVersion($phoneOsVersion){
        $this->phoneOsVersion=$phoneOsVersion;
    }
    public function setRegistrationId($registrationId){
        $this->registrationId=$registrationId;
    }
    public function setAndroidOriOS($androidOriOS){
        $this->androidOriOS=$androidOriOS;
    }
    public function setPhoneModel($phoneModel){
        $this->phoneModel=$phoneModel;
    }

    public function getId(){
        return $this->id;
    }

    public function getPushEnabled(){
        return $this->pushEnabled;
    }

    public function getRegistrationId(){
        return $this->registrationId;
    }

}