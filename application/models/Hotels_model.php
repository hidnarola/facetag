<?php

/**
 * Hotels Model - Manage Hotels of ICPS
 * @author KU
 */
class Hotels_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get result from table
     * @param int $business_id business id
     * @param string $condition Where condition to be checked
     * @return array
     */
    public function get_hotels($business_id, $condition = NULL, $type = 'result') {

        $columns = ['id', 'name', 'address', 'created'];
        $keyword = $this->input->get('search');

        if (!empty($keyword['value'])) {
            $where = '(name LIKE "%' . $keyword['value'] . '%" OR address LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }

        $this->db->where('business_id', $business_id);
        if (!is_null($condition)) {
            $this->db->where($condition);
        }

        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_HOTELS);
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_HOTELS);
            return $query->result_array();
        }
    }

    /**
     * Update record
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_record($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_HOTELS, $data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Insert record into table
     * @param array $data Data to be inserted
     * @return int
     */
    public function insert($data) {
        if ($this->db->insert(TBL_HOTELS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Get hotel details by its id
     * @param int $hotel_id Hotel id
     */
    public function get_hotel($hotel_id) {
        $this->db->where('id', $hotel_id);
        $query = $this->db->get(TBL_HOTELS);
        return $query->row_array();
    }

    /**
     * Delete record from table
     * @param int $hotel_id 
     * @return int
     */
    public function delete($hotel_id) {
        $this->db->where('id', $hotel_id);
        if ($this->db->delete(TBL_HOTELS)) {
            return 1;
        } else {
            return 0;
        }
    }

}
