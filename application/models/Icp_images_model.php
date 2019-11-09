<?php

/**
 * Icp Images Model - Manage ICP Images
 * @author KU
 */
class Icp_images_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get results based on datatable in ICP images list page
     * @param string $type - count or get records
     * @return array
     */
    public function get_icp_images($icp_id = NULL, $type = 'result') {
        $columns = ['id', 'image', 'created', 'icp_images', 'image_capture_time', 'created'];

        $keyword = $this->input->get('search');

        $this->db->select('*,(select count(id) from ' . TBL_ICP_IMAGES . ' where icp_id=id) as icp_images');

        if (!is_null($icp_id)) {
            $this->db->where('icp_id', $icp_id);
        }

        $this->db->where('is_delete', 0);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);

        if ($type == 'count') {
            $query = $this->db->get(TBL_ICP_IMAGES);
            return $query->num_rows();
        } else {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_ICP_IMAGES);
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
     * Update record
     * @param string $condition where condition
     * @param array $data Data to be updated
     * @return int
     */
    public function update_record($condition, $data) {
        $this->db->where($condition);
        if ($this->db->update(TBL_ICP_IMAGES, $data)) {
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
        if ($this->db->insert(TBL_ICP_IMAGES, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Insert icp physical product images into table
     * @param array $data Data to be updated
     * @return int
     */
    public function insert_physical_product($data) {
        if ($this->db->insert(TBL_ICP_PHYSICAL_PRODUCT_IMAGES, $data)) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Get ICP physical product images
     * @param int $icp_id 
     * @return array
     */
    public function get_physical_product_images($icp_id) {
        $this->db->where('icp_id', $icp_id);
        $query = $this->db->get(TBL_ICP_PHYSICAL_PRODUCT_IMAGES);
        return $query->result_array();
    }

    /**
     * Get ICP physical product image
     * @param int $image_id 
     * @return array
     */
    public function get_physical_product_image($image_id) {
        $this->db->where('id', $image_id);
        $query = $this->db->get(TBL_ICP_PHYSICAL_PRODUCT_IMAGES);
        return $query->row_array();
    }

    /**
     * Delete icp physical product image
     * @param int $image_id
     */
    public function delete_physical_product_image($image_id) {
        $this->db->where('id', $image_id);
        $this->db->delete(TBL_ICP_PHYSICAL_PRODUCT_IMAGES);
    }

    /**
     * Get count of icp_images stored in findface database
     */
    public function count_facerecog_images() {
        $this->db->select('icp_id,count(icp_id) as count');
        $this->db->where('is_face_detected=1');
        $this->db->where('is_deleted_from_face_recognition=0');
        $this->db->where('is_delete=0');
        $this->db->group_by('icp_id');
        $query = $this->db->get(TBL_ICP_IMAGES);
        return $query->result_array();
    }

    /**
     * Get count of icp_images stored in findface database
     */
    public function count_facerecogimages_by_businessid() {
        $this->db->select('ic.business_id,count(icp_id) as count');
        $this->db->where('i.is_face_detected=1');
        $this->db->where('i.is_delete=0');
        $this->db->group_by('ic.business_id');
        $this->db->join(TBL_ICPS . ' ic', 'i.icp_id=ic.id', 'left');
        $query = $this->db->get(TBL_ICP_IMAGES . ' i');
        return $query->result_array();
    }

    public function get_icpimages_bycond($condition = NULL) {
        $this->db->where($condition);
        $query = $this->db->get(TBL_ICP_IMAGES);
        return $query->result_array();
    }

    /**
     * Returns the number of free images purchased by user according to business id
     * @param int $business_id
     * @param string $where
     */
    public function no_of_free_images_purchased($business_id = NULL, $where = NULL) {
        if ($where != '') {
            $this->db->where($where);
        }
        $this->db->where('(is_low_image_free=1 OR is_high_image_free=1)');
        if ($business_id != '') {
            $this->db->where(array('i.business_id' => $business_id, 'imgtag.is_user_verified' => 1, 'imgtag.is_purchased' => 1, 'img.is_delete' => 0, 'i.is_delete' => 0));
        } else {
            $this->db->where(array('imgtag.is_user_verified' => 1, 'imgtag.is_purchased' => 1, 'img.is_delete' => 0, 'i.is_delete' => 0));
        }
        $this->db->join(TBL_ICP_IMAGES . ' img', 'img.id=imgtag.icp_image_id', 'left');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id', 'left');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'is.icp_id=i.id', 'left');
        $query = $this->db->get(TBL_ICP_IMAGE_TAG . ' imgtag');
        return $query->num_rows();
    }

    /**
     * Returns the number of free images purchased by user according to business id
     * @param int $business_id
     * @param string $where
     */
    public function no_of_free_images_purchased_by_date($business_id = NULL, $where = NULL) {
        $this->db->select("count(imgtag.id) as count,DATE_FORMAT(imgtag.modified,'%Y-%m-%d') as date");
        if ($where != '') {
            $this->db->where($where);
        }
        $this->db->where('(is_low_image_free=1 OR is_high_image_free=1)');
        if ($business_id != '') {
            $this->db->where(array('i.business_id' => $business_id, 'imgtag.is_user_verified' => 1, 'imgtag.is_purchased' => 1, 'img.is_delete' => 0, 'i.is_delete' => 0));
        } else {
            $this->db->where(array('imgtag.is_user_verified' => 1, 'imgtag.is_purchased' => 1, 'img.is_delete' => 0, 'i.is_delete' => 0));
        }
        $this->db->join(TBL_ICP_IMAGES . ' img', 'img.id=imgtag.icp_image_id', 'left');
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id', 'left');
        $this->db->join(TBL_ICP_SETTINGS . ' is', 'is.icp_id=i.id', 'left');
        $this->db->group_by("DATE_FORMAT(imgtag.modified,'%Y-%m-%d')");
        $query = $this->db->get(TBL_ICP_IMAGE_TAG . ' imgtag');
        return $query->result_array();
    }

    public function get_icp_images_to_post($icp_id = NULL, $type = 'result') {
        $columns = ['id', 'image', 'created', 'icp_images', 'image_capture_time', 'created'];

        $keyword = $this->input->get('search');

        $this->db->select('*,(select count(id) from ' . TBL_ICP_IMAGES . ' where icp_id=id) as icp_images');

        if (!is_null($icp_id)) {
            $this->db->where('icp_id', $icp_id);
        }

        $this->db->where('is_delete', 0);

        if ($type == 'count') {
            $query = $this->db->get(TBL_ICP_IMAGES);
            return $query->num_rows();
        } else {
            $query = $this->db->get(TBL_ICP_IMAGES);
            return $query->result_array();
        }
    }

    public function get_icp_selected_images($selected_images) {
        $columns = ['id', 'image', 'created', 'icp_images', 'image_capture_time', 'created'];

        $keyword = $this->input->get('search');

        $this->db->select('*');

        $this->db->where('is_delete', 0);
        $this->db->where_in('id', $selected_images);


        $query = $this->db->get(TBL_ICP_IMAGES);
        return $query->result_array();
    }

    /**
     * Get auto uploaded images by icp_id
     * @param int $icp_id
     * @author KU
     */
    public function get_auto_uploaded_images($icp_id) {
        $this->db->select("image");
        $this->db->where(['icp_id' => $icp_id, 'is_delete' => 0, 'upload_type' => 0]);
        $query = $this->db->get(TBL_ICP_IMAGES);
        return $query->result_array();
    }

    public function get_icp_access_token($icpid) {
        $this->db->select('c.access_token');
        $this->db->where(array('c.icp_id' => $icpid));
        $query = $this->db->get("connect_network" . ' c');
        return $query->row_array();
    }

    public function get_hashtags($icpid) {
        $this->db->select('i.hashtags');
        $this->db->where(array('i.id' => $icpid));
        $query = $this->db->get(TBL_ICPS . ' i');
        return $query->row_array();
    }

    /**
     * Get dossiers of icp images
     * @param int $icp_id
     */
    public function get_icp_dossiers($icp_id) {
        $this->db->select('dossier_id');
        $this->db->where('dossier_id IS NOT NULL');
        $this->db->where('icp_id', $icp_id);
        $this->db->where('is_delete=0');
        $this->db->where('is_deleted_from_face_recognition=0');
        $query = $this->db->get(TBL_ICP_IMAGES);
        return $query->result_array();
    }

    /**
     * Get ICP image which is having dossier id 
     * @param array $dossier_id
     */
    public function get_image_by_dossier($dossier_id) {
        $this->db->select('img.icp_id,img.image,img.id,i.business_id');
        $this->db->where('img.is_delete', 0);
        $this->db->where('img.dossier_id', $dossier_id);
        $this->db->join(TBL_ICPS . ' i', 'img.icp_id=i.id', 'left');
        $query = $this->db->get(TBL_ICP_IMAGES . ' img');
        return $query->row_array();
    }

}
