<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Facerecognition {

    var $ci;
    var $access_token;
    var $url;
    var $threshold_value;

    /**
     * Initialize parameters
     * Initialize access token and url
     */
    function __construct() {
        $this->ci = & get_instance();
        $this->access_token = FACE_RECOGNITION_TOKEN;
        //$this->url = 'https://api.findface.pro'; //-- Old version 
        $this->url = 'https://api.findface.pro/v0';
    }

    /**
     * Detect face in provided image
     * @param string $param_type : 'application/json' OR 'application/x-www-form-urlencoded'
     * @param string $photo : either URL or POST data
     */
    function detect($param_type = 'application/json', $photo) {

        $URL = $this->url . '/detect';
        $data = json_encode(array("photo" => $photo));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 300000); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $param_type, 'Authorization: Token ' . $this->access_token, 'Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Verify that two faces belong to the same person,or, alternatively, measures the similarity between the two faces.
     * @param string $param_type 'application/json' or 'application/x-www-form-urlencoded' 
     * @param array $data Array containing photo1,photo2,bbox1,bbox2,threshold,mf_selector(specifies behavior in a case of multiple faces on a photo)
     */
    function verify($param_type = 'application/json', $data) {

        $URL = $this->url . '/verify';
        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $param_type, 'Authorization: Token ' . $this->access_token, 'Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Search through the face database returns at most n faces (one by default)
     * @param string $param_type 'application/json' or 'application/x-www-form-urlencoded' 
     * @param array $data Array containing photo,[x1, y1, x2, y2 (optional)],threshold(optional),n(optional),mf_selector(optional)
     * @param string $gallery Name of gallery
     */
    function identify($param_type = 'application/json', $data, $gallery = '') {
        if ($gallery != '') {
            $URL = $this->url . '/faces/gallery/' . $gallery . '/identify/';
        } else {
            $URL = $this->url . '/identify/';
        }
        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $param_type, 'Authorization: Token ' . $this->access_token, 'Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Processes the uploaded image or provided URL, detects faces and adds the detected faces to the searchable database. 
     * If there are multiple faces on the photos, only the biggest face is added by default.
     * @param string $param_type
     * @param array $data - Array containing photo,meta[optional],(x1, y1, x2, y2 [optional]),mf_selector[optional]
     */
    function post_face($param_type = 'application/json', $data) {
        $URL = $this->url . '/face';
        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $param_type, 'Authorization: Token ' . $this->access_token, 'Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Returns the list of all faces stored in the database.
     */
    function faces($next = '') {
        if ($next != '')
            $URL = $this->url . '/faces' . $next;
        else
            $URL = $this->url . '/faces';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Returns detailed information about the face with id = FaceID or meta = Meta
     * @param string $method - Etiher id or meta
     * @param string $param - Face id or meta name
     */
    function get_face($method = 'id', $param) {
        $URL = $this->url . '/face/' . $method . '/' . urlencode($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));

        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Updates meta parameter of face
     * @param string $face_id - Face id
     * @param string $meta
     */
    function put_face($param_type, $face_id, $meta) {
        $URL = $this->url . '/face/id' . '/' . $face_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        $data = json_encode(array(
            'meta' => $meta
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $param_type, 'Authorization: Token ' . $this->access_token, 'Content-Length: ' . strlen($data)));
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Get all meta string with faces stored in facerecognition database
     * @param string $face_id - Face id
     * @param string $meta
     */
    public function get_meta() {
        $URL = $this->url . '/face/meta';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));

        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Deletes a face with the id = FaceId or all faces with the specified meta = Meta from the database.
     * @param string $method - Etiher id or meta
     * @param string $param - Face id or meta name
     */
    public function delete_face($method = 'id', $param) {
        $URL = $this->url . '/face/' . $method . '/' . urlencode($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token, 'Content-Length: 0'));
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Create gallery with specified name in face recognition database
     * @param string $name Name of the gallery to be created in face recognition database
     */
    public function post_gallery($name) {
        $URL = $this->url . '/galleries';
        $data = json_encode(array('name' => $name));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Token ' . $this->access_token, 'Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Get galleries stored in facerecogntion database
     */
    public function get_galleries() {
        $URL = $this->url . '/galleries';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));

        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Deletes the gallery and removes all the faces from it. .
     * @param string $name - Name of the gallery to be deleted
     */
    public function delete_galleries($name) {
        $URL = $this->url . '/galleries/' . $name;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token, 'Content-Length: 0'));
        $result = curl_exec($ch);
        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

}
