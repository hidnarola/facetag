<?php

class Social_media_model extends CI_Model {

    private $type = [
        1 => 'facebook',
        2 => 'twitter',
        3 => 'pinterest',
        4 => 'linkedin',
        5 => 'instagram',
        6 => 'google',
    ];

    public function is_network_exist($social_id, $icp_id) {
        $this->db->select('*');
        $this->db->from('connect_network');
        $this->db->where('social_id', $social_id);
        $this->db->where('is_deleted', '0');

        /* Added by KU */
        if ($icp_id)
            $this->db->where('icp_id', $icp_id);

        $this->db->limit(1);
        $result = $this->db->get()->row_array();
        if (empty($result))
            return false;
        return $result;
    }

    public function save_network($value, $escape) {

        if (!$escape) {
            $escaped_value = $this->db->escape($value);
            if (isset($value['token_timeout']))
                $escaped_value['token_timeout'] = $value['token_timeout'];
            $value = $escaped_value;
        }

        $this->db->insert('connect_network', $value, $escape);
        return $this->db->insert_id();
    }

    public function update_network($data, $escape) {

        $user_id = NULL;
        $social_id = NULL;

        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
            $this->db->where('user_id', $data['user_id']);
            unset($data['user_id']);
        }

        if (isset($data['social_id'])) {
            $social_id = $data['social_id'];
            $this->db->where('social_id', $data['social_id']);
            unset($data['user_id']);

            if (!$escape) {
                foreach ($data as $k => $v) {
                    if ($k == 'access_token_timeout')
                        $this->db->set($k, $v, false);
                    else
                        $this->db->set($k, $v);
                }
            }else {
                $this->db->set($data);
            }

            $this->db->update('connect_network');

            if (!empty($user_id) && !empty($social_id)) {
                $this->db->select('*');
                $this->db->from('connect_network');
                $this->db->where('user_id', $user_id);
                $this->db->where('social_id', $social_id);
                $result = $this->db->get()->row_array();

                if (!empty($result['id']))
                    return $result['id'];
            }
        }

        return false;
    }

    /* Save or update connect_network data */

    public function manage_network($data, $escape = true) {

        $this->db->trans_begin();

        $icp_network = $this->session->userdata('assign_network_to_icp');
        $this->session->unset_userdata('assign_network_to_icp');
        if (!empty($icp_network['icp_id']))
            $data['icp_id'] = $icp_network['icp_id'];

        $network = $this->is_network_exist($data['social_id'], $data['icp_id']);

        # If network already exist update it.
        if (!empty($network)) {

            $this->update_network($data, $escape);

            $this->session->set_flashdata('success', 'Your existing facebook account detail has been updated.');

            if (!empty($icp_network) && !empty($icp_network['icp_id']))
                redirect('business/icps');
        } else {

            $this->save_network($data, $escape);

            $this->session->set_flashdata('success', 'Your account has been successfully connected.');

            $redirect_to_connect = false;

            if (!empty($icp_network) && !empty($icp_network['icp_id'])) {
                $this->session->set_flashdata('success', 'The Facebook account has successfully been connected to this project.');
                $redirect_to_connect = true;
            }

            $redirect = 'business/icps';

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Unable to add connection. Please try later.');
            }

            redirect($redirect);
        }
    }
    
    /* disconnect from fb */

    public function disconnect_from_fb($icp_id) {
        $this->db->where('icp_id', $icp_id);
        $this->db->delete('connect_network');
        return TRUE;
    }

    public function curl($request = []) {

        $responce = ['status_code' => 0, 'error' => '', 'header' => [], 'body' => []];

        if (empty($request['url'])) {
            $responce['error'] = 'Invalid URL';
            return $responce;
        }

        if (empty($request['method']))
            $request['method'] = 'GET';

        $params = '';
        if (!empty($request['get_params']) && is_array($request['get_params']))
            $params = '?' . http_build_query($request['get_params']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request['url'] . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($request['header']) && is_array($request['header']))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request['header']);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($request['method'] == 'POST')
            curl_setopt($ch, CURLOPT_POST, true);

        if (!empty($request['post_params'])) {
            if (is_array($request['post_params']))
                $request['post_params'] = http_build_query($request['post_params']);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $request['post_params']);
        }

        if (!empty($request['request_body']))
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request['request_body']));


        $output = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responce['status_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responce['header'] = substr($output, 0, $header_size);
        $header = explode("\r\n", $responce['header']);
        $keyed_header = [];

        foreach ($header as $v) {
            $parts = explode(':', $v, 2);
            if (
                    isset($parts[1]))
                $keyed_header[strtolower(trim($parts[0]))] = trim($parts[1]);
        }

        $responce['header'] = $keyed_header;
        $responce['body'] = substr($output, $header_size);

        curl_close($ch);

        return $responce;
    }

    public function get_all_networks() {
        
        $this->db->select('*');
        $this->db->from('connect_network');
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->where('is_deleted', '0');

        return $this->db->get()->result_array();
    }

}
