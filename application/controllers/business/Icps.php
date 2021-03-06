<?php

/**
 * Icps Controller - Manage ICP of Business
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Icps extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model(['businesses_model', 'users_model', 'icps_model', 'location_model', 'icp_images_model',
            'social_media_model', 'icp_imagetag_model', 'hotels_model']);
        $this->load->library('push_notification');
        //-- New API integration
        $this->load->library('findface');
    }

    /**
     * Load view of businesses list
     * */
    public function index() {
        $data['title'] = 'facetag | ICPs';
        $data['heading'] = 'ICPs';
        $this->template->load('default', 'business/icps/list', $data);
    }

    /* @ANP : connect FB. */

    public function connect_fb($icp_id) {
        $this->session->set_userdata('assign_network_to_icp', ['icp_id' => $icp_id]);
        redirect('fb/connect');
    }

    /* @ANP : Disconnect FB. */

    public function disconnect_fb($icp_id) {

        if ($this->social_media_model->disconnect_from_fb($icp_id)) {
            $this->session->set_flashdata('success', 'Disconnect from facebook account successfully!');
        } else {
            $this->session->set_flashdata('error', 'Please try again!');
        }
        redirect('business/icps');
    }

    /**
     * Get ICPS of Businesses for data table
     * @param int $business_id - Business ID
     */
    public function get_icps() {
        $business_id = $this->session->userdata('facetag_admin')['business_id'];
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

    public function get_icp_images_to_post($icp_id = NULL) {

        $final = array();
        if ($icp_id != '') {
            $final['recordsTotal'] = $this->icp_images_model->get_icp_images_to_post($icp_id, 'count');
            $final['redraw'] = 1;
            $final['recordsFiltered'] = $final['recordsTotal'];
            $icp_images = $this->icp_images_model->get_icp_images_to_post($icp_id, 'result');

            $final['data'] = $icp_images;
        }
        echo json_encode($final);
    }

    /**
     * Add/Edit Business ICP
     * @param int $icp_id - ICP Id
     */
    public function edit($icp_id = NULL) {
        $business_id = $this->session->userdata('facetag_admin')['business_id'];

//        $this->form_validation->set_rules('name', 'ICP Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description of ICP', 'trim|max_length[160]');
        if (!$this->input->post('is_low_image_free')) {
//            $this->form_validation->set_rules('low_resolution_price', 'Low Resolution/Web Pic Price', 'trim|required|callback_decimal_numeric');
            $this->form_validation->set_rules('low_resolution_price', 'Low Resolution/Web Pic Price', 'trim|required');
        }
        if (!$this->input->post('is_high_image_free')) {
            $this->form_validation->set_rules('high_resolution_price', 'High Resolution/Printable Version Price', 'trim|required');
        }

        if ($this->input->post('offer_printed_souvenir')) {
            $this->form_validation->set_rules('printed_souvenir_price', 'Printed Souvenir Price', 'trim|required');
        }
//        $this->form_validation->set_rules('address', 'Address', 'trim|required');
//        $this->form_validation->set_rules('latitude', 'Latitiude', 'trim|required', array('required' => 'Latitude field is required. Please enter valid address!'));
//        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required', array('required' => 'Longitude field is required. Please enter valid address!'));

        $data['hotels'] = array();
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
//                $data['hotels'] = $this->hotels_model->get_hotels($icp_id);
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
//                    $this->session->set_flashdata('error', $this->upload->display_errors());
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
//                    $this->session->set_flashdata('error', $this->upload->display_errors());
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

                if (is_numeric($icp_id)) { //-- If ICP id is present then edit the ICP details
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

                    //--Create icp gallery in face recognition database
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
                $result['status'] = 'success';
                echo json_encode($result);
                exit;
            }
        }
        $this->template->load('default', 'business/icps/form', $data);
    }

    /**
     * Callback function to check price validation
     * @param string $str
     * @return boolean
     */
    public function decimal_numeric($str) {
        if (!is_numeric($str)) {
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
    public function delete($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $where = 'i.id = ' . $this->db->escape($icp_id);
            $icp_data = $this->icps_model->get_result($where);

            if ($icp_data) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->icps_model->update_record('id = ' . $this->db->escape($icp_id), $update_array);
                $this->session->set_flashdata('success', '"' . $icp_data[0]['name'] . '" ICP deleted successfully!');

                //-- Delete all dossiers
                $this->findface->delete_dossiers($icp_data[0]['dossierlist_id']);
                //-- Delete ICP gallery(dossier list/watch list) from findface database
                $this->findface->delete_dossierlist($icp_data[0]['dossierlist_id']);
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('business/icps/');
        } else {
            show_404();
        }
    }

    /* @anp: Generate shell script for automatic upload images. */

    public function generate_script() {
        $icp_id = $this->input->post('icp_id');
        $business_id = $this->session->userdata('facetag_admin')['business_id'];
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


//        $file_namess = "business_autoscript";
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
        fwrite($handle, "mput *.*");
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
//            $this->session->set_flashdata('success', 'Please run file to upload images');
//            redirect('business/icps');
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
                $this->template->load('default', 'admin/businesses/view', $data);
            } else {
                show_404();
            }
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
                $this->template->load('default', 'business/icps/images', $data);
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
            $icp_images = $this->icp_images_model->get_icp_images($icp_id, 'result');

            $start = $this->input->get('start') + 1;

            foreach ($icp_images as $key => $val) {
                $icp_images[$key] = $val;
                $icp_images[$key]['sr_no'] = $start++;
                $icp_images[$key]['filesize'] = formatSizeUnits(filesize(ICP_IMAGES . $val['image']));
                $icp_images[$key]['fileformat'] = substr(strtolower(strrchr(ICP_IMAGES . $val['image'], '.')), 1);
                $icp_images[$key]['created'] = date('d,M Y', strtotime($val['created']));
            }
            $final['data'] = $icp_images;
        }
        echo json_encode($final);
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
                $this->template->load('default', 'business/icps/add_icp_image', $data);
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
            $image_name = upload_image('files', ICP_IMAGES . $biz_dir . '/' . $icp_dir);
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
                $src = FCPATH . ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                $thumb_dest = FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                thumbnail_image($src, $thumb_dest);

                //-- Convert image into blur image
                blur_image(FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name, FCPATH . ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir . '/');
                $icp_image_id = $this->icp_images_model->insert($insert_data);

                //-- Gallery name for business and icp 
                $gallary_name = 'business_' . $icp_data[0]['business_id'];
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

                            $dossierlist_id = $icp_data[0]['dossierlist_id'];
                            $business_id = $icp_data[0]['business_id'];
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
                                $gallary_name = 'business_' . $icp_data[0]['business_id'];
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
            if (!file_exists(ICP_CROPPED_IMAGES . $biz_dir)) {
                mkdir(ICP_CROPPED_IMAGES . $biz_dir);
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

                $this->session->set_flashdata('success', 'Images uploaded successfully!');
                redirect('business/icps/icp_images/' . $icp_id);
            }
        } else {
            show_404();
        }
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
            redirect('business/icps/icp_images/' . $icp_image_data[0]['icp_id']);
        } else {
            show_404();
        }
    }

    /**
     * Activate/Deactive ICP
     * @param int $icp_id ICP Id
     */
    public function block($icp_id = NULL) {
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
                redirect('business/icps');
            } else {
                show_404();
            }
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
     * View matched user images
     * @param int $icp_id
     */
    public function matched_images($icp_id = NULL) {
        if (is_numeric($icp_id)) {
            $icp_data = $this->icps_model->get_result('i.id = ' . $this->db->escape($icp_id));
            if ($icp_data) {
                $data['icp_data'] = $icp_data[0];
                $data['title'] = 'facetag | Matched Images';
                $this->template->load('default', 'business/icps/matched_images', $data);
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
     * Get image to display in pop up
     */
    function get_image() {
        $image = $this->input->get('image');
        image_fix_orientation($image);
    }

}
