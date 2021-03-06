<?php

/**
 * Home Controller - Landing Page of Facetag
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display Landing page of facetag
     */
    public function index() {
        $data['title'] = 'Facetag Retailer';
        $this->template->load('frontend', 'home', $data);
    }

    /**
     * Test function to test email functionality
     */
    public function test() {
        $configs = mail_config();
        $this->load->library('email', $configs);
//        $this->email->initialize($configs);
        $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
        $this->email->to('ku@narola.email');
        $msg = '<!DOCTYPE HTML>
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<meta name="viewport" content="width=device-width"/><body>';
        $msg .= '<b>test email</b>';
        $msg .= '</body></html>';
        $this->email->subject('Invitation - facetag');
        $this->email->message(stripslashes($msg));
//        $this->email->set_header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
        $this->email->set_mailtype("html");
        if ($this->email->send()) {
            echo 'sent';
        } else {
            p($this->email->print_debugger());
        }
    }
    public function captcha_validation() {
        $secret = GOOGLE_SECRET_KEY;
        //get verify response data
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $this->input->post('g-recaptcha-response'));
        $responseData = json_decode($verifyResponse);
        if ($responseData->success) {
            return TRUE;
        } else {
            $this->form_validation->set_message('captcha_validation', 'You have not verified captcha properly! Please try again.');
            return FALSE;
        }
    }

    /**
     * Contact us functionality of frontend
     * @author KU
     */
    public function contact_us() {
        $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'required|callback_captcha_validation', array('required' => 'Please verify captcha'));
        if ($this->form_validation->run() == TRUE) {
            $configs = mail_config();
            $this->load->library('email', $configs);
//        $this->email->initialize($configs);
            $this->email->from($this->input->post('contact_email'), $this->input->post('contact_name'));
//        $this->email->to('info@facetag.com.au');
            $this->email->to('colin@facetag.com.au');
            $msg = 'Following are the details of contact us form filled by user<br>';
            $msg .= '<b>Name</b> : ' . $this->input->post('contact_name') . '<br>';
            $msg .= '<b>Email</b> : ' . $this->input->post('contact_email') . '<br>';
            $msg .= '<b>Subject</b> : ' . $this->input->post('contact_subject') . '<br>';
            $msg .= '<b>Message</b> : ' . $this->input->post('contact_message') . '<br>';
            $this->email->subject('Facetag - Contact US');
            $this->email->message($msg);
            $this->email->set_mailtype("html");
            if ($this->email->send()) {
                $this->session->set_flashdata('success', 'Thanks, Your email has been sent successfully!');
//            return true;
                redirect('home');
            } else {
                p($this->email->print_debugger());
                return false;
            }
        }
    }

    /**
     * Terms and condition page
     * @author KU
     */
    public function terms() {
        $data['title'] = 'Terms of Service';
        $this->template->load('frontend', 'terms', $data);
    }
    
    /**
     * App Terms and condition page
     * @author ANP
     */
    public function appterms() {
        $data['title'] = 'Terms of Service';
        $this->template->load('frontend', 'appterms', $data);
    }
    
    /**
     * Privacy policy page
     * @author ANP
     */
    public function privacy() {
        $data['title'] = 'Privacy Policy';
        $this->template->load('frontend', 'privacy', $data);
    }

}
