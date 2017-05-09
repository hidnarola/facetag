<?php

/**
 * Common db function 
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model {

    /**
     * 	function insert(),update(),delete()
     * 	@return Either Array Or Object Or Integer
     * */
    public function insert($table, $data) {
        $this->db->insert($table, $data);
        $id = $this->db->insert_id(); // fetch last inserted id in table
        return $id;
    }

    public function update($table, $id = null, $data) {
        if (is_array($id)) {
            $this->db->where($id);
        } else {
            $this->db->where('id', $id);
        }
        $this->db->update($table, $data);
        $update_id = $this->db->affected_rows(); // fetch affected rown in table.
        return $update_id;
    }

    /**
     * Common Delete function
     * @param type $id
     * @param type $table
     * @return boolean
     */
    public function delete($id, $table) {
        if (is_array($id)) {
            $this->db->where($id);
        } else {
            $this->db->where(array('id' => $id));
        }
        $this->db->delete($table);
    }

    /**
     * Common Delete_where function
     * @param string $where
     * @param string $table
     * @return boolean
     */
    public function delete_where($where, $table) {
        $this->db->where($where);
        if ($this->db->delete($table)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Common Delete_wherein function
     * @param string $field
     * @param string $values
     * @param string $table
     * @return boolean
     * @author KU
     */
    public function delete_wherein($field, $values, $table) {
        $this->db->where_in($field, $values);
        if ($this->db->delete($table)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Get Sorted array
     */
    public function alphasort($fieldname, $table) {
        $query = "select * from " . $table . " order by " . $fieldname . " ASC";
        $result = $this->db->query($query);
        return $result->result();
    }

    /**
     * Custom Query
     */
    public function customQuery($query) {
        $result = $this->db->query($query);
        return $result->result_array();
    }

    /**
     * insert_multiple records
     * @param string $table
     * @param array $data
     * @return boolean
     */
    public function insert_multiple($table, $data) {
        if ($this->db->insert_batch($table, $data))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Update batch - updates the multiple records
     * @param string $table - Name of the table
     * @param array $data - Data to be updated
     * @param string $field - Field to be used as condition
     */
    public function update_multiple($table, $data, $field) {
        $this->db->update_batch($table, $data, $field);
    }

    /**
     * Returns number of records of table
     * @param string $table - Table name
     * @param string $where - Where condition
     */
    public function num_of_records($table, $where = NULL) {
        if ($where != '') {
            $this->db->where($where);
        }
        $query = $this->db->get($table);
        return $query->num_rows();
    }

    /**
     * Returns number of records by date as key array
     * @param string $table - Table name
     * @param string $where - Where condition
     * @return array
     */
    public function num_of_records_by_date($table, $where = NULL) {
//        $query = "SELECT id,user_id,business_id,count(id) as count,created,DATE_FORMAT(created,'%Y-%m-%d') FROM `check_in` GROUP BY DATE_FORMAT(created,'%Y-%m-%d') ORDER BY `count`  DESC";
        $this->db->select("count(id) as count,DATE_FORMAT(created,'%Y-%m-%d') as date");
        if ($where != '') {
            $this->db->where($where);
        }
        $this->db->group_by("DATE_FORMAT(created,'%Y-%m-%d')");
        $query = $this->db->get($table);
        return $query->result_array();
    }

}

/* End of file Common_model.php */
/* Location: ./application/models/Common_model.php */
