<?php

/**
 * Hotels Controller - Manage ICPs local hotels
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Hotels extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('hotels_model');
        $this->load->model('businesses_model');
        $this->load->model('icps_model');
    }

    /**
     * Load view of hotels list
     * */
    public function index($business_id = NULL) {
        $where = 'b.id = ' . $this->db->escape($business_id);
        $business_data = $this->businesses_model->get_result($where);
        if ($business_data) {
            $data['business_data'] = $business_data[0];
            $data['title'] = 'facetag | Business Hotels';
            $this->template->load('default', 'admin/businesses/hotels', $data);
        } else {
            show_404();
        }
    }

    /**
     * Get businesses for data table
     * */
    public function get_hotels($business_id = NULL) {
        $final['recordsTotal'] = $this->hotels_model->get_hotels($business_id, NULL, 'count');
        $final['redraw'] = 1;
        $final['recordsFiltered'] = $final['recordsTotal'];
        $hotels = $this->hotels_model->get_hotels($business_id, NULL, 'result');
        $start = $this->input->get('start') + 1;

        foreach ($hotels as $key => $val) {
            $hotels[$key] = $val;
            $hotels[$key]['sr_no'] = $start++;
            $hotels[$key]['name'] = character_limiter(strip_tags($val['name']), 30);
            $hotels[$key]['address'] = character_limiter(strip_tags($val['address']), 100);
            $hotels[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $hotels;
        echo json_encode($final);
    }

    /**
     * Add/Edit Business Details
     */
    public function edit() {
        $business_id = $this->uri->segment(4);
        $hotel_id = $this->uri->segment(5);

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');

        $where = 'b.id = ' . $this->db->escape($business_id);
        $business_data = $this->businesses_model->get_result($where);
        $data['business_data'] = $business_data[0];

        if ($business_data) {
            if (is_numeric($hotel_id)) {
                $hotel_data = $this->hotels_model->get_hotel($hotel_id);
                if ($hotel_data) {
                    $data['hotel_data'] = $hotel_data;
                    $data['title'] = 'facetag | Edit Hotel';
                    $data['heading'] = 'Edit Hotel';
                } else {
                    show_404();
                }
            } else {
                $data['heading'] = 'Add Hotel for \'' . $business_data[0]['name'];
                $data['title'] = 'facetag | Add Hotel';
            }

            if ($this->form_validation->run() == FALSE) {
//            $this->form_validation->set_error_delimiters('<label class="validation-error-label">', '</label>');
            } else {
                //-- Upload hotel image
                if ($_FILES['preview_photo']['name'] != '') {
                    $img_array = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
                    $exts = explode(".", $_FILES['preview_photo']['name']);
                    $name = $exts[0] . time() . "." . $exts[1];
                    $name = "hotel-" . date("mdYhHis") . "." . end($exts);

                    $config['upload_path'] = HOTEL_IMAGES;
                    $config['allowed_types'] = implode("|", $img_array);
                    $config['max_size'] = '10240';
                    $config['file_name'] = $name;

                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('preview_photo')) {
                        $flag1 = 1;
//                    $this->session->set_flashdata('error', $this->upload->display_errors());
                        $data['preview_photo_validation'] = $this->upload->display_errors();
                    } else {
                        $file_info = $this->upload->data();
                        $hotel_image = $file_info['file_name'];
                    }
                }
                if (is_numeric($hotel_id)) { //-- If hotel id is present then edit hotel details
                    $update_array = array(
                        'name' => $this->input->post('name'),
                        'address' => $this->input->post('address'),
                        'hotel_pic' => $hotel_image,
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->hotels_model->update_record('id=' . $hotel_id, $update_array);
                    $this->session->set_flashdata('success', '"' . trim($this->input->post('name')) . '" Hotel updated successfully!');
                } else { //-- If hotel id is not present then add new hotel details
                    $insert_array = array(
                        'busienss_id' => $business_id,
                        'name' => $this->input->post('name'),
                        'address' => $this->input->post('address'),
                        'hotel_pic' => $hotel_image,
                        'modified' => date('Y-m-d H:i:s'),
                    );

                    $hotel_id = $this->hotels_model->insert($insert_array);
                    $this->session->set_flashdata('success', '"' . trim($this->input->post('name')) . '" Hotel added successfully!');
                }
                redirect('admin/hotels/index/' . $business_id);
            }

            $this->template->load('default', 'admin/businesses/hotel_form', $data);
        } else {
            show_404();
        }
    }

    /**
     * Delete Hotel 
     * @param int $hotel_id 
     */
    public function delete($hotel_id) {
        $hotel_data = $this->hotels_model->get_hotel($hotel_id);

        if ($hotel_data) {
            $this->hotels_model->delete($hotel_id);
            $this->session->set_flashdata('success', '"' . $hotel_data['name'] . '" deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid request. Please try again!');
        }
        redirect('admin/hotels/index/' . $hotel_data['business_id']);
    }

}
