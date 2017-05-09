<?php

/**
 * Payments Controller - Manage all payments
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('payments_model');
    }

    /**
     * Load view of businesses list
     * */
    public function index() {
        $data['title'] = 'facetag | Payments';
        $this->template->load('default', 'admin/payments/list', $data);
    }

    /**
     * Get paymentdetails for data table
     * */
    public function get_payments() {
        $final['recordsTotal'] = $this->payments_model->get_payments('count');
        $final['recordsTotal'] = 0;
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $payments = $this->payments_model->get_payments();
        $payments = array();
        $start = $this->input->get('start') + 1;

        foreach ($payments as $key => $val) {
            $payments[$key] = $val;
            $payments[$key]['sr_no'] = $start++;
            $payments[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $payments;
        echo json_encode($final);
    }

}
