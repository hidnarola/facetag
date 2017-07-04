<?php

/**
 * Orders Controller - Display all orders
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('orders_model');
    }

    /**
     * Load view of orders list
     * */
    public function index() {
        $data['title'] = 'facetag | Manage Orders';
        $business_id = $this->session->userdata('facetag_admin')['business_id'];
//        $business_id = 54;
        $orders = $this->orders_model->get_user_orders($business_id);
        $data['orders'] = $orders;
        $this->template->load('default', 'business/orders/list', $data);
    }

    /**
     * Display order details
     * @param int $order_id
     */
    public function view($order_id) {
        if (is_numeric($order_id)) {
            $business_id = $this->session->userdata('facetag_admin')['business_id'];
            $order_detail = $this->orders_model->get_order_details($order_id, $business_id);
//             p($order_detail);exit;
            if ($order_detail) {
                $data['order'] = $order_detail;
                $data['title'] = 'facetag | View Order Details';
                $data['heading'] = 'View Order';
                $this->template->load('default', 'business/orders/view', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Get businesses for data table
     * */
    public function get_orders() {
        $final['recordsTotal'] = $this->orders_model->get_all_orders('count');
        $final['recordsTotal'] = 0;
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $orders = $this->orders_model->get_all_orders();
        $start = $this->input->get('start') + 1;

        foreach ($orders as $key => $val) {
            $orders[$key] = $val;
            $orders[$key]['sr_no'] = $start++;
            $orders[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $orders;
        $final['data'] = array();
        echo json_encode($final);
    }

    /**
     * Delete Order
     * @param int $order_id - Order Id
     */
    public function delete($order_id) {
        show_404();
        $where = 'id = ' . $this->db->escape($order_id);
        $order = $this->orders_model->get_result($where);
        if ($order) {
            $this->orders_model->update_record($where, array('is_delete' => 1));
            $this->session->set_flashdata('success', 'Order deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid request. Please try again!');
        }
        redirect('admin/orders');
    }

}
