<?php

/**
 * Users model - Operations related to users table
 * @author KU
 */
class Users_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get Super admin/Business User details by admin id
     * @param int $id 
     */
    public function get_admin($id) {
        $this->db->select('*,(SELECT image from ' . TBL_USER_IMAGES . ' WHERE id=profile_image_id) as profile_image');
        $this->db->where('id', $id);
        $this->db->where('(user_role=1 OR user_role=2)');
        $query = $this->db->get(TBL_USERS);
        return $query->row_array();
    }

    /**
     * Update record
     * @param string $condition - Condition to be checked
     * @param array $user_array - Array to be updated
     * @return int
     */
    public function update_record($condition, $user_array) {
        $this->db->where($condition);
        if ($this->db->update(TBL_USERS, $user_array)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Count number of rows of table
     * @param string $condition - Condition to be applied 
     * @return int - Number of rows
     */
    public function num_of_rows($condition = null) {
        $this->db->select('id');
        if ($condition != null)
            $this->db->where($condition);

        $keyword = $this->input->get('search');
        if (!empty($keyword['value'])) {
            $this->db->where('(firstname LIKE "%' . $keyword['value'] . '%" OR lastname LIKE "%' . $keyword['value'] . '%" OR email LIKE "%' . $keyword['value'] . '%")');
        }
        $this->db->where('user_role', 3);
        $query = $this->db->get(TBL_USERS);
        return $query->num_rows();
    }

    /**
     * Get result based on datatable in user list page
     * @param string $table - Table Name
     * @param string $select - Fields to be selected from Table 
     * @return array
     */
    public function get_users($table, $select = null) {
        $columns = [TBL_USERS . '.id', TBL_USERS . '.profile_image', TBL_USERS . '.firstname', TBL_USERS . '.lastname', TBL_USERS . '.email', TBL_USERS . '.is_verified', TBL_USERS . '.created'];
        $this->db->select($select, FALSE);
        $keyword = $this->input->get('search');

        if (!empty($keyword['value'])) {
            $this->db->where('(firstname LIKE "%' . $keyword['value'] . '%" OR lastname LIKE "%' . $keyword['value'] . '%" OR email LIKE "%' . $keyword['value'] . '%")');
        }

        $this->db->where('user_role', 3);

        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        $this->db->limit($this->input->get('length'), $this->input->get('start'));

        $query = $this->db->get($table);
        return $query->result_array();
    }

    /**
     * Get result from the table based on condition
     * @param type $condition - Condition to be checked
     * @return array - Table record
     */
    public function get_result($condition = null) {
        $this->db->select('*');
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $this->db->where('user_role', 3);

        $query = $this->db->get(TBL_USERS);
        return $query->result_array();
    }

    /**
     * Insert user record into table
     * @param array $data - Data to be stored
     * @return int - Inserted Id on successful insert or 0
     */
    public function insert($data) {
        if ($this->db->insert(TBL_USERS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Check verification code exists or not in users table
     * @param string $verification_code
     * @return array
     */
    public function check_verification_code($verification_code) {
        $this->db->where('verification_code', $verification_code);
        $query = $this->db->get(TBL_USERS);
        return $query->row_array();
    }

    /**
     * Check email exist or not for Forgot password
     * @param string $email
     * @return array
     */
    public function check_email($email) {
        $this->db->where('email', $email);
        $this->db->where('user_role', 2);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_USERS);
        return $query->row_array();
    }

    /**
     * Check email exist or not for unique email
     * @param string $email
     * @return array
     */
    public function check_unique_email($email) {
        $this->db->where('email', $email);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_USERS);
        return $query->row_array();
    }

    /**
     * Check email exist or not for Subscription
     * @param string $email
     * @return array
     */
    public function check_subscribeunique_email($email) {
        $this->db->where('email', $email);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_SUBSCRIBED_USERS);
        return $query->row_array();
    }

    /**
     * Insert subscribed users record into subscribed_users table
     * @param array $data - Data to be stored
     * @return int - Inserted Id on successful insert or 0
     */
    public function insert_subscribeusers($data) {
        if ($this->db->insert(TBL_SUBSCRIBED_USERS, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Get result based on datatable in subscribed users list page
     * @param string $table - Table Name
     * @param string $select - Fields to be selected from Table 
     * @return array
     */
    public function get_subscribed_users($type = 'result') {
        $columns = ['id', 'name', 'email', 'created'];
        $keyword = $this->input->get('search');

        if (!empty($keyword['value'])) {
            $this->db->where('(first_name LIKE "%' . $keyword['value'] . '%" OR last_name LIKE "%' . $keyword['value'] . '%" OR email LIKE "%' . $keyword['value'] . '%")');
        }
        $this->db->where('is_delete', 0);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_SUBSCRIBED_USERS);
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_SUBSCRIBED_USERS);
            return $query->result_array();
        }
    }

    /**
     * Update Subscribed User record
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_subscribeduser($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_SUBSCRIBED_USERS, $data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get result from the subscribed_users table based on condition
     * @param string $condition - Condition to be checked
     * @return array - Table record
     */
    public function get_subscribed_user_result($condition = null) {
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $query = $this->db->get(TBL_SUBSCRIBED_USERS);
        return $query->result_array();
    }

    /**
     * Get User images based on condtion
     * @param string $condition - Condition to be checked
     * @return array - Table record
     */
    public function get_user_images($condition = null) {
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $query = $this->db->get(USER_IMAGES);
        return $query->result_array();
    }

    /**
     * Update User images
     * @param array $insert_array - Array to be inserted
     * @return int
     */
    public function insert_user_image($insert_array) {
        if ($this->db->insert(TBL_USER_IMAGES, $insert_array)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Insert User images
     * @param string $condition - Condition to be checked
     * @param array $update_array - Array to be updated
     * @return int
     */
    public function update_user_image($condition, $update_array) {
        $this->db->where($condition);
        if ($this->db->update(TBL_USER_IMAGES, $update_array)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get checked in users 
     * @param string $condition - Condition to be checked
     */
    public function checked_in_users($condition = NULL) {
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $select = '(SELECT GROUP_CONCAT(id) FROM ' . TBL_ICPS . ' WHERE business_id=c.business_id AND is_active=1 AND is_delete=0) business_icps';
        $this->db->select('c.business_id,c.icp_id,c.user_id,u.firstname,u.lastname,u.username,u.email,u.device_type,u.device_id,img.image as user_image,' . $select);
        $this->db->where('c.is_checked_in=1');
        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id', 'left');
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_CHECK_IN . ' c');
        return $query->result_array();
    }

    /**
     * Generated username from firstname and lastname and also checks uniqueness
     * @param string $firstname - Firstname
     * @param string $lastname - Lastname
     * @param int $id - Id not to be checked (In Edit time)
     * @return array
     */
    public function get_unique_username($firstname, $lastname, $id = NULL) {
        $username = $firstname . $lastname;
        for ($i = 0; $i < 1; $i++) {
            if ($id != NULL) {
                $this->db->where('id!=', $id);
            }
//            $this->db->where('is_delete', 0);
            $this->db->where('username', $username);
            $query = $this->db->get(TBL_USERS);
            $result = $query->row_array();

            if ($result) {
                $explode_slug = explode("-", $username);
                $last_char = $explode_slug[count($explode_slug) - 1];
                if (is_numeric($last_char)) {
                    $last_char++;
                    unset($explode_slug[count($explode_slug) - 1]);
                    $username = implode($explode_slug, "-");
                    $username.="-" . $last_char;
                } else {
                    $username.="-1";
                }
//                $text = $text . time();
                $i--;
            } else {
                return $username;
            }
        }
    }

    
    public function all_users() {
        $this->db->select('img.image,u.id as user_id,u.device_id,u.device_type');
        $this->db->where('u.is_active', 1);
        $this->db->where('u.is_delete', 0);
        $this->db->where('u.user_role', 3);
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_USERS . ' u');
//        echo $this->db->last_query();
//        exit;
        return $query->result_array();
    }
    
    /**
     * Returns all users who have checked in to particular icp/business
     * @param int $business_id - Business Id
     * @param int $icp_id - ICP id
     */
    public function get_checkedin_users($business_id, $icp_id) {
//        $query = $this->db->query('SELECT * FROM ' . TBL_CHECK_IN . ' WHERE (business_id=' . $business_id . ' OR FIND_IN_SET(' . $icp_id . ',icp_id) !=0)');
        $query = $this->db->query('SELECT * FROM ' . TBL_CHECK_IN . ' WHERE is_checked_in=1 AND (business_id=' . $business_id . ' OR FIND_IN_SET(' . $icp_id . ',icp_id) !=0)');
        return $query->result_array();
    }

    /**
     * Returns all users with bio selfi images who have checked in to particular business
     * @param int $business_id - Business Id
     */
    public function get_checkedinusers_by_business($business_id) {
        $this->db->select('DISTINCT(c.user_id),c.business_id,c.icp_id,img.image,u.device_id,u.device_type');
        $this->db->where('c.is_checked_in', 1);
        $this->db->where('(c.icp_id=\'\' OR c.icp_id IS NULL)');
        $this->db->where('c.business_id', $business_id);

        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id', 'left');
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_CHECK_IN . ' c');

        return $query->result_array();
    }
    

    /**
     * Returns all users with bio selfi images who have checked in to particular icp
     * @param int $icp_id - ICP id
     */
    public function get_checkedinusers_by_icp($icp_id) {
        $this->db->select('DISTINCT(c.user_id),c.business_id,c.icp_id,img.image,u.device_id,u.device_type');
        $this->db->where('c.is_checked_in', 1);
        $this->db->where('FIND_IN_SET(' . $icp_id . ',icp_id) !=0');

        $this->db->join(TBL_USERS . ' u', 'c.user_id=u.id', 'left');
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_CHECK_IN . ' c');

        return $query->result_array();
    }
    

    /**
     * Returns bi selfi images of users
     * @param array $user_id Array of user id
     */
    public function get_bio_selfi_images($user_id) {
        $this->db->select('img.user_id,img.image,u.device_type,u.device_id');
        $this->db->where_in('u.id', $user_id);
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_USERS . ' u');
        return $query->result_array();
    }

    /**
     * Custom Query
     * $option = 1 if return a row
     * $option = 2 if return an array
     */
    public function customQuery($query) {
        $result = $this->db->query($query);
        if ($option == 1) {
            return $result->row();
        } else if ($option == 2) {
            return $result->result();
        } else
            return $result->result_array();
    }

    /**
     * Returns the number of registered users
     */
    public function get_total_users() {
        $this->db->select('id');
        $this->db->where('is_active', 1);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_USERS);
        return $query->num_rows();
    }

    /**
     * Returns ids of check in table
     * Users whose check in time is more than 12 hours
     */
    public function get_ids_for_checked_out() {
        $this->db->select('id');
//        $this->db->where('is_checked_in=1 AND TIMESTAMPDIFF(HOUR, if(modified IS NULL,created,modified), NOW()) > 12');
        $this->db->where('is_checked_in=1 AND TIMESTAMPDIFF(HOUR,created,NOW()) > 12');
        $query = $this->db->get(TBL_CHECK_IN);
        return $query->result_array();
    }

    /**
     * Checked out users by its id array
     * @param array $ids_array
     */
    public function checked_out_users_by_id($ids_array) {
        $this->db->where_in('id', $ids_array);
        $this->db->update(TBL_CHECK_IN, array('is_checked_in' => 0));
    }

    /**
     * Returns all users who are enrolled within two hour
     */
    public function get_enrolled_users() {
        $this->db->select('u.id,u.device_type,u.device_id,img.image as user_image');
//        $this->db->where('TIMESTAMPDIFF(HOUR,u.created,NOW()) < 2');
        $this->db->where(array('u.is_active' => 1, 'u.is_delete' => 0, 'u.user_role' => 3, 'img.is_delete' => 0, 'u.searched_face_database' => 0));
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_USERS . ' u');
        return $query->result_array();
    }

    /**
     * Get result based on datatable in subscribed users list page
     * @param string $table - Table Name
     * @param string $select - Fields to be selected from Table 
     * @return array
     */
    public function get_registered_users($type = 'result') {
        $columns = ['u.id', 'img.image', 'u.firstname', 'u.email', 'u.device_type', 'u.created', 'u.is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('u.*,img.image');

        if (!empty($keyword['value'])) {
            $this->db->where('(firstname LIKE "%' . $keyword['value'] . '%" OR lastname LIKE "%' . $keyword['value'] . '%" OR email LIKE "%' . $keyword['value'] . '%")');
        }
        $this->db->where('u.is_delete', 0);
        $this->db->where('u.user_role', 3);
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_USERS . ' u');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_USERS . ' u');
            return $query->result_array();
        }
    }

    /**
     * Returns all users
     */
    public function get_active_users() {
        $this->db->select('u.id,u.device_type,u.device_id,img.image as user_image');
        $this->db->where(array('u.is_delete' => 0, 'u.user_role' => 3, 'img.is_delete' => 0));
        $this->db->join(TBL_USER_IMAGES . ' img', 'u.bio_selfie_id=img.id', 'left');
        $query = $this->db->get(TBL_USERS . ' u');
        return $query->result_array();
    }

}
