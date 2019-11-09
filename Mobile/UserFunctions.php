<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 03/03/15
 * Time: 4:16 PM
 */
require_once 'SecurityFunctions.php';

class UserFunctions
{
    function __construct()
    {

    }

    public function call_service($service, $postData)
    {
        switch ($service) {
            case "Login": {
                return $this->loginUser($postData);
            }
                break;
            case "Register": {
                return $this->registerUser($postData);
            }
                break;
            case "UserExistWithEmail": {
                return $this->userExistWithEmail($postData);
            }
                break;
            case "UserExistWithFacebookID": {
                return $this->userExistWithFacebookID($postData);
            }
                break;
            case "LoginWithFB": {
                return $this->loginWithFacebook($postData);
            }
                break;
            case "EditProfile": {
                return $this->editProfile($postData);
            }
                break;
            case "ChangePassword": {
                return $this->changePassword($postData);
            }
                break;
            case "GetUserSpecificImages": {
                return $this->getUserSpecificImages($postData);
            }
                break;
            case "GetDetectedSelfie": {
                return $this->getDetectedSelfie($postData);
            }
                break;
            case "UpdateSelfieVerification": {
                return $this->updateSelfieVerification($postData);
            }
                break;
            case "GetBusinessVerifiedSelfie": {
                return $this->getBusinessVerifiedSelfie($postData);
            }
                break;
            case "GetCheckinBusiness":
            {
                return $this->getCheckinBusiness($postData);
            }
            break;
            case "CheckOutBusiness":
            {
                return $this->checkOutBusiness($postData);
            }
                break;
            case "GetAllUserVerifiedSelfie":
            {
                return $this->getAllUserVerifiedSelfie($postData);
            }
                break;
            case "GetAllUserVerifiedSelfieClone":
            {
                return $this->getAllUserVerifiedSelfieClone($postData);
            }
                break;

            case "GetAllUserVerifiedSelfieTemp":
            {
                return $this->getAllUserVerifiedSelfieTemp($postData);
            }
                break;
            case "UpdateUserSelfie":
            {
                return $this->updateUserSelfie($postData);
            }
                break;
            case "SearchSelfie":
            {
                return $this->searchSelfie($postData);
            }
                break;
            case "searchSelfieNew":
            {
                return $this->searchSelfieNew($postData);
            }
                break;
            case "LogoutUser":
            {
                return $this->logoutUser($postData);
            }
                break;
            case "ForgotPassword": {
                return $this->forgotPassword($postData);
            }
                break;
            case "GetAllNonExpireSelfie": {
                return $this->getAllNonExpireSelfie($postData);
            }
                break;
            case "sendEmailVerificatoinMail": {
                return $this->sendEmailVerificatoinMail($postData);
            }
                break;
            case "ReSendEmailVerificatoinMail": {
                return $this->reSendEmailVerificatoinMail($postData);
            }
                break;

        }
    }


    public  function reSendEmailVerificatoinMail($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $username = validateObject($postdata, 'username', "");
        $username = addslashes($username);

        $email = validateObject($postdata, 'email', "");
        $email = addslashes($email);

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        include_once 'SendEmailFunction.php';
        $objMail = new SendEmailFunction();


        $verifyPageUrl=VERIFYPAGE.base64_encode($userid);
        $body = "Hi ". $username . "</br> <br/> Please <b><a href='".$verifyPageUrl."'>Click to Verify</a></b> your facetag account </br> <br/>Thanks,<br/>Facetag Team";
        $to = $email;
        $subject = "Account Verification";
        $objMail->sendEmail($body,$to,$subject);

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Please check your mail";
        return $data;

    }



    public  function sendEmailVerificatoinMail($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $username = validateObject($postdata, 'username', "");
        $username = addslashes($username);

        $emailId = validateObject($postdata, 'emailId', "");
        $emailId = addslashes($emailId);

        include_once 'SendEmailFunction.php';
        $objMail = new SendEmailFunction();


        $verifyPageUrl=VERIFYPAGE.base64_encode($userid);
        $body = "Hi ". $username . "</br> <br/> Please <b><a href='".$verifyPageUrl."'>Click to Verify</a></b> your facetag account </br> <br/>Thanks,<br/>Facetag Team";
        $to = $emailId;
        $subject = "Account Verification";
        $objMail->sendEmail($body,$to,$subject);

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Please check your mail";
        return $data;

    }

    public  function sendEmailVerificatoin($userid,$username,$emailId)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        include_once 'SendEmailFunction.php';
        $objMail = new SendEmailFunction();


