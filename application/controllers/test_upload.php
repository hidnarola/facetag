<?php

/**
 * Test Controller
 * @author Ankita
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function test_upload_file() {
        $file = "/uploads/new.txt";
        $remote_file = "C:\Users\pankita\Documents";
        echo $remote_file;exit;
        $ftp_server = '192.168.1.202';
        $ftp_user_name = 'hd';
        $ftp_user_pass = '9DrICc179Tc1apg';



// set up basic connection
        $conn_id = ftp_connect($ftp_server);

// login with username and password
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// upload a file
        if (ftp_put($conn_id, $file, $remote_file, FTP_ASCII)) {
            echo "successfully uploaded $file\n";
        } else {
            echo "There was a problem while uploading $file\n";
        }

// close the connection
        ftp_close($conn_id);
    }

}
