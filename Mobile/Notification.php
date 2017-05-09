<?php
/**
 * Created by PhpStorm.
 * User: c140
 * Date: 23/11/16
 * Time: 12:01 PM
 */

ini_set('display_errors', 'On');

class Notification
{
    function __construct()
    {

    }

    public function call_service($service, $postData)
    {
        switch ($service) {

            case "sendNotiWithDeviceToken": {

                return $this->sendNotiWithDeviceToken($postData);
            }
                break;

        }
    }

    public function  sendNotiWithDeviceToken($postData)
    {
      //print_r($postData);
//        echo "ok";exit();
        $response = array();


        //$regIds = "eR8suFoG_s4:APA91bH09aZdijlDGiFDFmSATNl07OJLlOpalCfP0pvMmZChdoHki2ZzrDnNI-7NopCAbPZlRwGunX4GPu1VQgEQpK39-S8v09VbMsxWJlulCJq3cQFvjoeZY9mN7ZmShopZd4c9wbrv";

        $regIds = $this -> getUserDeviceToken($postData['userId']);

        // optional payload
        $payload = array();
        $payload['id'] = $postData['id'];
        $payload['user_id'] = $postData['user_id'];
        $payload['name'] = $postData['name'];
        $payload['logo'] = $postData['logo'];
        $payload['description'] = $postData['description'];
        $payload['address1'] = $postData['address1'];
        $payload['latitude'] = $postData['latitude'];
        $payload['longitude'] = $postData['longitude'];
        $payload['body'] = "Hi this is body part";

        $push = new Push();
        $push->setTitle("facetag title");
        $push->setMessage("facetag message");
        $push->setIsBackground(FALSE);
        $push->setPayload($payload);

        $json = $push->getPush();

        $post = send($regIds, $json);
//        echo json_encode($post);
//         exit();
        return $post;
    }


    public function getUserDeviceToken($userId)
    {

//        $query = "SELECT device_token
//                  FROM " .TABLE_USER . "
//                  WHERE id ='" . $userId . "'";
//        $result = mysqli_query($GLOBALS['con'], $query) or $message = mysqli_error($GLOBALS['con']);
//        if ($result) {
//            if (mysqli_num_rows($result) > 0) {
//                while ($row = mysqli_fetch_assoc($result)) {
//                    $regIds [] = $row['device_id'];
//                }
//            } else {
//                $regIds = array();
//            }
//        }
//        return $regIds;

        $user_token = "";

        $connection = $GLOBALS['con'];
        $token_query = "select device_id from ".TABLE_USER. " where id = ?";

        $select_userToken_stmt = $connection->prepare($token_query);
        $select_userToken_stmt->bind_param("i",$userId);
        if($select_userToken_stmt->execute())
        {
            $select_userToken_stmt->store_result();
            if($select_userToken_stmt->num_rows > 0)
            {
                while($token = fetch_assoc_all_values($select_userToken_stmt))
                {
                    $user_token = $token['device_id'];
                }
                //print_r($user_token) ;exit();
                return $user_token;
            }else{
                //echo "ok1";exit();
                return $user_token;
            }
        }else{
            //echo "ok2";exit();
            return $user_token;
        }

    }





    /*
       public function sendNotification($postData)
       {
           $response = array();

           $regId = validateObject($postData, 'regId', "");
           $regId = addslashes($regId);

           $title = validateObject($postData, 'title', "0");
           $title = addslashes($title);

           $message = validateObject($postData, 'message', "0");
           $message = addslashes($message);

           $image = validateObject($postData, 'image', "0");
           $image = addslashes($image);

           $include_image = validateObject($postData, 'include_image', "false");
           $include_image = addslashes($include_image);

           $push_type = validateObject($postData, 'push_type', "Single");
           $push_type = addslashes($push_type);

           // optional payload
           $payload = array();
           $payload['team'] = 'FlyerFeed-Version';
           $payload['score'] = '2.11';

           $push = new Push();
           $push->setTitle($title);
           $push->setMessage($message);
           if ($include_image) {
               $push->setImage($image);
           } else {
               $push->setImage('');
           }
           $push->setIsBackground(FALSE);
           $push->setPayload($payload);

           $json = '';
           $post = '';

           $json = $push->getPush();
           $post = send($regId, $json);

           return $response;
       }
   */


}