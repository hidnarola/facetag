<?php

/**
 * Icps Model - Manage ICPS of Business
 * @author KU
 */
class Icps_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * count rows of table
     * @return number of rows
     */
    public function num_of_rows() {
        $keyword = $this->input->get('search');
        $this->db->select('id');
        if (!empty($keyword['value'])) {
            $where = '(name LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }

        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_ICPS);
        return $query->num_rows();
    }

    /**
     * Get results based on datatable in ICPs list page
     * @param string $type - count or get records
     * @return array
     */
    public function get_all_icps($business_id = NULL, $type = 'result') {
        $columns = ['i.id', 'i.icp_logo', 'i.name', 'i.description', 'icp_images', 'matched_images', 'i.created'];

        $keyword = $this->input->get('search');

        $select = '(SELECT count(m.id) FROM ' . TBL_ICP_IMAGE_TAG . ' m LEFT JOIN ' . TBL_ICP_IMAGES . ' img on m.icp_image_id=img.id WHERE img.icp_id=i.id) as matched_images';
        $this->db->select('i.*,is.local_hotel_delivery,(SELECT count(id) FROM ' . TBL_ICP_IMAGES . ' WHERE icp_id=i.id and is_delete=0) as icp_images,' . $select);

        if (!is_null($business_id)) {
            $this->db->where('i.business_id', $business_id);
        }
        if (!empty($keyword['value'])) {
            $where = '(i.name LIKE "%' . $keyword['value'] . '%" OR i.description LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }

        $this->db->where('i.is_delete', 0);
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'i.id=is.icp_id', 'left');
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_ICPS . ' i');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_ICPS . ' i');
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

        $this->db->select('i.*,b.name as businessname,b.address1 as businessaddress,is.preview_photo,is.addlogo_to_sharedimage,is.is_low_image_free,is.is_high_image_free,'
                . 'is.lowfree_on_highpurchase,is.digital_free_on_physical_purchase,is.collection_point_delivery,is.local_hotel_delivery,is.domestic_shipping,'
                . 'is.international_shipping,is.collection_address,is.collection_address_latitude,is.collection_address_longitude,'
                . 'is.collection_address_instructions,is.local_hotel_delivery_free,is.local_hotel_delivery_price,is.domestic_shipping_free,'
                . 'is.domestic_shipping_price,is.international_shipping_free,is.international_shipping_price,is.is_image_timelimited,'
                . 'is.image_availabilty_time_limit,is.allow_manual_search,is.allow_manual_search_for_date');
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $this->db->where('i.is_delete', 0);
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'i.id=is.icp_id', 'left');
        $this->db->join(TBL_BUSINESSES . ' b', 'i.business_id=b.id', 'left');
        $query = $this->db->get(TBL_ICPS . ' i');
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
        if ($this->db->update(TBL_ICPS, $data)) {
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
        if ($this->db->insert(TBL_ICPS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Update Settings
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_settings($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_ICP_SETTINGS, $data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Insert ICP Settings
     * @param array $data Data to be inserted
     * @return int
     */
    public function insert_settings($data) {
        if ($this->db->insert(TBL_ICP_SETTINGS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Returns all active icps
     */
    public function get_all_active_icps() {
        $this->db->select('i.id,i.name');
        $this->db->where(array('i.is_delete' => 0, 'i.is_active' => 1, 'b.is_delete' => 0));
        $this->db->join(TBL_BUSINESSES . ' b', 'i.business_id=b.id', 'left');
        $query = $this->db->get(TBL_ICPS . ' i');
        return $query->result_array();
    }

    public function get_icps() {
        $this->db->select('i.id,i.name');
        $this->db->where(array('i.is_delete' => 0, 'b.is_delete' => 0));
        $this->db->join(TBL_BUSINESSES . ' b', 'i.business_id=b.id', 'left');
        $query = $this->db->get(TBL_ICPS . ' i');
        return $query->result_array();
    }

}
