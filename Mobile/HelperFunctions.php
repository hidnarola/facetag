<?php
/**
 * Created by PhpStorm.
 * User: c218
 * Date: 26/07/16
 * Time: 9:45 AM
 */


function encryptPassword($str)
{
//    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( ENCRYPTION_KEY ), $str, MCRYPT_MODE_CBC, md5( md5( ENCRYPTION_KEY ) ) ) );

    $qEncoded = md5($str);

    return ($qEncoded);
}

function decryptPassword($str)
{
    $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(ENCRYPTION_KEY), base64_decode($str), MCRYPT_MODE_CBC, md5(md5(ENCRYPTION_KEY))), "\0");
    return ($qDecoded);
}


function validateValue($value, $placeHolder) {
    $value = strlen($value) > 0 ? $value : $placeHolder;
    return $value;
}

function validateObject($object, $key, $placeHolder) {

    if(isset($object -> $key))
    {
//        $value = validateValue($object->$key, "");
        return $object->$key;
    }
    else
    {
        return $placeHolder;
    }
}

function json_validate($string) {
    if (is_string($string)) {
        @json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    return false;
}

function getDefaultDate()
{
    return date("Y-m-d H:i:s");
}

function generateRandomStringForQRCode($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function array_combine_($keys, $values)
{
    $result = array();
    foreach ($keys as $i => $k) {
        $result[$k][$values[$i]] = [];
    }
    array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
    return    $result;
}

function fetch_assoc_stmt(\mysqli_stmt $stmt, $buffer = true)
{
    if ($buffer)
    {
        $stmt->store_result();
    }
    $fields = $stmt->result_metadata()->fetch_fields();
    $args = array();
    foreach($fields AS $field)
    {
        $key = str_replace(' ', '_', $field->name); // space may be valid SQL, but not PHP
        $args[$key] = &$field->name; // this way the array key is also preserved
    }

    call_user_func_array(array($stmt, "bind_result"), $args);

    $results = array();
    while($stmt->fetch())
    {
        //$results[] = array_map(array($this, "copy_value"), $args);
        $results[] = array_map("copy_value", $args);
    }

    if ($buffer)
    {
        $stmt->free_result();
    }
    return $results;
}


function fetch_assoc_single_value(\mysqli_stmt $stmt, $buffer = true)
{
    if ($buffer)
    {
        $stmt->store_result();
    }
    $fields = $stmt->result_metadata()->fetch_fields();
    $args = array();
    foreach($fields AS $field)
    {
        $key = str_replace(' ', '_', $field->name); // space may be valid SQL, but not PHP
        $args[$key] = &$field->name; // this way the array key is also preserved
    }
    call_user_func_array(array($stmt, "bind_result"), $args);

    $results = array();
    while($stmt->fetch())
    {
        //$results[] = array_map(array($this, "copy_value"), $args);
        $results[] = array_map("copy_value", $args);
    }
    if ($buffer)
    {
        $stmt->free_result();
    }
    return $results;
}

function fetch_assoc_all_values($stmt)
{
    if($stmt->num_rows>0)
    {
        $result = array();
        $md = $stmt->result_metadata();
        $params = array();
        while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        if($stmt->fetch())
            return $result;
    }

    return null;
}

function fetch_stmt_with_attributes($stmt)
{
    if($stmt->num_rows>0)
    {
        $result = array();
        $md = $stmt->result_metadata();
        $params = array();
        while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        if($stmt->fetch())
            return $result;
    }

    return null;
}

function send($to, $message) {
    $fields = array(
        'to' => $to,
        'data' => $message,
    );
    return sendPushNotification($fields);
}

function  sendPushNotification($fields) {

    include_once 'config.php';
    include_once 'ConstantValues.php';


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
        die('Curl failed: ' . curl_error($ch));
    }



    // Close connection
    curl_close($ch);

    return $result;
}

?>