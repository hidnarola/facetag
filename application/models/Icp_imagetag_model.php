<?php

/**
 * Icp Images Model - Manage ICP Images
 * @author KU
 */
class Icp_imagetag_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Insert record into table
     * @param array $data Data to be inserted
     * @return int
     */
    public function insert($data) {
        if ($this->db->insert(TBL_ICP_IMAGE_TAG, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Update record
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_record($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_ICP_IMAGE_TAG, $data)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_matchedimage_count($icp_id = NULL) {
        $this->db->select('m.id');
        if (!is_null($icp_id)) {
            $this->db->where('i.icp_id', $icp_id);
        }
        $this->db->join(TBL_ICP_IMAGES . ' i', 'm.icp_image_id=i.id', 'left');
        $query = $this->db->get(TBL_ICP_IMAGE_TAG . ' m');
        return $query->num_rows();
    }

    /**
     * Get matched images of icp
     * @param string $type - count or result get records
     * @return array
     */
    public function get_matchedimages($icp_id = NULL, $type = 'result') {
        $columns = ['m.id', 'b.image', 'i.image', 'u.firstname', 'm.is_verified', 'm.created'];

        $keyword = $this->input->get('search');

        $this->db->select('m.*,i.image as icp_image,b.image as user_image,u.firstname,u.lastname');

        $this->db->where('i.is_delete', 0);
        $this->db->where('m.is_delete', 0);

        if (!is_null($icp_id)) {
            $this->db->where('i.icp_id', $icp_id);
        }
        if (!empty($keyword['value'])) {
            $this->db->where('(firstname LIKE "%' . $keyword['value'] . '%" OR lastname LIKE "%' . $keyword['value'] . '%" OR concat(u.firstname," ",u.lastname) LIKE "%' . $keyword['value'] . '%")');
        }

        $this->db->join(TBL_ICP_IMAGES . ' i', 'm.icp_image_id=i.id', 'left');
        $this->db->join(TBL_USERS . ' u', 'm.user_id=u.id', 'left');
        $this->db->join(TBL_USER_IMAGES . ' b', 'u.bio_selfie_id=b.id', 'left');
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_ICP_IMAGE_TAG . ' m');
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_ICP_IMAGE_TAG . ' m');
            return $query->result_array();
        }
    }

    /**
     * count rows of table
     * @return number of rows
     */
    public function num_of_rows() {
        $this->db->select('id');
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_ICP_IMAGES);
        return $query->num_rows();
    }

    /**
     * Get result from table
     * @param string $condition where condition
     * @param string $select fields to be selected
     * @return array
     */
    public function get_result($condition = NULL) {
        $this->db->select('im.*,i.business_id');
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $this->db->where('im.is_delete', 0);
        $this->db->join(TBL_ICPS . ' i', 'im.icp_id=i.id', 'left');
        $query = $this->db->get(TBL_ICP_IMAGES . ' im');
        return $query->result_array();
    }

    /**
     * Check icp image tag exist or not for particular user
     * @param int $user_id
     * @param int $icp_image_id
     */
    public function check_imagetag_exist($user_id, $icp_image_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('icp_image_id', $icp_image_id);
        $query = $this->db->get(TBL_ICP_IMAGE_TAG);
        return $query->num_rows();
    }

}
