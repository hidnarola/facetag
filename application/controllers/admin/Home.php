<?php

/**
 * Home Controller - Manage dashboard of Super Admin
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('businesses_model', 'users_model', 'icp_images_model'));
    }

    /**
     * Dashboard page of CMS
     */
    public function index() {
        $data['title'] = 'facetag | Dashboard';
        //-- Returns the number of free images purchased
        $free_images_purchased = $this->icp_images_model->no_of_free_images_purchased();
        $date = $this->input->get('date');
        $date_array = array();
        $event_arr = array();
        $date_string = '';
        if ($date != '') {
            $dates = explode('-', $date);
            $start_date = $dates[0];
            $end_date = $dates[1];
            $date_array = array('created >=' => date('Y-m-d', strtotime($start_date)), 'created <=' => date('Y-m-d', strtotime($end_date)));
            $date_string = ' AND created >="' . date('Y-m-d', strtotime($start_date)) . '" AND created <="' . date('Y-m-d', strtotime($end_date)) . '"';
            $event_arr['from_date'] = date('Y-m-d', strtotime($start_date));
            $event_arr['to_date'] = date('Y-m-d', strtotime($end_date));
        }
        $data['json'] = json_encode("");

        if ($event_arr) {
            $free_images_purchased_by_date = $this->icp_images_model->no_of_free_images_purchased_by_date(NULL, 'imgtag.created >="' . date('Y-m-d', strtotime($start_date)) . '" AND imgtag.created <="' . date('Y-m-d', strtotime($end_date)) . '"');
        } else {
            $free_images_purchased_by_date = $this->icp_images_model->no_of_free_images_purchased_by_date();
        }

        //-- Json data for chart
        $json_data = array(
//            'ios_users' => $this->common_model->num_of_records_by_date(TBL_USERS, array_merge($date_array, array('user_role' => 3, 'is_delete' => 0, 'is_active' => 1, 'device_type' => 1))),
//            'android_users' => $this->common_model->num_of_records_by_date(TBL_USERS, array_merge($date_array, array('user_role' => 3, 'is_delete' => 0, 'is_active' => 1, 'device_type' => 0))),
            'registered_users' => $this->common_model->num_of_records_by_date(TBL_USERS, array_merge($date_array, array('user_role' => 3, 'is_delete' => 0, 'is_active' => 1))),
            'checked_in_users' => $this->common_model->num_of_records_by_date(TBL_CHECK_IN, array_merge($date_array, array('is_checked_in' => 1))),
            'total_images' => $this->common_model->num_of_records_by_date(TBL_ICP_IMAGES, 'icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0) AND is_delete=0' . $date_string),
            'total_matches' => $this->common_model->num_of_records_by_date(TBL_ICP_IMAGE_TAG, 'icp_image_id IN (SELECT id FROM ' . TBL_ICP_IMAGES . ' WHERE icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0) AND is_delete=0) AND is_user_verified=1' . $date_string),
            'free_images_purchased' => $free_images_purchased_by_date,
            'registered_businesses' => $this->common_model->num_of_records_by_date(TBL_BUSINESSES, array_merge($date_array, array('is_delete' => 0, 'is_invite' => 0))),
            'invited_businesses' => $this->common_model->num_of_records_by_date(TBL_BUSINESSES, array_merge($date_array, array('is_delete' => 0, 'is_invite!=' => 0))),
        );

        $new_json_data = array();
        $key_arrays = array();

        foreach ($json_data as $key => $val) {
            $new_array = array();
            foreach ($val as $val1) {
                $new_array[$val1['date']] = $val1['count'];
                $key_arrays[] = array($val1['date'], date('jS M \'y', strtotime($val1['date'])));
            }
            $new_json_data[$key] = $new_array;
        }

        $key_arrays = array_unique($key_arrays, SORT_REGULAR);
        usort($key_arrays, array($this, 'sortFunction'));

        $actions = [];
        foreach ($new_json_data as $k => $data_value) {
            $actions[$k] = array();
            foreach ($key_arrays as $key => $value) {
                if (isset($data_value[$value[0]])) {
                    $actions[$k][$value[0]] = array(
                        $data_value[$value[0]], $value[1]
                    );
                }
            }
        }

        $actions['key_array'] = $key_arrays;
        $data['json'] = json_encode($actions);

        $ios_users = $this->common_model->num_of_records(TBL_USERS, array_merge($date_array, array('user_role' => 3, 'is_delete' => 0, 'is_active' => 1, 'device_type' => 1)));
        $android_users = $this->common_model->num_of_records(TBL_USERS, array_merge($date_array, array('user_role' => 3, 'is_delete' => 0, 'is_active' => 1, 'device_type' => 0)));
        $dashboard_data = array(
            'ios_users' => $ios_users,
            'android_users' => $android_users,
            'registered_users' => $ios_users + $android_users,
            'checked_in_users' => $this->common_model->num_of_records(TBL_CHECK_IN, array_merge($date_array, array('is_checked_in' => 1))),
            'total_images' => $this->common_model->num_of_records(TBL_ICP_IMAGES, 'icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0) AND is_delete=0' . $date_string),
            'total_matches' => $this->common_model->num_of_records(TBL_ICP_IMAGE_TAG, 'icp_image_id IN (SELECT id FROM ' . TBL_ICP_IMAGES . ' WHERE icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0) AND is_delete=0) AND is_user_verified=1' . $date_string),
            'free_images_purchased' => $free_images_purchased,
            'registered_businesses' => $this->common_model->num_of_records(TBL_BUSINESSES, array_merge($date_array, array('is_delete' => 0, 'is_invite' => 0))),
            'invited_businesses' => $this->common_model->num_of_records(TBL_BUSINESSES, array_merge($date_array, array('is_delete' => 0, 'is_invite!=' => 0))),
            'businesses_having_icps' => $this->businesses_model->num_of_businesses_having_icp()
        );
        $data['dashboard_data'] = $dashboard_data;
        $this->template->load('default', 'admin/home/index', $data);
    }

    /**
     * Specifies sort for date array
     * @param string $a
     * @param string $b
     * @return type
     */
    function sortFunction($a, $b) {
        return strtotime($a[0]) - strtotime($b[0]);
    }

    /**
     * Change password of Admin
     */
    public function change_password() {
        $data = array();
        $data['title'] = 'Change Password';
        $data['heading'] = 'Change Password';
        $this->form_validation->set_rules('old_password', 'Old Password', 'required|callback_old_pwd_validation');
        $this->form_validation->set_rules('new_password', 'Password', 'required|min_length[5]|max_length[15]|matches[confirm_password]', array('required' => 'Please enter Password',
            'min_length' => 'Password should be of minimum 5 characters long',
            'max_length' => 'Password should be of maximum 15 characters long',
            'matches' => 'Password should match with Confirm Password'
                )
        );
        $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required', array('required' => 'Please enter Confirm Password'));

        if ($this->form_validation->run() == TRUE) {
            $admin = $this->session->userdata('facetag_admin');

            $update_data = array('password' => md5($this->input->post('new_password')));
            $this->users_model->update_record('id =' . $admin['id'], $update_data);
            $this->session->set_flashdata('success', 'Password has been changed successfully!');
            redirect('admin/change_password');
        }

        $this->template->load('default', 'admin/home/change_password', $data);
    }

    /**
     * Checks entered old password matches with saved database password
     * @return boolean
     */
    public function old_pwd_validation() {
        $admin = $this->session->userdata('facetag_admin');
        $admin_data = $this->users_model->get_admin($admin['id']);
        if (md5($this->input->post('old_password')) != $admin_data['password']) {
            $this->form_validation->set_message('old_pwd_validation', 'Please enter correct old passoword. It does not match');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Return date array for given date-range.
     * @param type $start_date
     * @param type $end_date
     * @param type $format
     * @return type
     */
    public function getRangeNDays($start_date, $end_date, $format = 'd-M') {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        $day = 86400; // Day in seconds  
        $sTime = strtotime($start_date); // Start as time  
        $eTime = strtotime($end_date); // End as time  
        $numDays = round(($eTime - $sTime) / $day) + 1;
        $days = array();
        for ($d = 0; $d < $numDays; $d++) {
            $days['"' . date('Y-m-d', ($sTime + ($d * $day))) . '"'] = date($format, ($sTime + ($d * $day)));
        }
        return $days;
    }

}
