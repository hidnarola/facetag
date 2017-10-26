<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 04/03/15
 * Time: 12:10 PM
 */

include_once 'Logger.php';

//ini_set('display_errors', 1);
//echo "okay";

$logger = new Logger();

date_default_timezone_set('UTC');
//$server = "192.168.1.201";
//$user = "facetag";
//$password = "W73vdXD1l3lc72u";
//$dbname = 'facetag';

$server = "localhost";
$user = "root";
$password = "narola21";
$dbname = 'facetag';

global $con;
$con = mysqli_connect($server, $user, $password,$dbname);
mysqli_set_charset($con, 'utf8');
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{
    //echo "connected successfully";
}


?>