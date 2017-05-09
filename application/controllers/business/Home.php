<?php

/**
 * Home Controller - Manage dashboard of Business User
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('businesses_model', 'users_model', 'location_model', 'icp_images_model', 'settings_model'));
    }

    /**
     * Dashboard page of CMS
     */
    public function index() {
        $data['title'] = 'facetag | Dashboard';
        $business_id = $this->session->userdata('facetag_admin')['business_id'];
        $free_images_purchased = $this->icp_images_model->no_of_free_images_purchased($business_id);
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
        if ($event_arr) {
            $free_images_purchased_by_date = $this->icp_images_model->no_of_free_images_purchased_by_date($business_id, 'imgtag.created >="' . date('Y-m-d', strtotime($start_date)) . '" AND imgtag.created <="' . date('Y-m-d', strtotime($end_date)) . '"');
        } else {
            $free_images_purchased_by_date = $this->icp_images_model->no_of_free_images_purchased_by_date($business_id);
        }

        //-- Json data for chart
        $json_data = array(
            'checked_in_users' => $this->common_model->num_of_records_by_date(TBL_CHECK_IN, array_merge($date_array, array('is_checked_in' => 1, 'business_id' => $business_id))),
            'total_images' => $this->common_model->num_of_records_by_date(TBL_ICP_IMAGES, 'icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0 AND business_id=' . $business_id . ') AND is_delete=0' . $date_string),
            'total_matches' => $this->common_model->num_of_records_by_date(TBL_ICP_IMAGE_TAG, 'icp_image_id IN (SELECT id FROM ' . TBL_ICP_IMAGES . ' WHERE icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0 AND business_id=' . $business_id . ') AND is_delete=0) AND is_user_verified=1' . $date_string),
            'free_images_purchased' => $free_images_purchased_by_date,
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

        $dashboard_data = array(
            'checked_in_users' => $this->common_model->num_of_records(TBL_CHECK_IN, array_merge($date_array, array('is_checked_in' => 1, 'business_id' => $business_id))),
            'total_images' => $this->common_model->num_of_records(TBL_ICP_IMAGES, 'icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0 AND business_id=' . $business_id . ') AND is_delete=0' . $date_string),
            'total_matches' => $this->common_model->num_of_records(TBL_ICP_IMAGE_TAG, 'icp_image_id IN (SELECT id FROM ' . TBL_ICP_IMAGES . ' WHERE icp_id IN (select id FROM ' . TBL_ICPS . ' WHERE is_delete=0 AND business_id=' . $business_id . ') AND is_delete=0) AND is_user_verified=1' . $date_string),
            'free_images_purchased' => $free_images_purchased,
        );
        $data['dashboard_data'] = $dashboard_data;
        /*
          $large_images_purchased = $this->icp_images_model->large_images_purchased($business_id);
          $medium_images_purchased = $this->icp_images_model->medium_images_purchased($business_id);
          $small_images_purchased = $this->icp_images_model->small_images_purchased($business_id);
         * */

        $this->template->load('default', 'business/home/index', $data);
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
     * Change password of Business User
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
            redirect('business/change_password');
        }

        $this->template->load('default', 'business/home/change_password', $data);
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
     * Updates Business Profile
     */
    public function profile() {
        $data['heading'] = 'Business Profile';
        $data['title'] = 'facetag | Business Profile';

        $business_id = $this->session->userdata('facetag_admin')['business_id'];
        $business_profile = $this->businesses_model->get_result('b.id=' . $business_id);

        $data['business_data'] = $business_profile[0];
        $data['open_times'] = json_decode($business_profile[0]['open_times']);
        $data['days'] = array('mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');
        $data['countries'] = $this->location_model->get_countries();
        $data['states'] = array();
        $data['cities'] = array();

        if ($business_profile[0]['country_id'] != '')
            $data['states'] = $this->location_model->get_states($business_profile[0]['country_id']);
        if ($business_profile[0]['state_id'] != '')
            $data['cities'] = $this->location_model->get_cities($business_profile[0]['state_id']);

        $this->form_validation->set_rules('name', 'Business Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('description', 'Business Description', 'trim|required|min_length[5]|max_length[4000]');
        $this->form_validation->set_rules('digits', 'Contact No', 'trim|regex_match[/^[0-9().-]+$/]|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('address1', 'Address1', 'trim|required');
        $this->form_validation->set_rules('latitude', 'Latitiude', 'trim|required', array('required' => 'Latitude field is required. Please enter valid address!'));
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required', array('required' => 'Longitude field is required. Please enter valid address!'));
        $this->form_validation->set_rules('contact_email', 'Contact Email', 'trim|valid_email');

        if ($this->form_validation->run() == FALSE) {
//            $this->form_validation->set_error_delimiters('<div class="alert alert-error alert-danger"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
//            $this->form_validation->set_error_delimiters('<label class="validation-error-label">', '</label>');
        } else {

            $open_times = array();
            foreach ($data['days'] as $key => $day) {
                if ($this->input->post($key . '_active') != '') {
                    if ($this->input->post($key . '_active') == 1) {
                        $open_times[$key]['open'] = 1;
                        $open_times[$key]['starttime'] = date('H:i:s', strtotime($this->input->post($key . '_starttime')));
                        $open_times[$key]['endtime'] = date('H:i:s', strtotime($this->input->post($key . '_endtime')));
                    } else {
                        $open_times[$key]['open'] = 0;
                    }
                }
            }
            $open_times_json = NULL;
            if ($open_times) {
                foreach ($data['days'] as $key => $val) {
                    if (!key_exists($key, $open_times)) {
                        $open_times[$key]['open'] = 0;
                    }
                }
                $open_times = array_merge(array_flip(array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')), $open_times);
                $open_times_json = json_encode($open_times);
            }
            $flag = 0;
            $business_logo = $business_profile[0]['logo'];
            $crop_img = $this->input->post('cropimg');
            if (!empty($crop_img)) {
                $file = BUSINESS_LOGO_IMAGES . '/Logo-' . str_replace(' ', '', time()) . '.png';
                $business_logo = 'Logo-' . str_replace(' ', '', time()) . '.png';
                $imgData = base64_decode(stripslashes(substr($crop_img, 22)));
                $fp = fopen($file, 'w');
                fwrite($fp, $imgData);
                fclose($fp);
            }
//            if ($_FILES['logo']['name'] != '') {
//                $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
//                $exts = explode(".", $_FILES['logo']['name']);
//                $name = $exts[0] . time() . "." . $exts[1];
//                $name = "Logo-" . date("mdYhHis") . "." . end($exts);
//
//                $config['upload_path'] = BUSINESS_LOGO_IMAGES;
//                $config['allowed_types'] = implode("|", $img_array);
//                $config['max_size'] = '2048';
//                $config['file_name'] = $name;
//
//                $this->upload->initialize($config);
//
//                if (!$this->upload->do_upload('logo')) {
//                    $flag = 1;
//                    $data['business_logo_validation'] = $this->upload->display_errors();
//                } else {
//                    $file_info = $this->upload->data();
//                    $business_logo = $file_info['file_name'];
//                    $size = getimagesize(BUSINESS_LOGO_IMAGES . $business_logo);
//                    if ($size[0] > 2048 || $size[1] > 2048) {
//                        resize_image(BUSINESS_LOGO_IMAGES . $business_logo, BUSINESS_LOGO_IMAGES . $business_logo, 2048, 2048);
//                    }
//                }
//            }
            if ($flag != 1) {

                //--Unlink the previosly uploaded image if new image is uploaded
                if ($_FILES['logo']['name'] != '') {
                    unlink(BUSINESS_LOGO_IMAGES . $data['business_data']['logo']);
                }
                $update_array = array(
                    'name' => $this->input->post('name'),
                    'logo' => $business_logo,
                    'description' => $this->input->post('description'),
                    'open_times' => $open_times_json,
                    'contact_no' => $this->input->post('digits'),
                    'contact_email' => $this->input->post('contact_email'),
                    'address1' => $this->input->post('address1'),
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
//                    'address2' => $this->input->post('address2'),
                    'facebook_url' => $this->input->post('facebook_url'),
                    'twitter_url' => $this->input->post('twitter_url'),
                    'instagram_url' => $this->input->post('instagram_url'),
                    'website_url' => $this->input->post('website_url'),
//                    'ticket_url' => $this->input->post('ticket_url'),
                    'modified' => date('Y-m-d H:i:s')
                );
                $this->businesses_model->update_record('id=' . $business_id, $update_array);
                $this->session->set_flashdata('success', 'Business details have been updated successfully!');

                //-- If Business logged in for first time then update is_ever_loggedin field to 1
                if ($this->session->userdata('facetag_admin')['is_ever_loggedin'] == 0) {
                    $this->users_model->update_record('id =' . $this->session->userdata('facetag_admin')['id'], array('is_ever_loggedin' => 1));
                    $facetag_admin = $this->session->userdata('facetag_admin');
                    $facetag_admin['is_ever_loggedin'] = 1;
                    $this->session->unset_userdata('facetag_admin');
                    $this->session->set_userdata('facetag_admin', $facetag_admin);
                }
                redirect('business/profile');
            }
        }
        $this->template->load('default', 'business/home/business_profile', $data);
    }

    /**
     * Updates Business Private Information
     */
    public function private_information() {
        $data['heading'] = 'Business Private Information';
        $data['title'] = 'facetag | Business Private Information';

        $business_id = $this->session->userdata('facetag_admin')['business_id'];
        $this->form_validation->set_rules('is_gst_no', 'GST/VAT Number', 'trim|required', array('required' => 'This field is required'));
        if ($this->input->post('is_gst_no') == 1) {
            $this->form_validation->set_rules('gst_no', 'GST/VAT Number', 'trim|required');
        }
        $where = 'settings_key="purge_facerecogdb_time_type" OR settings_key="purge_facerecogdb_time_value" OR settings_key="creditcard_debitcard_processing_fees" OR settings_key="international_card_processing_fees" OR settings_key="transaction_fees"';
        $settings = $this->settings_model->get_settings($where);

        $settings_arr = array();
        foreach ($settings as $key => $val) {
            $settings_arr[$val['settings_key']] = $val['settings_value'];
        }
        $data['settings'] = $settings_arr;
        $where = 'b.id = ' . $this->db->escape($business_id);
        $check_business = $this->businesses_model->get_result($where);
        if ($check_business) {
            $data['business_data'] = $check_business[0];
            $data['title'] = 'facetag | Edit Business Private Information';
            $data['heading'] = 'Edit ' . $check_business[0]['name'] . ' Business';
            $data['edit'] = 1; //-- IF request is made for edit business then set edit to 1
            $data['open_times'] = json_decode($check_business[0]['open_times']);
        } else {
            show_404();
        }

        if ($this->form_validation->run() == FALSE) {
            
        } else {
            $flag = 0;
            if ($flag != 1) {
                $business_settings_details = $this->businesses_model->get_business_settings_by_id($business_id);
                if (!empty($business_settings_details)) { //-- If business id is present then edit the business details
                    $update_array_business = array(
                        'name' => $this->input->post('name'),
                        'reg_no' => $this->input->post('reg_no'),
                        'is_gst_registered' => $this->input->post('is_gst_no'),
                        'gst_no' => $this->input->post('gst_no'),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->businesses_model->update_record('id=' . $business_id, $update_array_business);
                    $update_array = array(
                        'account_name' => $this->input->post('account_name'),
                        'bsb' => $this->input->post('bsb'),
                        'account_number' => $this->input->post('account_number'),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->businesses_model->update_business_settings('business_id=' . $business_id, $update_array);
                    $this->session->set_flashdata('success', 'business settings updated successfully!');
                } else { //-- If business id is not present then add new business details
                    $update_array_business = array(
                        'reg_no' => $this->input->post('reg_no'),
                        'is_gst_registered' => $this->input->post('is_gst_no'),
                        'gst_no' => $this->input->post('gst_no'),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->businesses_model->update_record('id=' . $business_id, $update_array_business);
                    $insert_business_data = array(
                        'business_id' => $business_id,
                        'account_name' => $this->input->post('account_name'),
                        'bsb' => $this->input->post('bsb'),
                        'account_number' => $this->input->post('account_number'),
                    );
                    $this->businesses_model->insert_business_settings($insert_business_data);
                    $this->session->set_flashdata('success', 'business settings updated successfully!');
                }
                redirect('business/private_information');
            }
        }
        $this->template->load('default', 'business/home/business_private_information', $data);
    }

    /**
     * Updates Account User Profile
     */
    public function account_profile() {
        $data['heading'] = 'Account Profile';
        $data['title'] = 'facetag | Account Profile';

        $id = $this->session->userdata('facetag_admin')['id'];
        $data['user_data'] = $this->users_model->get_admin($id);

        if ($this->input->post('email') != $data['user_data']['email']) {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback_is_uniquemail');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
        }

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
        $this->form_validation->set_rules('phone_no', 'Phone Number', 'trim|required|regex_match[/^[0-9().-]+$/]');

        if ($this->form_validation->run() == FALSE) {
//            $this->form_validation->set_error_delimiters('<div class="alert alert-error alert-danger"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
//            $this->form_validation->set_error_delimiters('<label class="validation-error-label">', '</label>');
        } else {

            $flag = 0;
            $profile_image = $data['user_data']['profile_image'];
            $profile_image_id = $data['user_data']['profile_image_id'];
            if ($_FILES['profile_image']['name'] != '') {
                $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
                $exts = explode(".", $_FILES['profile_image']['name']);
                $name = $exts[0] . time() . "." . $exts[1];
                $name = "Pro-" . date("mdYhHis") . "." . end($exts);

                $config['upload_path'] = PROFILE_IMAGES;
                $config['allowed_types'] = implode("|", $img_array);
                $config['max_size'] = '2048';
                $config['file_name'] = $name;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('profile_image')) {
                    $flag = 1;
                    $data['user_image_validation'] = $this->upload->display_errors();
                } else {
                    $file_info = $this->upload->data();
                    $profile_image = $file_info['file_name'];
                    if ($data['user_data']['profile_image_id'] != '') {
                        //--Unlink the previosly uploaded image if new image is uploaded
                        unlink(PROFILE_IMAGES . $data['user_data']['profile_image']);
                        $this->users_model->update_user_image('id=' . $data['user_data']['profile_image_id'], array('image' => $profile_image));
                    } else {
                        $profile_image_id = $this->users_model->insert_user_image(array('user_id' => $id, 'image' => $profile_image, 'created' => date('Y-m-d H:i:s')));
                    }
                }
            }
            if ($flag != 1) {

                $update_array = array(
                    'firstname' => trim($this->input->post('firstname')),
                    'lastname' => trim($this->input->post('lastname')),
                    'email' => trim($this->input->post('email')),
                    'profile_image_id' => $profile_image_id,
                    'gender' => $this->input->post('gender'),
                    'phone_no' => trim($this->input->post('phone_no')),
                    'modified' => date('Y-m-d H:i:s')
                );
                $this->users_model->update_record('id=' . $id, $update_array);
                $this->session->set_flashdata('success', 'Your Profile has been updated successfully!');

                //-- Update Session on profile update
                $facetag_admin = $this->session->userdata('facetag_admin');
                $facetag_admin['email'] = trim($this->input->post('email'));
                $facetag_admin['firstname'] = trim($this->input->post('firstname'));
                $facetag_admin['lastname'] = trim($this->input->post('lastname'));
                $facetag_admin['profile_image'] = $profile_image;
                $facetag_admin['gender'] = $this->input->post('gender');

                $this->session->unset_userdata('facetag_admin');
                $this->session->set_userdata('facetag_admin', $facetag_admin);

                redirect('business/account_profile');
            }
        }
        $this->template->load('default', 'business/home/account_profile', $data);
    }

    /**
     * Display Business promo feature image page
     */
    public function promo_images() {
        $data['heading'] = 'Upload Promo Images';
        $data['title'] = 'facetag | Business Promo Images';
        $this->template->load('default', 'business/home/promo_images', $data);
    }

    /**
     * Get promo feature images for datable
     */
    public function get_promo_images() {
        $business_id = $this->session->userdata('facetag_admin')['business_id'];
        $final['recordsTotal'] = $this->businesses_model->get_promo_images($business_id, 'count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $images = $this->businesses_model->get_promo_images($business_id, 'result');
        $start = $this->input->get('start') + 1;

        foreach ($images as $key => $val) {
            $images[$key] = $val;
            $images[$key]['sr_no'] = $start++;
            $images[$key]['filesize'] = formatSizeUnits(filesize(BUSINESS_PROMO_IMAGES . $val['image']));
            $images[$key]['fileformat'] = substr(strtolower(strrchr(BUSINESS_PROMO_IMAGES . $val['image'], '.')), 1);
            $images[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $images;
        echo json_encode($final);
    }

    /**
     * Delete Business Promo Image
     * @param int $image_id 
     */
    public function delete_promo_image($image_id = NULL) {
        if (is_numeric($image_id)) {
            $update_array = array(
                'is_delete' => 1
            );
            $this->businesses_model->update_promo_image($image_id, $update_array);
            $this->session->set_flashdata('success', 'Promo Image deleted successfully!');
            redirect('business/promo_images/');
        } else {
            show_404();
        }
    }

    /**
     * Uploads business promo images
     */
    public function upload_promo_image() {
        $business_id = $this->session->userdata('facetag_admin')['business_id'];
        $biz_dir = 'business_' . $business_id;
        if (!file_exists(BUSINESS_PROMO_IMAGES . $biz_dir)) {
            mkdir(BUSINESS_PROMO_IMAGES . $biz_dir);
        }
        if (!file_exists(BUSINESS_SMALL_PROMO_IMAGES . $biz_dir)) {
            mkdir(BUSINESS_SMALL_PROMO_IMAGES . $biz_dir);
        }

        $image_name = upload_image('files', BUSINESS_PROMO_IMAGES . $biz_dir);

        //-- If image is uploaded successfully
        if (!is_array($image_name)) {
            $src = BUSINESS_PROMO_IMAGES . $biz_dir . '/' . $image_name;
            $thumb_dest = BUSINESS_SMALL_PROMO_IMAGES . $biz_dir . '/' . $image_name;
            thumbnail_image($src, $thumb_dest);
            $insert_data = array(
                'business_id' => $business_id,
                'image' => $biz_dir . '/' . $image_name,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            );
            $this->businesses_model->insert_promo_images($insert_data);
        }
    }

    /**
     * Callback function to check email validation - Email is unique or not
     * @param string $email
     * @return boolean
     */
    public function is_uniquemail($email) {
//        $email = trim($this->input->post('email'));
        $user = $this->users_model->check_unique_email($email);
        if ($user) {
            $this->form_validation->set_message('is_uniquemail', 'Email alreay exist!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Check email address entered in email_id of account profile page is unique or not
     * Called throught ajax
     */
    public function checkUniqueEmail() {
        $email = $this->session->userdata('facetag_admin')['email'];
        $requested_email = $this->input->get('email');
        if ($email != $requested_email) {
            $user = $this->users_model->check_unique_email($requested_email);
            if ($user) {
                echo "false";
            } else {
                echo "true";
            }
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Return all states for particular country
     */
    public function getStates() {
        $country_id = $this->input->post('country_id');
        $states = $this->location_model->get_states($country_id);
        echo json_encode($states);
    }

    /**
     * Return all cities for particular state
     */
    public function getCities() {
        $state_id = $this->input->post('state_id');
        $cities = $this->location_model->get_cities($state_id);
        echo json_encode($cities);
    }

}
