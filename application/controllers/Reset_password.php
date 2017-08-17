<?php

/**
 * Reset Password controller for Business
 * @author KU 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
    }

    /**
     * Display email page to enter Email Id for forgot password 
     */
    public function index() {
        $data['title'] = 'facetag | Forgot Password';
        $this->form_validation->set_rules('email', 'Email', 'trim|required|callback_check_email');
        $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');
        if ($this->form_validation->run() == FALSE) {
            $this->template->load('frontend', 'reset_password', $data);
        } else {
            $this->session->set_flashdata('success', 'Your request has been submitted.Please check your email to reset your password!');
            redirect('reset_password');
        }
    }

    /**
     * Callback function to check email exist or not
     * @return boolean
     */
    public function check_email() {
        $email = $this->input->post('email');
        $email_result = $this->users_model->check_email($email);
        if ($email_result) {

            //--- valid email address
            $configs = mail_config();
            $this->load->library('email', $configs);
//            $this->email->initialize($configs);
            $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
            $this->email->to($email);
            $verification_code = $this->encrypt->encode($email_result['verification_code']);
            $encoded_verification_code = urlencode($verification_code);
            $url = site_url() . 'reset_password/change?code=' . $encoded_verification_code;
            $message_text = 'Hello ' . $email_result['firstname'] . ' ' . $email_result['lastname'] . ',<br/>';
            $message_text.= 'Please click on below link to reset your password.<br/>';
            $message_text.='<a href="' . $url . '">' . $url . '</a>';

            $this->email->subject('Reset Password - facetag');
            $this->email->set_mailtype("html");
            $this->email->message($message_text);
            $this->email->send();
            $this->email->print_debugger();
            return TRUE;
        } else {

            //--- invalid email address
            $this->form_validation->set_message('check_email', 'Invalid email address.');
            return FALSE;
        }
    }

    /**
     * Display page on click of forgot password email check verification code is valid or not
     */
    public function change() {

        $encoded_verification_code = $this->input->get_post('code');
        $verification_code = urldecode($this->encrypt->decode($encoded_verification_code));
        $result = array();
        if ($verification_code != '') {
            $result = $this->users_model->check_verification_code($verification_code);
        }

        if ($result) {
            //--- valid code
            $data['title'] = 'facetag | Change Password';
            $data['verification_code'] = $verification_code;
            $this->template->load('frontend', 'change_password', $data);
        } else {

            //--- invalid code
            $this->session->set_flashdata('error', 'Invalid request or already changed password');
            redirect('reset_password');
        }
    }

    /*
     * 	if verification code is valid,
     * 	request to reset password,
     * 	check validations
     */

    public function update_password() {
        $data['title'] = 'facetag | Change Password';
        $data['verification_code'] = $this->input->post('verification_code');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('con_password', 'Confirm password', 'trim|required|matches[password]');
        $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');

        if ($this->form_validation->run() == FALSE) {
            $this->template->load('frontend', 'change_password', $data);
        } else {
            //--- check again varification code is valid or not

            $result = $this->users_model->check_verification_code($this->input->post('verification_code'));
            if ($result) {

                //--- if valid then reset password and generate new verification code
                //--- generate verification code
                $new_verification_code = verification_code();
                $id = $result['id'];
                $data = array(
                    'password' => md5($this->input->post('password')),
                    'verification_code' => $new_verification_code
                );
                $this->users_model->update_record('id=' . $id, $data);
                $this->session->set_flashdata('success', 'Your password changed successfully');
                redirect('login');
            } else {

                //--- if invalid verification code
                $this->session->set_flashdata('error', 'Invalid request or already changed password');
                redirect('reset_password');
            }
        }
    }

    /**
     * Allow business user to set their own password for verification email sent by user
     * @author KU
     */
    public function set_password() {
        $data['title'] = 'facetag | Set Password';
        $encoded_verification_code = $this->input->get_post('code');
        $verification_code = urldecode($this->encrypt->decode($encoded_verification_code));
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('con_password', 'Confirm password', 'trim|required|matches[password]');
        $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');
        $result = $this->users_model->check_verification_code($verification_code);
        if ($result) {
            if ($this->form_validation->run() == FALSE) {
                $this->template->load('frontend', 'set_password', $data);
            } else {
                //--- check again varification code is valid or not
                //--- if valid then reset password and generate new verification code
                //--- generate verification code
                $new_verification_code = verification_code();
                $id = $result['id'];
                $data = array(
                    'password' => md5($this->input->post('password')),
                    'verification_code' => $new_verification_code
                );
                $this->users_model->update_record('id=' . $id, $data);
                $this->session->set_flashdata('success', 'Your password has been set successfully!Now login and complete your Business Profile.');
                redirect('login');
            }
        } else {

            //--- if invalid verification code
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('login');
        }
    }

}
