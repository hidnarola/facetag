<?php

/**
 * Business Model - Manage Businesses Table
 * @author KU
 */
class Businesses_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get all business types
     */
    public function get_all_types() {
        $this->db->select('id,name');
        $query = $this->db->get(TBL_BUSINESS_TYPES);
        return $query->result_array();
    }
    
    /**
     * Get all business types
     */
    public function get_all_hear_abouts() {
        $this->db->select('id,name');
        $query = $this->db->get(TBL_USER_HEAR_ABOUTS);
        return $query->result_array();
    }
    
    /**
     * count rows of table
     * @return number of rows
     */
    public function num_of_rows() {
        $keyword = $this->input->get('search');
        $this->db->select('id');
        if (!empty($keyword['value'])) {
            $where = '(name LIKE "%' . $keyword['value'] . '%" OR description LIKE "%' . $keyword['value'] . '%" OR address1 LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }
        $this->db->where('is_verified', 1);
        $this->db->where('is_active', 1);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_BUSINESSES);
        return $query->num_rows();
    }

    /**
     * Get results based on datatable in Businesss list page
     * @param string $type - count or get records
     * @return array
     */
    public function get_all_businesses($type = 'result') {
//        $columns = ['b.id', 'b.logo', 'b.name', 'b.description', 'b.address1', 'u.firstname', 'b.created'];
        $columns = ['b.id', 'b.logo', 'b.name', 'b.address1', 'u.firstname', 'icp', 'b.created'];

        $keyword = $this->input->get('search');
        $this->db->select('b.*,u.firstname,u.lastname,u.email,(select count(id) from ' . TBL_ICPS . ' where business_id=b.id AND is_delete=0) as icp,u.is_verified as user_verified,u.is_active as user_active,u.password');
        $this->db->join(TBL_USERS . ' u', 'b.user_id=u.id', 'left');

        if (!empty($keyword['value'])) {
            $where = '(b.name LIKE "%' . $keyword['value'] . '%" OR b.description LIKE "%' . $keyword['value'] . '%" OR b.address1 LIKE "%' . $keyword['value'] . '%" OR u.firstname LIKE "%' . $keyword['value'] . '%" OR u.lastname LIKE "%' . $keyword['value'] . '%" OR CONCAT(u.firstname , " " ,u.lastname) LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }
        $this->db->where('b.is_delete', 0);

        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_BUSINESSES . ' b');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_BUSINESSES . ' b');
            return $query->result_array();
        }
    }

    /**
     * Get result from table
     * @param string $condition where condition
     * @param string $select fields to be selected
     * @return array
     */
    public function get_result($condition = null) {
        $sub_select = ',(select IF(b.other_business_type IS NOT NULL,CONCAT(GROUP_CONCAT(name), ", ", b.other_business_type),GROUP_CONCAT(name)) from ' . TBL_BUSINESS_TYPES . ' WHERE FIND_IN_SET(id,b.business_type_id) !=0) as business_type';
        $sub_select_hear_about = ',(select GROUP_CONCAT(name) from ' . TBL_USER_HEAR_ABOUTS . ' WHERE FIND_IN_SET(id,b.user_hear_abouts_id) !=0) as hear_about';
        $this->db->select('b.*,bs.paypal_email_address,bs.account_name,bs.bsb,bs.account_number,bs.commission_on_digital_image_sales_percentage,bs.commission_on_digital_image_sales,bs.commission_on_product_sales,bs.commission_on_product_sales_percentage,bs.quota,bs.monthly_subscription,bs.comments,u.firstname,u.lastname,u.email,u.phone_no,c.name as country,s.name as state,ci.name as city' . $sub_select . $sub_select_hear_about);
        $this->db->join(TBL_USERS . ' u', 'b.user_id=u.id', 'left');
        $this->db->join(TBL_COUNTRIES . ' c', 'b.country_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' s', 'b.state_id=s.id', 'left');
        $this->db->join(TBL_CITIES . ' ci', 'b.city_id=ci.id', 'left');
        $this->db->join(TBL_BUSINESSES_SETTINGS . ' bs', 'b.id=bs.business_id', 'left');

        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $this->db->where('b.is_delete', 0);
        $query = $this->db->get(TBL_BUSINESSES . ' b');
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
        if ($this->db->update(TBL_BUSINESSES, $data)) {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function update_business_settings($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_BUSINESSES_SETTINGS, $data)) {
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
        if ($this->db->insert(TBL_BUSINESSES, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }
    
     public function insert_business_settings($data) {
        if ($this->db->insert(TBL_BUSINESSES_SETTINGS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Counts number of new request
     * @return number of new business request
     * */
    public function count_new_requests() {
        $this->db->select('id');
        $this->db->where('is_verified', 0);
        $this->db->where('is_active', 0);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_BUSINESSES);
        return $query->num_rows();
    }

    /**
     * Get results based on datatable in Business Requests list page
     * @param string $type - count or get records
     * @return array
     */
    public function get_all_business_requests($type = 'result') {

//        $columns = ['b.id', 'u.firstname', 'u.email', 'b.name', 'busiess_type', 'b.daily_vistiors', 'b.created'];
        $columns = ['b.id', 'u.firstname', 'u.email', 'b.name', 'busiess_type', 'b.created'];

        $keyword = $this->input->get('search');
        $sub_select = ',(select CONCAT(GROUP_CONCAT(name),",",b.other_business_type) from ' . TBL_BUSINESS_TYPES . ' WHERE FIND_IN_SET(id,b.business_type_id) !=0) as business_type';
        $this->db->select('b.*,CONCAT(u.firstname , " " ,u.lastname) as user_name,u.email,u.is_verified as user_verified,u.is_active as user_active' . $sub_select);
        $this->db->join(TBL_USERS . ' u', 'b.user_id=u.id', 'left');

        if (!empty($keyword['value'])) {
            $where = '(b.name LIKE "%' . $keyword['value'] . '%" OR u.email LIKE "%' . $keyword['value'] . '%" OR u.firstname LIKE "%' . $keyword['value'] . '%" OR u.lastname LIKE "%' . $keyword['value'] . '%" OR CONCAT(u.firstname , " " ,u.lastname) LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }
        $this->db->where('b.is_delete', 0);
        $this->db->where('b.is_active', 0);
        $this->db->where('b.is_verified', 0);

        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_BUSINESSES . ' b');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_BUSINESSES . ' b');
            return $query->result_array();
        }
    }

    /**
     * Get business details from user id
     * @param int $user_id UserId 
     */
    public function get_business_by_userid($user_id) {
        $this->db->select('id,name');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get(TBL_BUSINESSES);
        return $query->row_array();
    }

    /**
     * Get Promo images of Businesses
     * @param int $business_id
     * @param string $type - Count or Result
     */
    public function get_promo_images($business_id, $type = 'result') {
        $this->db->where('business_id', $business_id);
        $this->db->where('is_delete', 0);

        if ($type == 'count') {
            $query = $this->db->get(TBL_BUSINESS_PROMO_IMAGES);
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_BUSINESS_PROMO_IMAGES);
            return $query->result_array();
        }
    }

    /**
     * Insert business promo images into table
     * @param array $data Data to be inserted
     * @return int
     */
    public function insert_promo_images($data) {
        if ($this->db->insert(TBL_BUSINESS_PROMO_IMAGES, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Update promo images
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_promo_image($image_id, $data) {
        $this->db->where('id', $image_id);
        if ($this->db->update(TBL_BUSINESS_PROMO_IMAGES, $data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get promoimage by promo image id
     * @param int $image_id
     * @return array
     */
    public function get_promo_image_by_id($image_id) {
        $this->db->where('id', $image_id);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_BUSINESS_PROMO_IMAGES);
        return $query->row_array();
    }

    /**
     * Gets all icps of business
     * @param int $business_id
     */
    public function get_icps($business_id) {
        $this->db->select('id');
        $this->db->where('business_id', $business_id);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_ICPS);
        return $query->result_array();
    }

    /**
     * Returns the number of businesses having atleast one icp
     * @return int
     */
    public function num_of_businesses_having_icp() {
        $this->db->where(array('i.is_delete' => 0, 'b.is_delete' => 0));
        $this->db->join(TBL_BUSINESSES . ' b', 'i.business_id=b.id');
        $this->db->group_by('i.business_id');
        $query = $this->db->get(TBL_ICPS . ' i');
        return $query->num_rows();
    }

    /**
     * Get business details by its id
     * @param int $business_id
     */
    public function get_business_by_id($business_id = NULL) {
        $this->db->select('name');
        $this->db->where('id', $business_id);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_BUSINESSES);
        return $query->row_array();
    }
    
    /**
     * Get business settings details by its id
     * @param int $business_id
     */
    public function get_business_settings_by_id($business_id = NULL) {
        $this->db->select('*');
        $this->db->where('business_id', $business_id);
        $query = $this->db->get(TBL_BUSINESSES_SETTINGS);
        return $query->row_array();
    }
}
