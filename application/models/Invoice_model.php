<?php

/**
 * Business Model - Manage Businesses Table
 * @author KU
 */
class Invoice_model extends CI_Model {

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

    public function get_total_payment($business_id) {
        $this->db->select("SUM(c.total_amount) as payment");
        $this->db->where('c.id IN (SELECT cart_id FROM cart_item WHERE cart_item.business_id = '.$business_id.')');
        $query = $this->db->get(TBL_CART . ' c');
        return $query->row_array();
    }

    /**
     * Get results based on datatable in Businesss list page
     * @param string $type - count or get records
     * @return array
     */
    public function get_all_businesses($type = "result") {
//        $columns = ['b.id', 'b.logo', 'b.name', 'b.description', 'b.address1', 'u.firstname', 'b.created'];

        $this->db->select('b.*,c.total_amount as total,u.firstname,u.lastname,u.email,u.is_verified as user_verified,u.is_active as user_active,u.password');
        $this->db->join(TBL_USERS . ' u', 'b.user_id=u.id', 'left');
        $this->db->join(TBL_CART_ITEM . ' citem', 'b.id=citem.business_id', 'left');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id', 'left');
        $this->db->where('citem.business_id is NOT NULL', NULL, FALSE);
        $this->db->where('b.is_delete', 0);
        $this->db->where('b.is_active', 1);
        $this->db->group_by('b.id');

//        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_BUSINESSES . ' b');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_BUSINESSES . ' b');
            return $query->result_array();
        }
    }

    public function get_invoice_list($business_id) {
        $this->db->select("citem.id,citem.cart_id,citem.is_small_photo,citem.is_large_photo,citem.is_frame,citem.created as createddate,img.image,"
                . "i.business_id as businessId,i.name as icp_name,i.low_resolution_price,i.high_resolution_price,"
                . "i.offer_printed_souvenir,i.printed_souvenir_price,is.is_low_image_free,is.is_high_image_free,"
                . "is.lowfree_on_highpurchase,c.total_amount,c.is_payment_done,c.payment_type,c.created,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage,od.is_delivered");
        $this->db->join(TBL_ICP_IMAGE_TAG . ' imgtg', 'citem.selfie_id=imgtg.id');
        $this->db->join(TBL_ICP_IMAGES . ' img', 'imgtg.icp_image_id=img.id');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'img.icp_id=is.icp_id');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->join(TBL_ORDER_DETAIL . ' od', 'citem.cart_id=od.cart_id');
        $this->db->where('citem.business_id', $business_id);
//        $this->db->where('c.is_payment_done', 1);
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART_ITEM . ' citem');
        return $query->result_array();
    }
    
    public function is_transfer($business_id, $start_date, $end_date) {
        $this->db->select('*');
        $this->db->where('business_id', $business_id);
        $this->db->where('start_date', $start_date);
        $this->db->where('end_date', $end_date);
        $query = $this->db->get(TBL_INVOICES);
        return $query->num_rows();
    }
    
    public function get_all_invoice_list() {
        $this->db->select("citem.id,citem.cart_id,citem.is_small_photo,citem.is_large_photo,citem.is_frame,img.image,"
                . "i.business_id as businessId,i.name as icp_name,i.low_resolution_price,i.high_resolution_price,"
                . "i.offer_printed_souvenir,i.printed_souvenir_price,is.is_low_image_free,is.is_high_image_free,"
                . "is.lowfree_on_highpurchase,c.total_amount,c.is_payment_done,c.payment_type,c.created,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage,od.is_delivered");
        $this->db->join(TBL_ICP_IMAGE_TAG . ' imgtg', 'citem.selfie_id=imgtg.id');
        $this->db->join(TBL_ICP_IMAGES . ' img', 'imgtg.icp_image_id=img.id');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'img.icp_id=is.icp_id');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->join(TBL_ORDER_DETAIL . ' od', 'citem.cart_id=od.cart_id');
        $this->db->where('citem.business_id != ', NULL);
//        $this->db->where('c.is_payment_done', 1);
//        $this->db->group_by('citem.business_id');
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART_ITEM . ' citem');
        return $query->result_array();
    }

    public function get_weekly_invoice_list($business_id, $invoice_period) {
        $arr = explode("to", $invoice_period, 2);
        $start_date = $arr[0];
        $end_date = $arr[1];
        $this->db->select("citem.id,citem.cart_id,citem.is_small_photo,citem.is_large_photo,citem.is_frame,citem.created as createddate,img.image,"
                . "i.business_id as businessId,i.name as icp_name,i.low_resolution_price,i.high_resolution_price,"
                . "i.offer_printed_souvenir,i.printed_souvenir_price,is.is_low_image_free,is.is_high_image_free,"
                . "is.lowfree_on_highpurchase,c.total_amount,c.is_payment_done,c.payment_type,c.created,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage,od.is_delivered");
        $this->db->join(TBL_ICP_IMAGE_TAG . ' imgtg', 'citem.selfie_id=imgtg.id');
        $this->db->join(TBL_ICP_IMAGES . ' img', 'imgtg.icp_image_id=img.id');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'img.icp_id=is.icp_id');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->join(TBL_ORDER_DETAIL . ' od', 'citem.cart_id=od.cart_id');
        $this->db->where('citem.business_id', $business_id);
        $this->db->where('(DATE(citem.created) BETWEEN "' . $start_date . '" AND "' . $end_date . '")');
//        $this->db->where('(DATE(citem.created) > "' . $start_date . '" AND DATE(citem.created) <="' . $end_date . '")');
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART_ITEM . ' citem');
        return $query->result_array();
    }

    public function x_week_range($date) {
        $ts = strtotime($date);
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
        return array(date('Y-m-d', $start),
            date('Y-m-d', strtotime('next sunday', $start)));
    }

    /**
     * Get result from table
     * @param string $condition where condition
     * @param string $select fields to be selected
     * @return array
     */
    public function get_result($condition = null) {
        $sub_select = ',(select IF(b.other_business_type IS NOT NULL,CONCAT(GROUP_CONCAT(name), ", ", b.other_business_type),GROUP_CONCAT(name)) from ' . TBL_BUSINESS_TYPES . ' WHERE FIND_IN_SET(id,b.business_type_id) !=0) as business_type';
        $this->db->select('b.*,bs.account_name,bs.bsb,bs.account_number,bs.creditcard_processing_fee,bs.delivery_fees,bs.commission_on_digital_image_sales,bs.commission_on_product_sales,bs.quota,bs.monthly_subscription,bs.comments,u.firstname,u.lastname,u.email,u.phone_no,c.name as country,s.name as state,ci.name as city' . $sub_select);
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

     public function insert_data($tbl,$data) {
        if ($this->db->insert($tbl, $data)) {
            return $this->db->insert_id();
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
