<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Push_notification {

    public function sendPushiOS($postData, $pushData) {
//        $deviceToken = validateObject($postData, 'deviceToken', "");
        $deviceToken = $postData['deviceToken'];
        $deviceToken = addslashes($deviceToken);

//        $subject = $postData['subject'];
        $pushMessage = $postData['pushMessage'];

        $tokens = $deviceToken;
        $tokens = array($tokens);
        $development = true;
        $message = $pushMessage;
        //$message = $message;
        $badge = 0;
        $sound = 'default';

        $payload = array();
//        $payload['aps'] = array('alert' => $message, 'badge' => intval($badge), 'sound' => $sound);
        $payload['aps'] = $pushData;
        $payload['aps']['alert'] = $message;
        $payload['aps']['badge'] = intval($badge);
        $payload['aps']['sound'] = $sound;

        $payload = json_encode($payload);

        $apns_url = NULL;
        $apns_cert = NULL;
        $apns_port = 2195;


        if (ENVIRONMENT == "production") {
            $apns_url = 'gateway.push.apple.com';
            $apns_cert = 'APNS_Dis.pem';
        } else {
            $apns_url = 'gateway.sandbox.push.apple.com';
            $apns_cert = 'APNS_Dev.pem';
        }

//        $apns_url = 'gateway.push.apple.com';
//        $apns_cert = 'APNS_Dis.pem';
        $stream_context = stream_context_create();
//        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($stream_context, 'ssl', 'local_cert', dirname(__FILE__) . '/' . $apns_cert);
        stream_context_set_option($stream_context, 'ssl', 'passphrase', "password");

//        $apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $error_string, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $stream_context);

        $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $stream_context);

        if ($error) {
            //print("\nAPN: Maybe some errors: $error: $error_string");
        }

        if (!$apns) {

            if ($error) {
                //print("\nAPN Failed" . 'ssl://' . $apns_url . ':' . $apns_port . " to connect: $error $error_string");
            } else {
                //print("\nAPN Failed to connect: Something wrong with context");
            }
        } else {
            //print("\nAPN: Opening connection to: {ssl://" . $apns_url . ":" . $apns_port . "}");

            if (!empty($tokens)) {
                foreach ($tokens as $device_token) {
                    //$apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_token)) . chr(0) . chr(strlen($payload)) . $payload;
                    $apns_message = chr(1)           // command
                            . pack("N", time())       // identifier
                            . pack("N", time() + 30000) // expiry
                            . pack('n', 32)        // token length
                            . pack('H*', str_replace(' ', '', $device_token))   // device token
                            . pack('n', strlen($payload))  // payload length
                            . $payload;
                    $result = fwrite($apns, $apns_message, strlen($apns_message));

                    if ($result) {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Push Send Successfully";
                        return $data;
                        //print("\nAPN: Push OK - " . $payload);
                    } else {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Push Not Send Successfully";
                        return $data;
                        //print("\nAPN: Push Failed - " . $payload);
                    }
                }
            }
            @fclose($apns);
        }
    }

    /**
     * @param $deviceToken - will be array of tokens or single register id
     * @param $payload - data to be sent as an extra info
     * @param $isNotificationType - if notification type then true , if data type then false
     * @return mixed
     */
    public function sendPushToAndroid($deviceToken, $payload, $isNotificationType) {
        $fields = array();
        //echo "hi";die;
        if (is_array($deviceToken)) {
            $fields['registration_ids'] = $deviceToken;
        } else {
            $fields['to'] = $deviceToken;
        }

        $fields['priority'] = "high";
        // echo $payload;
        $fields[$isNotificationType ? 'notification' : 'data'] = $payload;


        $headers = array(
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, FIREBASE_FCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public function send_notification($registatoin_ids, $message, $key, $content) {
        // include config
        //include_once './config.php';
        //$GOOGLE_API_KEY = "AIzaSyABiZeJp_4W4P8mLr9YIEHsPbObdXFe6nw";
        //$GOOGLE_API_KEY = "AIzaSyA0wqkE5CHK-peZbqi2lzdAAvyo3Kb8qQw";

        $GOOGLE_API_KEY = "AIzaSyBrj8QuOdJ6i6uNmMkze2z1qwmhEFq027I";

        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $data['message'] = $message;
        $data['content'] = $content;
        $data['key'] = $key;
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $data,
        );

        $headers = array(
            'Authorization: key=' . $GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        // echo $result;
    }

    function send($to, $message) {
        $fields = array(
            'to' => $to,
            'data' => $message,
        );
        return sendPushNotification($fields);
    }

// Sending message to a topic by topic name
    function sendToTopic($to, $message) {
        $fields = array(
            'to' => '/topics/' . $to,
            'data' => $message,
        );
        return sendPushNotification($fields);
    }

// sending push message to multiple users by firebase registration ids
    function sendMultiple($registration_ids, $message) {
        $fields = array(
            'to' => $registration_ids,
            'data' => $message,
        );

        return sendPushNotification($fields);
    }

// function makes curl request to firebase servers
    function sendPushNotification($fields) {

        include_once 'config.php';


        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            echo curl_error($ch);
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        return $result;
    }

}

?>