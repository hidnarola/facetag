<?php
/**
 * Created by PhpStorm.
 * User: c174
 * Date: 07/06/17
 * Time: 5:43 PM
 */


include_once 'config.php';
include_once 'Paths.php';
include_once 'TableVars.php';
$connection = $GLOBALS['con'];
$userId=base64_decode($_GET['uidentifier']);
$update_query = "Update " . TABLE_USER . " set
                             is_emailverified = ?
                             where id = ?";
$verifiedvalue="1";
$update_query_stmt = $connection->prepare($update_query);

$update_query_stmt->bind_param("ss",
    $verifiedvalue,
    $userId);

if ($update_query_stmt->execute())
{

}
else
{
    echo '<h1 align="center" style="color: red"><b>Sorry this user not found in our system</b></h1>';
    exit();
}

?>
<!DOCTYPE html>
<html>
<Head>
    <meta charset="utf8mb4">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</Head>
<body style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding-top:0; padding-right:0; padding-bottom:0; padding-left:0;">
<div style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding-top:30px; padding-right:0; padding-bottom:30px; padding-left:0; background:#f6f6f6;">
    <div style="margin-top:0; margin-right:auto; margin-bottom:a; margin-left:auto; padding-top:0; padding-right:0; padding-bottom:0; padding-left:0; max-width:664px;">
        <div style="margin-top:0; margin-right:0; margin-bottom:30px; margin-left:0; padding-top:0; padding-right:0; padding-bottom:0; padding-left:0; border:1px solid #d6d6d6; background:#fff;">


            <div style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding-top:40px; padding-right:50px; padding-bottom:20px; padding-left:50px;">
                <table style="width:100%; vertical-align:middle; margin:0 0 30px; ">
                    <tr align="center">
                        <td style=" margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding-top:0; padding-right:0; padding-bottom:0; padding-left:0; position:relative; display:inline-block; vertical-align:top; width:100%; ">
                            <img src='http://facetag.co.nz/Mobile/facetaglogo.png' alt=""/>
                        </td>
                    </tr>
                    <tr align="center">
                        <td>

                        </td></tr>
                    <tr align="center">
                        <td>

                        </td></tr>
                    <tr align="center">
                        <td>
                            <h2 style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding-top:0;
                            padding-right:0; padding-bottom:10px; padding-left:0; font-family: Roboto, sans-serif;
                            color:#000; font-size:24px; font-weight:700; display:block;">Thank you !!</h2>
                        </td>
                    </tr><tr align="center">
                        <td>

                        </td></tr><tr align="center">
                        <td>
                            <h2 style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding-top:0;
                            padding-right:0; padding-bottom:10px; padding-left:0; font-family: Roboto, sans-serif;
                            color:green; font-size:24px; font-weight:700; display:block;">Success! Your email has been verified.</h2>

                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>