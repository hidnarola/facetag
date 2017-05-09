<?php

class Login_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Used to check Super admin/Business user login credentials is correct or wrong
     * @param string $email Email id of Super admin/Business user
     * @param string $password Password 
     * @return result of array if success else retrun false
     * */
    public function get_user($email, $password) {
        $this->db->select('id,user_role,firstname,lastname,(SELECT image from ' . TBL_USER_IMAGES . ' WHERE id=profile_image_id) as profile_image,email,is_verified,is_active,is_ever_loggedin,login_count');
        $this->db->where('(user_role=1 OR user_role=2)');
        $this->db->where('email', $email);
        $this->db->where('password', md5($password));
//        $this->db->where('is_verified', 1);
//        $this->db->where('is_active', 1);
        $this->db->where('is_delete', 0);
        $users = $this->db->get(TBL_USERS);
        $user_detail = $users->row_array();
        return $user_detail;
    }

    /**
     * Get Business Details from Business user id
     * @param int $user_id
     */
    public function get_busines_id_from_user($user_id) {
        $this->db->select('id,name,is_verified,is_active');
        $this->db->where('user_id', $user_id);
        $business = $this->db->get(TBL_BUSINESSES);
        $business_detail = $business->row_array();
        return $business_detail;
    }

}
