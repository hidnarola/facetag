<?php

/**
 * Register Controller - Registration Process for Business
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('businesses_model');
        $this->load->model('users_model');
        $this->load->library('facerecognition');
    }

    /**
     * Display Landing page of facetag
     */
    public function index() {
        $data['title'] = 'Facetag Register Business';
        $data['business_types'] = $this->businesses_model->get_all_types();
        $data['hear_abouts'] = $this->businesses_model->get_all_hear_abouts();
//        $this->form_validation->set_error_delimiters('<div class="alert alert-error alert-danger"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
        $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|regex_match[/[a-z]+$/i]', array('regex_match' => 'Invalid %s! Only alphabets allowed!'));
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|regex_match[/[a-z]+$/i]', array('regex_match' => 'Invalid %s! Only alphabets allowed!'));
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_is_uniquemail');
        $this->form_validation->set_rules('phone_no', 'Phone Number', 'trim|required|regex_match[/^[0-9().-]+$/]');

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|matches[confirm_password]', array('required' => 'Please enter Password',
            'min_length' => 'Password should be of minimum 5 characters long',
            'matches' => 'Password should match with Confirm Password'
        ));
        $this->form_validation->set_rules('confirm_password', 'Password', 'required', array('required' => 'Please enter Confirm Password'));
        $this->form_validation->set_rules('accept_terms', 'accept_terms', 'required', array('required' => 'Please accept facetags\' terms and conditions'));
        $this->form_validation->set_rules('business_name', 'Business Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('business_type[]', 'Select Business Type', 'required', array('required' => 'Please Select type that apply'));

        if ($this->input->post('business_type')) {
            if (in_array(0, $this->input->post('business_type'))) {
                $this->form_validation->set_rules('other_business_type', 'Other Business Type', 'required', array('required' => 'Enter other business type'));
            }
        }

        $this->form_validation->set_rules('visitor', 'Visitor Attendence', 'required', array('required' => 'Please Select Average daily visitor attendance'));
        $this->form_validation->set_rules('visitor_photo', 'Visitor photographs', 'required', array('required' => 'Please Select Average number of Visitor photographs taken daily'));
//        $this->form_validation->set_rules('distribute', 'Distribute Photograph', 'required', array('required' => 'Please Select one'));
//        if (($this->input->post('distribute') == 'forsale') || ($this->input->post('distribute') == 'both')) {
//            $this->form_validation->set_rules('sale_value', 'Sale Price', 'trim|required', array('required' => 'Please Enter Sale Price!'));
//        }

        $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'required|callback_captcha_validation', array('required' => 'Please verify captcha'));


        if ($this->form_validation->run() == TRUE) {

            $verification_code = verification_code();
            $user_name = $this->users_model->get_unique_username(trim($this->input->post('firstname')), trim($this->input->post('lastname')));
            $user_data = array(
                'user_role' => 2,
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'username' => $user_name,
                'email' => $this->input->post('email'),
                'password' => md5($this->input->post('password')),
                'phone_no' => $this->input->post('phone_no'),
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'verification_code' => $verification_code);

            $user_id = $this->users_model->insert($user_data);

            $business_data = array(
                'user_id' => $user_id,
                'name' => $this->input->post('business_name'),
                'business_type_id' => implode(',', $this->input->post('business_type')),
                'user_hear_abouts_id' => implode(',', $this->input->post('hear_about')),
                'other_business_type' => $this->input->post('other_business_type'),
                'daily_visitors' => $this->input->post('visitor'),
                'visitor_photographs' => $this->input->post('visitor_photo'),
                'distribute_photograph' => $this->input->post('distribute'),
                'sale_price' => $this->input->post('sale_value'),
                'is_verified' => 0,
                'is_active' => 0,
                'contact_email' => $this->input->post('email'),
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'));

            $business_id = $this->businesses_model->insert($business_data);

            if ($business_id) {
                $encoded_mail = urlencode($verification_code);
                $url = site_url() . 'register/verify?id=' . $encoded_mail;
                $configs = mail_config();
                $this->load->library('email', $configs);
//                $this->email->initialize($configs);
                $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
                $this->email->to($this->input->post('email'));

                $msg = $this->load->view('email_templates/verification_mail', array('firstname' => $this->input->post('firstname'), 'lastname' => $this->input->post('lastname'), 'url' => $url), true);
                $this->email->subject('Email Verification - facetag');
                $this->email->message(stripslashes($msg));
                $this->email->set_mailtype("html");
                if ($this->email->send()) {
                    $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
//                    $this->email->to("anp@narola.email");
                    $this->email->to("sales@facetag.com.au");

                    $msg = $this->load->view('email_templates/registration_mail', array('firstname' => $this->input->post('firstname'), 'lastname' => $this->input->post('lastname'), 'email' => $this->input->post('email')), true);
                    $this->email->subject('User Registration - facetag');
                    $this->email->message(stripslashes($msg));
                    $this->email->set_mailtype("html");
                    $this->email->send();
                }
                $this->email->print_debugger();
            }
            $this->session->set_flashdata('success', 'Thank you, Please verify your email! we look forward to working with you soon');

            //-- Unset subscribe name and email session if it is set
            $this->session->unset_userdata('subscribe_name');
            $this->session->unset_userdata('subscribe_email');
            redirect('register');
        }

        $this->template->load('frontend', 'register', $data);
    }

    /**
     * Verify Email id of Business User
     */
    public function verify() {
        $encoded_email = $this->input->get_post('id');
        $verification_code = urldecode($encoded_email);
        $result = $this->users_model->check_verification_code($verification_code);

        if (sizeof($result) > 0) {

            //--- generate verification code
            $new_verification_code = verification_code();
            //-- Make Users' is_active and is_verified fields to 1 
            $this->users_model->update_record('id=' . $result['id'], array('is_verified' => 1, 'verification_code' => $new_verification_code, 'is_active' => 1));
            $business = $this->businesses_model->get_business_by_userid($result['id']);

            //-- Make Business' is_active and is_verifed fields to 1
            $this->businesses_model->update_record('id=' . $business['id'], array('is_verified' => 1, 'is_active' => 1));

            //--Create business gallery in face recognition database
            /*
              $gallary_name = 'business_' . $business['id'];
              $this->facerecognition->post_gallery($gallary_name); */

            $this->session->set_flashdata('success', 'You have verified your email successfully! Please login to continue');
            redirect('login');
        } else {
            $this->session->set_flashdata('error', 'You are not authorized person');
            redirect('register');
        }
    }

    /**
     * Callback function to check email validation - Email is unique or not
     * @param string $str
     * @return boolean
     */
    public function is_uniquemail() {
        $email = trim($this->input->post('email'));
        $user = $this->users_model->check_unique_email($email);
        if ($user) {
            $this->form_validation->set_message('is_uniquemail', 'Email address is already in use!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Callback function to check price validation
     * @param string $str
     * @return boolean
     */
    public function decimal_numeric($str) {
        if (!is_numeric($str)) {
            $this->form_validation->set_message('decimal_numeric', 'The %s field must contain a  number.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Callback function to captcha validation
     * @return boolean
     */
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
     * Subscribes users
     */
    public function subscribe() {
        $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');
        $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_is_subscribeuniquemail');

        if ($this->form_validation->run() == TRUE) {

            $user_data = array(
                'first_name' => trim($this->input->post('first_name')),
                'last_name' => trim($this->input->post('last_name')),
                'email' => trim($this->input->post('email')),
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'));

            $user_id = $this->users_model->insert_subscribeusers($user_data);

            //-- Store name and email in session to get it into Business register page
            $this->session->set_userdata('subscribe_first_name', trim($this->input->post('first_name')));
            $this->session->set_userdata('subscribe_last_name', trim($this->input->post('last_name')));
            $this->session->set_userdata('subscribe_email', trim($this->input->post('email')));

            $this->session->set_flashdata('success', 'Thank you, You have been subscribed successfully. Please continue to register yourself');
            redirect('register');
        }

        $data['title'] = 'Facetag Retailer';
        $this->template->load('frontend', 'home', $data);
    }

    /**
     * Callback function to check email validation - Email is unique or not
     * @param string $str
     * @return boolean
     */
    public function is_subscribeuniquemail() {
        $email = trim($this->input->post('email'));
        $user = $this->users_model->check_subscribeunique_email($email);
        if ($user) {
            $this->form_validation->set_message('is_subscribeuniquemail', 'You have already subscribed!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Ask user to enter password to signup with Business
     */
    public function verify_invite() {
        $encoded_email = $this->input->get_post('id');
        $verification_code = urldecode($encoded_email);
        $result = $this->users_model->check_verification_code($verification_code);
        //-- if valid
        if ($result) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('con_password', 'Confirm password', 'trim|required|matches[password]');
            $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');
            if ($this->form_validation->run() == TRUE) {
                //--- generate verification code
                $new_verification_code = verification_code();
                //-- Make Users' is_active and is_verified fields to 1  and set password
                $data = array(
                    'password' => md5($this->input->post('password')),
                    'verification_code' => $new_verification_code,
                    'is_verified' => 1,
                    'is_active' => 1,
                );
                $this->users_model->update_record('id=' . $result['id'], $data);
                $this->session->set_flashdata('success', 'You have been registered successfully with facetag! Please login to continue');
                redirect('login');
            }

            $business = $this->businesses_model->get_business_by_userid($result['id']);
            $data['title'] = 'facetag | Register Business';
            $data['verification_code'] = $verification_code;
            $data['business'] = $business['name'];
            $data['user'] = $result;
            $this->template->load('frontend', 'invite_password', $data);
        } else {
            $this->session->set_flashdata('error', 'You are not authorized person');
            redirect('register');
        }
    }

    /**
     * Check email address entered in email_id of account profile page is unique or not
     * Called throught ajax
     */
    public function checkUniqueEmail() {
        $requested_email = $this->input->get('email');
        $user = $this->users_model->check_unique_email($requested_email);
        if ($user) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

}
