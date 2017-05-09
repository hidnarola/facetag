<?php

/**
 * Location Model - Manage Countries,States and Cities Tables
 * @author KU
 */
class Location_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get all Countries
     */
    public function get_countries() {
        $query = $this->db->get(TBL_COUNTRIES);
        return $query->result_array();
    }

    /**
     * Get states
     * @param int $country_id
     * @return array
     */
    public function get_states($country_id = NULL) {
        if ($country_id != '') {
            $this->db->where('country_id', $country_id);
        }
        $query = $this->db->get(TBL_STATES);
        return $query->result_array();
    }

    /**
     * Get states
     * @param int $state_id
     * @return array
     */
    public function get_cities($state_id = NULL) {
        if ($state_id != '') {
            $this->db->where('state_id', $state_id);
        }
        $query = $this->db->get(TBL_CITIES);
        return $query->result_array();
    }

}
