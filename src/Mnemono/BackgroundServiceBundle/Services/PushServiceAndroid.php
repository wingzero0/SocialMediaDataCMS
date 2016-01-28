<?php
/**
 * User: kit
 * Date: 21/01/16
 * Time: 10:54 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;


use AppBundle\Document\DeviceInfo;
use GearmanJob;
use Symfony\Component\DependencyInjection\Container;
use Mmoreram\GearmanBundle\Driver\Gearman;
use Mnemono\BackgroundServiceBundle\Services\BaseService;

/**
 * @Gearman\Work(
 *     description = "Property Android push worker",
 *     service="GCMService"
 * )
 */
class PushServiceAndroid extends BaseService
{
    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "testJob",
     *     description = "test object structure",
     *     defaultMethod = "doBackground"
     * )
     */
    public function test(\GearmanJob $job){
        var_dump($job->workload());
        $user = $this->getUserRepo()->findOneByUsername("admin");
        var_dump($user->getUsername());
        $deviceInfo = $this->getDeviceInfoRepo()->getQueryBuilderDeviceInfoAndroidByUser($user);
        if ($deviceInfo instanceof DeviceInfo){
            var_dump($deviceInfo);
        }else{
            echo "fail\n";
        }

    }
    /**
     * Push to android
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "PushJob",
     *     description = "push message to google server",
     *     defaultMethod = "doBackground"
     * )
     */
    public function SendNotification(\GearmanJob $job)
    {


        $key_json = json_decode($job->workload(), true);
//        print_r($key_json);
        // Message to be sent
        $title = $key_json ['title'];
        $threadId = $key_json['threadId'];
        $date = $key_json['date'];
        $receiveUsername = $key_json['recipientUsername'];
        $type = $key_json['type'];


        $dm = $this->getDM();
        $dm->getConnection()->close();
        $dm->getConnection()->connect();
        $prevQueryId = "0";
        $firstIn = true;
        while (true) {
            if ($type == "message") {
                $receiveUser = $this->getUserRepo()->findOneByUsername($receiveUsername);

                $query = $this->getDeviceInfoRepo()->getQueryBuilderDeviceInfoAndroidByUser($receiveUser);

                if (!$firstIn) {
                    $query->field("id")->gt($prevQueryId);
                    $query->skip(500);

                }

                $devicesInfo = $query->limit(500)
                    ->getQuery()->execute();

                $devicesInfoSize = count($devicesInfo);

                if ($devicesInfoSize <= 0) {
                    break;
                }

                $RegistrationId = array();

                foreach ($devicesInfo as $deviceInfo) {
                    $RegistrationId[] = $deviceInfo->getRegistrationId();

                    $prevQueryId = $deviceInfo->getId();
                }

                $firstIn = false;
//                print_r($RegistrationId . " " . $title . " " . $messageId . " ");


                // Execute post
                $result_str = $this->communicationWithGoogle($RegistrationId, $title, $threadId, $date,$type);

                $decode_result = json_decode($result_str, true);
                $results = $decode_result['results'];


                $i = 0;
                foreach ($devicesInfo as $deviceInfo) {
                    $result = $results[$i];
                    if (is_array($result) && array_key_exists('message_id', $result)) {
                        $successful = 1;

                        if (array_key_exists('registration_id', $result) != null) {
                            if ($deviceInfo instanceof DeviceInfo) { // update the latest registration_id
                                $deviceInfo->setRegistrationId($result['registration_id']);
                                $dm->persist($deviceInfo);
                            }
                        }

                    } else if (is_array($result) && array_key_exists('error', $result)) {

//                    if (array_key_exists('Unavailable', $result)) {
//
//                        $user_info->setRegistrationId('Unavailable');
//                        $em->flush();
//                    } else
                        if (in_array('InvalidRegistration', $result) != null) {
                            $deviceInfo->setPushEnabled(0);
                            $dm->persist($deviceInfo);
                        } else if (in_array('NotRegistered', $result) != null) {
                            $deviceInfo->setPushEnabled(0);
                            $dm->persist($deviceInfo);

                        }

                    }
                    $i++;
                }
                $dm->flush();
            }
        }
        return true;

    }

    function communicationWithGoogle($RegistrationId, $title, $threadId, $date,$type)
    {


        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        $api_key = 'AIzaSyARnrrDZWQQWl1sedELtoeBSzrp42e89wE';
        $fields = array(
            'registration_ids' => $RegistrationId,
//                'time_to_live' => 1800,
//                'delay_while_idle' => true,
            'data' => array("title" => $title, "threadId" => $threadId, "date" => $date,"type"=>$type),
        );

        $headers = array(
            'Authorization: key=' . $api_key,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result_str = curl_exec($ch);
        // Close connection
        curl_close($ch);
//            $current_time = new \DateTime('now');
        print_r($result_str);
        echo "\n";
//            print_r($current_time->format('Y-m-d H:i:s'));
//
//            $filesystem = $this->container->get('oneup_flysystem.acme_filesystem');
//            $filesystem->put($current_time->format('Y-m-d H:i:s'), $result_str);
        return $result_str;

    }
}