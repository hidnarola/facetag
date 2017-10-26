<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 03/03/15
 * Time: 4:10 PM
 */

include_once 'config.php';
include_once 'Paths.php';
include_once 'TableVars.php';
include_once 'ConstantValues.php';
include_once 'HelperFunctions.php';
include_once 'SecurityFunctions.php';
include_once 'Notification.php';
include_once 'Push.php';


$post_body = file_get_contents('php://input');
$post_body = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($post_body));
$reqData[] = json_decode($post_body);

$postData = $reqData[0];

$debug = 0;
$logger->Log($debug, 'POST DATA :', $postData);
$status = "";
error_reporting(-1);
ini_set('display_errors', 1);
$logger->Log($debug, 'Service :', $_REQUEST['Service']);

switch ($_REQUEST['Service']) {
    /*********************  User Functions ******************************/

    case "sendPushIOS":
    {
        include_once 'GCM.php';
        $objPush = new GCM();
        $data = $objPush->call_service($_REQUEST['Service'], $postData);
    }
        break;

    case "sendPushNotification":
    {
        include_once 'androidPush.php';
        $objAndroidPush = new androidPush();
        $data = $objAndroidPush->call_service($_REQUEST['Service'], $postData);
    }
        break;
    case "GetBusinessLocationData":
    {
        include_once 'BusinessFunction.php';
        $objBusiness = new BusinessFunction();
        $data = $objBusiness->call_service($_REQUEST['Service'], $postData);
    }
        break;

    case "refreshToken":
    case "updateTokenforUser":
    {
        include_once 'SecurityFunctions.php';
        $security = new SecurityFunctions();
        $data = $security->call_service($_REQUEST['Service'], $postData);
    }
        break;

    case "GetAllUserVerifiedSelfieClone":
    case "ReSendEmailVerificatoinMail":
    case "sendEmailVerificatoinMail":
    case "Login":
    case "Register":
    case "LoginWithFB":
    case "EditProfile":
    case "GetUserSpecificImages":
    case "ChangePassword":
    case "UserExistWithEmail":
    case "UserExistWithFacebookID":
    case "GetDetectedSelfie":
    case "GetBusinessVerifiedSelfie":
    case "GetCheckinBusiness":
    case "CheckOutBusiness":
    case "UpdateSelfieVerification":
    case "GetAllUserVerifiedSelfie":
    case "GetAllUserVerifiedSelfieTemp":
    case "UpdateUserSelfie":
    case "SearchSelfie":
    case "searchSelfieNew":
    case "LogoutUser":
    case "GetAllNonExpireSelfie":
    case "ForgotPassword":

    {
//        $isSecure= (new SecurityFunctions())->checkForSecurityNew($postData->access_key,$postData->secret_key);
        $isSecure = 'yes';
        if ($isSecure == 'no') {
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = MALICIOUS_SOURCE;

        }
        elseif($isSecure == 'error'){
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = TOKEN_ERROR;
        }
        else {
            include_once 'UserFunctions.php';
            $user = new UserFunctions();
            $data = $user->call_service($_REQUEST['Service'], $postData);

            if ($isSecure != 'yes' || $isSecure != 'yes') {
                if ($isSecure['key'] == "Temp") {
                    $data['temptoken'] = $isSecure['value'];
                } else {
                    $data['usertoken'] = $isSecure['value'];
                }
            }
        }
    }
        break;

    case "GetSpecificBusinessDetails":
    case "DeleteSelfie":
    case "GetAllBusiness":
    case "SearchBusiness":
    case "CheckInBusiness":
    case "GetCurrentBusiness":
    case "GetBusinessImage":
    case "GetBusinessICP":
    case "GetBusinessSpecificSelfie":
    case "ToggleBusinessLikes":
    case "ToggleBusinessFavorites":
    case "GetAllCheckinBusiness":
    case "GetBusinessSearchICP":
    case "GetBusinessHotel":
    case "GetNearByBusiness":
    {
        //$isSecure= (new SecurityFunctions())->checkForSecurityNew($postData->access_key,$postData->secret_key);
        $isSecure = 'yes';

        if ($isSecure == 'no') {
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = MALICIOUS_SOURCE;

        }
        elseif($isSecure == 'error'){
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = TOKEN_ERROR;
        }
        else {
        include_once 'BusinessFunction.php';
        $objBusiness = new BusinessFunction();
        $data = $objBusiness->call_service($_REQUEST['Service'], $postData);
            if ($isSecure != 'yes' || $isSecure != 'yes') {
                if ($isSecure['key'] == "Temp") {
                    $data['temptoken'] = $isSecure['value'];
                } else {
                    $data['usertoken'] = $isSecure['value'];
                }
            }
        }
    }
        break;

    case "SaveCardDetails":
    case "GetCardDetails":
    case "EditCard":
    case "DeleteCard":
    {
        //$isSecure= (new SecurityFunctions())->checkForSecurityNew($postData->access_key,$postData->secret_key);
        $isSecure = 'yes';
        if ($isSecure == 'no') {
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = MALICIOUS_SOURCE;

        }
        elseif($isSecure == 'error'){
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = TOKEN_ERROR;
        }
        else {
            include_once 'PaymentFunction.php';
            $objPayment = new PaymentFunction();
            $data = $objPayment->call_service($_REQUEST['Service'], $postData);

            if ($isSecure != 'yes' || $isSecure != 'yes') {
                if ($isSecure['key'] == "Temp") {
                    $data['temptoken'] = $isSecure['value'];
                } else {
                    $data['usertoken'] = $isSecure['value'];
                }
            }

        }

    }
        break;

    case "MakePaymentWithStripe":
    case "MakePaymentWithPayPal":
    case "SaveOrder":
    case "SaveOrderData":
    case "CancelOrder":
    case "GetUserOrderData":
    case "PutOrder":
    case "PurchaseFreeImage":
    case "PurchaseWithPaypal":
    case "GetUserOrder":
    {
        //$isSecure= (new SecurityFunctions())->checkForSecurityNew($postData->access_key,$postData->secret_key);
        $isSecure = 'yes';
        if ($isSecure == 'no') {
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = MALICIOUS_SOURCE;

        }
        elseif($isSecure == 'error'){
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = TOKEN_ERROR;
        }
        else
        {
            include_once 'OrderFunction.php';
            $objOrder = new OrderFunction();
            $data = $objOrder->call_service($_REQUEST['Service'], $postData);
            if ($isSecure != 'yes' || $isSecure != 'yes')
            {
                if ($isSecure['key'] == "Temp") {
                    $data['temptoken'] = $isSecure['value'];
                } else {
                    $data['usertoken'] = $isSecure['value'];
                }
            }
        }
    }
        break;

    case "GetShippingDetails":
    case "AddShippingAddress":
    case "UpdateAddress":
    case "DeleteAddress":
    case "GetUserAddress":

    {
        //$isSecure= (new SecurityFunctions())->checkForSecurityNew($postData->access_key,$postData->secret_key);
        $isSecure = 'yes';
        if ($isSecure == 'no') {
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = MALICIOUS_SOURCE;

        }
        elseif($isSecure == 'error'){
            $data['Result']['status'] = FAILED;
            $data['Result']['error_status'] = TOKEN_ERROR;
        }
        else
        {
            include_once 'ShippingFunction.php';
            $objShipping = new ShippingFunction();
            $data = $objShipping->call_service($_REQUEST['Service'], $postData);
            if ($isSecure != 'yes' || $isSecure != 'yes')
            {
                if ($isSecure['key'] == "Temp") {
                    $data['temptoken'] = $isSecure['value'];
                } else {
                    $data['usertoken'] = $isSecure['value'];
                }
            }
        }
    }
        break;

    default: {
        $data['data'] = 'No Service Found';
        $data['message'] = $_REQUEST['Service'];
    }
        break;
}

header('Content-type: application/json');

echo json_encode($data);
mysqli_close($con);

?>