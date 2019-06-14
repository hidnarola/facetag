<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('facerecognition');
        $this->load->library('push_notification');

        $this->load->model(array('icp_images_model', 'users_model', 'icp_imagetag_model', 'settings_model', 'icps_model'));
    }

    /**
     * Get all checked in users and match thier images with stored facerecogntion images
     * Send user push notification if match exist
     */
    public function match_image() {
        ini_set('max_execution_time', 0);
        $checkedin_users = $this->users_model->checked_in_users();
        $facerecog = $this->icp_images_model->count_facerecog_images();
        $new_arr = array();
        foreach ($checkedin_users as $key => $checkedin_user) {
            $icp_ids = array();
            if ($checkedin_user['icp_id'] != '') {
                $icp_ids = explode(",", $checkedin_user['icp_id']);
            } else if ($checkedin_user['business_icps'] != '') {
                $icp_ids = explode(",", $checkedin_user['business_icps']);
            }
            foreach ($icp_ids as $icp_id) {
                $checkedin_user['icp_id'] = $icp_id;
                $new_arr[] = $checkedin_user;
            }
        }

        $facerecog_images = array();
        foreach ($facerecog as $val) {
            $facerecog_images[$val['icp_id']] = $val['count'];
        }

        foreach ($new_arr as $user) {
            if ($user['user_image'] != '' && isset($facerecog_images[$user['icp_id']])) {
                $img_arr = array(
                    'photo' => base_url() . USER_IMAGE_SITE_PATH . $user['user_image'],
                    'threshold' => 0.72,
                    'mf_selector' => 'all',
                    'n' => $facerecog_images[$user['icp_id']],
                );
                $match_images = $this->facerecognition->identify('application/json', $img_arr, 'icp_' . $user['icp_id']);

                if (isset($match_images['results'])) {

                    $detected_images = $match_images['results'];
                    $key_arrays = array_keys($detected_images);

                    foreach ($key_arrays as $key_arr) {
                        $detected_imgs = $detected_images[$key_arr];
//                    $key = key($detected_images);
//                    $detected_images = $detected_images[$key];
                        foreach ($detected_imgs as $detected_image) {
                            $meta = $detected_image['face']['meta'];
                            $icp_image_id = explode('_', $meta);
                            $icp_image_id = $icp_image_id[2];

                            //--Check image is already tag or not
                            $tag_exist = $this->icp_imagetag_model->check_imagetag_exist($user['user_id'], $icp_image_id);

                            if ($tag_exist == 0) {
                                //-- if image is verified then store it into image_tag table and send push notification to user
                                $icp_image_tag = array(
                                    'icp_image_id' => $icp_image_id,
                                    'user_id' => $user['user_id'],
                                    'is_user_verified' => 0,
                                    'is_purchased' => 0,
                                    'created' => date('Y-m-d H:i:s'),
                                    'modified' => date('Y-m-d H:i:s'));
                                $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);


                                $url = $detected_image['face']['photo'];
                                $source_x = $detected_image['face']['x1'];
                                $x2 = $detected_image['face']['x2'];
                                $source_y = $detected_image['face']['y1'];
                                $y2 = $detected_image['face']['y2'];
                                $width = $x2 - $source_x;
                                $height = $y2 - $source_y;
                                crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);

                                $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                if ($user['device_type'] == 0) {
                                    $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                    $icp_img_data = $this->icp_images_model->get_result($where);
                                    $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                    $icp_data = $this->icps_model->get_result($where);

                                    $extension = explode('/', $icp_img_data[0]['image']);
                                    $image_name = explode('.', $extension[2]);
                                    $pushData = array(
                                        "notification_type" => "data",
                                        "body" => $messageText,
                                        "selfietagid" => $icp_image_tag_id,
                                        "businessid" => $icp_img_data[0]['business_id'],
                                        "businessname" => $icp_data[0]['businessname'],
                                        "businessaddress" => $icp_data[0]['businessaddress'],
                                        "icpid" => $user['icp_id'],
                                        "icpname" => $icp_data[0]['name'],
                                        "icpaddress" => $icp_data[0]['address'],
                                        "imgid" => $icp_image_id,
//                                        "image" => $icp_img_data[0]['image']
                                        "image" => $image_name[0] . $icp_image_tag_id . "." . $image_name[1]
                                    );

                                    $response = $this->push_notification->sendPushToAndroid(array($user['device_id']), $pushData, FALSE);
                                } else {

                                    $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                    $icp_img_data = $this->icp_images_model->get_result($where);
                                    $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                    $icp_data = $this->icps_model->get_result($where);

                                    $pushData = array(
                                        "selfietagid" => $icp_image_tag_id,
                                        "businessid" => $icp_img_data[0]['business_id'],
                                        "businessname" => $icp_data[0]['businessname'],
                                        "businessaddress" => $icp_data[0]['businessaddress'],
                                        "icpid" => $user['icp_id'],
                                        "icpname" => $icp_data[0]['name'],
                                        "icpaddress" => $icp_data[0]['address'],
                                        "imgid" => $icp_image_id,
                                        "image" => $icp_img_data[0]['image']
                                    );

                                    $response = $this->push_notification->sendPushiOS(array('deviceToken' => $user['device_id'], 'pushMessage' => $messageText), $pushData);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Purger images from facial recognition databse as per the settings set by admin
     */
    public function purge_images() {
        ini_set('max_execution_time', 0);
        //-- Get settings from database for purge records
        $where = 'settings_key="purge_facerecogdb_time_type" OR settings_key="purge_facerecogdb_time_value"';
        $settings = $this->settings_model->get_settings($where);

        $settings_arr = array();
        foreach ($settings as $key => $val) {
            $settings_arr[$val['settings_key']] = $val['settings_value'];
        }
        //-- If settings exist in datbase then get the icp images accroding to time settings
        if ($settings_arr) {
            if ($settings_arr['purge_facerecogdb_time_type'] == 'week') {
                $multiply = 7;
            } elseif ($settings_arr['purge_facerecogdb_time_type'] == 'month') {
                $multiply = 30;
            } elseif ($settings_arr['purge_facerecogdb_time_type'] == 'year') {
                $multiply = 365;
            }
            $time_val = $settings_arr['purge_facerecogdb_time_value'];
            $check_diff = $multiply * $time_val * 60 * 60 * 24;
            $icp_images = $this->icp_images_model->get_result('is_face_detected=1 AND is_deleted_from_face_recognition=0');
            $icp_image_tag = array();
            foreach ($icp_images as $icp_image) {
                $diff = strtotime(date('Y-m-d h:m:i')) - strtotime($icp_image['created']);
                if ($diff > $check_diff) {
                    $icp_image_tag[] = $icp_image;
                }
            }
            foreach ($icp_image_tag as $image) {
                $this->facerecognition->delete_face('meta', 'icp_img_' . $image['id']);
                $this->icp_images_model->update_record('id=' . $image['id'], array('is_deleted_from_face_recognition' => 1, 'modified' => date('Y-m-d h:i:s')));
            }
        }
    }

    /**
     * Function to automatically checked out user after 12 hours
     */
    public function checked_out_users() {
        ini_set('max_execution_time', 0);
        $checked_out_ids = $this->users_model->get_ids_for_checked_out();
        $ids = array();
        foreach ($checked_out_ids as $val) {
            $ids[] = $val['id'];
        }
        if ($ids) {
            $this->users_model->checked_out_users_by_id($ids);
        }
    }

    /**
     * Search against entire face recognition API database when user enrolls 
     */
    public function search_against_database() {
        ini_set('max_execution_time', 0);
        //-- Get all users who are enrolled and not searched for against entire database
        $users = $this->users_model->get_enrolled_users();
        //- Get all active icps from database
        $icps = $this->icps_model->get_all_active_icps();
        //-- Get image count
        $facerecog = $this->icp_images_model->count_facerecog_images();
        $facerecog_images = array();
        foreach ($facerecog as $val) {
            $facerecog_images[$val['icp_id']] = $val['count'];
        }

        foreach ($users as $user) {
            foreach ($icps as $icp) {
                if ($user['user_image'] != '' && isset($facerecog_images[$icp['id']])) {
                    $img_arr = array(
                        'photo' => base_url() . USER_IMAGE_SITE_PATH . $user['user_image'],
                        'threshold' => 0.72,
                        'mf_selector' => 'all',
                        'n' => $facerecog_images[$icp['id']],
                    );
                    $match_images = $this->facerecognition->identify('application/json', $img_arr, 'icp_' . $icp['id']);

                    if (isset($match_images['results'])) {

                        $detected_images = $match_images['results'];
                        $key_arrays = array_keys($detected_images);

                        foreach ($key_arrays as $key_arr) {
                            $detected_imgs = $detected_images[$key_arr];
                            foreach ($detected_imgs as $detected_image) {
                                $meta = $detected_image['face']['meta'];
                                $icp_image_id = explode('_', $meta);
                                $icp_image_id = $icp_image_id[2];

                                //--Check image is already tag or not
                                $tag_exist = $this->icp_imagetag_model->check_imagetag_exist($user['id'], $icp_image_id);

                                if ($tag_exist == 0) {
                                    //-- if image is verified then store it into image_tag table and send push notification to user
                                    $icp_image_tag = array(
                                        'icp_image_id' => $icp_image_id,
                                        'user_id' => $user['id'],
                                        'is_user_verified' => 0,
                                        'is_purchased' => 0,
                                        'created' => date('Y-m-d H:i:s'),
                                        'modified' => date('Y-m-d H:i:s'));
                                    $icp_image_tag_id = $this->icp_imagetag_model->insert($icp_image_tag);


                                    $url = $detected_image['face']['photo'];
                                    $source_x = $detected_image['face']['x1'];
                                    $x2 = $detected_image['face']['x2'];
                                    $source_y = $detected_image['face']['y1'];
                                    $y2 = $detected_image['face']['y2'];
                                    $width = $x2 - $source_x;
                                    $height = $y2 - $source_y;
                                    crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);

                                    $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                    if ($user['device_type'] == 0) {
                                        $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                        $icp_img_data = $this->icp_images_model->get_result($where);
                                        if (!empty($icp_img_data)) {
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);

                                            $extension = explode('/', $icp_img_data[0]['image']);
                                            $image_name = explode('.', $extension[2]);

                                            $pushData = array(
                                                "notification_type" => "data",
                                                "body" => $messageText,
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp['id'],
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
//                                            "image" => $icp_img_data[0]['image']
                                                "image" => $image_name[0] . $icp_image_tag_id . "." . $image_name[1]
                                            );

                                            $response = $this->push_notification->sendPushToAndroid(array($user['device_id']), $pushData, FALSE);
                                        }
                                    } else {

                                        $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                        $icp_img_data = $this->icp_images_model->get_result($where);
                                        if (!empty($icp_img_data)) {
                                            $where = 'i.id = ' . $this->db->escape($icp_img_data[0]['icp_id']);
                                            $icp_data = $this->icps_model->get_result($where);

                                            $pushData = array(
                                                "selfietagid" => $icp_image_tag_id,
                                                "businessid" => $icp_img_data[0]['business_id'],
                                                "businessname" => $icp_data[0]['businessname'],
                                                "businessaddress" => $icp_data[0]['businessaddress'],
                                                "icpid" => $icp['id'],
                                                "icpname" => $icp_data[0]['name'],
                                                "icpaddress" => $icp_data[0]['address'],
                                                "imgid" => $icp_image_id,
                                                "image" => $icp_img_data[0]['image']
                                            );

                                            $response = $this->push_notification->sendPushiOS(array('deviceToken' => $user['device_id'], 'pushMessage' => $messageText), $pushData);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //-- Change the status of searched_face_database to 1  
            $this->users_model->update_record(array('id' => $user['id']), array('searched_face_database' => 1));
        }
    }

    /**
     * Move images from automatic image folder to icp images folder and store that images to database and Facerecognition database
     * Send push notification if checked in user's image is detected
     * @author KU
     */
    public function automatic_upload() {
        $src = ICP_AUTO_UPLOAD_IMAGES;
        $dir = opendir($src);
        $business_dir = [];
        while (false !== ( $file = readdir($dir))) {
            if (($file != '.' ) && ($file != '..')) {
                $full = $src . $file;
                if (is_dir($full)) {
                    $business_dir[] = $full;
                    $this->check_icp_dir($full);
                }
            }
        }
        die;
    }

    /**
     * Traverse through ICP directory path from Business Directory
     * @param string $src - Business directory path
     */
    public function check_icp_dir($src) {
        $dir = opendir($src);
        $arr = explode('_', $src);
        $business_id = end($arr);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.' ) && ( $file != '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    $this->check_images($full, $business_id);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Loop through ICP images of ICP directory
     * @param string $src - Source directory 
     * @param int $business_id - Business Id of containing ICP directories
     */
    public function check_images($src, $business_id) {
        $images = [];
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.' ) && ($file != '..' )) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    //-- If icp directory contains other directory then unlink it
                    $this->deleteDir($full);
                } else {
                    $arr = explode('_', $src);
                    $icp_id = end($arr);

                    $auto_uploaded_images = $this->icp_images_model->get_auto_uploaded_images($icp_id);
                    $auto_uploaded_images_arr = array_column($auto_uploaded_images, 'image');

                    $ext = pathinfo($full, PATHINFO_EXTENSION);
                    $image_name = pathinfo($full, PATHINFO_FILENAME);
                    $image_name.='_auto.' . $ext;
                    $biz_dir = 'business_' . $business_id;
                    $icp_dir = 'icp_' . $icp_id;

                    //-- Check image already exist or not 
                    if (!in_array($biz_dir . '/' . $icp_dir . '/' . $image_name, $auto_uploaded_images_arr)) {
                        $ext_arr = ['png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG'];
                        if (!in_array($ext, $ext_arr)) {
                            unlink($full);
                        } else {
                            $images[] = ['img' => $file, 'icp_id' => $icp_id, 'business_id' => $business_id];

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

//                        $image_name = uniqid() . time() . '.' . $ext;
//                        copy($full, ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name);
                            rename($full, ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name);

                            $insert_data = array(
                                'icp_id' => $icp_id,
                                'image' => $biz_dir . '/' . $icp_dir . '/' . $image_name,
                                'upload_type' => 0,
                                'created' => date('Y-m-d H:i:s'),
                                'modified' => date('Y-m-d H:i:s'),
                                'image_capture_time' => date('Y-m-d H:i:s')
                            );

                            //-- Store image into thumb
                            $src1 = ICP_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                            $thumb_dest = ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                            thumbnail_image($src1, $thumb_dest);

                            //-- Convert image into blur image
                            blur_image(FCPATH . ICP_SMALL_IMAGES . $biz_dir . '/' . $icp_dir . '/' . $image_name, FCPATH . ICP_BLUR_IMAGES . $biz_dir . '/' . $icp_dir . '/');
                            $icp_image_id = $this->icp_images_model->insert($insert_data);


                            //-- Detects faces on uploaded ICP image and and store it in face recognition database
                            //-- Code start
                            $photo = base_url(ICP_IMAGES) . $biz_dir . '/' . $icp_dir . '/' . $image_name;
                            $icp_gallary_name = 'icp_' . $icp_id;

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

                                //-- Get all users
                                $users = $this->users_model->all_users();

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
                                    'threshold' => 0.72,
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
                                                crop_image($source_x, $source_y, $width, $height, $url, $icp_image_tag_id);


                                                $messageText = "Hello there, you have a new 'facetag' ...is this you?";
                                                if ($device_types[$user_id] == 0) {
                                                    $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                                    $icp_img_data = $this->icp_images_model->get_result($where);
                                                    if (!empty($icp_img_data)) {
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
                                                        if (!empty($device_tokens[$user_id])) {
                                                            $response = $this->push_notification->sendPushToAndroid(array($device_tokens[$user_id]), $pushData, FALSE);
                                                        }
                                                    }
                                                } else {
                                                    $url = '';
                                                    $where = 'im.id = ' . $this->db->escape($icp_image_id);
                                                    $icp_img_data = $this->icp_images_model->get_result($where);
                                                    if (!empty($icp_img_data)) {
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
                                                        if (!empty($device_tokens[$user_id])) {
                                                            $response = $this->push_notification->sendPushiOS(array('deviceToken' => $device_tokens[$user_id], 'pushMessage' => $messageText), $pushData);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //-- Code End
                        }
                    } else {
                        unlink($full);
                    }
                }
            }
        }
//        p($images);
        closedir($dir);
    }

    /**
     * Delete directory and files in it
     * @param string $dirPath
     */
    public function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
//            throw new InvalidArgumentException("$dirPath must be a directory");
            exit;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function test() {
        $this->db->insert('test', ['id' => 1]);
    }

    public function store_images_in_API() {
        $icps = $this->icps_model->get_icps();
        foreach ($icps as $icp) {
            //--Create icp gallery in face recognition database
            $gallary_name = 'icp_' . $icp['id'];
            $this->facerecognition->post_gallery($gallary_name);
        }
        //-- post user selfie gallery to FR
        $gallary_name = 'userselfies';
        $this->facerecognition->post_gallery($gallary_name);

        $users = $this->users_model->get_active_users();
        //-- Post userselfi into FR's userselfi gallery;
        foreach ($users as $user) {
            $photo = base_url() . USER_IMAGE_SITE_PATH . $user['user_image'];
            $gallery_name = "userselfies";
            $user_id = $user['id'];
            $data = array(
                'photo' => $photo,
                'meta' => 'user_' . $user_id,
                'galleries' => array($gallery_name)
            );
            $this->facerecognition->delete_face('meta', 'user_' . $user_id); //-- delete already added image
            $facerecog_data = $this->facerecognition->post_face('application/json', $data);
        }
    }
    
    public function test_upload_img() {
        error_reporting(E_ALL);
        $full = ICP_AUTO_UPLOAD_IMAGES."business_57/icp_169/dsc_0016.jpg";
        copy($full, ICP_IMAGES."business_1/icp_165/dsc_0016.jpg");
    }

}
