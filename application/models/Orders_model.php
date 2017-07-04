<?php

/**
 * Orders Model - Manage Orders
 * @author KU
 */
class Orders_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get all user orders for particular business
     * @param int $business_id
     */
    public function get_user_orders($business_id) {
        $this->db->select("citem.id,citem.cart_id,citem.is_small_photo,citem.is_large_photo,citem.is_frame,img.image,"
                . "i.name as icp_name,i.low_resolution_price,i.high_resolution_price,"
                . "i.offer_printed_souvenir,i.printed_souvenir_price,is.is_low_image_free,is.is_high_image_free,"
                . "is.lowfree_on_highpurchase,c.is_payment_done,c.payment_type,c.created,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage,od.is_delivered");
        $this->db->join(TBL_ICP_IMAGE_TAG . ' imgtg', 'citem.selfie_id=imgtg.id');
        $this->db->join(TBL_ICP_IMAGES . ' img', 'imgtg.icp_image_id=img.id');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'img.icp_id=is.icp_id');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->join(TBL_ORDER_DETAIL . ' od', 'citem.cart_id=od.cart_id');
        $this->db->where('i.business_id', $business_id);
//        $this->db->where('c.is_payment_done', 1);
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART_ITEM . ' citem');
        return $query->result_array();
    }

    /*
     * @anp : get carrt list for super admin order list.
     */

    public function get_cart_list() {
        $this->db->select(
                "c.id,c.is_payment_done,c.created,c.total_amount,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage");
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->where('c.is_payment_done', 1);
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART . ' c');
        return $query->result_array();
    }
    
    /*
     * @anp : get cart user detail for super admin order list.
     */

    public function get_cart_user($cart_id) {
        $this->db->select(
                "c.is_payment_done,c.created,c.total_amount,u.id,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage");
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->where('c.id', $cart_id);
        $query = $this->db->get(TBL_CART . ' c');
        return $query->row_array();
    }

    /* @anp: get carrt items for cart_id. */

    public function get_cart_items($cart_id) {
        $this->db->select("citem.id,citem.cart_id,citem.is_small_photo,citem.is_large_photo,citem.is_frame,citem.status,img.image,"
                . "i.name as icp_name,i.low_resolution_price,i.high_resolution_price,"
                . "i.offer_printed_souvenir,i.printed_souvenir_price,is.is_low_image_free,is.is_high_image_free,"
                . "is.lowfree_on_highpurchase,b.id as businessId,b.name,c.is_payment_done,c.payment_type,c.created,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage,od.is_delivered");
        $this->db->join(TBL_ICP_IMAGE_TAG . ' imgtg', 'citem.selfie_id=imgtg.id');
        $this->db->join(TBL_ICP_IMAGES . ' img', 'imgtg.icp_image_id=img.id');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'img.icp_id=is.icp_id');
        $this->db->join(TBL_BUSINESSES . ' b', 'b.id=i.business_id');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->join(TBL_ORDER_DETAIL . ' od', 'citem.cart_id=od.cart_id');
        $this->db->where('citem.cart_id', $cart_id);
//        $this->db->where('c.is_payment_done', 1);
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART_ITEM . ' citem');
        return $query->result_array();
    }

    /**
     * Get order details by order id
     * @param int $order_id
     * @param int $business_id
     */
    public function get_order_details($order_id, $business_id) {
        $this->db->select("citem.id,citem.cart_id,citem.is_small_photo,citem.is_large_photo,citem.is_frame,img.image,"
                . "i.name as icp_name,i.address as icp_address,i.low_resolution_price,i.high_resolution_price,"
                . "i.offer_printed_souvenir,i.printed_souvenir_price,is.is_low_image_free,is.is_high_image_free,"
                . "is.lowfree_on_highpurchase,c.is_payment_done,c.payment_type,c.created,u.firstname,u.lastname,"
                . "u.email,uimg.image as user_bioimage,od.is_delivered,"
                . "sa.company as shipping_company,sa.building_description,sa.post_code as shipping_post_code,sa.phone_no as shipping_phone_no");
        $this->db->join(TBL_ICP_IMAGE_TAG. ' imgtg', 'citem.selfie_id=imgtg.id');
        $this->db->join(TBL_ICP_IMAGES . ' img', 'imgtg.icp_image_id=img.id');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'img.icp_id=is.icp_id');
        $this->db->join(TBL_CART . ' c', 'citem.cart_id=c.id');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_USER_IMAGES . ' uimg', 'u.bio_selfie_id=uimg.id');
        $this->db->join(TBL_ORDER_DETAIL . ' od', 'citem.cart_id=od.cart_id');
        $this->db->join(TBL_SHIPPING_ADDRESS . ' sa', 'od.hotel_id=sa.id');
//        $this->db->where('i.business_id', $business_id);
        $this->db->where('citem.id', $order_id);
        $this->db->where('c.is_payment_done', 1);
        $this->db->order_by('c.created', 'DESC');
        $query = $this->db->get(TBL_CART_ITEM . ' citem');
//        echo $this->db->last_query();
//        exit;
        return $query->row_array();
    }

    /**
     * Get orders based on datatable in Orders list page
     * @param string $type - count or result 
     * @return array
     */
    public function get_all_orders($type = 'result') {
        $columns = ['c.id', 'u.firstname', 'c.total_amount', 'c.payment_type', 'o.is_delivered', 'c.created'];

        $keyword = $this->input->get('search');

        $this->db->select("c.*,CONCAT(u.firstname ,' ' ,u.lastname) as user,u.email,o.is_delivered");

        if (!empty($keyword['value'])) {
            $where = '(u.username LIKE "%' . $keyword['value'] . '%" OR c.total_amount LIKE "%' . $keyword['value'] . '%")';
            $this->db->where($where);
        }

        $this->db->where('u.is_delete', 0);
        $this->db->where('u.is_active', 1);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id');
        $this->db->join(TBL_ORDER_DETAIL . ' o', 'c.user_id=u.id');

        if ($type == 'count') {
            $query = $this->db->get(TBL_CART . ' c');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_CART . ' c');
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
        $query = $this->db->get(TBL_ORDERS);
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
        if ($this->db->update(TBL_ORDERS, $data)) {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function update_status($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_CART_ITEM, $data)) {
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
        if ($this->db->insert(TBL_ORDERS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

}
