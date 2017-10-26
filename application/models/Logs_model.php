<?php

/**
 * Logs_model - Operations related to Business user log table
 * @author KU
 */
class Logs_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get Business users details with last logged in details and count
     * @param string $type - count or get records
     * @return array
     */
    public function get_business_users($type = 'result') {
        $columns = ['u.id', 'u.profile_image', 'u.firstname', 'u.email', 'b.name', 'u.login_count', 'u.last_loggedin'];
        $this->db->select('u.id,ui.image as profile_image,CONCAT(u.firstname," ",u.lastname) as username,u.email,u.login_count,u.last_loggedin,b.name as business_name');
        $this->db->join(TBL_BUSINESSES . ' b', 'u.id=b.user_id', 'left');
        $this->db->join(TBL_USER_IMAGES . ' ui', 'u.profile_image_id=ui.id', 'left');

        $keyword = $this->input->get('search');

        if (!empty($keyword['value'])) {
            $this->db->where('(u.firstname LIKE "%' . $keyword['value'] . '%" OR u.lastname LIKE "%' . $keyword['value'] . '%" OR u.email LIKE "%' . $keyword['value'] . '%" OR CONCAT(u.firstname," ",u.lastname) LIKE "%' . $keyword['value'] . '%" OR b.name LIKE "%' . $keyword['value'] . '%" OR u.login_count LIKE "%' . $keyword['value'] . '%")');
        }

        $this->db->where('u.user_role', 2);
        $this->db->where('u.is_delete', 0);
        $this->db->where('u.is_verified', 1);
        $this->db->where('u.is_active', 1);

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

}
