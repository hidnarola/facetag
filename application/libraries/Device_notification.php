<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Event for DX_Auth
// You can use DX_Auth_Event to extend DX_Auth to fullfil your needs
// For example: you can use event below to PM user when he already activated the account, etc.

class Device_notification {

    public function sendMessageToAndroidPhone($API_KEY, $registrationIds, $messageText) {

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = 'Authorization: key=' . $API_KEY;

        $data = array(
            'registration_ids' => $registrationIds,
            'data' => array('message' => $messageText)
        );

        $data_string = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");

        if ($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    function sendMessageToIPhone($deviceToken, $msg = '', $url = '') {
        echo "<pre>";
        print_r($deviceToken);
        exit;
        // Put your device token here (without spaces):
        //print_r($deviceToken);
        $output = '';

        //$deviceToken = '3a80552dcc8a4198cefdf3576abad52c0413bf83e1eba5a21ff739d35e88ba92';
        //$deviceToken = '8b6534b09add34fe6f35c2b59da5161d7fc56ab8f5e30016d4ee9470aeadc385';
        // Put your private key's passphrase here:
        //$passphrase = '123';
        $passphrase = 'password';

        // Put your alert message here:
        //$message = 'This is a test Push Notification (manual) to you  from TopOfStack';
        //$message = 'Station 101 Kurabay Mosque';
        //$message = 'Station 102 Gold Coast Mosque';
        //$message = 'Station 103 Holland Park Mosque';
        $message = $msg;
        //echo $msg;
        ////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__) . '/ck.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__).'/ckN.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert',dirname(__FILE__).'/prock.pem');  // Production old
        //stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__) . '/ck_dev_7.pem');  // Development new 7oct

        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);



        // Open a connection to the APNS server
        //$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        //stream_set_blocking($fp, FALSE ); //THIS IS IMPORTANT 
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );
        $body['url'] = $url;
        // Encode the payload as JSON
        $payload = json_encode($body);

        //foreach($token as $deviceToken){
        //echo $deviceToken."<br>";
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        //$file = fopen("output.txt","w");
        //fwrite($file,$msg);

        if (!$result)
            $output.=$deviceToken . ",";
        else
            $output.=$result . '<br>';
        //}
        // Close the connection to the server
        fclose($fp);
        return $output;
    }

    function sendMessageToIPhones($deviceTokens = array(), $msg = '', $url = '') {
//          echo "<pre>";
//        print_r($deviceTokens);exit;
        // Put your device token here (without spaces):
        $output = '';

        // Put your private key's passphrase here:
        $passphrase = 'password';
        // Put your alert message here:
        //$message = 'This is a test Push Notification (manual) to you  from TopOfStack';
        //$message = 'Station 101 Kurabay Mosque';
        //$message = 'Station 102 Gold Coast Mosque';
        //$message = 'Station 103 Holland Park Mosque';
        $message = $msg;
        ////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        //stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__).'/ck.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__).'/ckN.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__).'/prock.pem'); 
        if (ENVIRONMENT == "production") {
            stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__) . '/APNS_Dis.pem');
        } else {
            stream_context_set_option($ctx, 'ssl', 'local_cert', dirname(__FILE__) . '/APNS_Dev.pem');
        }
        stream_context_set_option($ctx, 'ssl', 'passphrase', 'password');


        // Open a connection to the APNS server

        if (ENVIRONMENT == "production") {
            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 560, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        } else {
            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        }

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);


        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );
        $body['url'] = $url;
        // Encode the payload as JSON
        //$payload = json_encode($body);
        //$payload =json_encode($body, JSON_UNESCAPED_UNICODE);
        $payload = $this->my_json_encode($body);
        foreach ($deviceTokens as $dt) {

            $deviceToken = $dt;
            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            //$file = fopen("output.txt","w");
            //fwrite($file,$msg);

            if (!$result) {
                $output.= $deviceToken . ",";
            } else {
                $output.= $result . '<br>';
            }
        }
        // Close the connection to the server
        fclose($fp);
        return $output;
    }

    public function my_json_encode($arr) {
        //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item))
                $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
        });
        return mb_decode_numericentity(json_encode($arr), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
    }

}

?>