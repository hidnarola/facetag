<?php

/**
 * Settings Controller - Settings of Super Admin
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('settings_model');
    }

    /**
     * Load view of businesses list
     * */
    public function index() {
        $this->form_validation->set_error_delimiters('<span class="error-custom">', '</span>');

        $this->form_validation->set_rules('purge_facerecogdb_time_type', 'Time Interval to Purge Facial Recognition Database', 'trim|required', array('required' => 'Please select time interval to purge facial recognition database!'));
        $this->form_validation->set_rules('purge_facerecogdb_time_value', 'Time Period', 'trim|required|numeric');
        $this->form_validation->set_rules('creditcard_debitcard_processing_fees', 'Time Period', 'trim|required|numeric');
        $this->form_validation->set_rules('international_card_processing_fess', 'Time Period', 'trim|required|numeric');
        $this->form_validation->set_rules('transaction_fees', 'Time Period', 'trim|required|numeric');

        $where = 'settings_key="purge_facerecogdb_time_type" OR settings_key="purge_facerecogdb_time_value" OR settings_key="creditcard_debitcard_processing_fees" OR settings_key="international_card_processing_fees" OR settings_key="transaction_fees"';
        $settings = $this->settings_model->get_settings($where);

        $settings_arr = array();
        foreach ($settings as $key => $val) {
            $settings_arr[$val['settings_key']] = $val['settings_value'];
        }
        $data['settings'] = $settings_arr;

        if ($this->form_validation->run() == TRUE) {

            //-- Settings data to be inserted or updated
            $settings_data = array(
                array(
                    'settings_key' => 'purge_facerecogdb_time_type',
                    'settings_value' => $this->input->post('purge_facerecogdb_time_type'),
                    'modified' => date('Y-m-d H:i:s')
                ),
                array(
                    'settings_key' => 'purge_facerecogdb_time_value',
                    'settings_value' => $this->input->post('purge_facerecogdb_time_value'),
                    'modified' => date('Y-m-d H:i:s')
                ),
                 array(
                    'settings_key' => 'creditcard_debitcard_processing_fees',
                    'settings_value' => $this->input->post('creditcard_debitcard_processing_fees'),
                    'modified' => date('Y-m-d H:i:s')
                ),
                 array(
                    'settings_key' => 'transaction_fees',
                    'settings_value' => $this->input->post('transaction_fees'),
                    'modified' => date('Y-m-d H:i:s')
                ),
                array(
                    'settings_key' => 'international_card_processing_fees',
                    'settings_value' => $this->input->post('international_card_processing_fees'),
                    'modified' => date('Y-m-d H:i:s')
                )
            );

            //-- If settings already exist then update else insert
            if ($settings) {
                $this->settings_model->update_multiple($settings_data, 'settings_key');
            } else {
                $this->settings_model->insert_multiple($settings_data);
            }
            $this->session->set_flashdata('success', 'Your settings have been saved successfully!');
            redirect('admin/settings');
        }
        $data['title'] = 'facetag | Settings';
        $this->template->load('default', 'admin/settings/index', $data);
    }

}
