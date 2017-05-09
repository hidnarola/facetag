<?php

/**
 * Orders Controller - Display all orders
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('transactions_model');
    }

    /**
     * Load view of orders list
     * */
    public function index() {
        $data['title'] = 'facetag | Manage transactions';
        $this->template->load('default', 'business/transactions/list', $data);
    }

    /**
     * Get businesses for data table
     * */
    public function get_transactions() {
        $final['recordsTotal'] = $this->transactions_model->get_all_transactions('count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $transactions = $this->transactions_model->get_all_transactions();
        $start = $this->input->get('start') + 1;

        foreach ($transactions as $key => $val) {
            $transactions[$key] = $val;
            $transactions[$key]['sr_no'] = $start++;
            $transactions[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $transactions;
        echo json_encode($final);
    }

    /**
     * Delete Transaction
     * @param int $transaction_id - Transaction Id
     */
    public function delete($transaction_id) {
        $where = 'id = ' . $this->db->escape($transaction_id);
        $order = $this->transactions_model->get_result($where);
        if ($order) {
            $this->transactions_model->update_record($where, array('is_delete' => 1));
            $this->session->set_flashdata('success', 'Transaction deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid request. Please try again!');
        }
        redirect('admin/orders');
    }

}
