<?php

require APPPATH . 'vendor/Facebook/autoload.php';

class Fb extends CI_Controller {

//    private $fb_secret = [
//        'app_id' => '1804396313107006',
//        'app_secret' => '0fba39cb967816eac1b694fc4bb0d2ef',
//        'default_graph_version' => 'v2.7',
//    ];
//    private $fb_secret = [
//        'app_id' => '683046148540242',
//        'app_secret' => '09c69a0bbadb7c74cf3705ccdc1fa309',
//        'default_graph_version' => 'v2.8',
//    ];
    // Client app facebook api credentials

    private $fb_secret = [
        'app_id' => '135496957080945',
        'app_secret' => '49e301b41ee38cb9521def6364c251a4',
        'default_graph_version' => 'v2.10',
    ];
    private $callback_url = '';
    private $facebook = NULL;

    public function __construct() {

        parent::__construct();

//        if (!$this->is_loggedin)
//            redirect();

        $this->load->model('social_media_model');
        $this->load->model('icp_images_model');
        $this->callback_url = base_url('facebook/callback');
        $this->facebook = new Facebook\Facebook($this->fb_secret);
    }

    public function connect() {
        $temptoken = $this->session->userdata('fb_login_token');
        $helper = $this->facebook->getRedirectLoginHelper();
        if (!empty($temptoken['accesstoken'])) {
            $url = 'https://www.facebook.com/logout.php?next=' . base_url() . 'facebook/connect&access_token=' . $temptoken['accesstoken'];
            $this->session->unset_userdata('fb_login_token');
            redirect($url);
        }
        $scope = [
            'email',
            'public_profile',
            'publish_actions',
        ];

        $url = $helper->getLoginUrl($this->callback_url, $scope);
        redirect($url);
    }

    public function callback() {

        $helper = $this->facebook->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        $oAuth2Client = $this->facebook->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        $tokenMetadata->validateAppId($this->fb_secret['app_id']);

        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {

            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken, $this->fb_secret);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        $token = $accessToken->getValue();
        $expiry_time = $accessToken->getExpiresAt();

        if (!empty($expiry_time)) {
            $expiry_time = $expiry_time->format('Y-m-d H:i:00');
        }

        try {
            $response = $this->facebook->get('/me?fields=id,name,email,first_name,last_name,birthday,education,gender,location,picture.type(large)', $token);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();

        $data = [
            'account_name' => $user->getFirstName() . ' ' . $user->getLastName(),
            'image_url' => $user->getPicture()->getUrl(),
            'social_id' => $user->getId(),
//            'type' => 1,
            'access_token' => $token,
            'access_token_timeout' => "'" . $expiry_time . "'",
        ];
        $this->session->set_userdata('fb_login_token', ['accesstoken' => $token]);
        $this->social_media_model->manage_network($data, false);
    }

    public function get_pages($encoded_network_id = '') {

        $network_id = decode($encoded_network_id);

        $network = $this->query->select('connect_network', NULL, ['where' => ['id' => $network_id]], 1);

        if (empty($network))
            show_404();

        $response = $this->facebook->get('/' . $network['social_id'] . '/accounts', $network['access_token']);
        $response = $response->getBody();
        $response = json_decode($response, true);
        $this->data['network'] = $network;
        $this->data['pages'] = $response['data'];

        $this->load->view('front_end/settings/ajax_load_facebook_page', $this->data);
    }

    public function post_images() {
        // Since all the requests will be sent on behalf of the same user,
// we'll set the default fallback access token here.
        //Create an album
//        $album_details = array(
//            'message' => 'Album desc',
//            'name' => 'Album name'
//        );
//        $create_album = $this->facebook->request('/me/albums', 'post', $album_details);
//        print_r($create_album);
//        exit;
        $icpid = $this->input->post('connect_icp_id');
        $accesstoken = $this->icp_images_model->get_icp_access_token($icpid);
        $this->facebook->setDefaultAccessToken($accesstoken['access_token']);
        $path = base_url() . 'uploads/icp_images/';
        $icpid = $this->input->post('connect_icp_id');
        $selected_images = $this->input->post('shareimages');
        $share_images = $this->icp_images_model->get_icp_selected_images($selected_images);
        $batch = array();
        foreach ($share_images as $k => $v) {
            $batch[$k][$v['id']] = $this->facebook->request('POST', '/me/photos', array('source' => $this->facebook->fileToUpload($path . $v['image'])));
        }

        try {
            $responses = $this->facebook->sendBatchRequest($batch);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        foreach ($responses as $key => $response) {
            if ($response->isError()) {
                $e = $response->getThrownException();
                echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
                echo '<p>Graph Said: ' . "\n\n";
                var_dump($e->getResponse());
            } else {
                echo "<p>(" . $key . ") HTTP status code: " . $response->getHttpStatusCode() . "<br />\n";
                echo "Response: " . $response->getBody() . "</p>\n\n";
                echo "<hr />\n\n";
            }
        }
        $this->session->set_flashdata('success', 'images uploaded');
        redirect('business/icps');
    }

    public function get_groups($encoded_network_id = '') {

        $network_id = decode($encoded_network_id);

        $network = $this->query->select('connect_network', NULL, ['where' => ['id' => $network_id]], 1);

        if (empty($network))
            show_404();

        $response = $this->facebook->get('/' . $network['social_id'] . '/groups', $network['access_token']);
        $response = $response->getBody();
        $response = json_decode($response, true);

        $this->data['network'] = $network;
        $this->data['groups'] = $response['data'];

        $this->load->view('front_end/settings/ajax_load_facebook_group', $this->data);
    }

    public function change_group() {

        $this->output->set_content_type('json');

        $network_id = decode($this->input->post('network_id'));
        $id = $this->input->post('id');
        $name = $this->input->post('name');

        $this->db->where('id', $network_id);

        $this->db->set('board_or_group', $name);
        $this->db->set('board_or_group_id', $id);

        $this->db->set('page_name', '');
        $this->db->set('page_id', '');
        $this->db->set('page_token', '');

        $this->db->update('connect_network');

        echo json_encode(['status' => 1, 'name' => $name, 'network_id' => $network_id]);
    }

    public function change_page() {
        $this->output->set_content_type('json');

        $network_id = decode($this->input->post('network_id'));

        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $token = $this->input->post('token');

        $this->db->where('id', $network_id);

        $this->db->set('page_name', $name);
        $this->db->set('page_id', $id);
        $this->db->set('page_token', $token);

        $this->db->set('board_or_group', '');
        $this->db->set('board_or_group_id', '');

        $this->db->update('connect_network');

        echo json_encode(['status' => 1, 'name' => $name, 'page_id' => $id, 'page_token' => $token, 'network_id' => $network_id]);
    }

    /**
     * Disconnect facebook group or page
     * @author KU
     */
    public function disconnect_fb() {
        $connect_newtowrd_id = $this->input->post('network_id');
        $type = $this->input->post('type');
        $network_details = $this->get_connet_network($connect_newtowrd_id);
        if ($network_details) {
            if ($type == 1) {
                $update_arr = ['page_name' => NULL, 'page_id' => NULL, 'page_token' => NULL];
            } else {
                $update_arr = ['board_or_group' => '', 'board_or_group_id' => ''];
            }
            $this->db->where(['id' => $connect_newtowrd_id]);
            $this->db->update('connect_network', $update_arr);
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Return connected social network by its id
     * @param int $id
     * @author KU
     */
    private function get_connet_network($id) {
        $this->db->where(['id' => $id, 'is_deleted' => 0]);
        $query = $this->db->get('connect_network');
        return $query->row_array();
    }

}
