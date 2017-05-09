<?php
/**
 * Created by Kinjal Textwrangler.
 * User: c33
 * Date: 23/11/15
 * Time: 05:13 PM
 * To manage security related functions.
 */
include_once 'ApiCrypter.php';
include_once 'HelperFunctions.php';

class SecurityFunctions {

    function __construct()
    {

    }
    public function call_service($service, $postData)
    {
        switch($service)
        {
            case "refreshToken":
            {
                return $this->refreshToken($postData);
            }
                break;
            case "testEncryption":
            {
                return $this->testEncryption($postData);
            }
                break;
            case "updateTokenforUser":
            {
                return $this->updateTokenforUser($postData);
            }
                break;
        }
    }
    
 //============================================== Generate Random Unique Token Number =============================
     
	public function crypto_random_secure($min, $max)
	{
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);

        return $min + $rnd;
	}

	public function generateToken($length)
	{
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i=0; $i < $length; $i++) {
           $token .= $codeAlphabet[$this->crypto_random_secure(0, $max)];
        }

        return $token;
	}

    // USED METHODS
    public function refreshToken($userData){


        $access_key = validateObject($userData, 'access_key', "");
        $access_key = addslashes($access_key);

        $security=new SecurityFunctions();
        $isSecure = $security->checkForSecurityForRefreshToken($access_key,"");

        if ($isSecure == 'no')
        {
            $status=FAILED;
            $message = MALICIOUS_SOURCE;

        }
        elseif($isSecure == 'error'){
            $status = FAILED;
            $message = TOKEN_ERROR;
        }
        else {
            if($isSecure != "yes"){
                if($isSecure['key'] == "Temp"){
                    $data['Result']['TempToken'] = $isSecure['value'];
                } else {
                    $data['Result']['UserToken'] = $isSecure['value'];
                }
            }

            $status = "DONE";
            $message = "Token is generated.";
        }

        $data['Result']['status'] = $status;
        $data['Result']['error_status'] = $message;
        return $data;
    }
    public  function getUserAgent(){
        $string=$_SERVER ['HTTP_USER_AGENT'];
        $data['Result']['User_agent']=$string;
    return $data;
}
    public function testEncryption($userData){

        $simpleText = validateValue ($userData -> simpleText, "");

        $encryptedTextFromInput = validateValue($userData -> encrypted, "");

        $encryptionKey = validateValue($userData -> encryptionKey, "");
        $decryptionKey = validateValue($userData -> decryptionKey, "");

//        print_r($userData);


        //  echo '  Current PHP version: ' . phpversion();
        $security = new Security();
        $encrpt_acesskey = $security->encrypt("dfa2917e-3b62-45a8-a634-b5d0e837","_$(TechBuddy)!!AG==XHG10_08_2016");
        echo 'encrpted=> '.$encrpt_acesskey;

        exit;


        $s=mysqli_query($GLOBALS['connection'],"select uid from tbluser");

        while($row=mysqli_fetch_assoc($s)){
            $guid=$this->gen_uuid();
            echo "userid= ".$row['uid']." guid= ".$guid."\n";
            $u="update tbluser set guid='".$guid."' where uid=".$row['uid'];
            //$result = mysqli_query($GLOBALS['connection'], $u) or $message = mysqli_error($GLOBALS['con']);
        }
        echo "Done";
        exit;



        //
        $e = $security->encrypt("NgXLKwZf","4c458e37-fa11-49ae-b029-47961882");
        $e1 = $security->encrypt("2016-05-12","4c458e37-fa11-49ae-b029-47961882");
        $ds=$e."_".$e1;

        $df=explode("_",$ds);
        $d = $security->decrypt($df[0],"4c458e37-fa11-49ae-b029-47961882");
        if($d == "NgXLKwZf")
        {
             echo 'Got';
         }
         else{
            echo 'Not Got';
        }
         exit;
        //

        $s=mysqli_query($GLOBALS['connection'],"select * from tbluser ");

        while($row=mysqli_fetch_assoc($s)){
            $guid=$this->gen_uuid();
            $u="update tbluser set guid='".$guid."' where uid=".$row['uid'];
            $result = mysqli_query($GLOBALS['con'], $u) or $message = mysqli_error($GLOBALS['con']);
        }
        exit;

        $decryptedTextForEncrypted = $security->decrypt($encryptedTextFromInput, $decryptionKey);

        $encryptedText = $security->encrypt($simpleText, $encryptionKey);

        $decryptedText = $security->decrypt($encryptedText, $decryptionKey);

        $query = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='globalPassword' AND is_delete=0";
        $result = mysqli_query($GLOBALS['con'], $query) or $message = mysqli_error($GLOBALS['con']);
        $masterKey = mysqli_fetch_row($result);

        //step1= decrpt accesskey with global password

        $security = new Security();
        $my_key = $security->encrypt('4c458e37-fa11-49ae-b029-47961882d95a','_$(TechBuddy)!!AG==XHG10_08_2016');

        echo '\n'.' my encrpted key=>'.$my_key."->Done"; exit;
        echo  "encrypted ...." .$encryptedTextFromInput ."\n";
        echo  "master ...." .$masterKey[0] ."\n";
        $decrypted_Key = $security->decrypt($encryptedTextFromInput, $masterKey[0]);
//        1HnMaDzMBDf7OWzkxalJqI65t/oWHpT3pIaWPRw7s1E=

//        $guid = $this->gen_uuid();

        $data['simple'] = $simpleText;
        $data['encrypted'] = $encryptedText;
        $data['decryptedAccessKEy'] = $decryptedText;
        $data['decryptedTextByDecryptionKey'] = $decryptedTextForEncrypted;
        $data['decryptedTextByMaster'] = $decrypted_Key;
//        $data['gen_uuid']= $guid;

        return $data;
    }



    public function generateNewTokenForUser($userData)
    {

        $user_id = validateValue($userData['userId'],'');

        $tokenName = '';

        if($user_id != '') {

//            echo "USER ID" . $user_id;

            $generateToken = $this->generateToken(8);
            $insertTokenField = "`user_id`, `token`";
            $insertTokenValue = "" . $user_id . ",'" . $generateToken . "'";

            $queryAddToken = "INSERT INTO " . tblAppTokens . "(" . $insertTokenField . ") values (" . $insertTokenValue . ")";
            $resultAddToken = mysqli_query($GLOBALS['con'], $queryAddToken) or $message = mysqli_error($GLOBALS['con']);

            $guid = validateValue($userData['guid'],'');

            $security = new Security();
            $tokenName = $security->encrypt($generateToken, $guid);

//            echo "token ID" . $tokenName;

        }
//

        return $tokenName;
    }

    public function expiredAllTokenofUser($userData)
    {
        $user_id = validateValue($userData['userId'],'');

        if($user_id != '') {

            $modifiedDate = date('Y-m-d H:i:s', time());

            $updateQuery = "update ". TABLE_41_APP_TOKENS ." set is_delete = 1, modified_date = '".$modifiedDate."' where user_id = '".$user_id."' ";
            $res = mysqli_query($GLOBALS['con'],$updateQuery) or die('Error:  '. mysqli_error($GLOBALS['con']));
            if ($res)
            {
                return "yes";
            }
        }
        return "no";
    }


    // USED METHODS
    public function updateTokenforUser($userData)
    {
        $connection=$GLOBALS['con'];
        $user_id = validateValue($userData->userId,'');

        if($user_id != '') {

            $modifiedDate = date('Y-m-d H:i:s', time());

            $generateToken = $this->generateToken(8);

            $query = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='expiry_duration' AND is_delete=0";
            if($stmt_get_config = $connection->prepare($query)) {
                $stmt_get_config->execute();
                $stmt_get_config->store_result();
                if ($stmt_get_config->num_rows > 0) {
                    while($val=fetch_assoc_all_values($stmt_get_config))
                    {
                        $expiryDuration = $val['config_value'];
                    }
                }
                $stmt_get_config->close();
            }

            $currentdate = date("dmyHis", time() + $expiryDuration);

//            echo "... Date before addition..".date("dmyHis")."....Date after addition..".$currentdate."....addition value...". $expiryDuration[0];

            $updateQuery = "update ". tblAppTokens ." set token = ? , expiry = ? , created_date = ? where user_id = ?";

            if ($update_query_stmt = $connection->prepare($updateQuery)) {
                $update_query_stmt->bind_param('sssi', $generateToken, $currentdate,$modifiedDate,$user_id);
                if ($update_query_stmt->execute()) {

                    $update_query_stmt->store_result();
                    //$username = validateValue($userData->userName,'');
                    // $uuid = validateValue($userData->UUID,'');
                    $uuid = validateValue($userData->GUID,'');

                    $security = new Security();

                    $generateTokenEncrypted = $security->encrypt($generateToken, $uuid);
                    $currentdateEncrypted = $security->encrypt($currentdate, $uuid);

                    $encryptedTokenName = $generateTokenEncrypted."_".$currentdateEncrypted;//$security->encrypt($mixedToken, $uuid."_".$username);

                    if ($update_query_stmt->affected_rows > 0) {

                        $data['UserToken'] = $encryptedTokenName;
                        $data['status'] = 1;
                        return $data;
//                    return $encryptedTokenName;
                    }
                    else {
                        $insertTokenField = "`user_id`, `token`, `expiry`";

                        $insertQuery = "Insert into " . tblAppTokens . " (" . $insertTokenField . ") values(?,?,?)";
                        if ($insert_stmt = $connection->prepare($insertQuery)) {

                            $insert_stmt->bind_param('iss', $user_id, $generateToken, $currentdate);
                            if ($insert_stmt->execute()) {
                                $insert_stmt->close();

                                $data['UserToken'] = $encryptedTokenName;
                                $data['status'] = 1;

                                //  echo ' first encrypted token=> '.$encryptedTokenName;
                                return $data;
            //                    return $encryptedTokenName;
                            }
                        }

                    }

                }
                else{
                    $data['status'] = 0;
                    $data['UserToken'] = "no";
                    return $data;
                    //                return no;
                }
            }
            $update_query_stmt->close();

        }
        $data['status'] = 0;
        $data['UserToken'] = "no";
        return $data;
        //return no;
    }

    // USED METHODS
    public function gen_uuid() {
        // return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        //Remove last 4 charcter from above string to make string of 32 characters long.
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    // USED METHODS
    public function checkForSecurityNew($accessvalue,$secretvalue)
    {
        $connection=$GLOBALS['con'];

        if($accessvalue == "" || $secretvalue == "")
        {
            return 'error';
        }
        else{
            // get user-agent from database

            $query = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='userAgent' AND is_delete=0";

            if($stmt_get_user_agent = $connection->prepare($query)) {
                $stmt_get_user_agent->execute();
                $stmt_get_user_agent->store_result();
                if ($stmt_get_user_agent->num_rows > 0) {
                    while($value=fetch_assoc_all_values($stmt_get_user_agent))
                    {
                        $user_agent = $value['config_value'];
                        $separateKey = (explode(',', $user_agent));
                    }
                }
                $stmt_get_user_agent->close();
            }

            // echo "Initial access key...." . $accessvalue;
            // check user-agent is valid
            if ((strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[0]) !== false) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[1]) !== false) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[2]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[3]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[4]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[5]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[6]) !== false ))
            {
                // get temporary token for user.
                $query_config = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='tempToken' AND is_delete=0";

                if($stmt_get_temp_token = $connection->prepare($query_config)) {
                    $stmt_get_temp_token->execute();
                    $stmt_get_temp_token->store_result();
                    if ($stmt_get_temp_token->num_rows > 0) {
                        while($val=fetch_assoc_all_values($stmt_get_temp_token)){
                            $tempToken = $val['config_value'];
                        }
                    }
                    $stmt_get_temp_token->close();
                }

                // get global password to encrypt temp token
                $query_global_pwd = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='globalPassword' AND is_delete=0";
                if($stmt_get_global_pwd = $connection->prepare($query_global_pwd)) {
                    $stmt_get_global_pwd->execute();
                    $stmt_get_global_pwd->store_result();
                    if ($stmt_get_global_pwd->num_rows > 0) {

                        while($val=fetch_assoc_all_values($stmt_get_global_pwd)){
                            $masterKey = $val['config_value'];

                            $security = new Security();

//            echo "nousername ......";


                            // check user request it with temporary credentials or private credential
                            if ($accessvalue == "nousername")
                            {

//                echo "nousername ......";
                                // check user passed temporary token or request with temporary token.
                                if ($secretvalue == NULL)
                                {
                                    // return encrypted token

                                    $secretvalue = $security->encrypt($tempToken, $masterKey);

                                    $response = array();
                                    $response['key'] = "Temp";// return temporary token
                                    $response['value'] = $secretvalue;
                                    return $response;
                                }
                                else
                                {
                                    //echo '  Current PHP version: ' . phpversion();
                                       $secretvalue1 = $security->encrypt('allowAccessToApp', $masterKey[0]);
                                    //echo 'after '.$secretvalue1;
                                    //exit;
                                    $secretvalue = $security->decrypt($secretvalue, $masterKey);

                                    // match token is valid or not

                                    if ($secretvalue == $tempToken)
                                    {
                                        return "yes";
                                    }
                                    else
                                    {
                                         //echo "token not matched";exit;
                                        return "no";
                                    }
                                }
                            }
                            else
                            {

                                $tempToken = $security->encrypt($tempToken, $masterKey);//
                                // check security access with user's private credentials
                                // echo '\n'.'temp token=> '.$tempToken;
                                //echo $tempToken;
                                return $this->checkCredentialsForSecurityNew($accessvalue,$secretvalue,$tempToken);
                            }
                        }
                    }
                    $stmt_get_global_pwd->close();
                }
            }
            else
            {
            //  No valid user agent
           //  echo "No user-agent";
                return no;
            }
        }
    }

    // USED METHODS
    public function checkCredentialsForSecurityNew($accessvalue,$secretvalue,$tempToken)
    {

        $connection=$GLOBALS['con'];
        $query = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='globalPassword' AND is_delete=0";
        if($stmt_get_global_pwd = $connection->prepare($query)) {
            $stmt_get_global_pwd->execute();
            $stmt_get_global_pwd->store_result();
            if ($stmt_get_global_pwd->num_rows > 0) {

                while ($value = fetch_assoc_all_values($stmt_get_global_pwd)) {
                    $masterKey = $value['config_value'];

                    //step1= decrpt accesskey with global password
                    $security = new Security();

                    $decrypted_access_key = $security->decrypt($accessvalue, $masterKey);

                    $queryToCheckAccessKeyExist = "SELECT * FROM " . TABLE_USER . " WHERE accesscode ='" . $decrypted_access_key . "' AND is_delete = 0";

                    if($stmt_check_access_key = $connection->prepare($queryToCheckAccessKeyExist)) {

                        $stmt_check_access_key->execute();
                        $stmt_check_access_key->store_result();
                        if ($stmt_check_access_key->num_rows > 0)
                        {

                            while($user_value=fetch_assoc_all_values($stmt_check_access_key)){
                                $decrypted_secret_key = $user_value['guid'];
                                $queryToCheckRecordExist = "SELECT * FROM " . tblAppTokens . " WHERE user_id =" . $user_value['id']. " AND is_delete=0";
                                //    echo $queryToCheckRecordExist;exit;
                                if($stmt_check_record_in_token = $connection->prepare($queryToCheckRecordExist)) {
                                    $stmt_check_record_in_token->execute();
                                    $stmt_check_record_in_token->store_result();
                                    if ($stmt_check_record_in_token->num_rows > 0)
                                    {
                                        while($row_token=fetch_assoc_all_values($stmt_check_record_in_token))
                                        {

                                            $tokenName = $row_token['token'];
                                            $currentdate = $row_token['expiry'];

                                            if($secretvalue == $tempToken){

                                                // we can return user's private access token here
                                                // $tokenName = $tokenName."_".$currentdate;
                                                $currentdateEncrypt = $security->encrypt($currentdate,$decrypted_access_key);
                                                $tokenNameEncrypt = $security->encrypt($tokenName, $decrypted_access_key);
                                                //echo ' current date encrpt=> '.$currentdateEncrypt;
                                                //echo ' token name encrpt=> '.$tokenNameEncrypt;

                                                $tokenName = $tokenNameEncrypt."_".$currentdateEncrypt;

                                                $response = array();
                                                $response['key'] = "User"; // return user's private token
                                                $response['value'] = $tokenName;

                                                // echo ' secret=access scenario my token=> '.$tokenName;
                                                return $response;
                                            }
                                            else if($secretvalue == NULL) {

                                                $currentdateEncrypt = $security->encrypt($currentdate, $decrypted_access_key);
                                                $tokenNameEncrypt = $security->encrypt($tokenName, $decrypted_access_key);

                                                $tokenName = $tokenNameEncrypt."_".$currentdateEncrypt;

                                                $response = array();
                                                $response['key'] = "User";// return user's private token
                                                $response['value'] = $tokenName;

                                                // echo ' secret= null scenario my token=> '.$tokenName;

                                                return $response;
                                            }
                                            else {

                                                $secretvalue = explode("_",$secretvalue);

                                                //echo ' \n\n new explode secret value => \n '.$secretvalue[0];
                                                //echo "\ndecrypted access key =>".$decrypted_access_key;
                                                //$decrypted_secret_key = $security->decrypt($secretvalue[0], $user_id['guid']."_".$decrypted_access_key);

                                                $decrypted_secret_key = $security->decrypt($secretvalue[0], $decrypted_secret_key);
                                                $decrypted_secret_key1 = $security->decrypt($secretvalue[1], $decrypted_access_key);
                                                if ($decrypted_secret_key == $tokenName)
                                                {
                                                    return "yes";

                                                }
                                                else
                                                {
                                                    return "no";
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        //echo "token not available for user";
                                        return "no";

                                    }
                                    $stmt_check_record_in_token->close();
                                }

                            }

                        }
                        else
                        {
                            return "no";
                        }
                        $stmt_check_access_key->close();
                    }
                    else
                    {
                        return "no";
                    }
                }

                $stmt_get_global_pwd->close();
            }
        }



    }

    // USED METHODS
    public function checkForSecurityForRefreshToken($accessvalue,$secretvalue)
    {
        $connection=$GLOBALS['con'];
        if($accessvalue == "")
        {
            $data['status'] = FAILED;
            $data['error_status'] = TOKEN_ERROR;
        }
        else{
            // get user-agent from database

            $query = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='userAgent' AND is_delete=0";
            if($stmt_get_user_agent = $connection->prepare($query)) {
                $stmt_get_user_agent->execute();
                $stmt_get_user_agent->store_result();
                if ($stmt_get_user_agent->num_rows > 0) {
                    while($value=fetch_assoc_all_values($stmt_get_user_agent))
                    {
                        $user_agent = $value['config_value'];
                        $separateKey = (explode(',', $user_agent));
                    }
                }
                $stmt_get_user_agent->close();
            }


            // check user-agent is valid
            if ((strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[0]) !== false) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[1]) !== false) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[2]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[3]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[4]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[5]) !== false ) || (strpos($_SERVER ['HTTP_USER_AGENT'], $separateKey[6]) !== false ))
            {
                // get temporary token for user.
                $query_config = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='tempToken' AND is_delete=0";


                if($stmt_get_temp_token = $connection->prepare($query_config)) {
                    $stmt_get_temp_token->execute();
                    $stmt_get_temp_token->store_result();
                    if ($stmt_get_temp_token->num_rows > 0) {
                        while($val=fetch_assoc_all_values($stmt_get_temp_token)){
                            $tempToken = $val['config_value'];
                        }

                    }
                    $stmt_get_temp_token->close();
                }

                // get global password to encrypt temp token
                $query_global_pwd = "SELECT config_value FROM " . tblAdminConfig . " WHERE config_key='globalPassword' AND is_delete=0";

                if($stmt_get_global_pwd = $connection->prepare($query_global_pwd)) {
                    $stmt_get_global_pwd->execute();
                    $stmt_get_global_pwd->store_result();
                    if ($stmt_get_global_pwd->num_rows > 0) {
                        while($val=fetch_assoc_all_values($stmt_get_global_pwd)){
                            $masterKey = $val['config_value'];

                            $security = new Security();

//            echo "nousername ......";


                            // check user request it with temporary credentials or private credential
                            if ($accessvalue == "nousername")
                            {
//                echo "nousername ......";
                                // check user passed temporary token or request with temporary token.
                                if ($secretvalue == NULL)
                                {
                                    // return encrypted token

                                    $secretvalue = $security->encrypt($tempToken, $masterKey);

                                    $response = array();
                                    $response['key'] = "Temp";// return temporary token
                                    $response['value'] = $secretvalue;
                                    return $response;
                                }
                                else
                                {
                                    //  echo '  Current PHP version: ' . phpversion();
                                    //   $secretvalue1 = $security->encrypt('allowAccessToApp', $masterKey[0]);
                                    //echo 'after '.$secretvalue1;

                                    $secretvalue = $security->decrypt($secretvalue, $masterKey);

                                    // match token is valid or not
                                    if ($secretvalue == $tempToken)
                                    {
                                        return "yes";
                                    }
                                    else
                                    {
                                        // echo "token not matched";
                                        return "no";
                                    }
                                }
                            }
                            else
                            {
//                echo '\n'.' before temp token=> ';
//                print_r($tempToken);
                                $tempToken = $security->encrypt($tempToken[0], $masterKey[0]);
                                // check security access with user's private credentials
                                // echo '\n'.'temp token=> '.$tempToken;

                                // return $this->checkCredentialsForSecurityNew($accessvalue,$secretvalue,$tempToken);
                                return $this->checkCredentialsForSecurityNew_injection($accessvalue,$secretvalue,$tempToken);
                            }
                        }
                    }
                    $stmt_get_global_pwd->close();
                }



            }
            else
            {
                //  No valid user agents
//            echo "No user-agent";
                return no;
            }
        }

    }
}


?>