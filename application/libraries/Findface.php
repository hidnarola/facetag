<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * New Find Face API 
 * @author KU
 */
class Findface {

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
        $this->access_token = "4582f5bb9047164799aa283de40a0365a591aa67f865bb4459198bf838eb065d";
        $this->url = '52.236.81.69/';
        $this->threshold_value = 0.7;
    }

    /**
     * Detect face API
     * @param string $photo
     * @return array
     */
    function detect($photo) {
        $URL = $this->url . 'detect';
        $fields = [];

        $filenames = array($photo);

        $files = array();
        foreach ($filenames as $f) {
            $files['photo'] = file_get_contents($f);
        }

        // curl
        $curl = curl_init();

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = $this->build_data_files($boundary, $fields, $files);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token " . $this->access_token,
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)
            ),
        ));

        $result = curl_exec($curl);
        if ($result === false) {
            $response = array('curl_error' => curl_error($curl));
        } else {
            $response = json_decode($result, 1);
        }
        curl_close($curl);

        return $response;
    }

    /**
     * Verify that two faces belong to the same person,or, alternatively, measures the similarity between the two faces.
     * @param string $face1
     * @param string $face2
     * @return array
     */
    function verify($face1, $face2) {

        $URL = $this->url . 'verify?face1=' . $face1 . '&face2=' . $face2;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));
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
     * @param string $detection_id
     * @param int $dossierlist_id
     * @param int $limit
     * @param string $type (detection/dossierface)
     * @return array
     */
    function identify($detection_id, $dossierlist_id, $limit = null, $type = 'detection') {

        $URL = $this->url . "dossiers/?dossier_lists=$dossierlist_id&threshold=" . $this->threshold_value;

        if ($type == 'dossierface') {
            $URL .= "&looks_like=dossierface:$detection_id";
        } else {
            $URL .= "&looks_like=detection:$detection_id";
        }

        if (!is_null($limit) && $limit > 0) {
            $URL .= "&limit=" . $limit;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));
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
     * Created dossier list
     * @param string $list_name
     */
    function adddossier_list($list_name) {
        $URL = $this->url . 'dossier-lists/';
        $data = json_encode(array('active' => 'true', 'name' => $list_name));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Token ' . $this->access_token));
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
     * Create dossier
     * @param int $dossierlist_id
     * @param string $dossier_name
     */
    function adddossier($dossierlist_id, $dossier_name) {
        $URL = $this->url . 'dossiers/';
        $data = json_encode(array('active' => 'true', 'name' => $dossier_name, 'dossier_lists' => [$dossierlist_id]));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Token ' . $this->access_token));
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
     * Add face into dossier
     * @param int $dossier_id
     * @param string $detection_id
     * @param string $image
     */
    function adddossierface($dossier_id, $detection_id, $image) {
        $fields = ['dossier' => $dossier_id, 'create_from' => 'detection:' . $detection_id];

        $filenames = array($image);

        $files = array();
        foreach ($filenames as $f) {
            $files['source_photo'] = file_get_contents($f);
        }

        // URL to upload to
        $url = $this->url . "dossier-faces/";

        // curl
        $curl = curl_init();

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = $this->build_data_files($boundary, $fields, $files);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token  " . $this->access_token,
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)
            ),
        ));

        $result = curl_exec($curl);
        if ($result === false) {
            $response = array('curl_error' => curl_error($curl));
        } else {
            $response = json_decode($result, 1);
        }
        return $response;
    }

    /**
     * Get dossier face details by dossier face id
     * @param int $dossierface_id
     * @return array
     */
    function getdossierface($dossierface_id) {
        $URL = $this->url . "dossier-faces/$dossierface_id/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token ' . $this->access_token));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false) {
            $response = array('curl_error' => curl_error($ch));
        } else {
            $response = json_decode($result, 1);
        }

        return $response;
    }

    /**
     * Delete dossier
     * @param int $dossier_id
     * @return array
     */
    public function delete_dossier($dossier_id) {
        $URL = $this->url . 'dossiers/' . $dossier_id . '/';
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
     * Delete all dossiers from dossier list
     * @param int $dossierlist_id
     * @return array
     */
    public function delete_dossiers($dossierlist_id) {
        $URL = $this->url . 'dossier-lists/' . $dossierlist_id . '/purge/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
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
     * Delete dossier list
     * @param int $dossierlist_id
     * @return array
     */
    public function delete_dossierlist($dossierlist_id) {
        $URL = $this->url . "dossier-lists/$dossierlist_id/";
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
     * Build multi type form data params for curl
     * @param string $boundary
     * @param array $fields
     * @param array $files
     * @return string
     */
    function build_data_files($boundary, $fields, $files) {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                    . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol
                    . $content . $eol;
        }

        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                    . 'Content-Disposition: form-data; name="' . $name . '"; filename="58ac19df989a31487673823.jpeg"' . $eol
                    . 'Content-Transfer-Encoding: binary' . $eol
            ;

            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--" . $eol;
        return $data;
    }

}
