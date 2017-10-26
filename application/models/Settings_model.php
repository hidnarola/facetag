<?php

/**
 * Settings_model - Operations related to admin settings
 * @author KU
 */
class Settings_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get Settings
     * @param string $where - Where condition to be checked
     * @return array of Settings
     */
    public function get_settings($where = NULL) {
        if ($where) {
            $this->db->where($where);
        }
        $query = $this->db->get(TBL_SETTINGS);
        return $query->result_array();
    }

    /**
     * Insert Settings into Table
     * @param array $data
     * @return boolean
     */
    public function insert($data) {
        if ($this->db->insert(TBL_SETTINGS, $data))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Insert multiple settings into Table
     * @param array $data
     * @return boolean
     */
    public function insert_multiple($data) {
        if ($this->db->insert_batch(TBL_SETTINGS, $data))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Update batch - updates the multiple records
     * @param array $data - Data to be updated
     * @param string $field - Field to be used as condition
     */
    public function update_multiple($data, $field) {
        $this->db->update_batch(TBL_SETTINGS, $data, $field);
    }

}
