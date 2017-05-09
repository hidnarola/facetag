<?php

/**
 * Logs Controller - Display Business users logs
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('logs_model');
    }

    /**
     * Load view of businesses list
     * */
    public function index() {
        $data['title'] = 'facetag | Business User logs';
        $this->template->load('default', 'admin/logs/list', $data);
    }

    /**
     * Get business users for data table
     * */
    public function get_businessuser_log() {
        $final['recordsTotal'] = $this->logs_model->get_business_users('count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $businesse_users = $this->logs_model->get_business_users();
        $start = $this->input->get('start') + 1;

        foreach ($businesse_users as $key => $val) {
            $businesse_users[$key] = $val;
            $businesse_users[$key]['sr_no'] = $start++;
            if ($val['last_loggedin'] != '')
                $businesse_users[$key]['last_loggedin'] = date('d,M Y', strtotime($val['last_loggedin']));
        }

        $final['data'] = $businesse_users;
        echo json_encode($final);
    }

}
