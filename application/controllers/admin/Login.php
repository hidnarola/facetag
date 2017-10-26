<?php

/**
 * Login Controller for Super Admin Login
 * @author KU 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('login_model');
    }

    /**
     * Display login page for Super admin/Business user login
     */
    public function index() {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|callback_admin_validation');
        $this->form_validation->set_rules('password', 'Password', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['error'] = validation_errors();
        } else {
            //-- If redirect is set in URL then redirect user back to that page
            if ($this->input->get('redirect')) {
                redirect(base64_decode($this->input->get('redirect')));
            } else {
                if ($this->session->userdata('facetag_admin')['user_role'] == 2) {
                    redirect('business/home');
                } else {
                    redirect('admin/home');
                }
            }
        }
        $this->load->view('admin/login', $data);
    }

    /**
     * Callback Validate function to check Super Admin/Business User 
     * @return boolean
     */
    public function admin_validation() {
        $result = $this->login_model->get_user($this->input->post('email'), $this->input->post('password'));
        if ($result) {
            if ($result['is_verified'] == 0 || $result['is_active'] == 0) {
                $this->form_validation->set_message('admin_validation', 'You have not verified your email yet! Please verify it first.');
                return FALSE;
            } else {
                if ($result['user_role'] == 2) {
                    $business_data = $this->login_model->get_busines_id_from_user($result['id']);
                    if ($business_data['is_verified'] == 0 || $business_data['is_active'] == 0) {
                        $this->form_validation->set_message('admin_validation', 'Your Business has not been verified by Administrator yet! Once it will be verified then you would be able to login');
                        return FALSE;
                    }
                    $result['business_id'] = $business_data['id'];
                    $result['business_name'] = $business_data['name'];
                }
                $this->session->set_userdata('facetag_admin', $result);
                return TRUE;
            }
        } else {
            $this->form_validation->set_message('admin_validation', 'Invalid Email/Password.');
            return FALSE;
        }
    }

    /**
     * Clears the session and log out Super admin/Business User
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('admin/login');
    }

}
