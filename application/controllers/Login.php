<?php

/**
 * Login Controller for Business Login
 * @author KU 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('users_model');
    }

    /**
     * Display login page for Business user login
     */
    public function index() {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|callback_business_validation');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['error'] = validation_errors();
        } else {
            //-- If redirect is set in URL then redirect user back to that page
            if ($this->input->get('redirect')) {
                redirect(base64_decode($this->input->get('redirect')));
            } else {
                if ($this->session->userdata('facetag_admin')['user_role'] == 2) {
                    //-- update last login time and login count after successfull login
                    $update_userdata = array('last_loggedin' => date('Y-m-d H:i:s'), 'login_count' => $this->session->userdata('facetag_admin')['login_count'] + 1);
                    $this->users_model->update_record('id=' . $this->session->userdata('facetag_admin')['id'], $update_userdata);
                    if ($this->session->userdata('facetag_admin')['is_ever_loggedin'] == 0) {
                        redirect('business/profile');
                    } else {
                        redirect('business/home');
                    }
                } else {
                    redirect('admin/home');
                }
            }
        }
        $data['title'] = 'facetag | Business Login';
        $this->template->load('frontend', 'login', $data);
    }

    /**
     * Callback Validate function to check Super Admin/Business User 
     * @return boolean
     */
    public function business_validation() {
        $result = $this->login_model->get_user($this->input->post('email'), $this->input->post('password'));
        if ($result) {
            if ($result['is_verified'] == 0 || $result['is_active'] == 0) {
                $this->form_validation->set_message('business_validation', 'You have not verified your email yet! Please verify it first.');
                return FALSE;
            } else {
                if ($result['user_role'] == 2) {
                    $business_data = $this->login_model->get_busines_id_from_user($result['id']);
                    if ($business_data['is_active'] == 0) {
                        $this->form_validation->set_message('business_validation', 'Your Business has been blocked! Please contact system administrator');
                        return FALSE;
                    }
                    $result['business_id'] = $business_data['id'];
                    $result['business_name'] = $business_data['name'];
                }
                $this->session->set_userdata('facetag_admin', $result);
                return TRUE;
            }
        } else {
            $this->form_validation->set_message('business_validation', 'Invalid Email/Password.');
            return FALSE;
        }
    }

    /**
     * Clears the session and log out Super admin
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

}
