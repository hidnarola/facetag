<?php

/**
 * Businesses Controller - Manage Business users and business details
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Businesses extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['businesses_model', 'settings_model', 'users_model', 'icps_model', 'location_model',
            'icp_images_model', 'icp_imagetag_model', 'hotels_model']);
        $this->load->library('push_notification');
        //-- New API integration
        $this->load->library('findface');
    }

    /**
     * Load view of businesses list
     * */
    public function index() {
        $data['title'] = 'facetag | Approved Businesses';
        $this->template->load('default', 'admin/businesses/index', $data);
    }

    /**
     * Get businesses for data table
     * */
    public function get_businesses() {
        $final['recordsTotal'] = $this->businesses_model->get_all_businesses('count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $businesses = $this->businesses_model->get_all_businesses();
        $start = $this->input->get('start') + 1;

        foreach ($businesses as $key => $val) {
            $businesses[$key] = $val;
            $businesses[$key]['sr_no'] = $start++;
            $businesses[$key]['name'] = character_limiter(strip_tags($val['name']), 10);
            $businesses[$key]['description'] = character_limiter(strip_tags($val['description']), 15);
            $businesses[$key]['address1'] = character_limiter(strip_tags($val['address1']), 10);
            $businesses[$key]['created'] = date('d M Y', strtotime($val['created']));
        }

        $final['data'] = $businesses;
        echo json_encode($final);
    }

    /**
     * Display all new businesss requests
     */
    public function new_requests() {
        $data['title'] = 'facetag | New Requests';
        $this->template->load('default', 'admin/businesses/new_requests', $data);
    }

    /**
     * Get businesses for data table
     * */
    public function get_business_requests() {
        $final['recordsTotal'] = $this->businesses_model->get_all_business_requests('count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $businesses = $this->businesses_model->get_all_business_requests();
        $start = $this->input->get('start') + 1;

        foreach ($businesses as $key => $val) {
            $businesses[$key] = $val;
            $businesses[$key]['sr_no'] = $start++;
            $businesses[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $businesses;
        echo json_encode($final);
    }

    /**
     * Add/Edit Business Details
     */
    public function edit() {
        $business_id = $this->uri->segment(4);
        $business_logo = '';
        $data['edit'] = 0;
        $data['countries'] = $this->location_model->get_countries();
        $data['states'] = array();
        $data['cities'] = array();
        $data['days'] = array('mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');

        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
//        $this->form_validation->set_rules('reg_no', 'Registration Number', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[5]|max_length[4000]');
        $this->form_validation->set_rules('digits', 'Contact No', 'trim|regex_match[/^[0-9().-]+$/]|min_length[6]|max_length[20]');
//        $this->form_validation->set_rules('street_no', 'Street Number', 'trim|required');
//        $this->form_validation->set_rules('street_name', 'Street Name', 'trim|required');
        $this->form_validation->set_rules('address1', 'Address1', 'trim|required');

        $this->form_validation->set_rules('latitude', 'Latitiude', 'trim|required', array('required' => 'Latitude field is required. Please enter valid address!'));
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required', array('required' => 'Longitude field is required. Please enter valid address!'));

//        $this->form_validation->set_rules('country_id', 'Country', 'required');
//        $this->form_validation->set_rules('state_id', 'State', 'required');
//        $this->form_validation->set_rules('city_id', 'City', 'required');
//        $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required');
        $this->form_validation->set_rules('contact_email', 'Contact Email', 'trim|valid_email');
        if ($this->input->post('address_text')) {
            $this->form_validation->set_rules('address_display_text', 'Address display text', 'trim|required');
        }

        if (is_numeric($business_id)) {
            $where = 'b.id = ' . $this->db->escape($business_id);
            $check_business = $this->businesses_model->get_result($where);
            if ($check_business) {
                $data['business_data'] = $check_business[0];
                //-- IF business is saved then check user's email is uniquee or not
                if ($check_business[0]['is_invite'] == 2) {
                    if ($this->input->post('user_email') != $check_business[0]['email']) {
                        $this->form_validation->set_rules('user_email', 'User Email', 'trim|required|valid_email|callback_is_uniquemail');
                    }
                }
                $business_logo = $check_business[0]['logo'];
                $data['title'] = 'facetag | Edit Business';
                $data['heading'] = 'Edit ' . $check_business[0]['name'] . ' Business';
                $data['edit'] = 1; //-- IF request is made for edit business then set edit to 1
                $data['open_times'] = json_decode($check_business[0]['open_times']);
                /*
                  if ($check_business[0]['country_id'] != '')
                  $data['states'] = $this->location_model->get_states($check_business[0]['country_id']);
                  if ($check_business[0]['state_id'] != '')
                  $data['cities'] = $this->location_model->get_cities($check_business[0]['state_id']);
                 */
            } else {
                show_404();
            }
        } else {
            $data['heading'] = 'Add Business';
            $data['title'] = 'facetag | Add Business';

            $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|regex_match[/[a-z]+$/i]', array('regex_match' => 'Invalid %s! Only alphabets allowed!'));
            $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|regex_match[/[a-z]+$/i]', array('regex_match' => 'Invalid %s! Only alphabets allowed!'));
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_is_uniquemail');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|matches[repeat_password]');
            $this->form_validation->set_rules('repeat_password', 'Repeat Password', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
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
            if ($_FILES['logo']['name'] != '') {
                $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
                $exts = explode(".", $_FILES['logo']['name']);
                $name = $exts[0] . time() . "." . $exts[1];
                $name = "Logo-" . date("mdYhHis") . "." . end($exts);

                $config['upload_path'] = BUSINESS_LOGO_IMAGES;
                $config['allowed_types'] = implode("|", $img_array);
                $config['max_size'] = '2048';
                $config['file_name'] = $name;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('logo')) {
                    $flag = 1;
//                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    $data['business_logo_validation'] = $this->upload->display_errors();
                } else {
                    $file_info = $this->upload->data();
                    $business_logo = $file_info['file_name'];

                    $size = getimagesize(BUSINESS_LOGO_IMAGES . $business_logo);
                    if ($size[0] > 800 || $size[1] > 600) {
                        resize_image(BUSINESS_LOGO_IMAGES . $business_logo, BUSINESS_LOGO_IMAGES . $business_logo, 800, 600);
                    }
                }
            }
            if ($flag != 1) {
                if (is_numeric($business_id)) { //-- If business id is present then edit the business details
                    //--Unlink the previosly uploaded image if new image is uploaded
                    if ($_FILES['logo']['name'] != '') {
                        unlink(BUSINESS_LOGO_IMAGES . $data['business_data']['logo']);
                    }
                    if ($this->input->post('address_text') == 1) {
                        $address = $this->input->post('address_display_text');
                    } else {
                        $address = $check_business[0]['address_text'];
                    }
                    $update_array = array(
                        'logo' => $business_logo,
                        'name' => trim($this->input->post('name')),
                        'description' => $this->input->post('description'),
                        'ch_description' => $this->input->post('ch_description'),
                        'address1' => $this->input->post('address1'),
                        'display_text' => $this->input->post('address_text'),
                        'address_text' => $address,
                        'latitude' => $this->input->post('latitude'),
                        'longitude' => $this->input->post('longitude'),
//                        'street_no' => $this->input->post('street_no'),
//                        'street_name' => $this->input->post('street_name'),
                        'address2' => $this->input->post('address2'),
                        'facebook_url' => $this->input->post('facebook_url'),
                        'twitter_url' => $this->input->post('twitter_url'),
                        'instagram_url' => $this->input->post('instagram_url'),
                        'website_url' => $this->input->post('website_url'),
                        'ticket_url' => $this->input->post('ticket_url'),
                        'chinese_ticket_url' => $this->input->post('chinese_ticket_url'),
                        'contact_no' => $this->input->post('digits'),
                        'contact_email' => trim($this->input->post('contact_email')),
                        'open_times' => $open_times_json,
//                        'country_id' => $this->input->post('country_id'),
//                        'state_id' => $this->input->post('state_id'),
//                        'city_id' => $this->input->post('city_id'),
//                        'suburb' => $this->input->post('suburb'),
//                        'zipcode' => $this->input->post('zipcode'),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->businesses_model->update_record('id=' . $business_id, $update_array);
                    $this->session->set_flashdata('success', '"' . trim($this->input->post('name')) . '" business updated successfully!');
                    //-- IF business is saved by admin and email is updated then update user's email also
                    if ($check_business[0]['is_invite'] == 2) {
//                        $this->users_model->update_record('id=' . $check_business[0]['user_id'], array('email' => trim($this->input->post('contact_email'))));
                        $this->users_model->update_record('id=' . $check_business[0]['user_id'], array('email' => trim($this->input->post('user_email'))));
                    }
                } else { //-- If business id is not present then add new business details
                    $password = $this->input->post('password');
                    $user_name = $this->users_model->get_unique_username(trim($this->input->post('firstname')), trim($this->input->post('lastname')));
                    $insert_array = array(
                        'user_role' => 2,
                        'firstname' => $this->input->post('firstname'),
                        'lastname' => $this->input->post('lastname'),
                        'username' => $user_name,
                        'email' => $this->input->post('email'),
                        'password' => md5($password),
                        'is_verified' => 1,
                        'is_active' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                    );

                    $user_id = $this->users_model->insert($insert_array);

                    if ($this->input->post('address_text') == 1) {
                        $address = $this->input->post('address_display_text');
                    } else {
                        $address = null;
                    }

                    $insert_business_data = array(
                        'logo' => $business_logo,
                        'user_id' => $user_id,
                        'name' => $this->input->post('name'),
                        'description' => $this->input->post('description'),
                        'ch_description' => $this->input->post('ch_description'),
                        'address1' => $this->input->post('address1'),
                        'display_text' => $this->input->post('address_text'),
                        'address_text' => $address,
                        'latitude' => $this->input->post('latitude'),
                        'longitude' => $this->input->post('longitude'),
//                        'street_no' => $this->input->post('street_no'),
//                        'street_name' => $this->input->post('street_name'),
                        'address2' => $this->input->post('address2'),
                        'facebook_url' => $this->input->post('facebook_url'),
                        'twitter_url' => $this->input->post('twitter_url'),
                        'instagram_url' => $this->input->post('instagram_url'),
                        'website_url' => $this->input->post('website_url'),
                        'contact_no' => $this->input->post('digits'),
                        'contact_email' => $this->input->post('contact_email'),
                        'open_times' => $open_times_json,
                        'is_verified' => 1,
                        'is_active' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->businesses_model->insert($insert_business_data);
                    $this->session->set_flashdata('success', '"' . trim($this->input->post('name')) . '" business added successfully!');
                }
                redirect('admin/businesses');
            }
        }

        $this->template->load('default', 'admin/businesses/form', $data);
    }

    /**
     * Add/Edit Business Private Information
     */
    public function edit_private_information() {
        $business_id = $this->uri->segment(4);
        $data['edit'] = 0;

        $this->form_validation->set_rules('is_gst_no', 'GST/VAT Number', 'trim|required', array('required' => 'This field is required'));
        if ($this->input->post('is_gst_no') == 1) {
            $this->form_validation->set_rules('gst_no', 'GST/VAT Number', 'trim|required');
        }

        $where = 'b.id = ' . $this->db->escape($business_id);
        $check_business = $this->businesses_model->get_result($where);
        if ($check_business) {
            $data['business_data'] = $check_business[0];
            $data['title'] = 'facetag | Edit Business Private Information';
            $data['heading'] = 'Edit ' . $check_business[0]['name'] . ' Business';
            $data['edit'] = 1; //-- IF request is made for edit business then set edit to 1
            $data['open_times'] = json_decode($check_business[0]['open_times']);
            $where = 'settings_key="creditcard_debitcard_processing_fees" OR settings_key="transaction_fees"';
            $settings = $this->settings_model->get_settings($where);

            $settings_arr = array();
            foreach ($settings as $key => $val) {
                $settings_arr[$val['settings_key']] = $val['settings_value'];
            }
            $data['settings'] = $settings_arr;
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
                        'commission_on_digital_image_sales_percentage' => $this->input->post('commission_on_digital_image_sales_percentage'),
                        'commission_on_digital_image_sales' => $this->input->post('commission_on_digital_image_sales'),
                        'commission_on_product_sales_percentage' => $this->input->post('commission_on_product_sales_percentage'),
                        'commission_on_product_sales' => $this->input->post('commission_on_product_sales'),
                        'quota' => $this->input->post('quota'),
                        'monthly_subscription' => $this->input->post('monthly_subscription'),
//                        'comments' => $this->input->post('comments'),
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
                        'commission_on_digital_image_sales_percentage' => $this->input->post('commission_on_digital_image_sales_percentage'),
                        'commission_on_digital_image_sales' => $this->input->post('commission_on_digital_image_sales'),
                        'commission_on_product_sales_percentage' => $this->input->post('commission_on_product_sales_percentage'),
                        'commission_on_product_sales' => $this->input->post('commission_on_product_sales'),
                        'quota' => $this->input->post('quota'),
                        'monthly_subscription' => $this->input->post('monthly_subscription'),
//                        'comments' => $this->input->post('comments'),
                    );
                    $this->businesses_model->insert_business_settings($insert_business_data);
                    $this->session->set_flashdata('success', 'business settings updated successfully!');
                }
                redirect('admin/businesses');
            }
        }

        $this->template->load('default', 'admin/businesses/business_private_form', $data);
    }

    /**
     * Delete Business 
     * @param int $business_id - User id
     */
    public function delete($business_id) {
        $where = 'b.id = ' . $this->db->escape($business_id);
        $business_data = $this->businesses_model->get_result($where);

        if ($business_data) {
            $update_array = array(
                'is_delete' => 1
            );
            $user_id = $business_data[0]['user_id'];
            //-- make is_delete to 1 of businesses model
            $this->businesses_model->update_record('id = ' . $this->db->escape($business_id), $update_array);
            //-- make is_delete to 1 of users model
            $this->users_model->update_record('id = ' . $this->db->escape($user_id), $update_array);

            //-- Delete Business and ICP gallery from findface database
            $icps = $this->businesses_model->get_icps($business_id);
            foreach ($icps as $icp) {
                //-- Delete all dossiers
                $this->findface->delete_dossiers($icp['dossierlist_id']);
                $this->findface->delete_dossierlist($icp['dossierlist_id']);
            }
            $this->session->set_flashdata('success', '"' . $business_data[0]['name'] . '" business deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid request. Please try again!');
        }
        redirect('admin/businesses');
    }

    /* @anp: Generate shell script for automatic upload images. */

    public function generate_script() {
        $icp_id = $this->input->post('icp_id');
        $where = 'i.id = ' . $this->db->escape($icp_id);
        $icp_data = $this->icps_model->get_result($where);
        $business_id = $icp_data[0]['business_id'];
        $local_path = $this->input->post('local_path');
        $biz_dir = 'business_' . $business_id;
        $icp_dir = 'icp_' . $icp_id;
        //-- Create business directory if not exist
        if (!file_exists(ICP_AUTO_UPLOAD_IMAGES . $biz_dir)) {
            mkdir(ICP_AUTO_UPLOAD_IMAGES . $biz_dir);
            chmod(ICP_AUTO_UPLOAD_IMAGES . $biz_dir, 0777);
        }
        //-- Create icp directory inside business directory if not exist
        if (!file_exists(ICP_AUTO_UPLOAD_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
            mkdir(ICP_AUTO_UPLOAD_IMAGES . $biz_dir . '/' . $icp_dir);
            chmod(ICP_AUTO_UPLOAD_IMAGES . $biz_dir . '/' . $icp_dir, 0777);
        }


        $file_namess = uniqid() . time();
        $doc = ".sh";
        $name_for_file = $file_namess . $doc;
        $handle = fopen("shellscripts/" . $name_for_file, "w");
        $spacee = "\n";
        $testText = "hello";

        fwrite($handle, "#!/bin/sh");
        fwrite($handle, $spacee);
        fwrite($handle, "HOST='13.54.170.29'");
        fwrite($handle, $spacee);
        fwrite($handle, "USER='narola'");
        fwrite($handle, $spacee);
        fwrite($handle, "PASSWD='facetag123#'");
        fwrite($handle, $spacee);
        fwrite($handle, "FILE='" . $local_path . "'");
        fwrite($handle, $spacee);
        fwrite($handle, "REMOTEPATH='" . $biz_dir . "/" . $icp_dir . "'");
        fwrite($handle, $spacee);
        fwrite($handle, 'cd $FILE');
        fwrite($handle, $spacee);
        fwrite($handle, 'ftp -n $HOST <<END_SCRIPT');
        fwrite($handle, $spacee);
        fwrite($handle, 'quote USER $USER');
        fwrite($handle, $spacee);
        fwrite($handle, 'quote PASS $PASSWD');
        fwrite($handle, $spacee);
        fwrite($handle, 'cd $REMOTEPATH');
        fwrite($handle, $spacee);
        fwrite($handle, "prompt off");
        fwrite($handle, $spacee);
        fwrite($handle, 'mput *.*');
        fwrite($handle, $spacee);
        fwrite($handle, "quit");
        fwrite($handle, $spacee);
        fwrite($handle, "END_SCRIPT");
        fwrite($handle, $spacee);
        fwrite($handle, "exit 0");


        $file = "shellscripts/" . $name_for_file;
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    /**
     * Block/Unblock Business
     * @param int $business_id Business id
     */
    public function block($business_id = NULL) {
        if (is_numeric($business_id)) {

            $where = 'b.id = ' . $this->db->escape($business_id);
            $business_data = $this->businesses_model->get_result($where);
            if ($business_data) {
                $business = $business_data[0];
                if ($business['is_active'] == 0) {
                    $update_array = array(
                        'is_active' => 1
                    );
                    $this->session->set_flashdata('success', '"' . $business['name'] . '" business has been unblocked successfully!');
                } else {
                    $update_array = array(
                        'is_active' => 0
                    );
                    $this->session->set_flashdata('success', '"' . $business['name'] . '" business has been blocked successfully!');
                }
                $this->businesses_model->update_record('id = ' . $this->db->escape($business_id), $update_array);
                redirect('admin/businesses');
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Approve Business
     * @param int $business_id Business id
     */
    public function approve($business_id = NULL) {
        if (is_numeric($business_id)) {

            $where = 'b.id = ' . $this->db->escape($business_id);
            $business_data = $this->businesses_model->get_result($where);
            if ($business_data) {
                $business = $business_data[0];
                $update_array = array(
                    'is_verified' => 1,
                    'is_active' => 1
                );
                $this->businesses_model->update_record('id = ' . $this->db->escape($business_id), $update_array);
                $this->session->set_flashdata('success', '"' . $business['name'] . '" business has been approved successfully!');
                redirect('admin/businesses');
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * View Business details
     * @param int $business_id Business Id
     */
    public function view($business_id = NULL) {
        if (is_numeric($business_id)) {
            $where = 'b.id = ' . $this->db->escape($business_id);
            $business_data = $this->businesses_model->get_result($where);
            if ($business_data) {
                $data['business_data'] = $business_data[0];
                $data['title'] = 'facetag | View Business';
                $data['heading'] = 'View Business';
                $data['business_promo_images'] = $this->businesses_model->get_promo_images($business_id, 'count');
                $this->template->load('default', 'admin/businesses/view', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * List all ICPs of Businesses
     * @param int $business_id Business Id
     */
    public function icps($business_id = NULL) {
        if (is_numeric($business_id)) {
            $where = 'b.id = ' . $this->db->escape($business_id);
            $business_data = $this->businesses_model->get_result($where);
            if ($business_data) {
                $data['business_data'] = $business_data[0];
                $data['title'] = 'facetag | View Icps';
                $data['heading'] = $business_data[0]['name'] . ' ICPS';
                $this->template->load('default', 'admin/businesses/icps', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Get ICPS of Businesses for data table
     * @param int $business_id - Business ID
     */
    public function get_icps($business_id = NULL) {
        $final = array();
        if ($business_id != '') {
            $final['recordsTotal'] = $this->icps_model->get_all_icps($business_id, 'count');
            $final['redraw'] = 1;
            $final['recordsFiltered'] = $final['recordsTotal'];
            $icps = $this->icps_model->get_all_icps($business_id, 'result');
            $start = $this->input->get('start') + 1;

            foreach ($icps as $key => $val) {
                $icps[$key] = $val;
                $icps[$key]['sr_no'] = $start++;
                $icps[$key]['name'] = character_limiter(strip_tags($val['name']), 13);
                $icps[$key]['description'] = character_limiter(strip_tags($val['description']), 18);
                $icps[$key]['created'] = date('d,M Y', strtotime($val['created']));
            }
            $final['data'] = $icps;
        }
        echo json_encode($final);
    }

    /**
     * Add/Edit Business ICP
     * @param int $business_id - Business Id
     */
    public function add_icp($business_id = NULL) {
        if (is_numeric($business_id)) {
            $where = 'b.id = ' . $this->db->escape($business_id);
            $check_business = $this->businesses_model->get_result($where);
            $data['hotels'] = array();
            if ($check_business) {
                $data['business_data'] = $check_business[0];
                $icp_id = $this->uri->segment(5);

                $this->form_validation->set_rules('description', 'Description of ICP', 'trim|max_length[160]');
                if (!$this->input->post('is_low_image_free')) {
                    $this->form_validation->set_rules('low_resolution_price', 'Low Resolution/Web Pic Price', 'trim|required');
                }
                if (!$this->input->post('is_high_image_free')) {
                    $this->form_validation->set_rules('high_resolution_price', 'High Resolution/Printable Version Price', 'trim|required');
                }

                if ($this->input->post('offer_printed_souvenir')) {
                    $this->form_validation->set_rules('printed_souvenir_price', 'Printed Souvenir Price', 'trim|required');
                }

                if (is_numeric($icp_id)) {
                    $where = 'i.id = ' . $this->db->escape($icp_id);
                    $check_icp = $this->icps_model->get_result($where);
                    if ($check_icp) {
                        $icp_logo = $check_icp[0]['icp_logo'];
                        $icp_preview_image = $check_icp[0]['preview_photo'];
                        $icp_frame_image = $check_icp[0]['frame_image'];
                        $data['icp_data'] = $check_icp[0];
                        $data['physical_product_images'] = $this->icp_images_model->get_physical_product_images($icp_id);
                        $data['title'] = 'facetag | Edit ICP';
                        $data['heading'] = 'Edit ICP';
                    } else {
                        show_404();
                    }
                } else {
                    $icp_logo = '';
                    $icp_preview_image = NULL;
                    $data['heading'] = 'Add ICP';
                    $data['title'] = 'facetag | Add ICP';
                }

                if ($this->form_validation->run() == FALSE) {
//            $this->form_validation->set_error_delimiters('<div class="alert alert-error alert-danger"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
//            $this->form_validation->set_error_delimiters('<label class="validation-error-label">', '</label>');
                } else {
                    $hashtags = NULL;
                    $flag = $flag1 = $flag2 = 0;

                    //-- Upload icp logo
                    $crop_img = $this->input->post('cropimg');
                    if (!empty($crop_img)) {
                        $icp_logo = 'Logo-' . str_replace(' ', '', time()) . '.png';

                        $data_img = $crop_img;

                        list($type, $data_img) = explode(';', $data_img);
                        list(, $data_img) = explode(',', $data_img);
                        $data_img = base64_decode($data_img);

                        file_put_contents(ICP_LOGO . '/Logo-' . str_replace(' ', '', time()) . '.png', $data_img);
                    }

                    //-- Upload icp preview image
                    if ($_FILES['preview_photo']['name'] != '') {
                        $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
                        $exts = explode(".", $_FILES['preview_photo']['name']);
                        $name = $exts[0] . time() . "." . $exts[1];
                        $name = "Preview-" . date("mdYhHis") . "." . end($exts);

                        $config['upload_path'] = ICP_PREVIEW_IMAGES;
                        $config['allowed_types'] = implode("|", $img_array);
                        $config['max_size'] = '10240';
                        $config['file_name'] = $name;

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('preview_photo')) {
                            $flag1 = 1;
                            $data['preview_photo_validation'] = $this->upload->display_errors();
                        } else {
                            if (is_numeric($icp_id) && $icp_preview_image != '') {
                                unlink(ICP_PREVIEW_IMAGES . $icp_preview_image);
                            }
                            $file_info = $this->upload->data();
                            $icp_preview_image = $file_info['file_name'];
                        }
                    }
                    //-- Upload icp frame image
                    if ($_FILES['frame_image']['name'] != '') {
                        $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
                        $exts = explode(".", $_FILES['frame_image']['name']);
                        $name = $exts[0] . time() . "." . $exts[1];
                        $name = "Frame-" . date("mdYhHis") . "." . end($exts);

                        $config['upload_path'] = ICP_FRAMES;
                        $config['allowed_types'] = implode("|", $img_array);
                        $config['max_size'] = '10240';
                        $config['file_name'] = $name;

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('frame_image')) {
                            $flag1 = 1;
                            $data['frame_image_validation'] = $this->upload->display_errors();
                        } else {
                            if (is_numeric($icp_id) && $icp_frame_image != '') {
                                unlink(ICP_PREVIEW_IMAGES . $icp_frame_image);
                            }
                            $file_info = $this->upload->data();
                            $icp_frame_image = $file_info['file_name'];
                        }
                    }
                    //-- Upload icp physical printed image
                    if ($_FILES['printed_souvenir_images']['name'][0] != '') {
                        $filesCount = count($_FILES['printed_souvenir_images']['name']);
                        $uploadData = array();
                        for ($i = 0; $i < $filesCount; $i++) {
                            $_FILES['userFile']['name'] = $_FILES['printed_souvenir_images']['name'][$i];
                            $_FILES['userFile']['type'] = $_FILES['printed_souvenir_images']['type'][$i];
                            $_FILES['userFile']['tmp_name'] = $_FILES['printed_souvenir_images']['tmp_name'][$i];
                            $_FILES['userFile']['error'] = $_FILES['printed_souvenir_images']['error'][$i];
                            $_FILES['userFile']['size'] = $_FILES['printed_souvenir_images']['size'][$i];

                            $exts = explode(".", $_FILES['userFile']['name']);
                            $name = date("mdYhHis") . "." . end($exts);

                            $config['upload_path'] = ICP_PHYSICAL_PRODUCT_IMAGES;
                            $config['max_size'] = '10240';
                            $config['allowed_types'] = 'gif|jpg|png';
                            $config['file_name'] = $name;

                            $this->load->library('upload', $config);
                            $this->upload->initialize($config);
                            if ($this->upload->do_upload('userFile')) {
                                $fileData = $this->upload->data();
                                $uploadData[$i]['image'] = $fileData['file_name'];
                                $uploadData[$i]['created'] = date("Y-m-d H:i:s");
                                $uploadData[$i]['modified'] = date("Y-m-d H:i:s");
                            } else {
                                $flag2 = 1;
                                $data['printed_souvenir_image_validation'] = $this->upload->display_errors();
                            }
                        }
                    }
                    if ($flag != 1 && $flag1 != 1 && $flag2 != 1) {
                        $addlogo_to_sharedimage = $is_low_image_free = $is_high_image_free = $lowfree_on_highpurchase = $collection_point_delivery = $local_hotel_delivery = $domestic_shipping = $international_shipping = 0;
                        $low_resolution_price = $this->input->post('low_resolution_price');
                        $high_resolution_price = $this->input->post('high_resolution_price');
                        if ($this->input->post('addlogo_to_sharedimage')) {
                            $addlogo_to_sharedimage = 1;
                        }
                        if ($this->input->post('is_low_image_free')) {
                            $is_low_image_free = 1;
                            $low_resolution_price = NULL;
                        }
                        if ($this->input->post('is_high_image_free')) {
                            $is_high_image_free = 1;
                            $high_resolution_price = NULL;
                        }
                        if ($this->input->post('lowfree_on_highpurchase')) {
                            $lowfree_on_highpurchase = 1;
                        }
                        if ($this->input->post('digital_free_on_physical_purchase')) {
                            $digital_free_on_physical_purchase = 1;
                        } else {
                            $digital_free_on_physical_purchase = 0;
                        }
                        if ($this->input->post('collection_point_delivery') && $this->input->post('offer_printed_souvenir')) {
                            $collection_point_delivery = 1;
                        }
                        if ($this->input->post('local_hotel_delivery') && $this->input->post('offer_printed_souvenir')) {
                            $local_hotel_delivery = 1;
                        }
                        if ($this->input->post('domestic_shipping') && $this->input->post('offer_printed_souvenir')) {
                            $domestic_shipping = 1;
                        }
                        if ($this->input->post('international_shipping') && $this->input->post('offer_printed_souvenir')) {
                            $international_shipping = 1;
                        }
                        $offer_printed_souvenir = 0;
                        $printed_souvenir_price = NULL;
                        if ($this->input->post('offer_printed_souvenir')) {
                            $offer_printed_souvenir = 1;
                            $printed_souvenir_price = $this->input->post('printed_souvenir_price');
                        }
                        $address = $latitude = $longitude = $collection_address = $collection_address_latitude = $collection_address_longitude = $collection_address_instructions = NULL;
                        $local_hotel_delivery_price = $domestic_shipping_price = $international_shipping_price = $image_availabilty_time_limit = NULL;
                        if ($this->input->post('local_hotel_delivery_free') == 0) {
                            $local_hotel_delivery_price = $this->input->post('local_hotel_delivery_price');
                        }
                        if ($this->input->post('domestic_shipping_free') == 0) {
                            $domestic_shipping_price = $this->input->post('domestic_shipping_price');
                        }
                        if ($this->input->post('international_shipping_free') == 0) {
                            $international_shipping_price = $this->input->post('international_shipping_price');
                        }
                        if ($this->input->post('is_image_timelimited')) {
                            $image_availabilty_time_limit = $this->input->post('image_availabilty_time_limit');
                        }

                        if ($this->input->post('unique_location_for_icp')) {
                            $address = $this->input->post('address');
                            $latitude = $this->input->post('latitude');
                            $longitude = $this->input->post('longitude');
                        }
                        if ($this->input->post('collection_point_delivery')) {
                            $collection_address = $this->input->post('collection_address');
                            $collection_address_latitude = $this->input->post('collection_address_latitude');
                            $collection_address_longitude = $this->input->post('collection_address_longitude');
                            $collection_address_instructions = $this->input->post('collection_address_instructions');
                        }
                        if ($this->input->post('hashtags')) {
                            $hashtags = str_replace(' ', '', $this->input->post('hashtags'));
                        }
                        $update_settings = array(
                            'preview_photo' => $icp_preview_image,
                            'frame_image' => $icp_frame_image,
                            'addlogo_to_sharedimage' => $addlogo_to_sharedimage,
                            'is_low_image_free' => $is_low_image_free,
                            'is_high_image_free' => $is_high_image_free,
                            'lowfree_on_highpurchase' => $lowfree_on_highpurchase,
                            'digital_free_on_physical_purchase' => $digital_free_on_physical_purchase,
                            'collection_point_delivery' => $collection_point_delivery,
                            'local_hotel_delivery' => $local_hotel_delivery,
                            'domestic_shipping' => $domestic_shipping,
                            'international_shipping' => $international_shipping,
                            'collection_address' => $collection_address,
                            'collection_address_latitude' => $collection_address_latitude,
                            'collection_address_longitude' => $collection_address_longitude,
                            'collection_address_instructions' => $collection_address_instructions,
                            'local_hotel_delivery_free' => $this->input->post('local_hotel_delivery_free'),
                            'local_hotel_delivery_price' => $local_hotel_delivery_price,
                            'domestic_shipping_free' => $this->input->post('domestic_shipping_free'),
                            'domestic_shipping_price' => $domestic_shipping_price,
                            'international_shipping_free' => $this->input->post('international_shipping_free'),
                            'international_shipping_price' => $international_shipping_price,
                            'is_image_timelimited' => $this->input->post('is_image_timelimited'),
                            'image_availabilty_time_limit' => $image_availabilty_time_limit,
                            'allow_manual_search' => $this->input->post('allow_manual_search'),
                            'allow_manual_search_for_date' => $this->input->post('allow_manual_search_for_date')
                        );

                        if (is_numeric($icp_id)) { //-- If ICP id is present then edit ICP details
                            $update_array = array(
                                'name' => $this->input->post('name'),
                                'icp_logo' => $icp_logo,
                                'description' => $this->input->post('description'),
                                'address' => $address,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'low_resolution_price' => $low_resolution_price,
                                'high_resolution_price' => $high_resolution_price,
                                'offer_printed_souvenir' => $offer_printed_souvenir,
                                'printed_souvenir_price' => $printed_souvenir_price,
                                'hashtags' => $hashtags,
                                'modified' => date('Y-m-d H:i:s')
                            );

                            $this->icps_model->update_record('id=' . $icp_id, $update_array);
                            $this->icps_model->update_settings('icp_id=' . $icp_id, $update_settings);
                            $this->session->set_flashdata('success', '"' . trim($this->input->post('name')) . '" ICP updated successfully!');
                        } else { //-- If ICP id is not present then add new ICP details
                            $insert_icp_data = array(
                                'business_id' => $business_id,
                                'name' => $this->input->post('name'),
                                'icp_logo' => $icp_logo,
                                'description' => $this->input->post('description'),
                                'address' => $address,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'low_resolution_price' => $low_resolution_price,
                                'high_resolution_price' => $high_resolution_price,
                                'offer_printed_souvenir' => $offer_printed_souvenir,
                                'printed_souvenir_price' => $this->input->post('printed_souvenir_price'),
                                'hashtags' => $hashtags,
                                'is_active' => 1,
                                'created' => date('Y-m-d H:i:s'),
                                'modified' => date('Y-m-d H:i:s')
                            );
                            $icp_id = $this->icps_model->insert($insert_icp_data);

                            //-- Create icp gallery in face recognition database
                            $gallary_name = 'icp_' . $icp_id;

                            $api_response = $this->findface->adddossier_list($gallary_name);
                            if (!isset($api_response['curl_error']) && isset($api_response['id'])) {
                                $this->icps_model->update_record(['id' => $icp_id], ['dossierlist_id' => $api_response['id']]);
                            }

                            $update_settings['icp_id'] = $icp_id;
                            $this->icps_model->insert_settings($update_settings);
                            $this->session->set_flashdata('success', '"' . trim($this->input->post('name')) . '" ICP added successfully!');
                        }
                        if ($_FILES['printed_souvenir_images']['name'][0] != '') {
                            foreach ($uploadData as $val) {
                                $image_data = $val;
                                $image_data['icp_id'] = $icp_id;
                                $this->icp_images_model->insert_physical_product($image_data);
                            }
                        }
                        redirect('admin/businesses/icps/' . $business_id);
                    }
                }
                $this->template->load('default', 'admin/businesses/icp_form', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Callback function to check price validation
     * @param string $str
     * @return boolean
     */
    public function decimal_numeric($str) {
        if (!is_numeric($str)) { //Use your logic to check here
            $this->form_validation->set_message('decimal_numeric', 'The %s field must contain a decimal number.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Delete ICP 
     * @param int $icp_id - ICP id
     */
    public function delete_icp($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $where = 'i.id = ' . $this->db->escape($icp_id);
            $icp_data = $this->icps_model->get_result($where);

            if ($icp_data) {
                $update_array = array(
                    'is_delete' => 1,
                    'dossierlist_id' => null
                );
                $this->icps_model->update_record('id = ' . $this->db->escape($icp_id), $update_array);
                $this->session->set_flashdata('success', '"' . $icp_data[0]['name'] . '" ICP deleted successfully!');

                //-- Delete all dossiers
                $this->findface->delete_dossiers($icp_data[0]['dossierlist_id']);

                //-- Delete ICP gallery (Dossierlist/watchlist) from findface API database
                $this->findface->delete_dossierlist($icp_data[0]['dossierlist_id']);
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('admin/businesses/icps/' . $icp_data[0]['business_id']);
        } else {
            show_404();
        }
    }

    /**
     * Display all icp images
     * @param int $icp_id - ICP ID
     */
    public function icp_images($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $where = 'i.id = ' . $this->db->escape($icp_id);
            $icp_data = $this->icps_model->get_result($where);
            if ($icp_data) {
                $data['icp_data'] = $icp_data[0];
                $data['title'] = 'facetag | ICP Images';
                $data['heading'] = $icp_data[0]['name'] . ' Images';
                $data['matched_image_count'] = $this->icp_imagetag_model->get_matchedimage_count($icp_id);
                $this->template->load('default', 'admin/businesses/icp_images', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Get ICP images of ICP for data table
     * @param int $icp_id - ICP ID
     */
    public function get_icp_images($icp_id = NULL) {

        $final = array();
        if ($icp_id != '') {
            $final['recordsTotal'] = $this->icp_images_model->get_icp_images($icp_id, 'count');
            $final['redraw'] = 1;
            $final['recordsFiltered'] = $final['recordsTotal'];
            $icps = $this->icp_images_model->get_icp_images($icp_id, 'result');
            $start = $this->input->get('start') + 1;

            foreach ($icps as $key => $val) {
                $icps[$key] = $val;
                $icps[$key]['sr_no'] = $start++;
                $icps[$key]['filesize'] = formatSizeUnits(filesize(ICP_IMAGES . $val['image']));
                $icps[$key]['fileformat'] = substr(strtolower(strrchr(ICP_IMAGES . $val['image'], '.')), 1);
                $icps[$key]['created'] = date('d,M Y', strtotime($val['created']));
            }
            $final['data'] = $icps;
        }
        echo json_encode($final);
    }

    /**
     * Delete ICP Image
     * @param int $icp_image_id - ICP Image id
     */
    public function delete_icp_image($icp_image_id = NULL) {
        if (is_numeric($icp_image_id)) {
            $where = 'im.id = ' . $this->db->escape($icp_image_id);
            $icp_image_data = $this->icp_images_model->get_result($where);

            if ($icp_image_data) {
                $update_array = array(
                    'is_delete' => 1
                );

                //-- If face is detected in icp image then delete it from findface database

                if ($icp_image_data[0]['is_face_detected'] == 1) {
                    $this->findface->delete_dossier($icp_image_data[0]['dossier_id']);
                    $update_array['is_deleted_from_face_recognition'] = 1;
                }
                $this->icp_images_model->update_record('id = ' . $this->db->escape($icp_image_id), $update_array);

                $this->session->set_flashdata('success', 'ICP Image deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('admin/businesses/icp_images/' . $icp_image_data[0]['icp_id']);
        } else {
            show_404();
        }
    }

    /**
     * Add image to ICP
     * @param int $icp_id
     */
    function add_image($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $where = 'i.id = ' . $this->db->escape($icp_id);
            $icp_data = $this->icps_model->get_result($where);
            if ($icp_data) {
                $data['icp_data'] = $icp_data[0];
                $data['title'] = 'facetag | Add ICP Images';
                $data['heading'] = 'Upload ' . $icp_data[0]['name'] . ' Images';
                $this->template->load('default', 'admin/businesses/add_icp_image', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Uploads selected images
     * @param int $icp_id
     */
    public function upload_image($icp_id) {

        $where = 'i.id = ' . $this->db->escape($icp_id);
        $icp_data = $this->icps_model->get_result($where);
        if ($icp_data) {
            $dossierlist_id = $icp_data[0]['dossierlist_id'];
            $business_id = $icp_data[0]['business_id'];
            $icp_id = $icp_data[0]['id'];

            $biz_dir = 'business_' . $icp_data[0]['business_id'];
            //-- Create business directory if not exist
            if (!file_exists(ICP_IMAGES . $biz_dir)) {
                mkdir(ICP_IMAGES . $biz_dir);
            }
            if (!file_exists(ICP_BLUR_IMAGES . $biz_dir)) {
                mkdir(ICP_BLUR_IMAGES . $biz_dir);
            }
            if (!file_exists(ICP_SMALL_IMAGES . $biz_dir)) {
                mkdir(ICP_SMALL_IMAGES . $biz_dir);
            }
            $icp_dir = 'icp_' . $icp_id;
            //-- Create icp directory inside business directory if not exist
            if (!file_exists(ICP_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_IMAGES . $biz_dir . '/' . $icp_dir);
                chmod(ICP_IMAGES . $biz_dir . '/' . $icp_dir, 0777);
            }
            if (!file_exists(ICP_BLUR_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir);
            }
            if (!file_exists(ICP_SMALL_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir);
            }
            $image_name = upload_image('photo', ICP_IMAGES . $biz_dir . '/' . $icp_dir);

            //-- If image is uploaded successfully
            if (!is_array($image_name)) {
                $insert_data = array(
                    'icp_id' => $icp_id,
                    'image' => $biz_dir . '/' . $icp_dir . '/' . $image_name,
                    'upload_type' => 1,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                    'image_capture_time' => date('Y-m-d H:i:s', strtotime($this->input->post('image_capture_time')))
                );

                //-- Store image into thumb
                $src = ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                $thumb_dest = ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                thumbnail_image($src, $thumb_dest);

                //-- Convert image into blur image
                blur_image(FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name, FCPATH . ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir . '/');
                $icp_image_id = $this->icp_images_model->insert($insert_data);

                //-- Gallery name for business and icp 
                $icp_gallary_name = 'icp_' . $icp_id;

                //-- Detects faces on uploaded ICP image and and store it in face recognition database
                $photo = base_url(ICP_IMAGES) . $biz_dir . '/' . $icp_dir . '/' . $image_name;


                $response = $this->findface->detect($photo);

                if (isset($response['faces']) && !empty($response['faces'])) {

                    $total_users = $this->users_model->get_total_users();
                    //-- Get checked in users who have checked in to particualr icps/business
                    $business_users = $this->users_model->get_checkedinusers_by_business($business_id);
                    $icp_users = $this->users_model->get_checkedinusers_by_icp($icp_id);
                    //-- merge both users
                    $users = array_merge($business_users, $icp_users);

                    $userids = array_column($users, 'user_id');

                    //-- create dossier
                    $dresponse = $this->findface->adddossier($dossierlist_id, 'icpimage_' . $icp_image_id);

                    if (isset($dresponse['id'])) {
                        $dossier_id = $dresponse['id'];
                        $face_id = [];
                        foreach ($response['faces'] as $key1 => $face) {
                            $detection_id = $face['id'];
                            //-- add face into dossier
                            $fresponse = $this->findface->adddossierface($dossier_id, $detection_id, $photo);
                            if (isset($fresponse['id'])) {
                                $face_id[] = $fresponse['id'];
                            }

                            //-- Search face in userselfie list
                            $match_images = $this->findface->identify($detection_id, USERDOSSIERLIST_ID, $total_users);

                            if (isset($match_images['results']) && !empty($match_images['results'])) {
                                $dossier_ids = array_column($match_images['results'], 'id');
                                $detected_users = $this->users_model->get_users_by_dossier($dossier_ids);
                                foreach ($detected_users as $key2 => $detected_user) {

                                    if (in_array($detected_user['id'], $userids)) {
                                        //-- if image is verified then store it into image_tag table and send push notification to user
                                        $icp_image_tag = array(
                                            'icp_image_id' => $icp_image_id,
                                            'user_id' => $detected_user['id'],
                                            'is_user_verified' => 0,
                                            'is_purchased' => 0,
                                            'created' => date('Y-m-d H:i:s'),
                                            'modified' => date('Y-m-d H:i:s'));
                                        $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                                        $url = $photo;

                                        $source_x = trim($face['bbox']['left']);
                                        $source_y = trim($face['bbox']['top']);
                                        $x2 = trim($face['bbox']['right']);
                                        $y2 = trim($face['bbox']['bottom']);

                                        $width = $x2 - $source_x;
                                        $height = $y2 - $source_y;
                                        crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);


                                        $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                        if ($detected_user['device_type'] == 0) {
                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);
                                            $pushData = array(
                                                "notification_type" => "data",
                                                "body" => $messageText,
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp_id,
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );
                                            if (!empty($detected_user['device_id'])) {
                                                $response = $this->push_notification->sendPushToAndroid(array($detected_user['device_id']), $pushData, FALSE);
                                            }
                                        } else {
                                            $url = '';
                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);
                                            $pushData = array(
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp_id,
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );
                                            if (!empty($detected_user['device_id'])) {
                                                $response = $this->push_notification->sendPushiOS(array('deviceToken' => $detected_user['device_id'], 'pushMessage' => $messageText), $pushData);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $update_data = array('is_face_detected' => 1, 'dossier_id' => $dossier_id, 'dossierface_ids' => implode(",", $face_id));
                        $this->icp_images_model->update_record('id=' . $icp_image_id, $update_data);
                    }
                }
            }
        } else {
            show_404();
        }
    }

    /* @anp : upload image from directory. */

    public function upload_image_dir() {
        $icp_id = $this->input->post("icp_id");
        if ($_FILES) {
            if ($_FILES['files']['name'][0] != '') {
                $filesCount = count($_FILES['files']['name']);
                for ($i = 0; $i < $filesCount; $i++) {
                    $_FILES['userFile']['name'] = $_FILES['files']['name'][$i];
                    $_FILES['userFile']['type'] = $_FILES['files']['type'][$i];
                    $_FILES['userFile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['userFile']['error'] = $_FILES['files']['error'][$i];
                    $_FILES['userFile']['size'] = $_FILES['files']['size'][$i];
                    $allowed = array('gif', 'png', 'jpg', 'jpeg');
                    $filename = $_FILES['files']['name'][$i];
                    $ext = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
                    if (in_array($ext, $allowed)) {
                        $where = 'i.id = ' . $this->db->escape($icp_id);
                        $icp_data = $this->icps_model->get_result($where);

                        if ($icp_data) {
                            $business_id = $icp_data[0]['business_id'];
                            $dossierlist_id = $icp_data[0]['dossierlist_id'];

                            $biz_dir = 'business_' . $icp_data[0]['business_id'];
                            //-- Create business directory if not exist
                            if (!file_exists(ICP_IMAGES . $biz_dir)) {
                                mkdir(ICP_IMAGES . $biz_dir);
                            }
                            if (!file_exists(ICP_BLUR_IMAGES . $biz_dir)) {
                                mkdir(ICP_BLUR_IMAGES . $biz_dir);
                            }
                            if (!file_exists(ICP_SMALL_IMAGES . $biz_dir)) {
                                mkdir(ICP_SMALL_IMAGES . $biz_dir);
                            }
                            $icp_dir = 'icp_' . $icp_id;
                            //-- Create icp directory inside business directory if not exist
                            if (!file_exists(ICP_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                                mkdir(ICP_IMAGES . $biz_dir . '/' . $icp_dir);
                            }
                            if (!file_exists(ICP_BLUR_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                                mkdir(ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir);
                            }
                            if (!file_exists(ICP_SMALL_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                                mkdir(ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir);
                            }
                            $image_name = upload_image('userFile', ICP_IMAGES . $biz_dir . '/' . $icp_dir);
                            //-- If image is uploaded successfully
                            if (!is_array($image_name)) {
                                $insert_data = array(
                                    'icp_id' => $icp_id,
                                    'image' => $biz_dir . '/' . $icp_dir . '/' . $image_name,
                                    'upload_type' => 0,
                                    'created' => date('Y-m-d H:i:s'),
                                    'modified' => date('Y-m-d H:i:s'),
                                    'image_capture_time' => date('Y-m-d H:i:s', strtotime($this->input->post('image_dir_capture_time')))
                                );

                                //-- Store image into thumb
                                $src = ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                                $thumb_dest = ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                                thumbnail_image($src, $thumb_dest);

                                //-- Convert image into blur image
                                blur_image(FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name, FCPATH . ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir . '/');
                                $icp_image_id = $this->icp_images_model->insert($insert_data);

                                //-- Gallery name for business and icp 
                                $icp_gallary_name = 'icp_' . $icp_id;

                                //-- Detects faces on uploaded ICP image and and store it in face recognition database
                                $photo = base_url(ICP_IMAGES) . $biz_dir . '/' . $icp_dir . '/' . $image_name;

                                $response = $this->findface->detect($photo);

                                if (isset($response['faces']) && !empty($response['faces'])) {

                                    $total_users = $this->users_model->get_total_users();
                                    //-- Get checked in users who have checked in to particualr icps/business
                                    $business_users = $this->users_model->get_checkedinusers_by_business($business_id);
                                    $icp_users = $this->users_model->get_checkedinusers_by_icp($icp_id);
                                    //-- merge both users
                                    $users = array_merge($business_users, $icp_users);

                                    $userids = array_column($users, 'user_id');

                                    //-- create dossier
                                    $dresponse = $this->findface->adddossier($dossierlist_id, 'icpimage_' . $icp_image_id);

                                    if (isset($dresponse['id'])) {
                                        $dossier_id = $dresponse['id'];
                                        $face_id = [];
                                        foreach ($response['faces'] as $key1 => $face) {
                                            $detection_id = $face['id'];
                                            //-- add face into dossier
                                            $fresponse = $this->findface->adddossierface($dossier_id, $detection_id, $photo);
                                            if (isset($fresponse['id'])) {
                                                $face_id[] = $fresponse['id'];
                                            }

                                            //-- Search face in userselfie list
                                            $match_images = $this->findface->identify($detection_id, USERDOSSIERLIST_ID, $total_users);

                                            if (isset($match_images['results']) && !empty($match_images['results'])) {
                                                $dossier_ids = array_column($match_images['results'], 'id');
                                                $detected_users = $this->users_model->get_users_by_dossier($dossier_ids);
                                                foreach ($detected_users as $key2 => $detected_user) {

                                                    if (in_array($detected_user['id'], $userids)) {
                                                        //-- if image is verified then store it into image_tag table and send push notification to user
                                                        $icp_image_tag = array(
                                                            'icp_image_id' => $icp_image_id,
                                                            'user_id' => $detected_user['id'],
                                                            'is_user_verified' => 0,
                                                            'is_purchased' => 0,
                                                            'created' => date('Y-m-d H:i:s'),
                                                            'modified' => date('Y-m-d H:i:s'));
                                                        $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                                                        $url = $photo;

                                                        $source_x = trim($face['bbox']['left']);
                                                        $source_y = trim($face['bbox']['top']);
                                                        $x2 = trim($face['bbox']['right']);
                                                        $y2 = trim($face['bbox']['bottom']);

                                                        $width = $x2 - $source_x;
                                                        $height = $y2 - $source_y;
                                                        crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);


                                                        $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                                        if ($detected_user['device_type'] == 0) {
                                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                                            $icp_data = $this->icps_model->get_result($where);
                                                            $pushData = array(
                                                                "notification_type" => "data",
                                                                "body" => $messageText,
                                                                "selfietagid" => $icp_image_tag_id,
                                                                "businessid" => $icp_img_data[0]['business_id'],
                                                                "businessname" => $icp_data[0]['businessname'],
                                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                                "icpid" => $icp_id,
                                                                "icpname" => $icp_data[0]['name'],
                                                                "icpaddress" => $icp_data[0]['address'],
                                                                "imgid" => $icp_image_id,
                                                                "image" => $icp_img_data[0]['image']
                                                            );
                                                            if (!empty($detected_user['device_id'])) {
                                                                $response = $this->push_notification->sendPushToAndroid(array($detected_user['device_id']), $pushData, FALSE);
                                                            }
                                                        } else {
                                                            $url = '';
                                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                                            $icp_data = $this->icps_model->get_result($where);
                                                            $pushData = array(
                                                                "selfietagid" => $icp_image_tag_id,
                                                                "businessid" => $icp_img_data[0]['business_id'],
                                                                "businessname" => $icp_data[0]['businessname'],
                                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                                "icpid" => $icp_id,
                                                                "icpname" => $icp_data[0]['name'],
                                                                "icpaddress" => $icp_data[0]['address'],
                                                                "imgid" => $icp_image_id,
                                                                "image" => $icp_img_data[0]['image']
                                                            );
                                                            if (!empty($detected_user['device_id'])) {
                                                                $response = $this->push_notification->sendPushiOS(array('deviceToken' => $detected_user['device_id'], 'pushMessage' => $messageText), $pushData);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $update_data = array('is_face_detected' => 1, 'dossier_id' => $dossier_id, 'dossierface_ids' => implode(",", $face_id));
                                        $this->icp_images_model->update_record('id=' . $icp_image_id, $update_data);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->session->set_flashdata('success', 'Images uploaded successfully!');
                echo json_encode($icp_image_id);
                exit;
            } else {
                $data = ['success' => false, 'msg' => 'Please enter directory!'];
                $this->session->set_flashdata('error', 'Please enter directory!');
                echo json_encode($data);
                exit;
            }
        } else {
            $data = ['success' => false, 'msg' => 'Please try again!'];
            $this->session->set_flashdata('error', 'Please try again!');
            echo json_encode($data);
            exit;
        }
    }

    public function upload_crop_image($icp_id) {
        $where = 'i.id = ' . $this->db->escape($icp_id);
        $icp_data = $this->icps_model->get_result($where);
        if ($icp_data) {

            $biz_dir = 'business_' . $icp_data[0]['business_id'];

            $business_id = $icp_data[0]['business_id'];
            $dossierlist_id = $icp_data[0]['dossierlist_id'];

            //-- Create business directory if not exist
            if (!file_exists(ICP_IMAGES . $biz_dir)) {
                mkdir(ICP_IMAGES . $biz_dir);
            }
            if (!file_exists(ICP_BLUR_IMAGES . $biz_dir)) {
                mkdir(ICP_BLUR_IMAGES . $biz_dir);
            }
            if (!file_exists(ICP_SMALL_IMAGES . $biz_dir)) {
                mkdir(ICP_SMALL_IMAGES . $biz_dir);
            }
            if (!file_exists(ICP_CROPPED_IMAGES . '/' . $biz_dir)) {
                mkdir(ICP_CROPPED_IMAGES . $biz_dir);
                chmod(ICP_CROPPED_IMAGES . $biz_dir, 0777);
            }
            $icp_dir = 'icp_' . $icp_id;
            //-- Create icp directory inside business directory if not exist
            if (!file_exists(ICP_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_IMAGES . $biz_dir . '/' . $icp_dir);
            }
            if (!file_exists(ICP_BLUR_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir);
            }
            if (!file_exists(ICP_SMALL_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir);
            }
            if (!file_exists(ICP_CROPPED_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
                mkdir(ICP_CROPPED_IMAGES . $biz_dir . '/' . $icp_dir);
                chmod(ICP_CROPPED_IMAGES . $biz_dir . '/' . $icp_dir, 0777);
            }
            $image_name = upload_image('original_img', ICP_IMAGES . $biz_dir . '/' . $icp_dir);
            //-- If image is uploaded successfully
            if (!is_array($image_name)) {
                $insert_data = array(
                    'icp_id' => $icp_id,
                    'image' => $biz_dir . '/' . $icp_dir . '/' . $image_name,
                    'upload_type' => 1,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                    'image_capture_time' => date('Y-m-d H:i:s', strtotime($this->input->post('image_crop_capture_time')))
                );

                $icp_img = $image_name;
                $crop_img_dta = $this->input->post('cropimg');

                if (!empty($crop_img_dta)) {
                    unlink(FCPATH . ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $icp_img);
                    $matches = array();

                    preg_match('/^[a-zA-Z0-9]+/', $image_name, $matches);

                    $filenameOnly = $matches[0];
                    $crop_img = FCPATH . ICP_CROPPED_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $filenameOnly . '.png';
                    $icp_img = $filenameOnly . '.png';
                    $imgData = base64_decode(stripslashes(substr($crop_img_dta, 22)));
                    $fp = fopen($crop_img, 'w');
                    fwrite($fp, $imgData);
                    fclose($fp);
                    copy($crop_img, FCPATH . ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $icp_img);
                }


                //-- Store image into thumb
                $src = FCPATH . ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $icp_img;
                $thumb_dest = FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $icp_img;
                thumbnail_image($src, $thumb_dest);

                //-- Convert image into blur image
                blur_image(FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $icp_img, FCPATH . ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir . '/');

                $insert_data['image'] = $biz_dir . '/' . $icp_dir . '/' . $icp_img;
                $icp_image_id = $this->icp_images_model->insert($insert_data);
                //-- Gallery name for business and icp 
                $gallary_name = 'business_' . $icp_data[0]['business_id'];
                $icp_gallary_name = 'icp_' . $icp_id;

                //-- Detects faces on uploaded ICP image and and store it in face recognition database
                $photo = base_url(ICP_IMAGES) . $biz_dir . '/' . $icp_dir . '/' . $icp_img;

                $response = $this->findface->detect($photo);

                if (isset($response['faces']) && !empty($response['faces'])) {

                    $total_users = $this->users_model->get_total_users();
                    //-- Get checked in users who have checked in to particualr icps/business
                    $business_users = $this->users_model->get_checkedinusers_by_business($business_id);
                    $icp_users = $this->users_model->get_checkedinusers_by_icp($icp_id);
                    //-- merge both users
                    $users = array_merge($business_users, $icp_users);

                    $userids = array_column($users, 'user_id');

                    //-- create dossier
                    $dresponse = $this->findface->adddossier($dossierlist_id, 'icpimage_' . $icp_image_id);

                    if (isset($dresponse['id'])) {
                        $dossier_id = $dresponse['id'];
                        $face_id = [];
                        foreach ($response['faces'] as $key1 => $face) {
                            $detection_id = $face['id'];
                            //-- add face into dossier
                            $fresponse = $this->findface->adddossierface($dossier_id, $detection_id, $photo);
                            if (isset($fresponse['id'])) {
                                $face_id[] = $fresponse['id'];
                            }

                            //-- Search face in userselfie list
                            $match_images = $this->findface->identify($detection_id, USERDOSSIERLIST_ID, $total_users);

                            if (isset($match_images['results']) && !empty($match_images['results'])) {
                                $dossier_ids = array_column($match_images['results'], 'id');
                                $detected_users = $this->users_model->get_users_by_dossier($dossier_ids);
                                foreach ($detected_users as $key2 => $detected_user) {

                                    if (in_array($detected_user['id'], $userids)) {
                                        //-- if image is verified then store it into image_tag table and send push notification to user
                                        $icp_image_tag = array(
                                            'icp_image_id' => $icp_image_id,
                                            'user_id' => $detected_user['id'],
                                            'is_user_verified' => 0,
                                            'is_purchased' => 0,
                                            'created' => date('Y-m-d H:i:s'),
                                            'modified' => date('Y-m-d H:i:s'));
                                        $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                                        $url = $photo;

                                        $source_x = trim($face['bbox']['left']);
                                        $source_y = trim($face['bbox']['top']);
                                        $x2 = trim($face['bbox']['right']);
                                        $y2 = trim($face['bbox']['bottom']);

                                        $width = $x2 - $source_x;
                                        $height = $y2 - $source_y;
                                        crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);


                                        $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                        if ($detected_user['device_type'] == 0) {
                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);
                                            $pushData = array(
                                                "notification_type" => "data",
                                                "body" => $messageText,
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp_id,
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );
                                            if (!empty($detected_user['device_id'])) {
                                                $response = $this->push_notification->sendPushToAndroid(array($detected_user['device_id']), $pushData, FALSE);
                                            }
                                        } else {
                                            $url = '';
                                            $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                            $icp_img_data = $this->icp_images_model->get_result($where);
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);
                                            $pushData = array(
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp_id,
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );
                                            if (!empty($detected_user['device_id'])) {
                                                $response = $this->push_notification->sendPushiOS(array('deviceToken' => $detected_user['device_id'], 'pushMessage' => $messageText), $pushData);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $update_data = array('is_face_detected' => 1, 'dossier_id' => $dossier_id, 'dossierface_ids' => implode(",", $face_id));
                        $this->icp_images_model->update_record('id=' . $icp_image_id, $update_data);
                    }
                }

                $this->session->set_flashdata('success', 'Image uploaded successfully!');
                redirect('admin/businesses/icp_images/' . $icp_id);
            }
        } else {
            show_404();
        }
    }

    /**
     * Send invitation link to business for signup with facetag
     * @param int $ajax either 0 or 1
     * @param int $invite either 1 or 2, 1 to send invitation email to business and 2 to only save data
     */
    public function invite($ajax = 0, $invite = 1) {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('address1', 'Address1', 'trim|required');
        $this->form_validation->set_rules('latitude', 'Latitiude', 'trim|required', array('required' => 'Latitude field is required. Please enter valid address!'));
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required', array('required' => 'Longitude field is required. Please enter valid address!'));
        $this->form_validation->set_rules('digits', 'Contact No', 'trim|regex_match[/^[0-9().-]+$/]');
        $this->form_validation->set_rules('contact_email', 'Contact Email', 'trim|required|valid_email|callback_is_uniquemail');
        $this->form_validation->set_rules('description', 'Description', 'trim|min_length[5]|max_length[4000]');

        if ($this->input->post('email')) {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback_is_uniquemail');
        }

        $data['heading'] = 'Invite Business';
        $data['title'] = 'facetag | Invite Business';
        $data['days'] = array('mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');

        if ($this->form_validation->run() == TRUE) {
            $flag = 0;
            $business_logo = NULL;
            $user_email = trim($this->input->post('contact_email'));
            if ($this->input->post('email') != '') {
                $user_email = trim($this->input->post('email'));
            }
            $user_name = $this->users_model->get_unique_username($user_email, '');
            $crop_img = $this->input->post('cropimg');
            if (!empty($crop_img)) {
                $business_logo = 'Logo-' . str_replace(' ', '', time()) . '.png';

                $data_img = $crop_img;

                list($type, $data_img) = explode(';', $data_img);
                list(, $data_img) = explode(',', $data_img);
                $data_img = base64_decode($data_img);

                file_put_contents(BUSINESS_LOGO_IMAGES . '/Logo-' . str_replace(' ', '', time()) . '.png', $data_img);
            }
            if ($flag != 1) {
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

                $verification_code = verification_code();
                $insert_array = array(
                    'user_role' => 2,
                    'username' => $user_name,
                    'email' => $user_email,
                    'password' => md5($verification_code),
                    'is_verified' => 1,
                    'is_active' => 1,
                    'verification_code' => $verification_code,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                );

                $user_id = $this->users_model->insert($insert_array);

                $insert_business_data = array(
                    'logo' => $business_logo,
                    'user_id' => $user_id,
                    'name' => $this->input->post('name'),
                    'address1' => $this->input->post('address1'),
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
                    'is_verified' => 1,
                    'is_active' => 1,
                    'facebook_url' => $this->input->post('facebook_url'),
                    'twitter_url' => $this->input->post('twitter_url'),
                    'instagram_url' => $this->input->post('instagram_url'),
                    'website_url' => $this->input->post('website_url'),
                    'ticket_url' => $this->input->post('ticket_url'),
                    'chinese_ticket_url' => $this->input->post('chinese_ticket_url'),
                    'contact_no' => $this->input->post('digits'),
                    'contact_email' => $this->input->post('contact_email'),
                    'description' => $this->input->post('description'),
                    'ch_description' => $this->input->post('ch_description'),
                    'open_times' => $open_times_json,
                    'is_invite' => $invite,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s')
                );
                $business_id = $this->businesses_model->insert($insert_business_data);

                if ($invite == 1) {
//                    $url = site_url() . 'login';
                    $verification_code = $this->encrypt->encode($verification_code);
                    $encoded_verification_code = urlencode($verification_code);
                    $url = site_url() . 'set_password?code=' . $encoded_verification_code;

                    $configs = mail_config();
                    $this->load->library('email', $configs);
//                    $this->email->initialize($configs);
                    $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
                    $this->email->to($user_email);

//            $msg = $this->load->view('email_templates/invite_email', array('firstname' => $this->input->post('firstname'), 'lastname' => $this->input->post('lastname'), 'url' => $url, 'business' => $this->input->post('name')), true);
                    $msg = $this->load->view('email_templates/invite_email', array('email' => $user_email, 'password' => $verification_code, 'url' => $url, 'business' => $this->input->post('name')), true);
                    $this->email->subject('Invitation - facetag');
                    $this->email->message($msg);
                    $this->email->set_mailtype("html");
                    $this->email->send();
                    $this->email->print_debugger();
                    $this->session->set_userdata('invitation_success_msg', '"' . trim($this->input->post('name')) . '" business added successfully! and Invitation Email has been sent to ' . $user_email . ' Successfully');
                } else {
                    $this->session->set_userdata('invitation_success_msg', '"' . trim($this->input->post('name')) . '" business added successfully!');
                }

                //-- if ajax request is not made then redirect to businesss page or else echo business id 
                if ($ajax == 0) {
                    redirect('admin/businesses');
                } else {
                    echo json_encode(array('error' => 0, 'business_id' => $business_id));
                    exit;
                }
            } else {
                if ($ajax == 1) {
                    echo json_encode(array('error' => 1, 'error_message' => $logo_upload_error_msg));
                    exit;
                }
            }
        } else {
            if ($ajax == 1) {
                echo json_encode(array('error' => 1, 'error_message' => validation_errors()));
                exit;
            }
        }
        $this->template->load('default', 'admin/businesses/invite', $data);
    }

    /**
     * Send invitation email to business which has been saved by Super Admin
     * @param int $business_id
     */
    public function invite_mail($business_id = NULL) {
        $where = 'b.id = ' . $this->db->escape($business_id);
        $check_business = $this->businesses_model->get_result($where);
        if ($check_business && ($check_business[0]['is_invite'] == 2)) {

            $business_data = $check_business[0];
            $verification_code = verification_code();

            $update_array = array(
                'password' => md5($verification_code),
                'verification_code' => $verification_code,
                'modified' => date('Y-m-d H:i:s'),
            );

//            $url = site_url() . 'login';

            $verification_code = $this->encrypt->encode($verification_code);
            $encoded_verification_code = urlencode($verification_code);
            $url = site_url() . 'set_password?code=' . $encoded_verification_code;

            $configs = mail_config();
            $this->load->library('email', $configs);
//            $this->email->initialize($configs);
            $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
            $this->email->to($business_data['email']);

            $msg = $this->load->view('email_templates/invite_email', array('email' => $business_data['email'], 'password' => $verification_code, 'url' => $url, 'business' => $business_data['name']), true);
            $this->email->subject('Invitation - facetag');
            $this->email->set_mailtype("html");

            $this->email->message($msg);
            $this->email->send();
            $this->email->print_debugger();

            //-- update users password and verification code
            $user_id = $business_data['user_id'];
            $this->users_model->update_record('id = ' . $this->db->escape($user_id), $update_array);

            //-- update business table set is_invite to 1
            $this->businesses_model->update_record('id = ' . $this->db->escape($business_id), array('is_invite' => 1));
            $this->session->set_userdata('invitation_success_msg', 'Invitation Email has been sent to ' . $business_data['email'] . ' successfully!');
            redirect('admin/businesses');
        }
    }

    /**
     * Callback function to check email validation - Email is unique or not
     * @param string $str
     * @return boolean
     */
    public function is_uniquemail($email) {
        $user = $this->users_model->check_unique_email($email);
        if ($user) {
            $this->form_validation->set_message('is_uniquemail', 'Business alreay exist with this Email!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Display business admin's dashboard
     * @param type $business_id
     */
    public function dashboard($business_id = NULL) {

        if (is_numeric($business_id)) {
            $check_business = $this->businesses_model->get_business_by_id($business_id);
            if ($check_business) {
                $data['title'] = 'facetag | ' . $check_business['name'] . ' Dashboard';
                $data['business'] = $check_business;
                //-- Returns the number of free images purchased
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
                $this->template->load('default', 'admin/businesses/dashboard', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
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
     * Uploads business promo images
     * @param int $business_id
     */
    public function upload_promo_image($business_id) {
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

    /* reorder promo images list */

    public function change_promo_images_order() {
        $business_id = $this->input->post('business_id');
        $promo_images = $this->businesses_model->get_promo_images($business_id);
        $reorderlist = $this->input->post('reorderlist');
//        p($reorderlist);exit;
        for ($i = 0; $i < count($reorderlist); $i++) {
            $this->businesses_model->change_order(array("priority" => $i), $reorderlist[$i], $business_id);
        }
        $data['result'] = 'success';
        print json_encode($data, JSON_NUMERIC_CHECK);
        exit;
    }

    /**
     * Activate/Deactive ICP
     * @param int $icp_id ICP Id
     */
    public function block_icp($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $icp_data = $this->icps_model->get_result('i.id = ' . $this->db->escape($icp_id));
            if ($icp_data) {
                $icp_data = $icp_data[0];
                if ($icp_data['is_active'] == 0) {
                    $update_array = array(
                        'is_active' => 1
                    );
                    $this->session->set_flashdata('success', '"' . $icp_data['name'] . '" ICP has been activated successfully!');
                } else {
                    $update_array = array(
                        'is_active' => 0
                    );
                    $this->session->set_flashdata('success', '"' . $icp_data['name'] . '" ICP has been deactivated successfully!');
                }
                $this->icps_model->update_record('id = ' . $this->db->escape($icp_id), $update_array);
                redirect('admin/businesses/icps/' . $icp_data['business_id']);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Get promo feature images for datable of business view page
     * @param int $business_id
     */
    public function get_promo_images($business_id) {
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
            $promo_image = $this->businesses_model->get_promo_image_by_id($image_id);
            $update_array = array(
                'is_delete' => 1
            );
            $this->businesses_model->update_promo_image($image_id, $update_array);
            $this->session->set_flashdata('success', 'Promo Image deleted successfully!');
            redirect('admin/businesses/view/' . $promo_image['business_id']);
        } else {
            show_404();
        }
    }

    /**
     * Delete Physical Product Image
     * @param int $image_id 
     */
    public function delete_physical_product_image($image_id = NULL) {
        if (is_numeric($image_id)) {
            $physical_image = $this->icp_images_model->get_physical_product_image($image_id);
            unlink(ICP_PHYSICAL_PRODUCT_IMAGES . $physical_image['image']);
            $this->icp_images_model->delete_physical_product_image($image_id);
        }
    }

    /**
     * Add hotels
     */
    public function add_hotel() {
        $icp_id = $this->input->post('icp_id');
        $hotel_name = $this->input->post('hotel_name');
        $hotel_address = $this->input->post('hotel_address');
        $data = array(
            'icp_id' => $icp_id,
            'name' => $hotel_name,
            'address' => $hotel_address,
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s')
        );
        $hotel_id = $this->hotels_model->insert($data);
        $data['hotels'] = $this->hotels_model->get_hotels($icp_id);
    }

    /**
     * View matched user images
     * @param int $icp_id
     */
    public function matched_images($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $icp_data = $this->icps_model->get_result('i.id = ' . $this->db->escape($icp_id));
            if ($icp_data) {
                $data['icp_data'] = $icp_data[0];
                $data['title'] = 'facetag | Matched Images';
                $this->template->load('default', 'admin/businesses/matched_images', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
     * Get matched images of icp
     * @param int $icp_id - ICP ID
     */
    public function get_matched_images($icp_id = NULL) {
        $final = array();
        if ($icp_id != '') {
            $final['recordsTotal'] = $this->icp_imagetag_model->get_matchedimages($icp_id, 'count');
            $final['redraw'] = 1;
            $final['recordsFiltered'] = $final['recordsTotal'];
            $icps = $this->icp_imagetag_model->get_matchedimages($icp_id, 'result');
            $start = $this->input->get('start') + 1;

            foreach ($icps as $key => $val) {
                $icps[$key] = $val;
                $icps[$key]['sr_no'] = $start++;
                $icps[$key]['created'] = date('d,M Y', strtotime($val['created']));
            }
            $final['data'] = $icps;
        }
        echo json_encode($final);
    }

    /**
     * Check email address entered in email_id of business invite page to check is it unique or not
     * Called throught ajax
     */
    public function checkUniqueEmail() {
        $requested_email = $this->input->get('email');
        $user = $this->users_model->check_unique_email($requested_email);
        if ($user) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Check email address entered in contact email of business invite page to check is it unique or not
     * Called throught ajax
     */
    public function check_contact_email() {
        $requested_email = $this->input->get('contact_email');
        $user = $this->users_model->check_unique_email($requested_email);
        if ($user) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Get image to display in pop up
     */
    function get_image() {
        $image = $this->input->get('image');
        image_fix_orientation($image);
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
