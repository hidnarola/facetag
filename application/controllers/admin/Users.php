<?php

/**
 * Users Controller - Display all users
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
    }

    /**
     * Load view of users list
     * */
    public function index() {
        $data['title'] = 'facetag | Users';
        $this->template->load('default', 'admin/users/list', $data);
    }

    /**
     * Get businesses for data table
     * */
    public function get_users() {
        $final['recordsTotal'] = $this->users_model->get_registered_users('count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $users = $this->users_model->get_registered_users();
        $start = $this->input->get('start') + 1;

        foreach ($users as $key => $val) {
            $users[$key] = $val;
            $users[$key]['sr_no'] = $start++;
            $users[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $users;
        echo json_encode($final);
    }

    /**
     * Delete registered user
     * @param int $user_id - App registered user id
     */
    public function delete($user_id) {
        $where = 'id = ' . $this->db->escape($user_id);
        $registered_user = $this->users_model->get_result($where);
        if ($registered_user) {
            $this->users_model->update_record($where, array('is_delete' => 1));
            $this->session->set_flashdata('success', $registered_user[0]['firstname'] . ' ' . $registered_user[0]['lastname'] . ' user deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid request. Please try again!');
        }
        redirect('admin/users');
    }

}
