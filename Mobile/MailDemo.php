<?php
/**
 * Created by PhpStorm.
 * User: c174
 * Date: 09/09/17
 * Time: 10:26 AM
 */
include 'class.smtp.php';



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
        //$mail->Username = "demo.narolainfotech@gmail.com";
        //$mail->Password = "password123#";
        //$mail->SetFrom('demo.narolainfotech@gmail.com', 'facetag');
        $mail->Username = "verify@facetag.com.au";
        $mail->Password = "verify01";
        $mail->SetFrom('verify@facetag.com.au', 'facetag');
        $mail->Subject = "Test mail subject";
        $mail->IsHTML(true);
        $mail->Body = "Test Body123";
        $mail->AddAddress("ank@narola.email");
        $mail->Send();


