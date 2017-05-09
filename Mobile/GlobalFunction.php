<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 03/03/15
 * Time: 4:16 PM
 */

class GlobalFunction
{
    function __construct()
    {

    }
    function distanceInkilometer($userLat, $userLong, $evntLat, $evntLong)
    {

        // convert decimal degrees to radians
        $dLat = deg2rad($evntLat - $userLat);
        $dLng = deg2rad($evntLong - $userLong);
        $lat1 = deg2rad(floatval($userLat));
        $lat2 = deg2rad(floatval($evntLat));

        // haversine formula
        $a = sin($dLat / 2) * sin($dLat / 2) +
            sin($dLng / 2) * sin($dLng / 2) *
            cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return 6371 * $c;
    }

    function generateBase32UniqueString(){
        $randStr = strtoupper(md5(rand(0, 1000000))); // Create md5 hash
        $rand_start = rand(5,strlen($randStr)); // Get random start point
        if($rand_start+8 > strlen($randStr)) {
            $rand_start -= 8; // make sure it will always be $length long
        } if($rand_start == strlen($randStr)) {
            $rand_start = 1; // otherwise start at beginning!
        }
        // Extract the 'random string' of the required length
        $randStr = strtoupper(substr(md5($randStr), $rand_start, 8));
        return $randStr;
    }
//    function generateBase32UniqueString(){
//
//        $timeIn = microtime() * 1000000;
//        $milliseconds = round(abs($timeIn));
//
//        $milliseconds = substr($milliseconds, 0, 3);
//        $secondInterval = time();
//
//        $timeInterval =  $secondInterval.$milliseconds;
//        $binary = decbin($timeInterval);
//
//        $array = array(
//            "0" => "00000", "1" => "00001", "2" => "00010",
//            "3" => "00011", "4" => "00100", "5" => "00101",
//            "6" => "00110", "7" => "00111", "8" => "01000",
//            "9" => "01001", "A" => "01010", "B" => "01011",
//            "C" => "01100", "D" => "01101", "E" => "01110",
//            "F" => "01111", "G" => "10000", "H" => "10001",
//            "I" => "10010", "J" => "10011", "K" => "10100",
//            "L" => "10101", "M" => "10110", "N" => "10111",
//            "O" => "11000", "P" => "11001", "Q" => "11010",
//            "R" => "11011", "S" => "11100", "T" => "11101",
//            "U" => "11110", "V" => "11111"
//        );
//
//        $strResult = "";
//
//        $modulo = strlen($binary) % 5;
//
//        if($modulo > 0){
//            for ($i = 0;$i < (5 - $modulo); $i++)
//            {
//                $binary = "0".$binary;
//            }
//        }
//
//        for ($i = strlen($binary);$i > 0; $i-=5)
//        {
//            $strBinaryKey = substr($binary, $i-5, 5);
//            $strResult = $strResult."".array_search($strBinaryKey."", $array);
//        }
//
//        $strResult = strrev($strResult);
//
//        $user['uniqueID'] = $strResult;
//
//        return $user;
//    }


    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }



}
?>