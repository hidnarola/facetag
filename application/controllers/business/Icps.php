<?php

/**
 * Icps Controller - Manage ICP of Business
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Icps extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('businesses_model');
        $this->load->model('users_model');
        $this->load->model('icps_model');
        $this->load->model('location_model');
        $this->load->model('icp_images_model');
        $this->load->model('icp_imagetag_model');
        $this->load->model('hotels_model');
        $this->load->library('facerecognition');
        $this->load->library('device_notification');
        $this->load->library('push_notification');
    }

    /**
     * Load view of businesses list
     * */
    public function index() {
        $data['title'] = 'facetag | ICPs';
        $data['heading'] = 'ICPs';
        $this->template->load('default', 'business/icps/list', $data);
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

            $flag = $flag1 = $flag2 = 0;

            //-- Upload icp logo
            $crop_img = $this->input->post('cropimg');
            if (!empty($crop_img)) {
//                    $file = ICP_LOGO . '/Logo-' . str_replace(' ', '', time()) . '.png';
//                    $icp_logo = 'Logo-' . str_replace(' ', '', time()) . '.png';
//                    $imgData = base64_decode(stripslashes(substr($crop_img, 22)));
//                    $fp = fopen($file, 'w');
//                    fwrite($fp, $imgData);
//                    fclose($fp);

                $data_img = $crop_img;

                list($type, $data_img) = explode(';', $data_img);
                list(, $data_img) = explode(',', $data_img);
                $data_img = base64_decode($data_img);

                file_put_contents(ICP_LOGO . '/Logo-' . str_replace(' ', '', time()) . '.png', $data_img);
            }

//            if ($_FILES['icp_logo']['name'] != '') {
//                $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
//                $exts = explode(".", $_FILES['icp_logo']['name']);
//                $name = $exts[0] . time() . "." . $exts[1];
//                $name = "Logo-" . date("mdYhHis") . "." . end($exts);
//
//                $config['upload_path'] = ICP_LOGO;
//                $config['allowed_types'] = implode("|", $img_array);
//                $config['max_size'] = '10240';
//                $config['file_name'] = $name;
//
//                $this->upload->initialize($config);
//
//                if (!$this->upload->do_upload('icp_logo')) {
//                    $flag = 1;
////                    $this->session->set_flashdata('error', $this->upload->display_errors());
//                    $data['icp_logo_validation'] = $this->upload->display_errors();
//                } else {
//                    if (is_numeric($icp_id) && $icp_logo != '') {
//                        unlink(ICP_LOGO . $icp_logo);
//                    }
//                    $file_info = $this->upload->data();
//                    $icp_logo = $file_info['file_name'];
//                    $size = getimagesize(ICP_LOGO . $icp_logo);
//                    if ($size[0] > 800 || $size[1] > 600) {
//                        resize_image(ICP_LOGO . $icp_logo, ICP_LOGO . $icp_logo, 800, 600);
//                    }
//                }
//            }
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
                $update_settings = array(
                    'preview_photo' => $icp_preview_image,
                    'addlogo_to_sharedimage' => $addlogo_to_sharedimage,
                    'is_low_image_free' => $is_low_image_free,
                    'is_high_image_free' => $is_high_image_free,
                    'lowfree_on_highpurchase' => $lowfree_on_highpurchase,
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
                        'is_active' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $icp_id = $this->icps_model->insert($insert_icp_data);

                    //--Create icp gallery in face recognition database
                    $gallary_name = 'icp_' . $icp_id;
                    $this->facerecognition->post_gallery($gallary_name);

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
                //-- Delete ICP gallery from facerecognition API database
                $this->facerecognition->delete_galleries('icp_' . $icp_id);
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
        }
        //-- Create icp directory inside business directory if not exist
        if (!file_exists(ICP_AUTO_UPLOAD_IMAGES . '/' . $biz_dir . '/' . $icp_dir)) {
            mkdir(ICP_AUTO_UPLOAD_IMAGES . $biz_dir . '/' . $icp_dir);
        }


        $file_namess = "business_autoscript";
        $doc = ".sh";
        $name_for_file = $file_namess . $doc;
        $handle = fopen("shellscripts/" . $name_for_file, "w");
        $spacee = "\n";
        $testText = "hello";

        fwrite($handle, "#!/bin/sh");
        fwrite($handle, $spacee);
        fwrite($handle, "HOST='123.201.110.194'");
        fwrite($handle, $spacee);
        fwrite($handle, "USER='hd'");
        fwrite($handle, $spacee);
        fwrite($handle, "PASSWD='9DrICc179Tc1apg'");
        fwrite($handle, $spacee);
        fwrite($handle, "FILE='" . $local_path . "'");
        fwrite($handle, $spacee);
        fwrite($handle, "REMOTEPATH='/facetag/uploads/automatic_upload/'" . $biz_dir . "/" . $icp_dir);
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

                                $photo_array = array(
                                    'photo' => $photo,
                                    'meta' => 'icp_img_' . $icp_image_id,
                                    'mf_selector' => 'all', //-- Detect all faces and post them into facerecognition IDS
                                    'galleries' => array($icp_gallary_name)
                                );

                                $facerecog_data = $this->facerecognition->post_face('application/json', $photo_array);

                                //-- check if face detected or not in uploaded icp image
                                if (isset($facerecog_data['code'])) {
                                    $update_data = array('is_face_detected' => 0);
                                } else if (isset($facerecog_data['results'])) {

                                    //-- if face detected 
                                    $result = $facerecog_data['results'];
                                    $face_recog_ids = array();
                                    foreach ($result as $val) {
                                        $face_recog_ids[] = $val['id'];
                                    }
                                    $update_data = array(
                                        'is_face_detected' => 1,
                                        'face_recognition_ids' => implode(',', $face_recog_ids));

                                    $this->icp_images_model->update_record('id=' . $icp_image_id, $update_data);

                                    $business_id = $icp_data[0]['business_id'];
                                    $icp_id = $icp_data[0]['id'];

                                    //-- Get checked in users who have checked in to particualr icps/business
//                                    $business_users = $this->users_model->get_checkedinusers_by_business($business_id);
//                                    $icp_users = $this->users_model->get_checkedinusers_by_icp($icp_id);
//                                    //-- merge both users
//                                    $users = array_merge($business_users, $icp_users);
                                    $users = $this->users_model->all_users();

                                    foreach ($users as $image) {

                                        $img_arr = array(
                                            'photo1' => base_url() . USER_IMAGE_SITE_PATH . $image['image'],
                                            'photo2' => $photo,
                                            'threshold' => 0.7,
                                            'mf_selector' => 'all'
                                        );

                                        //-- Verifies the uploaded data with user uploaded image
                                        $verification_data = $this->facerecognition->verify('application/json', $img_arr);

                                        if (isset($verification_data['verified'])) {
                                            if ($verification_data['verified'] == TRUE) {

                                                //-- if image is verified then store it into image_tag table and send push notification to user
                                                $icp_image_tag = array(
                                                    'icp_image_id' => $icp_image_id,
                                                    'user_id' => $image['user_id'],
                                                    'is_user_verified' => 0,
                                                    'is_purchased' => 0,
                                                    'created' => date('Y-m-d H:i:s'),
                                                    'modified' => date('Y-m-d H:i:s'));
                                                $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                                                $v_result = $verification_data['results'];
                                                foreach ($v_result as $ar) {
                                                    if ($ar['verified'] == TRUE) {
                                                        $url = ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                                                        $source_x = $ar['bbox2']['x1'];
                                                        $x2 = $ar['bbox2']['x2'];
                                                        $source_y = $ar['bbox2']['y1'];
                                                        $y2 = $ar['bbox2']['y2'];
                                                        $width = $x2 - $source_x;
                                                        $height = $y2 - $source_y;
                                                        crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);
                                                    }
                                                }


                                                $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                                if ($image['device_type'] == 0) {
//                                    $response = $this->device_notification->sendMessageToAndroidPhone(ANDROIDAPIKEY, array($image['device_id']), $messageText);
                                                    $extension = explode('.', $image_name);
                                                    $pushData = array(
                                                        "notification_type" => "data",
                                                        "body" => $messageText,
                                                        "selfietagid" => $icp_image_tag_id,
                                                        "businessid" => $business_id,
                                                        "businessname" => $icp_data[0]['businessname'],
                                                        "businessaddress" => $icp_data[0]['businessaddress'],
                                                        "icpid" => $icp_id,
                                                        "icpname" => $icp_data[0]['name'],
                                                        "icpaddress" => $icp_data[0]['address'],
                                                        "imgid" => $icp_image_id,
//                                                        "image" => $biz_dir . '/' . $icp_dir . '/' . $image_name
                                                        "image" => $extension[0] . $icp_image_tag_id . "." . $extension[1]
                                                    );
//                                    $pushData = array("notification_type" => "data", "body" => $messageText);
//                                    $response = $this->push_notification->sendPushToAndroid($image['device_id'], $pushData, TRUE);
                                                    $response = $this->push_notification->sendPushToAndroid(array($image['device_id']), $pushData, FALSE);
                                                } else {
                                                    $url = '';
//                                    $response = $this->device_notification->sendMessageToIPhones(array($image['device_id']), $messageText, $url);
                                                    $pushData = array(
                                                        "selfietagid" => $icp_image_tag_id,
                                                        "businessid" => $business_id,
                                                        "businessname" => $icp_data[0]['businessname'],
                                                        "businessaddress" => $icp_data[0]['businessaddress'],
                                                        "icpid" => $icp_id,
                                                        "icpname" => $icp_data[0]['name'],
                                                        "icpaddress" => $icp_data[0]['address'],
                                                        "imgid" => $icp_image_id,
                                                        "image" => $biz_dir . '/' . $icp_dir . '/' . $image_name
                                                    );

                                                    $response = $this->push_notification->sendPushiOS(array('deviceToken' => $image['device_id'], 'pushMessage' => $messageText), $pushData);
                                                }
                                            }
                                        }
                                    }
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
            $this->session->set_flashdata('error', 'Please try again!');
            echo json_encode($icp_image_id);
            exit;
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

                $photo_array = array(
                    'photo' => $photo,
                    'meta' => 'icp_img_' . $icp_image_id,
                    'mf_selector' => 'all', //-- Detect all faces and post them into facerecognition IDS
                    'galleries' => array($icp_gallary_name) //-- Store in  particular business gallery
                );

                $facerecog_data = $this->facerecognition->post_face('application/json', $photo_array);

                //-- check if face detected or not in uploaded icp image
                if (isset($facerecog_data['code'])) {
                    $update_data = array('is_face_detected' => 0);
                } else if (isset($facerecog_data['results'])) {

                    //-- if face detected 
                    $result = $facerecog_data['results'];
                    $face_recog_ids = array();
                    foreach ($result as $val) {
                        $face_recog_ids[] = $val['id'];
                    }
                    $update_data = array(
                        'is_face_detected' => 1,
                        'face_recognition_ids' => implode(',', $face_recog_ids));

                    $this->icp_images_model->update_record('id=' . $icp_image_id, $update_data);

                    $business_id = $icp_data[0]['business_id'];
                    $icp_id = $icp_data[0]['id'];

                    //-- Get checked in users who have checked in to particualr icps/business
//                    $business_users = $this->users_model->get_checkedinusers_by_business($business_id);
//                    $icp_users = $this->users_model->get_checkedinusers_by_icp($icp_id);
//                    //-- merge both users
//                    $users = array_merge($business_users, $icp_users);
                    $users = $this->users_model->all_users();
                    /*
                      $userids = array();
                      $device_tokens = array();
                      $device_types = array();
                      $total_users = $this->users_model->get_total_users();
                      //-- Make array of userids,device tokens and device types
                      foreach ($users as $val) {
                      $userids[] = $val['user_id'];
                      $device_tokens[$val['user_id']] = $val['device_id'];
                      $device_types[$val['user_id']] = $val['device_type'];
                      }
                      $img_arr = array(
                      'photo' => $photo,
                      'threshold' => 0.7,
                      'mf_selector' => 'all',
                      'n' => $total_users,
                      );
                      $match_images = $this->facerecognition->identify('application/json', $img_arr, 'userselfies');

                      if (isset($match_images['results'])) {
                      $detected_images = $match_images['results'];
                      $key_arrays = array_keys($detected_images);

                      foreach ($key_arrays as $key_arr) {

                      $detected_imgs = $detected_images[$key_arr];
                      foreach ($detected_imgs as $detected_image) {
                      $meta = $detected_image['face']['meta'];
                      $user_id = explode('_', $meta);
                      $user_id = $user_id[1];

                      if (in_array($user_id, $userids)) {
                      //-- if image is verified then store it into image_tag table and send push notification to user
                      $icp_image_tag = array(
                      'icp_image_id' => $icp_image_id,
                      'user_id' => $user_id,
                      'is_user_verified' => 0,
                      'is_purchased' => 0,
                      'created' => date('Y-m-d H:i:s'),
                      'modified' => date('Y-m-d H:i:s'));
                      $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                      $url = $photo;
                      $new_key = substr($key_arr, 1, -1);
                      $bounds = explode(",", $new_key);

                      $source_x = trim($bounds[0]);
                      $source_y = trim($bounds[1]);
                      $x2 = trim($bounds[2]);
                      $y2 = trim($bounds[3]);

                      $width = $x2 - $source_x;
                      $height = $y2 - $source_y;
                      crop_image($source_x, $source_y, $width, $height, $url,$icp_image_tag_id);


                      $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                      if ($device_types[$user_id] == 0) {
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
                      $response = $this->push_notification->sendPushToAndroid(array($device_tokens[$user_id]), $pushData, FALSE);
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
                      $response = $this->push_notification->sendPushiOS(array('deviceToken' => $device_tokens[$user_id], 'pushMessage' => $messageText), $pushData);
                      }
                      }
                      }
                      }
                      }
                     */

                    foreach ($users as $image) {

                        $img_arr = array(
                            'photo1' => base_url() . USER_IMAGE_SITE_PATH . $image['image'],
                            'photo2' => $photo,
                            'threshold' => 0.7,
                            'mf_selector' => 'all'
                        );

                        //-- Verifies the uploaded data with user uploaded image
                        $verification_data = $this->facerecognition->verify('application/json', $img_arr);

                        if (isset($verification_data['verified'])) {
                            if ($verification_data['verified'] == TRUE) {

                                //-- if image is verified then store it into image_tag table and send push notification to user
                                $icp_image_tag = array(
                                    'icp_image_id' => $icp_image_id,
                                    'user_id' => $image['user_id'],
                                    'is_user_verified' => 0,
                                    'is_purchased' => 0,
                                    'created' => date('Y-m-d H:i:s'),
                                    'modified' => date('Y-m-d H:i:s'));

                                $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);

                                $v_result = $verification_data['results'];
                                foreach ($v_result as $ar) {
                                    if ($ar['verified'] == TRUE) {
                                        $url = ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                                        $source_x = $ar['bbox2']['x1'];
                                        $x2 = $ar['bbox2']['x2'];
                                        $source_y = $ar['bbox2']['y1'];
                                        $y2 = $ar['bbox2']['y2'];
                                        $width = $x2 - $source_x;
                                        $height = $y2 - $source_y;
                                        crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);
                                    }
                                }

                                $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                if ($image['device_type'] == 0) {
                                    // $response = $this->device_notification->sendMessageToAndroidPhone(ANDROIDAPIKEY, array($image['device_id']), $messageText);
                                    $extension = explode('.', $image_name);
                                    $pushData = array(
                                        "notification_type" => "data",
                                        "body" => $messageText,
                                        "selfietagid" => $icp_image_tag_id,
                                        "businessid" => $business_id,
                                        "businessname" => $icp_data[0]['businessname'],
                                        "businessaddress" => $icp_data[0]['businessaddress'],
                                        "icpid" => $icp_id,
                                        "icpname" => $icp_data[0]['name'],
                                        "icpaddress" => $icp_data[0]['address'],
                                        "imgid" => $icp_image_id,
//                                        "image" => $biz_dir . '/' . $icp_dir . '/' . $image_name
                                        "image" => $extension[0] . $icp_image_tag_id . "." . $extension[1]
                                    );
                                    //                                    $pushData = array("notification_type" => "data", "body" => $messageText);
                                    //                                    $response = $this->push_notification->sendPushToAndroid($image['device_id'], $pushData, TRUE);
                                    $response = $this->push_notification->sendPushToAndroid(array($image['device_id']), $pushData, FALSE);
                                } else {
                                    $url = '';
                                    $pushData = array(
                                        "selfietagid" => $icp_image_tag_id,
                                        "businessid" => $business_id,
                                        "businessname" => $icp_data[0]['businessname'],
                                        "businessaddress" => $icp_data[0]['businessaddress'],
                                        "icpid" => $icp_id,
                                        "icpname" => $icp_data[0]['name'],
                                        "icpaddress" => $icp_data[0]['address'],
                                        "imgid" => $icp_image_id,
                                        "image" => $biz_dir . '/' . $icp_dir . '/' . $image_name
                                    );
                                    //                                    $response = $this->device_notification->sendMessageToIPhones(array($image['device_id']), $messageText, $url);
                                    $response = $this->push_notification->sendPushiOS(array('deviceToken' => $image['device_id'], 'pushMessage' => $messageText), $pushData);
                                }
                            }
                        }
                    }
                }
//                $this->session->set_flashdata('success', 'Images uploaded successfully!');
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
                //-- If face is detected in icp image then delete it from facerecognition database
                if ($icp_image_data[0]['is_face_detected'] == 1) {
                    $this->facerecognition->delete_face('meta', 'icp_img_' . $icp_image_id);
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