        $verifyPageUrl=VERIFYPAGE.base64_encode($userid);
        $body = "Hi ". $username . "</br> <br/> Please <b><a href='".$verifyPageUrl."'>Click to Verify</a></b> your facetag account </br> <br/>Thanks,<br/>Facetag Team";
        $to = $emailId;
        $subject = "Account Verification";
        $objMail->sendEmail($body,$to,$subject);

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Please check your mail";
        return $data;

    }

    public  function getAllNonExpireSelfie($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);


        $select_selfie_query = "select selfie.id as selfieid,
                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie
                                left join
                                (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join
                                (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id
                                left join
                                (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where selfie.user_id = ? and selfie.is_currentuser = ? and selfie.is_delete = ?  and selfie.is_purchased = ?
                                and selfie.closingtime is not null and selfie.closingtime > CURDATE()";//" and selfie.closingtime >= CURDATE()";


        //echo $select_selfie_query;exit;
        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $iscurrentuser = "1";
        $isdelete = "0";
        $ispurchase = "0";
        $currentdate = date("Y-m-d H:i:s");
        $select_selfie_stmt->bind_param("isss",$userid,$iscurrentuser,$isdelete,$ispurchase);

        if($select_selfie_stmt->execute())
        {
            $select_selfie_stmt->store_result();
            if ($select_selfie_stmt->num_rows > 0)
            {
                $canpurchage = 0;
                while($selfie = fetch_assoc_all_values($select_selfie_stmt))
                {
                    $posts[] = $selfie;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of non expire photos...";
                $data['photos'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Selfie not found ...";
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Some thing went wrong please try again...";
            return $data;
        }
    }

    public function forgotPassword($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $email = validateObject($userData, 'email', "");
        $email = addslashes($email);

        $select_user_query = "select * from ". TABLE_USER ." where email = ? and is_delete = ?";
        $select_user_stmt = $connection->prepare($select_user_query);
        $isdelete = 0;
        $select_user_stmt->bind_param("si",$email,$isdelete);

        if($select_user_stmt->execute())
        {
            $select_user_stmt->store_result();
            if($select_user_stmt->num_rows > 0)
            {
                include_once 'GlobalFunction.php';
                $objGlobalFunction = new GlobalFunction();
                $randomstr = $objGlobalFunction -> generateRandomString(8);
                $password = encryptPassword($randomstr);

                //update in database

                $update_password_query = "update " . TABLE_USER . " set password = ? where email = ?";
                $update_password_stmt = $connection->prepare($update_password_query);
                $update_password_stmt->bind_param("ss",$password,$email);

                if($update_password_stmt->execute()) {
                    //Send Mail
                    include_once 'SendEmailFunction.php';
                    $objMail = new SendEmailFunction();

                    $user = fetch_assoc_all_values($select_user_stmt);
                    $body = "Hi <b>". $user['username'] . "</b> <br/>you can login with this new password <b>" .$randomstr . "</b> <br/><br/> Thanks, <br/> <b>facetag team</b>";
                    $to = $email;
                    $subject = "Password Recovery";
                    $objMail->sendEmail($body,$to,$subject);

                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please check your mail";
                    return $data;
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again !!!";
                    return $data;
                }


            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User not exist with this email id !!!";
                return $data;
            }

        }

        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Plaese try again !!!";
            return $data;

        }




    }

        public function logoutUser($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $update_user_query = "Update " . TABLE_USER . " set device_id = NULL";
        $update_user_query_stmt = $connection->prepare($update_user_query);

        if ($update_user_query_stmt->execute())
        {
            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Logout Successfully !!!";
            return $data;
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Plaese try again !!!";
            return $data;
        }
    }


    function delete_face($userID) {
        /*
        $user_id = 306;
        $access_token = "Ug2-NOC3O86aadLQzbOBLvYFt2Rymyay";
        $param = 'user_' . $userID;
        $URL = 'https://api.findface.pro/v0/face/meta/' . urlencode($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $access_token, 'Content-Length: 0'));
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;*/
        $response = [];
        $connection = $GLOBALS['con'];
        $select_user = "select imguser.dossier_id from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . " as imguser on imguser.id =  user.profile_image_id where user.id =  ?";

        $select_user_stmt = $connection->prepare($select_user);
        $select_user_stmt->bind_param("i",$userID);
        $select_user_stmt->execute();
        $select_user_stmt->store_result();
        
        if ($select_user_stmt->num_rows > 0) {
            $user = fetch_assoc_all_values($select_user_stmt);
            if(!empty($user['dossier_id'])){
                $response = $this->delete_dossier($user['dossier_id']);
            }
        }
        return $response;

    }

    function post_face($picName , $userid)
    {
        $photo = UPLOAD_SELFIE_PATH . $picName;
        /*$gallery_name = "userselfies";
        $user_id = $userid;
        $data = array(
            'photo' => $photo,
            'meta' => 'user_' . $user_id,
            'galleries' => array($gallery_name)
        );

        $param_type = 'application/json';
        $URL = 'https://api.findface.pro/v0/face';
        $access_token = "Ug2-NOC3O86aadLQzbOBLvYFt2Rymyay";

        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $param_type, 'Authorization: Token ' . $access_token, 'Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;*/
        $connection = $GLOBALS['con'];
        $response = $this->detect($photo);
            
        if (isset($response['faces']) && !empty($response['faces'])) {
            $detection_id = $response['faces'][0]['id'];
            //-- create dossier
            $dresponse = $this->adddossier(25, 'user_' . $userid);
            if (isset($dresponse['id'])) {
                $dossier_id = $dresponse['id'];
                //-- add face into dossier
                $fresponse = $this->adddossierface($dossier_id, $detection_id, $photo);
                if (isset($fresponse['id'])) {
                    $dossierface_id = $fresponse['id'];
                    
                    $update_query = "Update " . TABLE_USER_IMAGES . " set dossier_id = ? , dossierface_id = ? where user_id = ?";
                    $update_user_stmt = $connection->prepare($update_query);
                    $update_user_stmt->bind_param("isi",$dossier_id,$dossierface_id,$userid);
                    $update_user_stmt->execute();
                    $update_user_stmt->close();
                    
                }
            }
        }
        return $response;
    }

    public function searchSelfieNew($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $businessId = validateObject($userData, 'businessid', "");
        $businessId = addslashes($businessId);

        $icpId = validateObject($userData, 'icpid', "");
        $icpId = addslashes($icpId);

        $startdate = validateObject($userData, 'startdate', "");
        $startdate = addslashes($startdate);

        $enddate = validateObject($userData, 'enddate', "");
        $enddate = addslashes($enddate);

        $currentuser = "1";
        $select_selfie_query = "select selfie.id as selfieid,
                                selfie.is_purchased,
                                selfie.is_small_purchase,
                                selfie.is_large_purchase,
                                selfie.is_printed_purchase,
                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                selfie.icp_image_id as icpimgid,
                                selfie.user_id as userid,
                                imgselfie.image as selfieimg,
                                imgselfie.id as icpimgid,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE . " as imgselfie left join (select * from " . TABLE_ICP_IMAGE_TAG . ") as selfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id left join (select * from " . TABLE_ICP_SETTING . ")
                                as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where ";


        $isFirstCriteria = 1;

        if(strlen($businessId) > 0)
        {
            if($isFirstCriteria == 1)
            {
                $select_selfie_query = $select_selfie_query . " business.id = '". $businessId ."'";
                $isFirstCriteria = 0;
            }
            else
            {
                $select_selfie_query = $select_selfie_query . " and business.id = '". $businessId ."'";
            }
        }

        if(strlen($icpId) > 0)
        {
            if($isFirstCriteria == 1)
            {
                $select_selfie_query = $select_selfie_query . "  icps.id in  (" . $icpId. ")";
                $isFirstCriteria = 0;
            }
            else
            {
                $select_selfie_query = $select_selfie_query . " and  icps.id in  (" . $icpId. ")";
            }
        }

        if(strlen($startdate) > 0)
        {
            if($isFirstCriteria == 1)
            {
                $select_selfie_query = $select_selfie_query . "  imgselfie.created > '" . $startdate . "' AND imgselfie.created < '". $enddate ."'";
                $isFirstCriteria = 0;
            }
            else
            {
                $select_selfie_query = $select_selfie_query . " and  imgselfie.created > '" . $startdate . "' AND imgselfie.created < '". $enddate ."'";
            }
        }
        $isdelete = 0;
        $select_selfie_query = $select_selfie_query ." and imgselfie.is_delete = " . $isdelete . "
             order by imgselfie.created desc";

//            and (selfie.closingtime > ? or selfie.closingtime is null or selfie.closingtime = '') order by imgselfie.created desc";
//            echo $select_selfie_query;
//            exit;


        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $currentdate = date("Y-m-d H:i:s");

        if($select_selfie_stmt->execute())
        {
            $select_selfie_stmt->store_result();

            if($select_selfie_stmt->num_rows > 0)
            {
                while ($selfie = fetch_assoc_all_values($select_selfie_stmt))
                {
                    $posts[] = $selfie;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Search selfie !!!";
                $data['searchselfie'] =  $posts;
                return $data;
            }
            else
            {

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No selfie found !!!";
                return $data;
            }
        }
        else
        {
            //echo $select_selfie_query->error;
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;
        }
    }

    public function searchSelfie($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $businessId = validateObject($userData, 'businessid', "");
        $businessId = addslashes($businessId);

        $icpId = validateObject($userData, 'icpid', "");
        $icpId = addslashes($icpId);

        $startdate = validateObject($userData, 'startdate', "");
        $startdate = addslashes($startdate);

        $enddate = validateObject($userData, 'enddate', "");
        $enddate = addslashes($enddate);



        $currentuser = "1";
        $select_selfie_query = "select selfie.id as selfieid,
                                selfie.is_purchased as is_purchased,
                                selfie.is_small_purchase,
                                selfie.is_large_purchase,
                                selfie.is_printed_purchase,
                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.id as icpimgid,
                                selfie.user_id as userid,
                                imgselfie.image as selfieimg,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price

                                from " . TABLE_ICP_IMAGE . " as imgselfie left join (select * from " . TABLE_ICP_IMAGE_TAG . ") as selfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id left join (select * from " . TABLE_ICP_SETTING . ")
                                as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where ";


            $isFirstCriteria = 1;

            if(strlen($businessId) > 0)
            {
                if($isFirstCriteria == 1)
                {
                    $select_selfie_query = $select_selfie_query . " business.id = '". $businessId ."'";
                    $isFirstCriteria = 0;
                }
                else
                {
                    $select_selfie_query = $select_selfie_query . " and business.id = '". $businessId ."'";
                }
            }

            if(strlen($icpId) > 0)
            {
                if($isFirstCriteria == 1)
                {
                    $select_selfie_query = $select_selfie_query . "  icps.id in  (" . $icpId. ")";
                    $isFirstCriteria = 0;
                }
                else
                {
                    $select_selfie_query = $select_selfie_query . " and  icps.id in  (" . $icpId. ")";
                }
            }

             if(strlen($startdate) > 0)
             {
                 if($isFirstCriteria == 1)
                 {
                     $select_selfie_query = $select_selfie_query . "  imgselfie.created > '" . $startdate . "' AND imgselfie.created < '". $enddate ."'";
                     $isFirstCriteria = 0;
                 }
                 else
                 {
                     $select_selfie_query = $select_selfie_query . " and  imgselfie.created > '" . $startdate . "' AND imgselfie.created < '". $enddate ."'";
                 }
             }
             $isdelete = 0;
             $select_selfie_query = $select_selfie_query ." and imgselfie.is_delete = " . $isdelete . "
             order by imgselfie.created desc";

//            and (selfie.closingtime > ? or selfie.closingtime is null or selfie.closingtime = '') order by imgselfie.created desc";
//            echo $select_selfie_query;
//            exit;


            $select_selfie_stmt = $connection->prepare($select_selfie_query);
            $currentdate = date("Y-m-d H:i:s");

            if($select_selfie_stmt->execute())
            {
                $select_selfie_stmt->store_result();

                if($select_selfie_stmt->num_rows > 0)
                {
                    while ($selfie = fetch_assoc_all_values($select_selfie_stmt))
                    {
                        if($selfie['userid'] == null || $selfie['userid'] == 'null')
                        {
                            $selfie['is_purchased'] = "0";
                        }

                        if($selfie['userid'] != $userid)
                        {
                            $selfie['is_purchased'] = "0";
                        }

                        if($selfie['selfieid'] != null && $selfie['userid'] != $userid)
                        {
                            $selfie['selfieid'] = null;
                        }

                        $posts[] = $selfie;
                    }

                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Search selfie !!!";
                    $data['searchselfie'] =  $posts;
                    return $data;
                }
                else
                {

                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "No selfie found !!!";
                    return $data;
                }
            }
        else
        {
            //echo $select_selfie_query->error;
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;
        }
    }

    public function updateUserSelfie($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $selfipic = validateObject($userData, 'selfipic', "");
        $selfipic = addslashes($selfipic);


            //select pic id
            $select_user_query = "select bio_selfie_id from " . TABLE_USER . " where id = ?";
            $select_user_stmt = $connection->prepare($select_user_query);
            $select_user_stmt->bind_param("i",$userid);

            if($select_user_stmt->execute())
            {
                $select_user_stmt->store_result();
                if($select_user_stmt->num_rows > 0)
                {
                    //Delete Selfie

                    $this->delete_face($userid);

                    $user = fetch_assoc_all_values($select_user_stmt);

                    if (strlen($user['bio_selfie_id']) == 0) {
                        // Upload pic as new user

                        if (strlen($selfipic) > 0) {

                            //Selfie Pic
                            $selfi_image_name = 'profile_' . date("Y-m-d_H_i_s") . ".png";
                            $selfi_image_upload_dir = SELFI_IMAGES . $selfi_image_name;
                            file_put_contents($selfi_image_upload_dir, base64_decode($selfipic));


                            $insertFields = "user_id,
                                    image,
                                    modified";
                            $valuesFields = "?,?,?";
                            $modifieddate = date("Y-m-d H:i:s");
                            $insert_query = "Insert into " . TABLE_USER_IMAGES . " (" . $insertFields . ") values(" . $valuesFields . ")";
                            $insert_pic_stmt = $connection->prepare($insert_query);
                            $insert_pic_stmt->bind_param("iss", $userid, $selfi_image_name, $modifieddate);
                            if ($insert_pic_stmt->execute()) {
                                
                                $pic_inserted_id = mysqli_insert_id($connection);
                                $insert_pic_stmt->close();
                                
                                //Post Face
                                $this->post_face($selfi_image_name, $userid);
                                
                                $update_query = "Update " . TABLE_USER . " set profile_image_id = ?, bio_selfie_id = ? where id = ? ";
                                $update_user_stmt = $connection->prepare($update_query);
                                $update_user_stmt->bind_param("iii", $pic_inserted_id, $pic_inserted_id, $userid);
                                if ($update_user_stmt->execute()) {
                                    $select_query = "Select user.*,imguser.image from " . TABLE_USER . " as user JOIN " . TABLE_USER_IMAGES . " as imguser on imguser.id =  user.profile_image_id where user.id = ?";
                                    if ($select_query_stmt = $connection->prepare($select_query)) {
                                        $select_query_stmt->bind_param("i", $userid);

                                        if ($select_query_stmt->execute()) {
                                            $select_query_stmt->store_result();
                                            if ($select_query_stmt->num_rows > 0) {
                                                $posts[] = fetch_assoc_all_values($select_query_stmt);

                                                $status = 1;
                                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                                $data['message'] = "Pic update successfully!!!";
                                                $data['user'] = $posts;
                                                return $data;
                                            }
                                        }
                                    } else {
                                        $status = 2;
                                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                        $data['message'] = "Please try again !!!" . $select_user_stmt->error;
                                        return $data;
                                    }

                                } else {
                                    $status = 2;
                                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                    $data['message'] = "Please try again !!!" . $select_user_stmt->error;
                                    $data['user'] = $posts;
                                    return $data;
                                }
                            }
                        }
                    }
                    else
                    {
                        $selfi_image_name = 'profile_' . date("Y-m-d_H_i_s") . ".png";
                        $selfi_image_upload_dir = SELFI_IMAGES . $selfi_image_name;
                        file_put_contents($selfi_image_upload_dir, base64_decode($selfipic));

                        //Post Face
                        $this->post_face($selfi_image_name,$userid);

                        $update_pic_query = "Update " . TABLE_USER_IMAGES . " set image = '" . $selfi_image_name . "' ,modified = '" . date("Y-m-d H:i:s")  . "'
                        where id = '" . $user['bio_selfie_id'] . "'";
                        $update_user_pic = mysqli_query($connection, $update_pic_query) or $errorMsg = mysqli_error($connection);

                        if(!$update_user_pic)
                        {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Please try again !!!";

                            return $data;
                        }
                        else
                        {
                            $select_query = "Select user.*,imguser.image from " . TABLE_USER . " as user JOIN " . TABLE_USER_IMAGES . " as
                            imguser on imguser.id =  user.profile_image_id where user.id = ?";

                            if ($select_query_stmt = $connection->prepare($select_query)) {
                                $select_query_stmt->bind_param("i", $userid);

                                if ($select_query_stmt->execute()) {
                                    $select_query_stmt->store_result();
                                    if ($select_query_stmt->num_rows > 0) {
                                        $posts[] = fetch_assoc_all_values($select_query_stmt);
                                    }
                                }
                            }

                            $status = 1;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Pic update successfully!!!";
                            $data['user']  = $posts;
                            return $data;
                        }

                    }
                }
                else
                {

                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again !!!";
                    return $data;
                }
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Please try again !!!";
                return $data;
            }
    }


    public  function getAllUserVerifiedSelfieTemp($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);


        $select_selfie_query = "select selfie.id as selfieid,
                                selfie.is_purchased,
                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                imgselfie.id  as icpimgid,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie left join (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id left join (select * from " . TABLE_ICP_SETTING . ") as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where selfie.user_id = ? and selfie.is_currentuser = ? and selfie.is_delete = ? order by selfie.verifiedtime desc ";



        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $iscurrentuser = "1";
        $isdelete = 0;
        $select_selfie_stmt->bind_param("isi",$userid,$iscurrentuser,$isdelete);

        if($select_selfie_stmt->execute()) {
            $select_selfie_stmt->store_result();
            if ($select_selfie_stmt->num_rows > 0) {
                while($selfie = fetch_assoc_all_values($select_selfie_stmt))
                {
                    if($selfie['is_image_timelimited'] == 1)
                    {
//                        echo "Closing Time :" .$selfie['closingtime'];
//                        echo "Current Time :" .date("Y-m-d H:i:s");
//                        break;

                        $next_date = $selfie['verifiedtime'];
                        $minutes_to_add = 60*intval($selfie['image_availabilty_time_limit']);
                        $time = new DateTime($next_date);
                        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                        $ctamp = $time->format('Y-m-d H:i:s');

                        if(date("Y-m-d H:i:s") < $selfie['closingtime'])
                        {
                            $posts[] = $selfie;
                        }
                        elseif(is_null($selfie['closingtime']))
                        {
                            $selfie['closingtime'] = $ctamp;
                            if(date("Y-m-d H:i:s") < $selfie['closingtime'])
                            {
                                $posts[] = $selfie;
                            }
                        }
                    }
                    else
                    {
                        $posts[] = $selfie;
                    }
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User verified selfie !!!";
                $data['verifiedselfie'] =  $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No verified selfi found!!!";
                return $data;
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again!!!";
            $data['user'] = $posts;
            return $data;
        }
    }

    public  function getAllUserVerifiedSelfieClone($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $noofrecord = validateObject($postdata, 'noofrecord', "");
        $noofrecord = addslashes($noofrecord);

        $offset = validateObject($postdata, 'offset', "");
        $offset = addslashes($offset);

        $select_selfie_query = "select  selfie.id as selfieid,
                                selfie.is_purchased,

                                selfie.is_small_purchase,
                                selfie.is_large_purchase,
                                selfie.is_printed_purchase,

                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                imgselfie.id as icpimgid,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie left join (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id
                                left join (select * from " . TABLE_ICP_SETTING . ") as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where
                                selfie.user_id = ? and
                                selfie.is_currentuser = ? and
                                selfie.is_delete = ? and (selfie.closingtime > ? or selfie.closingtime is null or selfie.closingtime = '')
                                order by selfie.verifiedtime desc";

//        echo $select_selfie_query;
//        exit;


        //echo $select_selfie_query;exit;
        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $iscurrentuser = "1";
        $isdelete = 0;
        $currentdate = date("Y-m-d H:i:s");

        $select_selfie_stmt->bind_param("isis",$userid,$iscurrentuser,$isdelete,$currentdate);

        if($select_selfie_stmt->execute()) {
            $select_selfie_stmt->store_result();
            if ($select_selfie_stmt->num_rows > 0) {

                $canpurchage = 0;

                $arrSelfie = array();

                while($selfie = fetch_assoc_all_values($select_selfie_stmt))
                {
                    if($selfie['is_purchased']==0)
                    {
                        $canpurchage = $canpurchage + 1;
                    }

                    $arrSelfie[] =  $selfie['selfieid'];
                    $posts[] = $selfie;
                }


                // Select all purchase image by current user

                $arrPurchase = array();
                $select_purchase_selfie  = "select DISTINCT cartitem.selfie_id as selfieid from cart as ordercart
                left join (select * from cart_item) as cartitem on cartitem.cart_id = ordercart.id where user_id = ? and is_payment_done = '1'";

                $select_purchase_selfie_stmt = $connection->prepare($select_purchase_selfie);
                $select_purchase_selfie_stmt->bind_param("i",$userid);
                if($select_purchase_selfie_stmt->execute())
                {
                    $select_purchase_selfie_stmt->store_result();
                    if ($select_purchase_selfie_stmt->num_rows > 0)
                    {
                        while($purchase = fetch_assoc_all_values($select_purchase_selfie_stmt))
                        {

                            $arrPurchase[] =  $purchase['selfieid'];
                        }
                    }
                }
                else
                {
                    $data['status'] = FAILED;
                    $data['message'] = "Some thing went wrong please try again...";
                    return $data;

                }

                $purchase=array_diff($arrPurchase ,$arrSelfie);


                foreach ($purchase as $selfie_id)
                {


                    $select_purchaseselfie_query = "select  selfie.id as selfieid,
                                selfie.is_purchased,
                                selfie.is_small_purchase,
                                selfie.is_large_purchase,
                                selfie.is_printed_purchase,

                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                imgselfie.id as icpimgid,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie left join (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id
                                left join (select * from " . TABLE_ICP_SETTING . ") as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where
                                selfie.id = ? and
                                selfie.is_delete = '0'";


                    $select_purchaseselfie_stmt = $connection->prepare($select_purchaseselfie_query);
                    $select_purchaseselfie_stmt->bind_param("i",$selfie_id);

                    if($select_purchaseselfie_stmt->execute())
                    {
                        $select_purchaseselfie_stmt->store_result();
                        if ($select_purchaseselfie_stmt->num_rows > 0)
                        {
                            while($photo = fetch_assoc_all_values($select_purchaseselfie_stmt))
                            {
                                $posts[] = $photo;
                            }
                        }
                    }
                }

                $sortOrder = array();
                foreach ($posts as $objPhoto) {
                    $created = $objPhoto['detectedtime'].$objPhoto['selfieid'];
                    $sortOrder[(string)$created] = $objPhoto;
                }

                krsort($sortOrder);
                unset($posts);
                $posts = array();

                foreach ($sortOrder as $objRest) {
                    array_push($posts, $objRest);
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User verified selfie !!!";
                $data['noofphotos'] = strval(count($posts));
                $data['canpurchage'] = strval($canpurchage);
                $data['verifiedselfie'] =  array_slice($posts,intval($offset),intval($noofrecord));
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No verified selfi found!!!";
                return $data;
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again!!!";
            $data['user'] = $posts;
            return $data;
        }
    }

    public  function getAllUserVerifiedSelfie($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $noofrecord = validateObject($postdata, 'noofrecord', "");
        $noofrecord = addslashes($noofrecord);

        $offset = validateObject($postdata, 'offset', "");
        $offset = addslashes($offset);

        $select_selfie_query = "select selfie.id as selfieid,
                                selfie.is_purchased,

                                selfie.is_small_purchase,
                                selfie.is_large_purchase,
                                selfie.is_printed_purchase,

                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                imgselfie.id as icpimgid,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie left join (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id
                                left join (select * from " . TABLE_ICP_SETTING . ") as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where
                                selfie.user_id = ? and
                                selfie.is_currentuser = ? and
                                selfie.is_delete = ? and (selfie.closingtime > ? or selfie.closingtime is null or selfie.closingtime = '')
                                order by selfie.verifiedtime desc";

//        echo $select_selfie_query;
//        exit;


        //echo $select_selfie_query;exit;
        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $iscurrentuser = "1";
        $isdelete = 0;
        $currentdate = date("Y-m-d H:i:s");

        $select_selfie_stmt->bind_param("isis",$userid,$iscurrentuser,$isdelete,$currentdate);

        if($select_selfie_stmt->execute()) {
            $select_selfie_stmt->store_result();
            if ($select_selfie_stmt->num_rows > 0) {

                $canpurchage = 0;

                $arrSelfie = array();

                while($selfie = fetch_assoc_all_values($select_selfie_stmt))
                {
                    if($selfie['is_purchased']==0)
                    {
                        $canpurchage = $canpurchage + 1;
                    }

                    $arrSelfie[] =  $selfie['selfieid'];
                    $posts[] = $selfie;
                }


                // Select all purchase image by current user

                $arrPurchase = array();
                $select_purchase_selfie  = "select cartitem.selfie_id as selfieid from cart as ordercart
                left join (select * from cart_item) as cartitem on cartitem.cart_id = ordercart.id where user_id = ? and is_payment_done = '1'";

                $select_purchase_selfie_stmt = $connection->prepare($select_purchase_selfie);
                $select_purchase_selfie_stmt->bind_param("i",$userid);
                if($select_purchase_selfie_stmt->execute())
                {
                    $select_purchase_selfie_stmt->store_result();
                    if ($select_purchase_selfie_stmt->num_rows > 0)
                    {
                        while($purchase = fetch_assoc_all_values($select_purchase_selfie_stmt))
                        {

                            $arrPurchase[] =  $purchase['selfieid'];
                        }
                    }
                }
                else
                {
                    $data['status'] = FAILED;
                    $data['message'] = "Some thing went wrong please try again...";
                    return $data;

                }

                $purchase=array_diff($arrPurchase ,$arrSelfie);


                foreach ($purchase as $selfie_id)
                {


                    $select_purchaseselfie_query = "select selfie.id as selfieid,
                                selfie.is_purchased,
                                selfie.is_small_purchase,
                                selfie.is_large_purchase,
                                selfie.is_printed_purchase,

                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                imgselfie.id as icpimgid,
                                icps.id as icpid,
                                icps.name as icpname ,
                                icps.icp_logo,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie left join (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id
                                left join (select * from " . TABLE_ICP_SETTING . ") as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where
                                selfie.id = ? and
                                selfie.is_delete = '0'";


                $select_purchaseselfie_stmt = $connection->prepare($select_purchaseselfie_query);
                $select_purchaseselfie_stmt->bind_param("i",$selfie_id);

                    if($select_purchaseselfie_stmt->execute())
                    {
                        $select_purchaseselfie_stmt->store_result();
                        if ($select_purchaseselfie_stmt->num_rows > 0)
                        {
                            while($photo = fetch_assoc_all_values($select_purchaseselfie_stmt))
                            {
                                $posts[] = $photo;
                            }
                       }
                    }
                }

                //Sorting Based on created data

                $sortOrder = array();
                foreach ($posts as $objPhoto) {
                    $created = $objPhoto['detectedtime'].$objPhoto['selfieid'];
                    $sortOrder[(string)$created] = $objPhoto;
                }

                krsort($sortOrder);
                unset($posts);
                $posts = array();

                foreach ($sortOrder as $objRest) {
                    array_push($posts, $objRest);
                }


                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User verified selfie !!!";
                $data['noofphotos'] = strval(count($posts));
                $data['canpurchage'] = strval($canpurchage);
                $data['verifiedselfie'] =  array_slice($posts,intval($offset),intval($noofrecord));
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No verified selfi found!!!";
                return $data;
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again!!!";
            $data['user'] = $posts;
            return $data;
        }
    }

    public  function updateSelfieVerification($postdata)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $arrUserVerificatin = array();
        $errorMsg = "Service responce data";

        $selfie_tagid = validateObject($postdata, 'selfie_tagid', "");
        $selfie_tagid = addslashes($selfie_tagid);

        $isCurrentUser = validateObject($postdata, 'user_verification', "");
        $isCurrentUser = addslashes($isCurrentUser);

        $arrTagID = explode(',', $selfie_tagid);
        $arrUserVerificatin = explode(',', $isCurrentUser);


        $isVerified = "1";
        $index = 0;
        foreach ($arrTagID as $tagID)
        {
            //select icp settings
            $select_icp_settings = "select icpsettings.icp_id,icpsettings.is_image_timelimited,icpsettings.image_availabilty_time_limit  from ". TABLE_ICP_IMAGE_TAG . " as icptagimg left
             join (select * from " . TABLE_ICP_IMAGE . ") as icpimg on icptagimg.icp_image_id = icpimg.id left join (select * from " . TABLE_ICP_SETTING . ")
             as icpsettings on icpimg.icp_id = icpsettings.icp_id where icptagimg.id = ?";

            $stmt_select_icp_settings = $connection->prepare($select_icp_settings);
            $stmt_select_icp_settings->bind_param("i",$tagID);
            $stmt_select_icp_settings->execute();
            $stmt_select_icp_settings->store_result();

            $icpsettings = fetch_assoc_all_values($stmt_select_icp_settings);

            if($icpsettings['is_image_timelimited'] == "0")
            {
                $update_imgtag_query = "update " . TABLE_ICP_IMAGE_TAG . " set is_user_verified = ? , is_currentuser = ?,verifiedtime = ?  where id = ?";
                $update_imgtag_stmt = $connection->prepare($update_imgtag_query);
                $verifiedtime = date("Y-m-d H:i:s");
                $update_imgtag_stmt->bind_param("sssi",$isVerified,$arrUserVerificatin[$index],$verifiedtime,$tagID);
                if(!$update_imgtag_stmt->execute())
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again !!!";
                    return $data;
                }
                $index = $index + 1;
            }
            else
            {

                $next_date = date('Y-m-d H:i:s', time());
                $minutes_to_add = 60*intval($icpsettings['image_availabilty_time_limit']);
                $time = new DateTime($next_date);
                $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $ctamp = $time->format('Y-m-d H:i:s');

                $verifiedtime = date("Y-m-d H:i:s");
                $update_imgtag_query = "update " . TABLE_ICP_IMAGE_TAG . " set is_user_verified = ? , is_currentuser = ? ,closingtime = ?, verifiedtime = ? where id = ?";
                $update_imgtag_stmt = $connection->prepare($update_imgtag_query);
                $update_imgtag_stmt->bind_param("ssssi",$isVerified,$arrUserVerificatin[$index],$ctamp,$verifiedtime,$tagID);

                if(!$update_imgtag_stmt->execute())
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again !!!";
                    return $data;
                }
                $index = $index + 1;
            }
        }
        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Tag selfie verifired !!!";
        return $data;
    }


    public  function checkOutBusiness($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $businessid = validateObject($postdata, 'businessid', "");
        $businessid = addslashes($businessid);

        $checkout_business = "update " . TABLE_CHECKIN . " set is_checked_in = ? where user_id = ? and business_id = ?";
        $checkout_business_stmt = $connection->prepare($checkout_business);
        $ischeckin = "0";
        $checkout_business_stmt->bind_param("sii",$ischeckin,$userid,$businessid);

        if($checkout_business_stmt->execute()) {
            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Checkout successfully !!!";
            return $data;
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;
        }
    }

    public  function getCheckinBusiness($postdata)
    {


        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);


        $select_checkin_business = "select checkin.user_id,
        checkin.is_checked_in,
        checkin.business_id
       ,business.* from " . TABLE_CHECKIN . " as checkin left join (select * from ". TABLE_BUSINESS .") as business on
        checkin.business_id = business.id  where checkin.user_id = ? and checkin.is_checked_in = ? and business.is_delete =? order by checkin.modified desc";

        //echo $select_checkin_business;exit;

        $select_checkin_business_stmt = $connection->prepare($select_checkin_business);
        $isCheckin = "1";
        $isdelete = 0;
        $select_checkin_business_stmt->bind_param("isi",$userid,$isCheckin,$isdelete);

        if($select_checkin_business_stmt->execute())
        {
            $select_checkin_business_stmt->store_result();
            $checkin =array();
            if ($select_checkin_business_stmt->num_rows > 0) {

                while($business = fetch_assoc_all_values($select_checkin_business_stmt))
                {
                    //check is favorite business
                    $check_isfavorite_query = "select * from " . TABLE_FAVORITES . " where businessid = ? and userid = ?";
                    $check_isfavorite_stmt = $connection->prepare($check_isfavorite_query);

                    $check_isfavorite_stmt->bind_param("ii",$business['id'],$userid);
                    $check_isfavorite_stmt->execute();
                    $check_isfavorite_stmt->store_result();
                    if($check_isfavorite_stmt->num_rows > 0)
                    {
                        $favorite = fetch_assoc_all_values($check_isfavorite_stmt);
                        $business['isfavorite'] = $favorite['isfavorite'];
                    }
                    else
                    {
                        $business['isfavorite'] = "0";
                    }

                    //chcek is like business
                    $check_islike_query = "select * from " . TABLE_LIKES . " where businessid = ? and userid = ?";
                    $check_islike_stmt = $connection->prepare($check_islike_query);
                    $check_islike_stmt->bind_param("ii",$business['id'],$userid);
                    $check_islike_stmt->execute();
                    $check_islike_stmt->store_result();
                    if($check_islike_stmt->num_rows > 0)
                    {
                        $like = fetch_assoc_all_values($check_islike_stmt);
                        $business['islike'] = $like['islike'];
                    }
                    else
                    {
                        $business['islike'] = "0";
                    }
                    $checkin[] = $business;
                   // print_r($checkin."\n");
                   // echo $select_checkin_business;exit;
                }

                //echo "Hi " .count($checkin);

                $select_checkin_business_stmt->close();

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User checkin business !!!";
                $data['checkinbusiness'] =  $checkin;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No checkin found !!!";
                $data['checkinbusiness'] =  $checkin;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;
        }
    }

    public  function getBusinessVerifiedSelfie($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $businessid = validateObject($postdata, 'businessid', "");
        $businessid = addslashes($businessid);

        $select_selfie_query = "select selfie.id as selfieid,
                                selfie.created as detectedtime,
                                selfie.closingtime as closingtime,
                                selfie.verifiedtime as verifiedtime,
                                imgselfie.image as selfieimg,
                                imgselfie.id as icpimgid,
                                selfie.is_purchased,
                                icps.id as icpid,
                                icps.name as icpname ,
                                business.id as businessid,
                                business.name as businessname,
                                icpsettings.preview_photo ,
                                icpsettings.addlogo_to_sharedimage ,
                                icpsettings.is_low_image_free ,
                                icpsettings.is_high_image_free ,
                                icpsettings.lowfree_on_highpurchase ,
                                icpsettings.digital_free_on_physical_purchase,
                                icpsettings.is_image_timelimited,
                                icpsettings.image_availabilty_time_limit,
                                icpsettings.local_hotel_delivery_free,
                                icpsettings.local_hotel_delivery_price,
                                icpsettings.domestic_shipping_free ,
                                icpsettings.domestic_shipping_price,
                                icpsettings.international_shipping_free,
                                icpsettings.international_shipping_price,
                                icps.low_resolution_price,
                                icps.high_resolution_price,
                                icps.offer_printed_souvenir,
                                icps.printed_souvenir_price
                                from " . TABLE_ICP_IMAGE_TAG . " as selfie left join (select * from " . TABLE_ICP_IMAGE . ") as imgselfie on imgselfie.id = selfie.icp_image_id
                                left join (select * from " . TABLE_ICPS . ") as icps on imgselfie.icp_id = icps.id left join (select * from " . TABLE_ICP_SETTING . ") as icpsettings on icps.id = icpsettings.icp_id
                                left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icps.business_id
                                where selfie.user_id = ? and selfie.is_currentuser = ? and selfie.is_purchased = ? and business.id  = ? and selfie.is_delete = ?
                                and (selfie.closingtime > ? or selfie.closingtime is null or selfie.closingtime = '')
                                order by selfie.verifiedtime desc";




//        echo $select_selfie_query;exit();

        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $iscurrentuser = "1";
        $ispurchagse = "0";
        $isdelete = 0;
        $currentdate = date("Y-m-d H:i:s");

        $select_selfie_stmt->bind_param("issiis",$userid,$iscurrentuser,$ispurchagse,$businessid,$isdelete,$currentdate);

        if($select_selfie_stmt->execute()) {
            $select_selfie_stmt->store_result();
            if ($select_selfie_stmt->num_rows > 0) {

                while($selfie = fetch_assoc_all_values($select_selfie_stmt))
                {
                    $posts[] = $selfie;

//                    $selfie['ispromoimg'] = "0";
//                    if($selfie['is_image_timelimited'] == 1)
//                    {
//                        echo "Closing Time :" .$selfie['closingtime'];
//                        echo "Current Time :" .date("Y-m-d H:i:s");
//                        break;
//
//                        $next_date = $selfie['verifiedtime'];
//                        $minutes_to_add = 60*intval($selfie['image_availabilty_time_limit']);
//                        $time = new DateTime($next_date);
//                        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
//                        $ctamp = $time->format('Y-m-d H:i:s');
//
//                        if(date("Y-m-d H:i:s") < $selfie['closingtime'])
//                        {
//                            $posts[] = $selfie;
//                        }
//                        elseif(is_null($selfie['closingtime']))
//                        {
//                            $selfie['closingtime'] = $ctamp;
//                            if(date("Y-m-d H:i:s") < $selfie['closingtime'])
//                            {
//                                $posts[] = $selfie;
//                            }
//                        }
//                    }
//                    else
//                    {
//                        $posts[] = $selfie;
//                    }
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User verified selfie !!!";
                $data['verifiedselfie'] =  $posts;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No verified selfi found!!!";
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again!!!";
            $data['verifiedselfie'] = $posts;
            return $data;
        }

        return $data;
    }

//    public  function updateSelfieVerification($postdata)
//    {
//        $connection = $GLOBALS['con'];
//        $status = 2;
//        $posts = array();
//        $arrUserVerificatin = array();
//        $errorMsg = "Service responce data";
//
//        $selfie_tagid = validateObject($postdata, 'selfie_tagid', "");
//        $selfie_tagid = addslashes($selfie_tagid);
//
//        $isCurrentUser = validateObject($postdata, 'user_verification', "");
//        $isCurrentUser = addslashes($isCurrentUser);
//
//        $arrTagID = explode(',', $selfie_tagid);
//        $arrUserVerificatin = explode(',', $isCurrentUser);
//
//
//            $isVerified = "1";
//            $index = 0;
//            foreach ($arrTagID as $tagID)
//            {
//                $update_imgtag_query = "update " . TABLE_ICP_IMAGE_TAG . " set is_user_verified = ? , is_currentuser = ? where id = ?";
//                $update_imgtag_stmt = $connection->prepare($update_imgtag_query);
//                $update_imgtag_stmt->bind_param("ssi",$isVerified,$arrUserVerificatin[$index],$tagID);
//                if(!$update_imgtag_stmt->execute())
//                {
//                    $status = 2;
//                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                    $data['message'] = "Please try again !!!";
//                    return $data;
//                }
//                $index = $index + 1;
//            }
//            $status = 1;
//            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//            $data['message'] = "Tag selfie verifired !!!";
//            return $data;
//    }

    public  function getDetectedSelfie($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);


        $select_selfie_query = "select icptag.id as selfietagid ,
                                icptag.icp_image_id as imgid, icpimg.image as image,icp.id as icpid,icp.name as icpname,icp.address as icpaddress,
                                business.id as businessid,business.name as businessname,business.address1 as businessaddress   from icp_image_tag as icptag left join
                                (select * from icp_images) as icpimg on icptag.icp_image_id =  icpimg.id  left join (select * from icps) as icp on icpimg.icp_id = icp.id left join
                                (select * from businesses) as business on icp.business_id = business.id where icptag.user_id = ? and icptag.is_user_verified = ? and icpimg.is_delete = ?";


//        echo $select_selfie_query;exit;

        $select_selfie_stmt = $connection->prepare($select_selfie_query);
        $isVerified = "0";
        $isdelete = 0;
        $select_selfie_stmt->bind_param("isi", $userid,$isVerified,$isdelete);
        if($select_selfie_stmt->execute()) {
            $select_selfie_stmt->store_result();

            if ($select_selfie_stmt->num_rows > 0) {

                while($images = fetch_assoc_all_values($select_selfie_stmt))
                {
                    $posts[] = $images;
                }
                $status = 1;

                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User selfie detected !!!";
                $data['selfie'] =  $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No selfie detected!!!";
                return $data;
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again!!!";
            $data['user'] = $posts;
            return $data;
        }

    }


    public  function userExistWithFacebookID($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";
        $facebookID = validateObject($userData, 'facebookid', "");
        $facebookID = addslashes($facebookID);

        $deviceToken = validateObject($userData, 'deviceid', "");
        $deviceToken = addslashes($deviceToken);

        $deviceType = validateObject($userData, 'devicetype', "");
        $deviceType = addslashes($deviceType);

        $select_facebookid_query = "Select * from " . TABLE_USER . "  where facebook_id = ? and is_delete = ?";
        $select_facebookid_stmt = $connection->prepare($select_facebookid_query);
        $isdelete = '0';
        $select_facebookid_stmt->bind_param("ss", $facebookID,$isdelete);

        if($select_facebookid_stmt->execute()) {
            $select_facebookid_stmt->store_result();
            if ($select_facebookid_stmt->num_rows > 0) {

                //update device type and token

                $user = fetch_assoc_all_values($select_facebookid_stmt);
                $login_count = intval($user['login_count']) + 1;

                $update_query = "Update " . TABLE_USER . " set
                             device_type = ?,
                             device_id = ?,
                             last_loggedin = ?,
                             login_count = ?
                             where facebook_id = ? ";

                $update_query_stmt = $connection->prepare($update_query);
                $currentdate = date("Y-m-d H:i:s");
                $update_query_stmt->bind_param("sssis", $deviceType, $deviceToken, $currentdate, $login_count, $facebookID);

                if ($update_query_stmt->execute()) {

                    $select_fb_user = "select user.*,imguser.image from " . TABLE_USER . " as user JOIN " . TABLE_USER_IMAGES . " as imguser on
                    imguser.id =  user.profile_image_id where user.facebook_id =  ?";
                    // echo $select_fb_user;exit;

                    $select_fb_user_stmt = $connection->prepare($select_fb_user);
                    $select_fb_user_stmt->bind_param("s", $facebookID);
                    $select_fb_user_stmt->execute();
                    $select_fb_user_stmt->store_result();
                    unset($user);
                    $user = fetch_assoc_all_values($select_fb_user_stmt);
                    unset($user['password']);
                    $status = 1;
                    $posts[] = $user;

                    $tokenData = new stdClass;
                    $tokenData->GUID = $user['guid'];
                    $tokenData->userId = $user['id'];

                    $security = new SecurityFunctions();
                    $user_token = $security->updateTokenforUser($tokenData);
                    if ($user_token['status'] == 1) {
                        $data['usertoken'] = $user_token['UserToken'];
                    }

                    $data['status'] = SUCCESS;
                    $data['message'] = "User Login successfully...";
                    $data['isExist'] = "YES";
                    $data['user'] = $posts;
                    return $data;
                }
                else
                {
                    $status = 2;
                    $data['status'] = FAILED;
                    $data['message'] = "Please try again...";
                    $data['isExist'] = "NO";
                    return $data;
                }
            }
            else
            {
                $status = 1;
                $data['status'] = SUCCESS;
                $data['message'] = "User with this facebook id not exist";
                $data['isExist'] = "NO";
                return $data;
            }
        }
        else
        {
            $data['status'] = FAILED;
            $data['message'] = "Please try again!!!";
            $data['user'] = $posts;
            return $data;
        }
    }

    public  function userExistWithEmail($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";
        $useremail = validateObject($userData, 'email', "");
        $useremail = addslashes($useremail);


        $username = validateObject($userData, 'username', "");
        $username = addslashes($username);

        $select_email_query = "Select email from " . TABLE_USER . "  where email = ? and is_delete = ?";
        $select_email_stmt = $connection->prepare($select_email_query);
        $isdelete = '0';
        $select_email_stmt->bind_param("ss", $useremail,$isdelete);

        if($select_email_stmt->execute())
        {
            $select_email_stmt->store_result();
            if ($select_email_stmt->num_rows > 0) {
                $select_email_stmt->close();
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Email address already exist !!!";
                $data['isExist'] = "YES";
                return $data;
            }
            else
            {

                $select_username_query = "Select username from " . TABLE_USER . "  where username = ? and is_delete = ?";
                $select_username_stmt = $connection->prepare($select_username_query);
                $isdelete = '0';
                $select_username_stmt->bind_param("ss", $useremail,$isdelete);

                if($select_username_stmt->execute())
                {
                    $select_username_stmt->store_result();
                    if ($select_username_stmt->num_rows > 0)
                    {
                        $select_username_stmt->close();
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Username address already exist...";
                        $data['isExist'] = "YES";
                        return $data;
                    }
                    else
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "User with this email id not exist";
                        $data['isExist'] = "NO";
                        return $data;
                    }
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again!!!";
                    $data['user'] = $posts;
                    return $data;

                }
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again!!!";
            $data['user'] = $posts;
            return $data;
        }
    }

    public function getUserSpecificImages($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";


        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $select_icp_images = "select  icp_images.id as icp_img_id , icp_image_tag.id as user_tag_id,
                              icp_image_tag.is_user_verified as is_user_verified , icp_image_tag.user_id as user_id,
                              icp_image_tag.is_purchased as is_user_purchased, icp_images.image as image,
                              icp_images.upload_type as upload_type,icp_images.is_face_detected as is_face_detected
                              from icp_images,icp_image_tag where
                              icp_image_tag.icp_image_id in (select icp_images.id from icp_image_tag) and icp_image_tag.user_id = ? and icp_images.is_delete = ?";

        $select_icp_stmt = $connection->prepare($select_icp_images);
        $isdelete = 0;
        $select_icp_stmt->bind_param("ii",$userid,$isdelete);

        if($select_icp_stmt->execute())
        {
            $select_icp_stmt->store_result();
            if($select_icp_stmt->num_rows > 0)
            {
                while($images = fetch_assoc_all_values($select_icp_stmt))
                {
                    $posts[] = $images;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List user detected images!!!";
                $data['imgdetected'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No images found!!!";
                $data['imgdetected'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            $data['imgdetected'] = $posts;
            return $data;

        }


    }
    public function changePassword($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $oldPassword = validateObject($userData, 'oldpassword', "");
        $oldPassword = encryptPassword($oldPassword);

        $newpassword = validateObject($userData, 'newpassword', "");
        $newpassword = encryptPassword($newpassword);

        $select_userpassword_query = "Select password from " . TABLE_USER . " where id = '" . $userid . "' ";
        $user_userpassword_response = mysqli_query($connection, $select_userpassword_query) or $errorMsg = mysqli_error($connection);
        $userpassword = mysqli_fetch_assoc($user_userpassword_response);

        if ($userpassword['password'] == $oldPassword) {

                $update_password_query = "update " . TABLE_USER . " set password = '" . $newpassword . "' where id = '" . $userid . "'";
                $response_update_password = mysqli_query($connection, $update_password_query) or $error = mysqli_error($connection);

                if ($response_update_password) {
                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Password Update successfully !!!";
                    return $data;

                } else {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Old password not matched !!!";
                    return $data;
                }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Old password not matched !!!";
            return $data;
        }
}
    public function editProfile($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($userData, 'userId', "");
        $userid = addslashes($userid);

        $profilepicid = validateObject($userData, 'profilepicid', "");
        $profilepicid = addslashes($profilepicid);

        $firstname = validateObject($userData, 'firstname', "");
        $firstname = addslashes($firstname);

        $lastname = validateObject($userData, 'lastname', "");
        $lastname = addslashes($lastname);

        $username = validateObject($userData, 'username', "");
        $username = addslashes($username);

        $email = validateObject($userData, 'email', "");
        $email = addslashes($email);

        $gender = validateObject($userData, 'gender', "");
        $gender = addslashes($gender);

        $phoneno = validateObject($userData, 'phoneno', "");
        $phoneno = addslashes($phoneno);

        $dob = validateObject($userData, 'dob', "");
        $dob = addslashes($dob);

        $selfipic = validateObject($userData, 'selfipic', "");
        $selfipic = addslashes($selfipic);

        $city = validateObject($userData, 'city', "");
        $city = addslashes($city);

        $update_query = "Update " . TABLE_USER . " set";


        if(strlen($email) > 0) {
            $select_email_query = "Select email from " . TABLE_USER . "  where email = ? and id != ?";
            $select_email_stmt = $connection->prepare($select_email_query);
            $select_email_stmt->bind_param("si", $email, $userid);
            $select_email_stmt->execute();
            $select_email_stmt->store_result();

            if ($select_email_stmt->num_rows > 0) {
                $select_email_stmt->close();
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Email address already exists !!!";
                $data['usertoken'] = '';
                $data['user'] = $posts;
                return $data;
            }
        }

        if(strlen($username) > 0)
        {
            $select_username_query = "Select username from " . TABLE_USER . "  where username = ? and id != ?";
            $select_username_stmt = $connection->prepare($select_username_query);
            $select_username_stmt->bind_param("si", $username, $userid);
            $select_username_stmt->execute();
            $select_username_stmt->store_result();

            if ($select_username_stmt->num_rows > 0) {
                $select_username_stmt->close();
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "User name already exists !!!";
                $data['usertoken'] = '';
                $data['user'] = $posts;
                return $data;
            }
        }

        if (strlen($selfipic) > 0) {

            $this->delete_face($userid);

            //Selfie Pic
            $selfi_image_name = 'profile_' . date("Y_m_d_H_i_s") . ".png";
            $selfi_image_upload_dir = SELFI_IMAGES . $selfi_image_name;
            file_put_contents($selfi_image_upload_dir, base64_decode($selfipic));

            //Post Face
            $this->post_face($selfi_image_name,$userid);

            $update_pic_query = "Update " . TABLE_USER_IMAGES . " set image = '" . $selfi_image_name . "' ,modified = '" . date("Y-m-d H:i:s")  . "'
            where user_id = '" . $userid . "'";
            $update_user_pic = mysqli_query($connection, $update_pic_query) or $errorMsg = mysqli_error($connection);

            if(!$update_user_pic)
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Please try again !!!";
                $data['user'] = $posts;
                return $data;
            }
        }

        if (strlen($firstname) > 0) {
            $update_query = $update_query . " firstname = '" . $firstname . "',";
        }

        if (strlen($lastname) > 0) {
            $update_query = $update_query . " lastname = '" . $lastname . "',";
        }

        if (strlen($email) > 0) {
            $update_query = $update_query . " email = '" . $email . "',";
        }

        if (strlen($gender) > 0) {
            $update_query = $update_query . " gender = '" . $gender . "',";
        }

        if (strlen($phoneno) > 0) {
            $update_query = $update_query . " phone_no = '" . $phoneno . "',";
        }

        if (strlen($dob) > 0) {
            $update_query = $update_query . " dob = '" . $dob . "',";
        }

        if(strlen($city) > 0){
            $update_query = $update_query . " city = '" . $city . "',";
        }

        if(strlen($username)>0){
            $update_query = $update_query . " username = '" . $username . "',";
        }


        $update_query = $update_query . " modified = '" . date("Y-m-d H:i:s")  . "'";
        $update_query = $update_query ." where id = '" . $userid ."'";


        $update_user = mysqli_query($connection, $update_query) or $errorMsg = mysqli_error($connection);


        if($update_user)
        {
            $status = 1;

            $select_query = "Select * from " . TABLE_USER . " where id = '" . $userid ."'";


            $fetch_user = mysqli_query($connection, $select_query) or $errorMsg = mysqli_error($connection);

            if($fetch_user)
            {
                if ((mysqli_num_rows($fetch_user)) > 0) {
                    $user = mysqli_fetch_assoc($fetch_user);
                    unset($user['password']);

                    $select_image_query = "select * from " . TABLE_USER_IMAGES . " where user_id = ?";
                    $select_image_query_stmt = $connection->prepare($select_image_query);
                    $select_image_query_stmt->bind_param("i",$user['id']);
                    $select_image_query_stmt->execute();
                    $select_image_query_stmt->store_result();
                    $image  = fetch_assoc_all_values($select_image_query_stmt);

                    $user['image'] = $image['image'];


                    $status = 1;
                    $posts[] = $user;

                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = 'User details !!!';
                    $data['user'] = $posts;
                    return $data;
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "User not found  !!!";
                    $data['user'] = $posts;
                    return $data;
                }
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Profile updated successfully !!!";
                $data['user'] = $posts;
                return $data;
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Please try again !!!";
                $data['user'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            $data['user'] = $posts;
            return $data;
        }
    }
    public function loginUser($userData)
    {
        $connection = $GLOBALS['con'];
        $posts = array();
        $errorMsg = "";
        $select_query_stmt = "";

        $username = validateObject($userData, 'username', "");
        $username = addslashes($username);

        $password = validateObject($userData, 'password', "");
        $password = encryptPassword($password);




        $deviceid = validateObject($userData, 'deviceid', 0);
        $deviceid = addslashes($deviceid);

        $devicetype = validateObject($userData, 'devicetype', 0);
        $devicetype = addslashes($devicetype);

//        echo $password;exit;

        $select_query = "Select * from " . TABLE_USER . "  where (username = ? or email = ?) and password = ? and is_delete = ?";

//        echo $select_query;exit;

        if ($select_query_stmt = $connection->prepare($select_query)) {
            $isdelete = 0;
            $select_query_stmt->bind_param("sssi", $username,$username,$password,$isdelete);

            if($select_query_stmt->execute())
            {
                $select_query_stmt->store_result();
                if ($select_query_stmt->num_rows > 0)
                {
                    $user = fetch_assoc_all_values($select_query_stmt);
                    $select_query_stmt->close();

                    $login_count = intval($user['login_count']) + 1;
                    $update_query = "Update " . TABLE_USER . " set
                    login_count =  ?  ,
                    is_active='" . 1 . "',
                    last_loggedin ='" . date("Y-m-d H:i:s")  . "',
                    device_id = ?,
                    device_type = ?  where (username = ? or email = ?) ";

                    //echo $update_query;exit;
                    if ($user_query_stmt = $connection->prepare($update_query)) {
                        $user_query_stmt->bind_param("sssss", $login_count,$deviceid,$devicetype,$username,$username);
                        $user_query_stmt->execute();
                        $user_query_stmt->close();
                    }

                    if (!$user['guid'] || ($user['guid'] = trim($user['guid']))  || !$user['accesscode'] || ($user['accesscode'] = trim($user['accesscode'])) ) {
                        // generate Token for User


                        $security=new SecurityFunctions();

                        $generate_guid=$security->gen_uuid();
                        include_once 'GlobalFunction.php';
                        $objGlobalFunction = new GlobalFunction();
                        $accesscode = $objGlobalFunction -> generateBase32UniqueString();

                        //update user GUID and accesscode

                        $update_user_query = "update " . TABLE_USER ." set guid = ? , accesscode = ? where id = ?";
                        $update_user_query_stmt = $connection->prepare($update_user_query);
                        $update_user_query_stmt->bind_param("ssi", $generate_guid,$accesscode,$user['id']);
                        if($update_user_query_stmt->execute())
                        {
                            $select_query = "Select * from " . TABLE_USER . " where id = ?";
                            $select_query_stmt = $connection->prepare($select_query);
                            $select_query_stmt->bind_param("i",$user['id']);

                            if($select_query_stmt->execute())
                            {
                                $select_query_stmt->store_result();
                                $user = fetch_assoc_all_values($select_query_stmt);

                                $tokenData = new stdClass;
                                $tokenData -> GUID = $generate_guid;
                                $tokenData -> userId = $user['id'];

                                $security=new SecurityFunctions();
                                $user_token = $security->updateTokenforUser($tokenData);
                                if($user_token['status'] == 1) {
                                    $data['usertoken'] = $user_token['UserToken'];
                                }


                                $select_image_query = "select * from " . TABLE_USER_IMAGES . " where user_id = ? and is_delete='0'";
                                $select_image_query_stmt = $connection->prepare($select_image_query);
                                $select_image_query_stmt->bind_param("i",$user['id']);
                                $select_image_query_stmt->execute();
                                $select_image_query_stmt->store_result();
                                $image  = fetch_assoc_all_values($select_image_query_stmt);

                                $user['image'] = $image['image'];

                                unset($user['password']);
                                $status = 1;
                                $posts[] = $user;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = 'Login successfully !!!';
                                $data['user'] = $posts;
                                return $data;
                            }
                            else
                            {
                                $status = 2;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = 'Please try again !!!';
                                return $data;
                            }

                        }
                        else
                        {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = 'Please try again !!!';
                            return $data;
                        }

                    }
                    else
                    {
                        $tokenData = new stdClass;
                        $tokenData -> GUID = $user['guid'];
                        $tokenData -> userId = $user['id'];

                        $security=new SecurityFunctions();
                        $user_token = $security->updateTokenforUser($tokenData);
                        if($user_token['status'] == 1) {
                            $data['usertoken'] = $user_token['UserToken'];
                        }

                        //Select image

                        $select_image_query = "select * from " . TABLE_USER_IMAGES . " where user_id = ? and is_delete='0'";
                        $select_image_query_stmt = $connection->prepare($select_image_query);
                        $select_image_query_stmt->bind_param("i",$user['id']);
                        $select_image_query_stmt->execute();
                        $select_image_query_stmt->store_result();
                        $image  = fetch_assoc_all_values($select_image_query_stmt);

                        $user['image'] = $image['image'];

                        unset($user['password']);
                        $status = 1;
                        $posts[] = $user;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = 'Login successfully !!!';
                        $data['user'] = $posts;
                        return $data;
                    }

                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Your Id or Password not matched !!!";
                    $data['user'] = $posts;
                    return $data;

                }
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Please try again!!!";
                $data['user'] = $posts;
                return $data;

            }
        }
    }

    public function registerUser($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();

        $errorMsg = "Service responce data";
        $userrole = validateObject($userData, 'userrole', "");
        $userrole = addslashes($userrole);

        $firstname = validateObject($userData, 'firstname', "");
        $firstname = addslashes($firstname);

        $lastname = validateObject($userData, 'lastname', "");
        $lastname = addslashes($lastname);

        $username = validateObject($userData, 'username', "");
        $username = addslashes($username);

        $email = validateObject($userData, 'email', "");
        $email = addslashes($email);

        $password = validateObject($userData, 'password', "");
        $password = encryptPassword($password);

        $gender = validateObject($userData, 'gender', "");
        $gender = addslashes($gender);

        $phoneno = validateObject($userData, 'phoneno', "");
        $phoneno = addslashes($phoneno);

        $dob = validateObject($userData, 'dob', "");
        $dob = addslashes($dob);

        $devicetype = validateObject($userData, 'devicetype', "");
        $devicetype = addslashes($devicetype);

        $deviceid = validateObject($userData, 'deviceid', "");
        $deviceid = addslashes($deviceid);

        $selfipic = validateObject($userData, 'selfipic', "");
        $selfipic = addslashes($selfipic);

        $secret_key = validateObject($userData, 'secret_key', "");
        $secret_key = addslashes($secret_key);

        $access_key = validateObject($userData, 'access_key', "");
        $access_key = addslashes($access_key);

        $security=new SecurityFunctions();
        $isSecure = $security->checkForSecurityNew($access_key,$secret_key);

        $isdeleted = '0';

        if ($isSecure == 'no')
        {
            $data['error_status']= MALICIOUS_SOURCE;
            $data['status'] = FAILED;
        }
        else
        {
//            if(strlen($phoneno) > 0)
//            {
//                $select_phone_query = "Select phone_no from " . TABLE_USER . "  where phone_no = ? and is_delete = ? ";
//                $select_phone_stmt = $connection->prepare($select_phone_query);
//                $select_phone_stmt->bind_param("ss", $phoneno,$isdeleted);
//                $select_phone_stmt->execute();
//                $select_phone_stmt->store_result();
//
//                if ($select_phone_stmt->num_rows > 0) {
//                    $select_phone_stmt->close();
//                    $status = 2;
//                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                    $data['message'] = "Phone number already exists !!!";
//                    $data['usertoken'] = '';
//                    $data['user'] = $posts;
//                    return $data;
//                }
//            }


            if(strlen($email) > 0) {
                $select_email_query = "Select email from " . TABLE_USER . "  where email = ? and is_delete = ?";

                $select_email_stmt = $connection->prepare($select_email_query);
                $select_email_stmt->bind_param("ss", $email,$isdeleted);
                $select_email_stmt->execute();
                $select_email_stmt->store_result();

                if ($select_email_stmt->num_rows > 0) {
                    $select_email_stmt->close();
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Email address already exists !!!";
                    $data['usertoken'] = '';
                    $data['user'] = $posts;
                    return $data;
                }
            }

            if(strlen($username) > 0)
            {
                $select_username_query = "Select username from " . TABLE_USER . "  where username = ? and is_delete = ?";
                $select_username_stmt = $connection->prepare($select_username_query);
                $select_username_stmt->bind_param("ss", $username,$isdeleted);
                $select_username_stmt->execute();
                $select_username_stmt->store_result();

                if ($select_username_stmt->num_rows > 0) {
                    $select_username_stmt->close();
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "User name already exists !!!";
                    $data['usertoken'] = '';
                    $data['user'] = $posts;
                    return $data;
                }
            }


                $generate_guid=$security->gen_uuid();
                include_once 'GlobalFunction.php';
                $objGlobalFunction = new GlobalFunction();
                $accesscode = $objGlobalFunction -> generateBase32UniqueString();

                $insertFields = "user_role,
                             guid,
                             accesscode,
                             firstname,
                             lastname,
                             username,
                             email,
                             password,
                             gender,
                             phone_no,
                             dob,
                             device_type,
                             device_id,
                             modified,
                             last_loggedin,
                             login_count,
                             is_active
                             ";


                $valuesFields = "?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? ";
                $insert_query = ""."Insert into " . TABLE_USER . " (" . $insertFields . ") values (" .$valuesFields.")";

                $select_insert_stmt = $connection->prepare($insert_query);
                $modifieddate = date("Y-m-d H:i:s");
                $logincount = "1";
                $isactive = "1";


                $select_insert_stmt->bind_param('issssssssssssssii',$userrole,$generate_guid,$accesscode,$firstname,$lastname,
                    $username,$email,$password,$gender,$phoneno,$dob,$devicetype,$deviceid, $modifieddate, $modifieddate,$logincount, $isactive );


                if($select_insert_stmt) {
                    if ($select_insert_stmt->execute())
                    {
                        $select_insert_stmt->close();
                        $user_inserted_id = mysqli_insert_id($connection);

                        //Update Token for user
                        $tokenData = new stdClass;
                        $tokenData -> GUID = $generate_guid;
                        $tokenData -> userId = $user_inserted_id;

                        $user_token = $security->updateTokenforUser($tokenData);
                        if($user_token['status'] == 1){
                            $data['usertoken'] = $user_token['UserToken'];
                        }


                        if (strlen($selfipic) > 0) {

                            //Selfie Pic
                            $selfi_image_name = 'profile_' . date("Y_m_d_H_i_s") . ".png";
                            $selfi_image_upload_dir = SELFI_IMAGES . $selfi_image_name;
                            file_put_contents($selfi_image_upload_dir, base64_decode($selfipic));

                            $insertFields = "user_id,
                                    image,
                                    modified";
                            $valuesFields = "?,?,?";
                            $modifieddate = date("Y-m-d H:i:s");
                            $insert_query = "Insert into " . TABLE_USER_IMAGES . " (" . $insertFields . ") values(" . $valuesFields . ")";
                            $insert_pic_stmt = $connection->prepare($insert_query);
                            $insert_pic_stmt->bind_param("iss",$user_inserted_id,$selfi_image_name,$modifieddate);
                            if($insert_pic_stmt->execute())
                            {
                          
                                $pic_inserted_id = mysqli_insert_id($connection);
                                $insert_pic_stmt->close();
                                
                                //Post Face
                                $this->post_face($selfi_image_name,$user_inserted_id);
                                
                                $update_query = "Update " . TABLE_USER . " set profile_image_id = ?, bio_selfie_id = ? where id = ? ";
                                $update_user_stmt = $connection->prepare($update_query);
                                $update_user_stmt->bind_param("iii",$pic_inserted_id,$pic_inserted_id,$user_inserted_id);
                                if($update_user_stmt->execute())
                                {
                                    $update_user_stmt->close();

                                    $select_user_query = "select user.*,imguser.image from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . "
                                    as imguser on imguser.id =  user.profile_image_id
                                    where user.id =  ?
                                    and imguser.is_delete='0'";
                                    $select_user_stmt = $connection->prepare($select_user_query);
                                    $select_user_stmt->bind_param("i",$user_inserted_id);
                                    if($select_user_stmt->execute())
                                    {
                                        $select_user_stmt->store_result();
                                        if($select_user_stmt->num_rows > 0)
                                        {
                                            $posts[] = fetch_assoc_all_values($select_user_stmt);
                                            $select_user_stmt->close();

                                            //Send Verification Mail
                                            $this->sendEmailVerificatoin($user_inserted_id,$username,$email);

                                            $status = 1;
                                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                            $data['message'] = "User register successfully !!!";
                                            $data['user'] = $posts;
                                            //return $data;
                                        }

                                    }
                                    else
                                    {
                                        $status = 2;
                                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                        $data['message'] = "Please try again !!!". $select_user_stmt->error;
                                        $data['user'] = $posts;
                                        //return $data;

                                    }

                                }
                                else
                                {
                                    $status = 2;
                                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                    $data['message'] = "Please try again !!!". $update_user_stmt->error;
                                    $data['user'] = $posts;
                                    //return $data;

                                }
                            }
                            else
                            {
                                $status = 2;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "Please try again !!!". $insert_pic_stmt->error;
                                $data['user'] = $posts;
                                //return $data;

                            }
                        }
                        else
                        {


                            $select_user_query = "select user.*,imguser.image from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . " as imguser
                            on imguser.id =  user.profile_image_id where user.id =  ? and imguser.is_delete='0'";

//                            $select_user_query = "Select * from " . TABLE_USER . " where id = ?";
                            $select_user_stmt = $connection->prepare($select_user_query);
                            $select_user_stmt->bind_param("i",$user_inserted_id);
                            if($select_user_stmt->execute())
                            {
                                $select_user_stmt->store_result();

                                if($select_user_stmt->num_rows > 0)
                                {
                                    $posts[] = fetch_assoc_all_values($select_user_stmt);
                                    $select_user_stmt->close();

                                    //Send Verification Mail
                                    $this->sendEmailVerificatoin($user_inserted_id,$username,$email);

                                    $status = 1;
                                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                    $data['message'] = "User register successfully !!!";
                                    $data['user'] = $posts;
                                   // return $data;
                                }

                            }
                            else
                            {
                                $status = 2;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "Please try again !!!". $select_insert_stmt->error;
                                $data['user'] = $posts;
                                //return $data;

                            }
                        }
                    }
                    else
                    {

                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again !!!". $select_insert_stmt->error;
                        $data['user'] = $posts;
//                        echo $errorMsg;exit;
                    }
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again !!!". $select_insert_stmt->error;
                    $data['user'] = $posts;
                }

            }

            if($isSecure != "yes"){
                if($isSecure['key'] == "Temp"){
                    $data['tempToken']= $isSecure['value'];
                } else {
                    $data['usertoken'] = $isSecure['value'];
                }
            }
            return $data;
        }

    public function loginWithFacebook($userData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;

        $facebookid = validateObject($userData, 'facebookid', "");
        $facebookid = addslashes($facebookid);

        $userrole = validateObject($userData, 'userrole', "");
        $userrole = addslashes($userrole);

        $firstname = validateObject($userData, 'firstname', "");
        $firstname = addslashes($firstname);

        $lastname = validateObject($userData, 'lastname', "");
        $lastname = addslashes($lastname);

        $username = validateObject($userData, 'username', "");
        $username = addslashes($username);

        $email = validateObject($userData, 'email', "");
        $email = addslashes($email);

        $gender = validateObject($userData, 'gender', "");
        $gender = addslashes($gender);

        $phoneno = validateObject($userData, 'phoneno', "");
        $phoneno = addslashes($phoneno);

        $dob = validateObject($userData, 'dob', "");
        $dob = addslashes($dob);

        $devicetype = validateObject($userData, 'devicetype', "");
        $devicetype = addslashes($devicetype);

        $deviceid = validateObject($userData, 'deviceid', "");
        $deviceid = addslashes($deviceid);

        $selfipic = validateObject($userData, 'selfipic', "");
        $selfipic = addslashes($selfipic);

        $posts = array();

        $errorMsg = "";

        $security=new SecurityFunctions();

        $select_query = "Select * from " . TABLE_USER . " where facebook_id = ? or email = ? or username = ?";

        $select_user_stmt = $connection->prepare($select_query);
        $select_user_stmt->bind_param("sss",$facebookid,$email,$username);
        $select_user_stmt->execute();
        $select_user_stmt->store_result();

        if ($select_user_stmt->num_rows > 0) {

            $user = fetch_assoc_all_values($select_user_stmt);
            //update login count

            $login_count = intval($user['login_count']) + 1;

            $update_query = "Update " . TABLE_USER . " set
                             user_role = ? ,
                             firstname = ? ,
                             lastname = ?,
                             gender=? ,
                             phone_no=?,
                             dob = ? ,
                             facebook_id = ?,
                             device_type = ?,
                             device_id = ?,
                             last_loggedin = ?,
                             login_count = ?
                             where facebook_id = ? or email = ? or username = ?";

            $update_query_stmt = $connection->prepare($update_query);
            $currentdate = date("Y-m-d H:i:s");
            $update_query_stmt->bind_param("issssssississs",$userrole,$firstname,$lastname,$gender,$phoneno,$dob,$facebookid,$devicetype,$deviceid,
                $currentdate,$login_count,$facebookid,$email,$username);

            if($update_query_stmt->execute())
            {
                if (strlen($selfipic) > 0) {
                    //Selfie Pic
                    $selfi_image_name = 'profile_' . date("Y-m-d_H_i_s") . ".png";
                    $selfi_image_upload_dir = SELFI_IMAGES . $selfi_image_name;
                    file_put_contents($selfi_image_upload_dir, base64_decode($selfipic));

                    //Post Face
                    $this->post_face($selfi_image_name,$user['id']);

                    $update_query = "Update " . TABLE_USER_IMAGES . " set image = ? where user_id = ? ";
                    $update_user_stmt = $connection->prepare($update_query);

                    $update_user_stmt->bind_param("si",$selfi_image_name,$user['id']);
                    $update_user_stmt->execute();
                }

                $select_fb_user = "select user.*,imguser.image from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . " as imguser on imguser.id =  user.profile_image_id where
                user.facebook_id =  ?";
//              echo $select_fb_user;exit;

                $select_fb_user_stmt = $connection->prepare($select_fb_user);
                $select_fb_user_stmt->bind_param("s",$facebookid);
                $select_fb_user_stmt->execute();
                $select_fb_user_stmt->store_result();
                if ($select_fb_user_stmt->num_rows > 0) {
                    unset($user);
                    $user = fetch_assoc_all_values($select_fb_user_stmt);
                    unset($user['password']);
                    $status = 1;
                    $posts[] = $user;

                    $tokenData = new stdClass;
                    $tokenData -> GUID = $user['guid'];
                    $tokenData -> userId = $user['id'];

                    $security=new SecurityFunctions();
                    $user_token = $security->updateTokenforUser($tokenData);
                    if($user_token['status'] == 1) {
                        $data['usertoken'] = $user_token['UserToken'];
                    }


                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "User Login successfully !!!";
                    $data['user'] = $posts;
                    return $data;
                }
                else{
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Some thing went wrong please try again";
                    $data['user'] = $posts;
                    return $data;
                }

            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Please try again !!!". $update_query_stmt->error;
                $data['user'] = $posts;
                return $data;
            }
        }
        else {
            //Check if Email already exists

            $select_user_query = "Select * from " . TABLE_USER . " where email = ? ";
            $select_user_query_stmt = $connection->prepare($select_user_query);
            $select_user_query_stmt->bind_param("s",$email);
            $select_user_query_stmt->execute();
            $select_user_query_stmt->store_result();

            if ($select_user_query_stmt->num_rows > 0) {
                $user = fetch_assoc_all_values($select_user_query_stmt);
                $login_count = intval($user['login_count']) + 1;

                $update_query = "Update " . TABLE_USER . "
                set facebook_id = ?,
                login_count = ?
                where email = ? ";
                $update_query_stmt = $connection->prepare($update_query);
                $update_query_stmt->bind_param("sis",$facebookid,$login_count,$email);

                if( $update_query_stmt->execute())
                {
                    $select_user_query = "select user.*,imguser.image from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . " as imguser on imguser.id =  user.profile_image_id where user.email =  ?";
//                    $select_user_query = "Select * from " . TABLE_USER . " where email = ? ";
                    $select_user_query_stmt = $connection->prepare($select_user_query);
                    $select_user_query_stmt->bind_param("s",$email);
                    $select_user_query_stmt->execute();
                    $select_user_query_stmt->store_result();
                    $user = fetch_assoc_all_values($select_user_query_stmt);
                    unset($user['password']);

                    $errorMsg = "User Login successfully !!!";
                    $posts[] = $user;

                    $tokenData = new stdClass;
                    $tokenData -> GUID = $user['guid'];
                    $tokenData -> userId = $user['id'];

                    $security=new SecurityFunctions();
                    $user_token = $security->updateTokenforUser($tokenData);
                    if($user_token['status'] == 1) {
                        $data['usertoken'] = $user_token['UserToken'];
                    }

                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "User Login successfully !!!";
                    $data['user'] = $posts;
                    return $data;
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again !!!". $update_query_stmt->error;
                    $data['user'] = $posts;
                    return $data;
                }
            } else {


                $generate_guid=$security->gen_uuid();
                include_once 'GlobalFunction.php';
                $objGlobalFunction = new GlobalFunction();
                $accesscode = $objGlobalFunction -> generateBase32UniqueString();

                $insertFields = "facebook_id,
                             guid,
                             accesscode,
                             user_role,
                             firstname,
                             lastname,
                             username,
                             email,
                             gender,
                             phone_no,
                             dob,
                             device_type,
                             device_id,
                             modified,
                             last_loggedin,
                             login_count,
                             is_active
                             ";

                $valuesFields = "?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?";

                $insert_query = "Insert into " . TABLE_USER . " (" . $insertFields . ") values(" . $valuesFields . ")";
                $insert_query_stmt = $connection->prepare($insert_query);
                $currentdate = date("Y-m-d H:i:s");
                $logincount = 1;
                $isactive =1;
                $insert_query_stmt->bind_param("sssisssssssisssii",$facebookid,$generate_guid,$accesscode,$userrole,$firstname,$lastname,$username,$email,$gender,$phoneno,$dob,$devicetype,$deviceid,$currentdate,$currentdate,$logincount,$isactive);
                if($insert_query_stmt->execute())
                {
                    $user_inserted_id = mysqli_insert_id($connection);

                    if (strlen($selfipic) > 0) {

                        //Selfie Pic
                        $selfi_image_name = 'profile_' . date("Y-m-d_H_i_s") . ".png";
                        $selfi_image_upload_dir = SELFI_IMAGES . $selfi_image_name;
                        file_put_contents($selfi_image_upload_dir, base64_decode($selfipic));

                        $insertFields = "user_id,
                                    image,
                                    modified";
                        $valuesFields = "?,?,?";
                        $modifieddate = date("Y-m-d H:i:s");
                        $insert_query = "Insert into " . TABLE_USER_IMAGES . " (" . $insertFields . ") values(" . $valuesFields . ")";
                        $insert_pic_stmt = $connection->prepare($insert_query);
                        $insert_pic_stmt->bind_param("iss",$user_inserted_id,$selfi_image_name,$modifieddate);
                        if($insert_pic_stmt->execute())
                        {

                            $pic_inserted_id = mysqli_insert_id($connection);
                            $insert_pic_stmt->close();
                            
                            //Post Face
                            $this->post_face($selfi_image_name,$user_inserted_id);
                            
                            $update_query = "Update " . TABLE_USER . " set profile_image_id = ?, bio_selfie_id = ? where id = ? ";
                            $update_user_stmt = $connection->prepare($update_query);
                            $update_user_stmt->bind_param("iii",$pic_inserted_id,$pic_inserted_id,$user_inserted_id);
                            if($update_user_stmt->execute())
                            {
                                $update_user_stmt->close();
                                $select_user_query = "select user.*,imguser.image from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . " as imguser on imguser.id =  user.profile_image_id where user.id =  ?";
//                                $select_user_query = "Select * from " . TABLE_USER . " where id = ?";

                                $select_user_stmt = $connection->prepare($select_user_query);
                                $select_user_stmt->bind_param("i",$user_inserted_id);
                                if($select_user_stmt->execute())
                                {
                                    $select_user_stmt->store_result();
                                    if($select_user_stmt->num_rows > 0)
                                    {
                                        //$posts[] = fetch_assoc_all_values($select_user_stmt);
                                        $user = fetch_assoc_all_values($select_user_stmt);

//                                        print_r($user);
//                                        exit;

                                        $tokenData = new stdClass;
                                        $tokenData -> GUID = $user['guid'];
                                        $tokenData -> userId = $user['id'];

                                       // print_r($tokenData);exit;
                                        $security=new SecurityFunctions();
                                        $user_token = $security->updateTokenforUser($tokenData);

//                                        print_r($user_token);
//                                        exit;
                                        if($user_token['status'] == 1) {
                                            $data['usertoken'] = $user_token['UserToken'];
                                        }


                                        //Send Verification Mail
                                        $this->sendEmailVerificatoin($user['id'],$username,$email);

                                        $status = 1;
                                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                        $data['message'] = "User register successfully 1!!!";
                                        $data['isNewUser'] = "YES";
                                        $posts[] = $user;
                                        $data['user'] = $posts;
                                        return $data;
                                    }

                                }
                                else
                                {
                                    $status = 2;
                                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                    $data['message'] = "Please try again !!!". $select_user_stmt->error;
                                    $data['user'] = $posts;
                                    return $data;

                                }

                            }
                            else
                            {
                                $status = 2;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "Please try again !!!". $update_user_stmt->error;
                                $data['user'] = $posts;
                                return $data;

                            }
                        }
                        else
                        {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Please try again !!!". $insert_pic_stmt->error;;
                            $data['user'] = $posts;
                            return $data;

                        }
                    }
                    else
                    {
                        $select_user_query = "select user.*,imguser.image from " . TABLE_USER ." as user JOIN " . TABLE_USER_IMAGES . " as imguser on imguser.id =  user.profile_image_id where user.id =  ?";
//                        $select_user_query = "Select * from " . TABLE_USER . " where id = ?";
                        $select_user_stmt = $connection->prepare($select_user_query);
                        $select_user_stmt->bind_param("i",$user_inserted_id);
                        if($select_user_stmt->execute())
                        {
                            $select_user_stmt->store_result();
                            if($select_user_stmt->num_rows > 0)
                            {
                                $posts[] = fetch_assoc_all_values($select_user_stmt);
                                $user = fetch_assoc_all_values($select_user_stmt);
                                $select_user_stmt->close();

                                $tokenData = new stdClass;
                                $tokenData -> GUID = $user['guid'];
                                $tokenData -> userId = $user['id'];

                                $security=new SecurityFunctions();
                                $user_token = $security->updateTokenforUser($tokenData);
                                if($user_token['status'] == 1) {
                                    $data['usertoken'] = $user_token['UserToken'];
                                }

                                //Send Verification Mail
                                $this->sendEmailVerificatoin($user['id'],$username,$email);

                                $status = 1;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "User register successfully !!!";
                                $data['isNewUser'] = "YES";

                                $data['user'] = $posts;
                                return $data;
                            }

                        }
                        else
                        {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Please try again !!!". $select_user_stmt->error;
                            $data['user'] = $posts;
                            return $data;

                        }
                    }
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again  !!!" . $insert_query_stmt ->error;
                    $data['user'] = $posts;
                    return $data;
                }
            }
        }
        $status = 2;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = $errorMsg;
        $data['user'] = $posts;
        return $data;
    }
    
    public function detect($photo) {
        $URL = '52.236.81.69/detect';
        $fields = [];

        $filenames = array($photo);

        $files = array();
        foreach ($filenames as $f) {
            $files['photo'] = file_get_contents($f);
        }

        // curl
        $curl = curl_init();

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = $this->build_data_files($boundary, $fields, $files);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token 4582f5bb9047164799aa283de40a0365a591aa67f865bb4459198bf838eb065d",
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)
            ),
        ));

        $result = curl_exec($curl);
        if ($result === false) {
            $response = array('curl_error' => curl_error($curl));
        } else {
            $response = json_decode($result, 1);
        }
        curl_close($curl);

        return $response;
    }
    
    public function adddossier($dossierlist_id, $dossier_name) {
        $URL = '52.236.81.69/dossiers/';
        $data = json_encode(array('active' => 'true', 'name' => $dossier_name, 'dossier_lists' => [$dossierlist_id]));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Token 4582f5bb9047164799aa283de40a0365a591aa67f865bb4459198bf838eb065d'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }
    
    public function adddossierface($dossier_id, $detection_id, $image) {
        $fields = ['dossier' => $dossier_id, 'create_from' => 'detection:' . $detection_id];

        $filenames = array($image);

        $files = array();
        foreach ($filenames as $f) {
            $files['source_photo'] = file_get_contents($f);
        }

        // URL to upload to
        $url = "52.236.81.69/dossier-faces/";

        // curl
        $curl = curl_init();

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = $this->build_data_files($boundary, $fields, $files);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token 4582f5bb9047164799aa283de40a0365a591aa67f865bb4459198bf838eb065d",
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)
            ),
        ));

        $result = curl_exec($curl);
        if ($result === false) {
            $response = array('curl_error' => curl_error($curl));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }
    
    public function build_data_files($boundary, $fields, $files) {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                    . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol
                    . $content . $eol;
        }

        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                    . 'Content-Disposition: form-data; name="' . $name . '"; filename="58ac19df989a31487673823.jpeg"' . $eol
                    . 'Content-Transfer-Encoding: binary' . $eol
            ;

            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--" . $eol;
        return $data;
    }
    
    public function delete_dossier($dossier_id) {
        $URL = '52.236.81.69/dossiers/' . $dossier_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token 4582f5bb9047164799aa283de40a0365a591aa67f865bb4459198bf838eb065d', 'Content-Length: 0'));
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

}

?>