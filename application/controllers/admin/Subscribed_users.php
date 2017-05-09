<?php

/**
 * Subscribed Users Controller - Display all subscribed users
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribed_users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
    }

    /**
     * Load view of users list
     * */
    public function index() {
        $data['title'] = 'facetag | Subscribed Users';
        $this->template->load('default', 'admin/subscribed_users/list', $data);
    }

    /**
     * Get businesses for data table
     * */
    public function get_subscribed_users() {
        $final['recordsTotal'] = $this->users_model->get_subscribed_users('count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $users = $this->users_model->get_subscribed_users();
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
     * Delete Subscribed users 
     * @param int $subscribed_user_id - Subscribed_user_id id
     */
    public function delete($subscribed_user_id) {
        $where = 'id = ' . $this->db->escape($subscribed_user_id);
        $subscribed_user = $this->users_model->get_subscribed_user_result($where);
        if ($subscribed_user) {
            $this->users_model->update_subscribeduser($where, array('is_delete' => 1));
            $this->session->set_flashdata('success', $subscribed_user[0]['name'] . ' user deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid request. Please try again!');
        }
        redirect('admin/subscribed_users');
    }

}
