<?php

/**
 * Test Controller
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->headers = array(
            "X-PAYPAL-SECURITY-USERID: nm.narola-facilitator_api1.narolainfotech.com",
            "X-PAYPAL-SECURITY-PASSWORD: PURBT7QJ8269REDX",
            "X-PAYPAL-SECURITY-SIGNATURE: An5ns1Kso7MWUdW4ErQKJJJ4qi4-ALAY2A-6E0F2AV4GES-mVAHKPval",
            "X-PAYPAL-REQUEST-DATA-FORMAT: JSON",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
            "X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T",
            "Content-Type: application/json"
        );
        $this->load->library('push_notification');
        $this->load->library('facerecognition');
        $this->load->model('users_model');
    }

    /**
     * index function
     * */
    public function index($device_token) {
//        $device_token = 'b47e56ec8db06310ee7616f6d8ec73045d9b8d076f5c2d0109f25f255c5b1da0';
        $messageText = 'Hello there! We have found one of your Image. Verify it is yours or not';
//        $pushData = array("notification_type" => "data", "body" => $messageText);

        /*
          $pushData = array(
          "notification_type" => "data",
          "body" => $messageText,
          "selfietagid" => 33,
          "businessid" => 6,
          "businessname" => "CB Photography Pty Ltd test",
          "businessaddress" => 'This field should be optional',
          "icpid" => 15,
          "icpname" => 'Schoolies 2016',
          "icpaddress" => 'Gold Coast Schoolies 2016',
          "imgid" => 407,
          "image" => 'business_6/icp_15/584e9681d7f571481545345.jpeg'
          ); */

        $pushData = array(
            "selfietagid" => 33,
            "businessid" => 6,
            "businessname" => "CB Photography Pty Ltd test",
            "businessaddress" => 'This field should be optional',
            "icpid" => 15,
            "icpname" => 'Schoolies 2016',
            "icpaddress" => 'Gold Coast Schoolies 2016',
            "imgid" => 407,
            "image" => 'business_6/icp_15/584e9681d7f571481545345.jpeg'
        );
//        $data = array("selfietagid" => 33,
//            "businessid" => 6,
//            "businessname" => "CB Photography Pty Ltd test",
//            "businessaddress" => 'This field should be optional',
//            "icpid" => 15,
//            "icpname" => 'Schoolies 2016',
//            "icpaddress" => 'Gold Coast Schoolies 2016',
//            "imgid" => 407,
//            "image" => 'business_6/icp_15/584e9681d7f571481545345.jpeg');
//        $pushData['data'] = json_encode($data);
//        $pushData['data'] = $pushData;
//                                    $pushData = array("notification_type" => "data", "body" => $messageText);
//        $image['device_id'] = 'f0qe2ctOVRU:APA91bFlYFUZydHB4m5a0Pk6L6nqf_0-M5fUndmM8QJWzNb6rWQHKVaUsKGG71QIT33PtAsyScCjll6mOC-gkDJ0qqQs1gcOYQUn0BoyxHBetnL6_iNIZEmSSkebgGzDba_PHiGDhq1j';
//        $response = $this->push_notification->sendPushToAndroid(array($image['device_id']), $pushData, FALSE);

        $response = $this->push_notification->sendPushiOS(array('deviceToken' => $device_token, 'pushMessage' => $messageText), $pushData);
        p($response);
    }

    public function getPaymentOptions($paykey) {
        
    }

    public function setPaymentOptions() {

// Set request-specific fields.
        $vEmailSubject = 'PayPal payment';
        $emailSubject = urlencode($vEmailSubject);
        $receiverType = urlencode('EmailAddress');
        $currency = urlencode('USD'); // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
// Receivers
// Use '0' for a single receiver. In order to add new ones: (0, 1, 2, 3...)
// Here you can modify to obtain array data from database.
        $receivers = array(
            array(
                'receiverEmail' => "pav@narola.email",
                'amount' => "1.00",
//                'uniqueID' => "id_001", // 13 chars max
                'note' => " payment of commissions")
        );
        $receiversLenght = count($receivers);

// Add request-specific fields to the request string.
        $nvpStr = "&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency";

        $receiversArray = array();

        for ($i = 0; $i < $receiversLenght; $i++) {
            $receiversArray[$i] = $receivers[$i];
        }

        foreach ($receiversArray as $i => $receiverData) {
            $receiverEmail = urlencode($receiverData['receiverEmail']);
            $amount = urlencode($receiverData['amount']);
//            $uniqueID = urlencode($receiverData['uniqueID']);
            $note = urlencode($receiverData['note']);
            $nvpStr .= "&L_EMAIL$i=$receiverEmail&L_Amt$i=$amount&L_NOTE$i=$note";
        }

// Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = $this->PPHttpPost('MassPay', $nvpStr);

        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            exit('MassPay Completed Successfully: ' . print_r($httpParsedResponseAr, true));
        } else {
            exit('MassPay failed: ' . print_r($httpParsedResponseAr, true));
        }
    }

    function PPHttpPost($methodName_, $nvpStr_) {

        // Set up your API credentials, PayPal end point, and API version.
        // How to obtain API credentials:
        // https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_NVPAPIBasics#id084E30I30RO
        $API_UserName = urlencode('nm.narola-facilitator_api1.narolainfotech.com');
        $API_Password = urlencode('PURBT7QJ8269REDX');
        $API_Signature = urlencode('An5ns1Kso7MWUdW4ErQKJJJ4qi4-ALAY2A-6E0F2AV4GES-mVAHKPval');
        $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
        $version = urlencode('51.0');

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }

    public function paypalSend($data, $call) {
        $apiUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
        $paypalUrl = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=";
        $headers;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . $call);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HEADER, $this->headers);
        $response = json_decode(curl_exec($ch), true);
        p(curl_error($ch));
        exit;
        return $response;
    }

    public function splitPay() {

        // create the pay request
        $createPacket = array(
            "actionType" => "PAY",
            "currencyCode" => "USD",
            "receiverList" => array(
                "receiver" => array(
                    array(
                        "amount" => "1.00",
                        "email" => "kek.narola@narolainfotech.com"
                    ),
                    array(
                        "amount" => "2.00",
                        "email" => "kek.narola@narolainfotech.com"
                    ),
                ),
            ),
            "returnUrl" => "http://test.local/payments/confirm",
            "cancelUrl" => "http://test.local/payments/cancel",
            "requestEnvelope" => array(
                "errorLanguage" => "en_US",
                "detailLevel" => "ReturnAll",
            ),
        );

        $response = $this->paypalSend($createPacket, "Pay");
        p($response);
        exit;
    }

    public function test_android($device_token) {
//        $device_token = 'b47e56ec8db06310ee7616f6d8ec73045d9b8d076f5c2d0109f25f255c5b1da0';
        $messageText = 'Hello there! We have found one of your Image. Verify it is yours or not';
//        $pushData = array("notification_type" => "data", "body" => $messageText);


        $pushData = array(
            "notification_type" => "data",
            "body" => $messageText,
            "selfietagid" => 33,
            "businessid" => 6,
            "businessname" => "CB Photography Pty Ltd test",
            "businessaddress" => 'This field should be optional',
            "icpid" => 15,
            "icpname" => 'Schoolies 2016',
            "icpaddress" => 'Gold Coast Schoolies 2016',
            "imgid" => 407,
            "image" => 'business_6/icp_15/584e9681d7f571481545345.jpeg'
        );

//        $data = array("selfietagid" => 33,
//            "businessid" => 6,
//            "businessname" => "CB Photography Pty Ltd test",
//            "businessaddress" => 'This field should be optional',
//            "icpid" => 15,
//            "icpname" => 'Schoolies 2016',
//            "icpaddress" => 'Gold Coast Schoolies 2016',
//            "imgid" => 407,
//            "image" => 'business_6/icp_15/584e9681d7f571481545345.jpeg');
//        $pushData['data'] = json_encode($data);
//        $pushData['data'] = $pushData;
//                                    $pushData = array("notification_type" => "data", "body" => $messageText);
//        $image['device_id'] = 'f0qe2ctOVRU:APA91bFlYFUZydHB4m5a0Pk6L6nqf_0-M5fUndmM8QJWzNb6rWQHKVaUsKGG71QIT33PtAsyScCjll6mOC-gkDJ0qqQs1gcOYQUn0BoyxHBetnL6_iNIZEmSSkebgGzDba_PHiGDhq1j';
        $response = $this->push_notification->sendPushToAndroid(array($device_token), $pushData, FALSE);

//        $response = $this->push_notification->sendPushiOS(array('deviceToken' => $device_token, 'pushMessage' => $messageText), $pushData);
        p($response);
    }

    public function store_selfie() {
        //-- post user selfie gallery to FR
        $gallary_name = 'userselfies';
        $this->facerecognition->post_gallery($gallary_name);

        $users = $this->users_model->get_active_users();
        //-- Post userselfi into FR's userselfi gallery;
        foreach ($users as $user) {
            $photo = base_url() . USER_IMAGE_SITE_PATH . $user['user_image'];
            $gallery_name = "userselfies";
            $user_id = $user['id'];
            $data = array(
                'photo' => $photo,
                'meta' => 'user_' . $user_id,
                'galleries' => array($gallery_name)
            );
            $this->facerecognition->delete_face('meta', 'user_' . $user_id); //-- delete already added image
            $facerecog_data = $this->facerecognition->post_face('application/json', $data);
            p($facerecog_data);
        }
    }

    public function transparency() {
//        $trans = $this->is_alpha_png('http://clientapp.narola.online/HD/facetag/uploads/icp_preview_images/gift-575653_640.png');
        /*
          $trans = $this->is_alpha_png('http://clientapp.narola.online/HD/facetag/assets/images/logo-admin-1.png');
          echo "trans = $trans <br>";
          exit; */

//        $im = imagecreatefrompng('http://clientapp.narola.online/HD/facetag/uploads/icp_preview_images/test.png');
        $im = imagecreatefrompng('http://clientapp.narola.online/HD/facetag/uploads/icp_preview_images/Preview-1124201605051616.jpg');
//        $im = imagecreatefrompng('http://clientapp.narola.online/HD/facetag/assets/images/logo-admin-1.png');
//        $im = imagecreatefrompng('http://clientapp.narola.online/HD/facetag/uploads/icp_preview_images/buzz-logo1.png');
        if ($this->check_transparent($im)) {
            echo 'Transparent-DA';
        } else {
            echo 'Nottransparent-NU';
        }
    }

    public function check_transparent($im) {

        $width = imagesx($im); // Get the width of the image
        $height = imagesy($im); // Get the height of the image
        // We run the image pixel by pixel and as soon as we find a transparent pixel we stop and return true.
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                $rgba = imagecolorat($im, $i, $j);
                if (($rgba & 0x7F000000) >> 24) {
                    return true;
                }
            }
        }

        // If we dont find any pixel the function will return false.
        return false;
    }

    public function view_image() {
        $this->load->view('test');
    }

    function is_alpha_png($fn) {
        return (ord(@file_get_contents($fn, NULL, NULL, 25, 1)) == 6);
    }

    function test2() {
//        $url = FCPATH . USER_IMAGE_SITE_PATH . "profile_2016-12-07_10_30_24.png";
        $url = "http://clientapp.narola.online/HD/facetag/mobile/images/selfipic/profile_2016-12-07_10_30_24.png";
        $source_x = 250;
        $x2 = 593;
        $source_y = 403;
        $y2 = 745;
        $width = $x2 - $source_x;
        $height = $y2 - $source_y;
        crop_image($source_x, $source_y, $width, $height, $url);
        /*
          //        $url = FCPATH . USER_IMAGE_SITE_PATH . "profile_2016-12-07_10_30_24.png";
          $url = FCPATH . ICP_IMAGES . "business_6/icp_16/586c8fe90ff501483509737.jpeg";
          $size = getimagesize($url);
          p($size);
          exit;
          $source_x = 98;
          $x2 = 403;
          $source_y = 440;
          $y2 = 745;
          $width = $x2 - $source_x;
          $height = $y2 - $source_y;
          crop_image($source_x, $source_y, $width, $height, $url); */
    }

    public function get_images() {
        $condition = 'icp_id=22 AND is_face_detected=1';
        $images = $this->icp_images_model->get_icpimages_bycond($condition);
        foreach ($images as $image) {
            $this->facerecognition->delete_face('meta', 'icp_img_' . $image['id']);
//                $this->icp_images_model->update_record('id=' . $image['id'], array('is_deleted_from_face_recognition' => 1));
        }
    }

    public function checkedin_users() {
        ini_set('max_execution_time', 0);

        //-- Get users who have checked in to particular business
        $business_checkedin = $this->users_model->checked_in_users('c.icp_id=\' \'');

        //-- Get verified images with group by business id
        $query = 'SELECT i.user_id,ic.business_id FROM ' . TBL_ICP_IMAGE_TAG . ' i LEFT JOIN ' . TBL_ICP_IMAGES . ' im on i.icp_image_id=im.id LEFT JOIN ' . TBL_ICPS . ' ic ON im.icp_id=ic.id group by ic.business_id,i.user_id';
        $matchedimage_bybusiness = $this->common_model->customQuery($query);

        $icp_checkedin = $this->users_model->checked_in_users('c.icp_id!=\'\'');

        //-- Get verified images with group by icp id
        $query = 'SELECT i.user_id,im.icp_id,i.icp_image_id,i.created FROM ' . TBL_ICP_IMAGE_TAG . ' i LEFT JOIN ' . TBL_ICP_IMAGES . ' im on i.icp_image_id=im.id group by im.icp_id,i.user_id';
        $matchedimage_byicp = $this->common_model->customQuery($query);

        $i = $j = 0;

        $business_checkedin_users = array();
        $icp_checkedin_users = array();
        $biz_matched_users = array();
        $icp_matched_users = array();

        foreach ($icp_checkedin as $val) {
            $icp_ids = explode(',', $val['icp_id']);
            //-- Checks icp checked in user's image is already matched or not
            foreach ($icp_ids as $icp_id) {
                $test_array = array(array('user_id' => $val['user_id'], 'icp_id' => $icp_id));
                $intersect = count(array_uintersect($matchedimage_byicp, $test_array, array($this, 'compareDeepValue')));
                if ($intersect == 0) {
                    $icp_checkedin_users[$i] = $val;
                    $icp_checkedin_users[$i]['icp_id'] = $icp_id;
                    $i++;
                } else {
                    $intersect_arr = array_uintersect($matchedimage_byicp, $test_array, array($this, 'compareDeepValue'));

                    $key = key($intersect_arr);
                    $icp_matched_users[$j] = $val;
                    $icp_matched_users[$j]['icp_id'] = $icp_id;
                    $icp_matched_users[$j]['icp_image_id'] = $intersect_arr[$key]['icp_image_id'];
                    $icp_matched_users[$j]['created'] = $intersect_arr[$key]['created'];
                    $j++;
                }
            }
        }
        //-- ICP matched users
        foreach ($icp_matched_users as $matched_user) {
            $condition = 'icp_id=' . $matched_user['icp_id'] . ' AND is_face_detected=1 AND id<' . $matched_user['icp_image_id'] . ' AND is_deleted_from_face_recognition=0 AND is_delete=0';
            $images = $this->icp_images_model->get_icpimages_bycond($condition);
            echo 'array';
            p($images);
        }

        foreach ($business_checkedin as $val) {
            $test_array = array(array('user_id' => $val['user_id'], 'business_id' => $val['business_id']));

            //-- Checks business checked in user's image is already matched or not
            $intersect = count(array_uintersect($matchedimage_bybusiness, $test_array, array($this, 'compareBusinessValue')));
            if ($intersect == 0) {
                $business_checkedin_users[] = $val;
            } else {
                $biz_matched_users[] = $val;
            }
        }
    }

    /**
     * Store ICP blur images
     */
    public function blur_icp_images() {
        ini_set('max_execution_time', 0);
        $icp_images = $this->icp_images_model->get_result();
        foreach ($icp_images as $icp_image) {
            $dirs = explode('/', $icp_image['image']);
            $biz_dir = $dirs[0];
            $icp_dir = $dirs[1];
            if (!file_exists(ICP_BLUR_IMAGES . $biz_dir)) {
                mkdir(ICP_BLUR_IMAGES . $biz_dir);
            }
            if (!file_exists(ICP_BLUR_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir);
            }
            blur_image(FCPATH . ICP_IMAGES . $icp_image['image'], FCPATH . ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir . '/');
        }
    }

    /**
     * Deletes all stored faces from facerecognition database
     */
    /*
      public function deleteall_facerecognition_faces() {
      $icp_images = $this->icp_images_model->get_result('im.is_face_detected=1');
      foreach ($icp_images as $image) {
      $this->facerecognition->delete_face('meta', 'icp_img_' . $image['id']);
      }
      }
     */

    public function test() {
        $date_a = new DateTime('2016-10-20 11:46:16');
        $date_b = new DateTime('2016-12-05 11:26:00');

        $interval = date_diff($date_a, $date_b);
        echo ($interval->y >= 1) ? $interval->y . ' year<br/>' : '<br/>';
        echo ($interval->m >= 1) ? $interval->m . ' month<br/>' : '<br/>';
        echo ($interval->d >= 1) ? $interval->d . ' day<br/>' : '<br/>';
        echo ($interval->h >= 1) ? $interval->h . ' hour<br/>' : '<br/>';
        echo ($interval->i >= 1) ? $interval->i . ' minute<br/>' : '<br/>';

        var_dump($interval);
        exit;
    }

    public function match_image_old() {
        ini_set('max_execution_time', 0);
        //-- Get all checked in users with all the users whose match image is verified from database
        $query = 'SELECT c.*,a.icp_id as match_icp_id,a.business_id as match_business_id FROM ' . TBL_CHECK_IN . ' c '
                . 'LEFT JOIN (SELECT i.*,im.icp_id,ic.business_id FROM ' . TBL_ICP_IMAGE_TAG . ' i LEFT JOIN ' . TBL_ICP_IMAGES . ' im on i.icp_image_id=im.id LEFT JOIN ' . TBL_ICPS . ' ic ON im.icp_id=ic.id group by im.icp_id,i.user_id) a '
                . 'ON c.user_id=a.user_id';
        $checkedin_users = $this->common_model->customQuery($query);
        $user_array = array();
        $user_ids = array();

        p($checkedin_users);
        exit;
        foreach ($checkedin_users as $key => $checkedin_user) {
            if ($checkedin_user['match_icp_id'] == '' || $checkedin_user['match_business_id'] == '') {

                $user_array[$key]['user_id'] = $checkedin_user['user_id'];
                $user_array[$key]['business_id'] = $checkedin_user['business_id'];
                $user_array[$key]['icp_id'] = $checkedin_user['icp_id'];
                $user_ids[$checkedin_user['user_id']] = $checkedin_user['user_id'];
            } else if ($checkedin_user['icp_id'] != '') {
                $temp = explode(',', $checkedin_user['icp_id']);
                if (!in_array($checkedin_user['match_icp_id'], $temp)) {

                    $user_array[$key]['user_id'] = $checkedin_user['user_id'];
                    $user_array[$key]['business_id'] = $checkedin_user['business_id'];
                    $user_array[$key]['icp_id'] = $checkedin_user['icp_id'];
                    $user_ids[$checkedin_user['user_id']] = $checkedin_user['user_id'];
                }
            } else if ($checkedin_user['business_id'] != $checkedin_user['match_business_id']) {

                $user_array[$key]['user_id'] = $checkedin_user['user_id'];
                $user_array[$key]['business_id'] = $checkedin_user['business_id'];
                $user_array[$key]['icp_id'] = $checkedin_user['icp_id'];
                $user_ids[$checkedin_user['user_id']] = $checkedin_user['user_id'];
            }
        }

        //-- Remove duplicates from array [Checked in users whose images are not verified by uploaded icp image]
        $user_array = array_unique($user_array, SORT_REGULAR);

        //-- If checked in users exist
        if ($user_array) {
            $user_images = $this->users_model->get_bio_selfi_images($user_ids);
            $face_recog = $this->icp_images_model->count_facerecog_images();
            $face_recog_by_businessid = $this->icp_images_model->count_facerecogimages_by_businessid();

            $bio_selfi_images = array();
            $face_recog_images = array();
            $face_recog_images_by_businessid = array();
            $device_id = array();
            foreach ($user_images as $user_image) {
                $bio_selfi_images[$user_image['user_id']] = $user_image['image'];
                $device_id[$user_image['user_id']]['device_id'] = $user_image['device_id'];
                $device_id[$user_image['user_id']]['device_type'] = $user_image['device_type'];
            }
            foreach ($face_recog as $val) {
                $face_recog_images[$val['icp_id']] = $val['count'];
            }
            foreach ($face_recog_by_businessid as $val) {
                $face_recog_images_by_businessid[$val['business_id']] = $val['count'];
            }

            //-- Loop through all users and verify match with the uploaded icp image in face recognition data
            foreach ($user_array as $user) {
                if (isset($user_images[$user['user_id']])) {
                    //-- if user hase checked in to multiple icps
                    if ($user['icp_id'] != '') {
                        $icp_ids = explode(',', $user['icp_id']);
                        foreach ($icp_ids as $icp_id) {
                            if (isset($face_recog_images[$icp_id])) {
                                $img_arr = array(
                                    'photo' => base_url() . USER_IMAGE_SITE_PATH . $user_images[$user['user_id']],
                                    'threshold' => 0.7,
                                    'mf_selector' => 'all',
                                    'n' => $face_recog_images[$icp_id],
                                );
                                $match_images = $this->facerecognition->identify('application/json', $img_arr, 'icp_' . $icp_id);

                                if (isset($match_images['results'])) {

                                    $detected_images = $match_images['results'];
                                    $key = key($detected_images);
                                    $detected_images = $detected_images[$key];
                                    foreach ($detected_images as $detected_image) {
                                        $meta = $detected_image['face']['meta'];
                                        $icp_image_id = explode('_', $meta);
                                        $icp_image_id = $icp_image_id[2];

                                        //-- if image is verified then store it into image_tag table and send push notification to user
                                        $icp_image_tag = array(
                                            'icp_image_id' => $icp_image_id,
                                            'user_id' => $user['user_id'],
                                            'is_user_verified' => 0,
                                            'is_purchased' => 0,
                                            'created' => date('Y-m-d H:i:s'),
                                            'modified' => date('Y-m-d H:i:s'));
                                        $this->icp_imagetag_model->insert($icp_image_tag);

                                        $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                        if ($device_id[$user['user_id']]['device_type'] == 0) {
//                                            $response = $this->device_notification->sendMessageToAndroidPhone(ANDROIDAPIKEY, array($device_id[$user['user_id']]['device_id']), $messageText);
//                                            $pushData = array("notification_type" => "data", "body" => $messageText);

                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);

                                            $pushData = array(
                                                "notification_type" => "data",
                                                "body" => $messageText,
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp_id,
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );
//                                            $response = $this->push_notification->sendPushToAndroid($device_id[$user['user_id']]['device_id'], $pushData, TRUE);
                                            $response = $this->push_notification->sendPushToAndroid(array($device_id[$user['user_id']]['device_id']), $pushData, FALSE);
                                        } else {
                                            $url = '';
//                                            $response = $this->device_notification->sendMessageToIPhones(array($device_id[$user['user_id']]['device_id']), $messageText, $url);
                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);
                                            $pushData = array(
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp_id,
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );
                                            $response = $this->push_notification->sendPushiOS(array('deviceToken' => $device_id[$user['user_id']]['device_id'], 'pushMessage' => $messageText), $pushData);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($face_recog_by_businessid[$user['business_id']])) {
                            $img_arr = array(
                                'photo' => base_url() . USER_IMAGE_SITE_PATH . $user_images[$user['user_id']],
                                'threshold' => 0.7,
                                'mf_selector' => 'all',
                                'n' => $face_recog_by_businessid[$user['business_id']],
                            );
                            $match_images = $this->facerecognition->identify('application/json', $img_arr, 'business_' . $user['business_id']);

                            if (isset($match_images['results'])) {

                                $detected_images = $match_images['results'];
                                $key = key($detected_images);
                                $detected_images = $detected_images[$key];
                                foreach ($detected_images as $detected_image) {
                                    $meta = $detected_image['face']['meta'];
                                    $icp_image_id = explode('_', $meta);
                                    $icp_image_id = $icp_image_id[2];

                                    //-- if image is verified then store it into image_tag table and send push notification to user
                                    $icp_image_tag = array(
                                        'icp_image_id' => $icp_image_id,
                                        'user_id' => $user['user_id'],
                                        'is_user_verified' => 0,
                                        'is_purchased' => 0,
                                        'created' => date('Y-m-d H:i:s'),
                                        'modified' => date('Y-m-d H:i:s'));
                                    $this->icp_imagetag_model->insert($icp_image_tag);

                                    $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                    if ($device_id[$user['user_id']]['device_type'] == 0) {
//                                        $response = $this->device_notification->sendMessageToAndroidPhone(ANDROIDAPIKEY, array($device_id[$user['user_id']]['device_id']), $messageText);
//                                        $pushData = array("notification_type" => "data", "body" => $messageText);
                                        $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                        $icp_img_data = $this->icp_images_model->get_result($where);
                                        $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                        $icp_data = $this->icps_model->get_result($where);

                                        $pushData = array(
                                            "notification_type" => "data",
                                            "body" => $messageText,
                                            "selfietagid" => $icp_image_tag_id,
                                            "businessid" => $icp_img_data[0]['business_id'],
                                            "businessname" => $icp_data[0]['businessname'],
                                            "businessaddress" => $icp_data[0]['businessaddress'],
                                            "icpid" => $icp_id,
                                            "icpname" => $icp_data[0]['name'],
                                            "icpaddress" => $icp_data[0]['address'],
                                            "imgid" => $icp_image_id,
                                            "image" => $icp_img_data[0]['image']
                                        );

//                                        $response = $this->push_notification->sendPushToAndroid($device_id[$user['user_id']]['device_id'], $pushData, TRUE);
                                        $response = $this->push_notification->sendPushToAndroid(array($device_id[$user['user_id']]['device_id']), $pushData, FALSE);
                                    } else {
                                        $url = '';
//                                        $response = $this->device_notification->sendMessageToIPhones(array($device_id[$user['user_id']]['device_id']), $messageText, $url);
                                        $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                        $icp_img_data = $this->icp_images_model->get_result($where);
                                        $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                        $icp_data = $this->icps_model->get_result($where);
                                        $pushData = array(
                                            "selfietagid" => $icp_image_tag_id,
                                            "businessid" => $icp_img_data[0]['business_id'],
                                            "businessname" => $icp_data[0]['businessname'],
                                            "businessaddress" => $icp_data[0]['businessaddress'],
                                            "icpid" => $icp_id,
                                            "icpname" => $icp_data[0]['name'],
                                            "icpaddress" => $icp_data[0]['address'],
                                            "imgid" => $icp_image_id,
                                            "image" => $icp_img_data[0]['image']
                                        );
                                        $response = $this->push_notification->sendPushiOS(array('deviceToken' => $device_id[$user['user_id']]['device_id'], 'pushMessage' => $messageText), $pushData);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Store icp images in face recognition database (Used only for testing)
     */
    public function store_icp_images() {
        ini_set('max_execution_time', 0);

        $icp_images = $this->icp_images_model->get_result('im.is_face_detected=0 AND im.face_recognition_ids IS NULL');
//        $icp_images = $this->icp_images_model->get_result();
        $detect_image_id = array();
        foreach ($icp_images as $image) {
            //-- Get galleries stored in face recogntion database
            $galleries = $this->facerecognition->get_galleries();
            $galleries = $galleries['results'];
            $gallary_name = 'business_' . $image['business_id'];
            $icp_gallary_name = 'icp_' . $image['icp_id'];
            //-- If gallery is not created for business in Face recognition database then create 
            if (!in_array($gallary_name, $galleries)) {
                $this->facerecognition->post_gallery($gallary_name);
            }
            //-- If gallery is not created for icp in Face recognition database then create 
            if (!in_array($icp_gallary_name, $galleries)) {
                $this->facerecognition->post_gallery($icp_gallary_name);
            }
            //-- Detects faces on uploaded ICP image and and store it in face recognition database
            $photo = base_url(ICP_IMAGES) . $image['image'];
            $photo_array = array(
                'photo' => $photo,
                'meta' => 'icp_img_' . $image['id'],
                'mf_selector' => 'all', //-- Detect all faces and post them into facerecognition IDS,
                'galleries' => array($gallary_name, $icp_gallary_name) //-- Store in  particular business gallery
            );

            $facerecog_data = $this->facerecognition->post_face('application/json', $photo_array);
            var_dump($facerecog_data);
//            $facerecog_data = (array) $facerecog_data;
//            echo '<script>console.log("' . implode(',', $facerecog_data) . '")</script>';
            if (isset($facerecog_data['code'])) {
                $update_data = array('is_face_detected' => 0);
            } else if (isset($facerecog_data['results'])) {
                $result = $facerecog_data['results'];
                $face_recog_ids = array();
                foreach ($result as $val) {
                    $face_recog_ids[] = $val['id'];
                }
                $update_data = array(
                    'is_face_detected' => 1,
                    'face_recognition_ids' => implode(',', $face_recog_ids));
                $this->icp_images_model->update_record('id=' . $image['id'], $update_data);
                $detect_image_id[] = $image['id'];
            }
        }
        echo "Count of icp images " . count($detect_image_id);
        p($detect_image_id);
    }

    /**
     * Get all faces stored in face recognition database
     * @param string $next Next Page Url
     */
    public function get_all_faces() {
        $max_id = $this->input->get('max_id');
        $min_id = $this->input->get('min_id');

        $next = '';
        if ($max_id != '')
            $next = '?max_id=' . $max_id;
        if ($min_id != '')
            $next = '?min_id=' . $min_id;

        $all_faces = $this->facerecognition->faces($next);
        echo "Number of images stored in face recognition database " . count($all_faces->results);
        echo '<pre>';
        print_r($all_faces);
        echo '</pre>';
    }

    public function remove() {
        ini_set('max_execution_time', 0);
        $images = $this->db->query('SELECT img.* FROM `icp_images` img LEFT JOIN icps i ON img.icp_id=i.id LEFT JOIN businesses b ON i.business_id=b.id WHERE img.is_deleted_from_face_recognition=1 AND img.is_delete=0 AND i.is_delete=0 AND b.is_delete=0');
        $images = $images->result_array();
        foreach ($images as $image) {
            $this->facerecognition->delete_face('meta', 'icp_img_' . $image['id']);
        }
    }

    public function test1() {
        $images = $this->db->query('SELECT img.* FROM `icp_images` img LEFT JOIN icps i ON img.icp_id=i.id LEFT JOIN businesses b ON i.business_id=b.id WHERE img.is_deleted_from_face_recognition=1 AND img.is_delete=0 AND i.is_delete=0 AND b.is_delete=0');
        $images = $images->result_array();
        foreach ($images as $image) {
            $icp_gallary_name = 'icp_' . $image['icp_id'];

            //-- Detects faces on uploaded ICP image and and store it in face recognition database
            $photo = base_url(ICP_IMAGES) . $image['image'];

            $photo_array = array(
                'photo' => $photo,
                'meta' => 'icp_img_' . $image['id'],
                'mf_selector' => 'all', //-- Detect all faces and post them into facerecognition IDS
                'galleries' => array($icp_gallary_name)
            );

            $facerecog_data = $this->facerecognition->post_face('application/json', $photo_array);

            //-- check if face detected or not in uploaded icp image
            if (isset($facerecog_data['code'])) {
                $update_data = array('is_face_detected' => 0);
            } else if (isset($facerecog_data['results'])) {

                //-- if face detected 
                $result = $facerecog_data['results'];
                $face_recog_ids = array();
                foreach ($result as $val) {
                    $face_recog_ids[] = $val['id'];
                }
                $update_data = array(
                    'is_face_detected' => 1,
                    'face_recognition_ids' => implode(',', $face_recog_ids),
                    'is_deleted_from_face_recognition' => 0
                );

                $this->icp_images_model->update_record('id=' . $image['id'], $update_data);
            }
        }
    }

    /**
     * Match image function - match checked in user images with stored icp images of face recognition database
     */
    public function match_image() {
        ini_set('max_execution_time', 0);

        //-- Get users who have checked in to particular business
        $business_checkedin = $this->users_model->checked_in_users('c.icp_id=\' \'');
        $icp_checkedin = $this->users_model->checked_in_users('c.icp_id!=\'\'');

        //-- Get verified images with group by business id
        $query = 'SELECT i.user_id,ic.business_id FROM ' . TBL_ICP_IMAGE_TAG . ' i LEFT JOIN ' . TBL_ICP_IMAGES . ' im on i.icp_image_id=im.id LEFT JOIN ' . TBL_ICPS . ' ic ON im.icp_id=ic.id group by ic.business_id,i.user_id';
        $matchedimage_bybusiness = $this->common_model->customQuery($query);

        //-- Get verified images with group by icp id
        $query = 'SELECT i.user_id,im.icp_id FROM ' . TBL_ICP_IMAGE_TAG . ' i LEFT JOIN ' . TBL_ICP_IMAGES . ' im on i.icp_image_id=im.id group by im.icp_id,i.user_id';
        $matchedimage_byicp = $this->common_model->customQuery($query);

        $business_checkedin_users = array();
        $icp_checkedin_users = array();

        foreach ($business_checkedin as $val) {
            $test_array = array(array('user_id' => $val['user_id'], 'business_id' => $val['business_id']));
            //-- Checks business checked in user's image is already matched or not
            $intersect = count(array_uintersect($matchedimage_bybusiness, $test_array, array($this, 'compareBusinessValue')));
            if ($intersect == 0) {
                $business_checkedin_users[] = $val;
            }
        }

        $i = 0;
        foreach ($icp_checkedin as $val) {
            $icp_ids = explode(',', $val['icp_id']);
            //-- Checks icp checked in user's image is already matched or not
            foreach ($icp_ids as $icp_id) {
                $test_array = array(array('user_id' => $val['user_id'], 'icp_id' => $icp_id));
                $intersect = count(array_uintersect($matchedimage_byicp, $test_array, array($this, 'compareDeepValue')));
                if ($intersect == 0) {
                    $icp_checkedin_users[$i] = $val;
                    $icp_checkedin_users[$i]['icp_id'] = $icp_id;
                    $i++;
                }
            }
        }

        $facerecog = $this->icp_images_model->count_facerecog_images();
        $facerecog_by_businessid = $this->icp_images_model->count_facerecogimages_by_businessid();
        $facerecog_images = array();
        $facerecogimages_by_businessid = array();

        foreach ($facerecog as $val) {
            $facerecog_images[$val['icp_id']] = $val['count'];
        }
        foreach ($facerecog_by_businessid as $val) {
            $facerecogimages_by_businessid[$val['business_id']] = $val['count'];
        }

        p($icp_checkedin_users);
        p($business_checkedin_users, 1);

        foreach ($business_checkedin_users as $user) {
            if ($user['user_image'] != '' && isset($facerecogimages_by_businessid[$user['business_id']])) {
                $img_arr = array(
                    'photo' => base_url() . USER_IMAGE_SITE_PATH . $user['user_image'],
                    'threshold' => 0.7,
                    'mf_selector' => 'all',
                    'n' => $facerecogimages_by_businessid[$user['business_id']],
                );
                $match_images = $this->facerecognition->identify('application/json', $img_arr, 'business_' . $user['business_id']);

                if (isset($match_images['results'])) {
                    $detected_images = $match_images['results'];

                    $key_arrays = array_keys($detected_images);
                    foreach ($key_arrays as $key_arr) {
                        $detected_imgs = $detected_images[$key_arr];

//                        $key = key($detected_images);
//                        $detected_images = $detected_images[$key];
                        foreach ($detected_imgs as $detected_image) {
                            $meta = $detected_image['face']['meta'];
                            $icp_image_id = explode('_', $meta);
                            $icp_image_id = $icp_image_id[2];

                            //-- if image is verified then store it into image_tag table and send push notification to user
                            $icp_image_tag = array(
                                'icp_image_id' => $icp_image_id,
                                'user_id' => $user['user_id'],
                                'is_user_verified' => 0,
                                'is_purchased' => 0,
                                'created' => date('Y-m-d H:i:s'),
                                'modified' => date('Y-m-d H:i:s'));
                            $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                            $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                            if ($user['device_type'] == 0) {
//                            $response = $this->device_notification->sendMessageToAndroidPhone(ANDROIDAPIKEY, array($user['device_id']), $messageText);

                                $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                $icp_img_data = $this->icp_images_model->get_result($where);
                                $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                $icp_data = $this->icps_model->get_result($where);
                                $pushData = array(
                                    "notification_type" => "data",
                                    "body" => $messageText,
                                    "selfietagid" => $icp_image_tag_id,
                                    "businessid" => $icp_img_data[0]['business_id'],
                                    "businessname" => $icp_data[0]['businessname'],
                                    "businessaddress" => $icp_data[0]['businessaddress'],
                                    "icpid" => $icp_id,
                                    "icpname" => $icp_data[0]['name'],
                                    "icpaddress" => $icp_data[0]['address'],
                                    "imgid" => $icp_image_id,
                                    "image" => $icp_img_data[0]['image']
                                );
//                            $pushData = array("notification_type" => "data", "body" => $messageText);
//                            $response = $this->push_notification->sendPushToAndroid($user['device_id'], $pushData, TRUE);
                                $response = $this->push_notification->sendPushToAndroid(array($user['device_id']), $pushData, FALSE);
                            } else {
                                $url = '';
//                            $response = $this->device_notification->sendMessageToIPhones(array($user['device_id']), $messageText, $url);
                                $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                $icp_img_data = $this->icp_images_model->get_result($where);
                                $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                $icp_data = $this->icps_model->get_result($where);
                                $pushData = array(
                                    "selfietagid" => $icp_image_tag_id,
                                    "businessid" => $icp_img_data[0]['business_id'],
                                    "businessname" => $icp_data[0]['businessname'],
                                    "businessaddress" => $icp_data[0]['businessaddress'],
                                    "icpid" => $icp_id,
                                    "icpname" => $icp_data[0]['name'],
                                    "icpaddress" => $icp_data[0]['address'],
                                    "imgid" => $icp_image_id,
                                    "image" => $icp_img_data[0]['image']
                                );
                                $response = $this->push_notification->sendPushiOS(array('deviceToken' => $user['device_id'], 'pushMessage' => $messageText), $pushData);
                            }
                        }
                    }
                }
            }
        }

        foreach ($icp_checkedin_users as $user) {
            if ($user['user_image'] != '' && isset($facerecog_images[$user['icp_id']])) {
                $img_arr = array(
                    'photo' => base_url() . USER_IMAGE_SITE_PATH . $user['user_image'],
                    'threshold' => 0.7,
                    'mf_selector' => 'all',
                    'n' => $facerecog_images[$user['icp_id']],
                );
                $match_images = $this->facerecognition->identify('application/json', $img_arr, 'icp_' . $user['icp_id']);

                if (isset($match_images['results'])) {

                    $detected_images = $match_images['results'];
                    $key_arrays = array_keys($detected_images);

                    foreach ($key_arrays as $key_arr) {
                        $detected_imgs = $detected_images[$key_arr];
//                    $key = key($detected_images);
//                    $detected_images = $detected_images[$key];
                        foreach ($detected_imgs as $detected_image) {
                            $meta = $detected_image['face']['meta'];
                            $icp_image_id = explode('_', $meta);
                            $icp_image_id = $icp_image_id[2];

                            //-- if image is verified then store it into image_tag table and send push notification to user
                            $icp_image_tag = array(
                                'icp_image_id' => $icp_image_id,
                                'user_id' => $user['user_id'],
                                'is_user_verified' => 0,
                                'is_purchased' => 0,
                                'created' => date('Y-m-d H:i:s'),
                                'modified' => date('Y-m-d H:i:s'));
                            $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);


                            $url = $detected_image['face']['photo'];
                            $source_x = $detected_image['face']['x1'];
                            $x2 = $detected_image['face']['x2'];
                            $source_y = $detected_image['face']['y1'];
                            $y2 = $detected_image['face']['y2'];
                            $width = $x2 - $source_x;
                            $height = $y2 - $source_y;
                            crop_image($source_x, $source_y, $width, $height, $url);

                            $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                            if ($user['device_type'] == 0) {
//                            $response = $this->device_notification->sendMessageToAndroidPhone(ANDROIDAPIKEY, array($user['device_id']), $messageText);
                                $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                $icp_img_data = $this->icp_images_model->get_result($where);
                                $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                $icp_data = $this->icps_model->get_result($where);

                                $pushData = array(
                                    "notification_type" => "data",
                                    "body" => $messageText,
                                    "selfietagid" => $icp_image_tag_id,
                                    "businessid" => $icp_img_data[0]['business_id'],
                                    "businessname" => $icp_data[0]['businessname'],
                                    "businessaddress" => $icp_data[0]['businessaddress'],
                                    "icpid" => $icp_id,
                                    "icpname" => $icp_data[0]['name'],
                                    "icpaddress" => $icp_data[0]['address'],
                                    "imgid" => $icp_image_id,
                                    "image" => $icp_img_data[0]['image']
                                );

//                            $pushData = array("notification_type" => "data", "body" => $messageText);
//                            $response = $this->push_notification->sendPushToAndroid($user['device_id'], $pushData, TRUE);
                                $response = $this->push_notification->sendPushToAndroid(array($user['device_id']), $pushData, FALSE);
                            } else {

                                $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                $icp_img_data = $this->icp_images_model->get_result($where);
                                $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                $icp_data = $this->icps_model->get_result($where);

                                $pushData = array(
                                    "selfietagid" => $icp_image_tag_id,
                                    "businessid" => $icp_img_data[0]['business_id'],
                                    "businessname" => $icp_data[0]['businessname'],
                                    "businessaddress" => $icp_data[0]['businessaddress'],
                                    "icpid" => $icp_id,
                                    "icpname" => $icp_data[0]['name'],
                                    "icpaddress" => $icp_data[0]['address'],
                                    "imgid" => $icp_image_id,
                                    "image" => $icp_img_data[0]['image']
                                );

                                $url = '';
//                            $response = $this->device_notification->sendMessageToIPhones(array($user['device_id']), $messageText, $url);
                                $response = $this->push_notification->sendPushiOS(array('deviceToken' => $user['device_id'], 'pushMessage' => $messageText), $pushData);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Call back function to check array elements of two array 
     * @param array $val1 - element of first array
     * @param array $val2 - element of second array
     * @return int
     */
    public function compareDeepValue($val1, $val2) {
        if (($val1['icp_id'] == $val2['icp_id']) && ($val1['user_id'] == $val2['user_id']))
            return 0;
        else
            return -1;
    }

    /**
     * Call back function to check array elements of two array 
     * @param array $val1 - element of first array
     * @param array $val2 - element of second array
     * @return int
     */
    public function compareBusinessValue($val1, $val2) {
        if (($val1['business_id'] == $val2['business_id']) && ($val1['user_id'] == $val2['user_id']))
            return 0;
        else
            return -1;
    }

    public function run_script() {

        $output = shell_exec("uploads/upload.sh");

//        $f = fopen('uploads/create.php', "r");
//        echo fread($f, filesize('uploads/create.php'));
//        fclose($f);
//        include 'uploads/create.php';
//        die;
    }

    /**
     * Upload small promo images to the folder
     */
    public function promo_images() {
        ini_set('max_execution_time', 0);
        $res = $this->db->query('SELECT image,business_id from business_promo_images where is_delete=0 AND id BETWEEN 501 AND 1000');
        $result = $res->result_array();
        foreach ($result as $image) {
            $biz_dir = 'business_' . $image['business_id'];

            if (!file_exists(BUSINESS_SMALL_PROMO_IMAGES . $biz_dir)) {
                mkdir(BUSINESS_SMALL_PROMO_IMAGES . $biz_dir);
            }
            $src = BUSINESS_PROMO_IMAGES . $image['image'];
            if (!file_exists(BUSINESS_SMALL_PROMO_IMAGES . $image['image'])) {
                $thumb_dest = BUSINESS_SMALL_PROMO_IMAGES . $image['image'];
                thumbnail_image($src, $thumb_dest);
                echo 'image is ..' . $image['image'];
            }
        }
    }
}
