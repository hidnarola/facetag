<?php
/**
 * Created by PhpStorm.
 * User: c174
 * Date: 20/04/17
 * Time: 4:20 PM
 */
include 'class.smtp.php';

class SendAttachmentMail
{
    function __construct()
    {

    }

//    function sendEmail($body,$toEmail,$subject,$attachment)
//    {
//        require_once('class.phpmailer.php');
//
//        $headers = 'MIME-Version: 1.0' . "\r\n";
//        $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
//        $headers .= 'From: facetag' . "\r\n";
//        $mail = new PHPMailer();
//        $mail->IsSMTP();
//        $mail->SMTPAuth = true;
//        $mail->SMTPSecure = "ssl";
//        $mail->Host = "smtp.gmail.com";
//        $mail->Port = 465;
//        $mail->Username = "demo.narolainfotech@gmail.com";
//        $mail->Password = "password123#";
//        $mail->SetFrom('demo.narolainfotech@gmail.com', 'facetag');
//        $mail->Subject = $subject;
//        $mail->IsHTML(true);
//        $mail->Body = $body;
//        $mail->AddAddress($toEmail);
//        $mail->AddCC("delivery@facetag.com.au");
//
//
//        $i = 1;
//        foreach ($attachment as $selfie) {
//            $imageName = "Photo".$i.".png";
//            $mail->AddAttachment( $selfie,$imageName );
//            $i = $i + 1;
//        }
//
//        $mail->Send();
//    }

    function sendEmail($body,$toEmail,$subject,$attachment)
    {
        require_once('class.phpmailer.php');

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: facetag' . "\r\n";
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "webcloud50.au.syrahost.com";
        $mail->Port = 465;
        $mail->Username = "verify@facetag.com.au";
        $mail->Password = "verify01";
        $mail->SetFrom('verify@facetag.com.au', 'facetag');
        $mail->Subject = $subject;
        $mail->IsHTML(true);
        $mail->Body = $body;
        $mail->AddAddress($toEmail);
        $mail->AddCC("delivery@facetag.com.au");


        $i = 1;
        foreach ($attachment as $selfie) {
            $imageName = "Photo".$i.".png";
            $mail->AddAttachment( $selfie,$imageName );
            $i = $i + 1;
        }

        $mail->Send();
    }
}

?>