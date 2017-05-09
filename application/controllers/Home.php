<?php

/**
 * Home Controller - Landing Page of Facetag
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display Landing page of facetag
     */
    public function index() {
        $data['title'] = 'Facetag Retailer';
        $this->template->load('frontend', 'home', $data);
    }

    /**
     * Test function to test email functionality
     */
    public function test() {
        $configs = mail_config();
        $this->load->library('email', $configs);
        $this->email->initialize($configs);
        $this->email->from(EMAIL_FROM, EMAIL_FROM_NAME);
        $this->email->to('ku@narola.email');
        $msg = 'test email';
        $this->email->subject('Invitation - facetag');
        $this->email->message($msg);
        if ($this->email->send()) {
            echo 'sent';
        } else {
            p($this->email->print_debugger());
        }
    }

}
