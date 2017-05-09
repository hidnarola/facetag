<?php
/**
 * Created by PhpStorm.
 * User: c174
 * Date: 05/12/16
 * Time: 9:46 AM
 */

class androidPush
{

    function __construct()
    {

    }

    public function call_service($service, $postData)
    {
        switch ($service) {
            case "sendPushNotification": {
                return $this->sendPushNotification($postData);
            }
                break;
        }
    }

    public function sendPushNotification($postData)
    {
        $message = "give Review on your Product.";
        include_once 'GCM.php';
        $gcm_obj = new GCM();
        //$data = $gcm_obj->sendPushiOS(array("4b5cc0a14e40c60d733547b7a8038c33339d0d0a3bb74b70a18f8ddab2c0b071"), "REVI APP", $message);
        $pushData = array("notification_type" => "notification", "body" => $message,'sound' => 'default',);
        $data = $gcm_obj->sendPushToAndroid("fi080SFTMgo:APA91bEcRgT3XnUAIzylZpLFo2aL5gCiVnqGSG5wif3snORzIN47zF-Y0jR6c2k3OEmFbnpOrG7jYQf42gO0WKon5T-WZeeqDtpDMhnxVnd4Et5PwqyZZAlbYddeM26758tSp49XVH1E", $pushData, TRUE);
        return $data;
    }
}
?>