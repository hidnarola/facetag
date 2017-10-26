<?php

/**
 * Transactions Model - Manage Business Transactions
 * @author KU
 */
class Transactions_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get orders based on datatable in Orders list page
     * @param string $type - count or result 
     * @return array
     */
    public function get_all_transactions($type = 'result') {
        $columns = ['t.id', 'o.order_id', 't.transactionid', 't.amount', 't.payment_status', 't.created'];

        $keyword = $this->input->get('search');
        $this->db->select('o.*');

//        if (!empty($keyword['value'])) {
//            $where = '(name LIKE "%' . $keyword['value'] . '%" OR image_price LIKE "%' . $keyword['value'] . '%")';
//            $this->db->where($where);
//        }
//
        $this->db->where('is_delete', 0);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_TRANSACTIONS . ' t');
            return $query->num_rows();
        } else {

            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_TRANSACTIONS . ' t');
            return $query->result_array();
        }
    }

    /**
     * Get result from table
     * @param string $condition where condition
     * @param string $select fields to be selected
     * @return array
     */
    public function get_result($condition = NULL) {

        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_TRANSACTIONS);
        return $query->result_array();
    }

    /**
     * Update record
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_record($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_TRANSACTIONS, $data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Insert record into table
     * @param array $data Data to be updated
     * @return int
     */
    public function insert($data) {
        if ($this->db->insert(TBL_TRANSACTIONS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

}
