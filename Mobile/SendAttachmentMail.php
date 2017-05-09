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

    function sendEmail($body,$toEmail,$subject,$attachment)
    {
        require_once('class.phpmailer.php');

//        $subject = "Sending Attchement";
//        $body = "Test Attchement Mail";
//        $toEmail = "ank@narola.email";

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: facetag' . "\r\n";
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->Username = "demo.narolainfotech@gmail.com";
        $mail->Password = "Narola102";
        $mail->SetFrom('demo.narolainfotech@gmail.com', 'facetag');
        $mail->Subject = $subject;
        $mail->IsHTML(true);
        $mail->Body = $body;
        $mail->AddAddress($toEmail);
        $mail->AddCC("delivery@facetag.com.au");
        //$mail->AddCC("pr@narola.email");


        $i = 1;
        foreach ($attachment as $selfie) {
            $imageName = "Selfie".$i.".png";
            $mail->AddAttachment( $selfie,$imageName );
            $i = $i + 1;
        }

        //Send Attachment
//        $file_to_attach = '../uploads/icp_images/business_6/icp_15/5853e143c5c591481892163.jpeg';
//        $file_to_attach1 = './Images/selfiPic/profile_2017-04-12_11_57_20.png';
//
//        $mail->AddAttachment( $file_to_attach,'promo.png' );
//        $mail->AddAttachment( $file_to_attach1,'promo1.png' );


        $mail->Send();
    }
}

?>