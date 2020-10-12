<?php

class Auth_model extends CI_Model {

    //put your code here

    protected $db_table = '_user';
    protected $sess_username = 'username';
    protected $sess_password = 'password';
    private $user_data = null;
    private $usaha_data = null;
    private $username = '';
    private $password = '';
    private $url_controller = "";
    private $pass_login = array(
        'autentikasi/'
    );
    public $login_status = false;

    public function __construct() {
        parent::__construct();
        $this->username = $this->session->userdata($this->sess_username);
        $this->password = $this->session->userdata($this->sess_password);
        $this->set_url_controller();


        if (!in_array($this->get_url_controller(), $this->pass_login)) {
            $this->is_login();

            $this->set_user_data();
            $this->set_usaha_data();
        }
    }

    function get_user_data() {
        return $this->user_data;
    }

    function get_usaha_data() {
        return $this->usaha_data;
    }

    function login($username, $password) {
        $data = array();
        $data['succes'] = 1;
        $data['message'] = '';


        $this->db->or_where('username', $username);
        $this->db->or_where('email', $username);
        $db = $this->db->get($this->db_table);

        if ($db->num_rows() > 0) {
            if ($db->row_object()->password == $password) {
                $data['succes'] = 1;
                $this->session->set_userdata($this->sess_username, $username);
                $this->session->set_userdata($this->sess_password, $password);
            } else {
                $data['succes'] = 0;
                $data['message'] = "Password Salah";
            }
        } else {
            $data['succes'] = 0;
            $data['message'] = "Username atau Email tidak dikenali";
        }

        return $data;
    }

    function logout() {
        $this->session->unset_userdata($this->sess_username);
        $this->session->unset_userdata($this->sess_password);
    }

    private function set_url_controller() {
        $this->url_controller = $this->router->directory . "" . $this->router->class;
        $this->url_controller = rtrim(strtolower($this->url_controller), "/") . "/";
    }
    
    

    public function get_url_controller() {
        return $this->url_controller;
    }

    private function set_user_data() {
        $user_data = array();

        $this->db->group_start();
        $this->db->or_where('_user.username', $this->username);
        $this->db->or_where('_user.email', $this->username);
        $this->db->group_end();

        $this->db->where('password', $this->password);
        $db = $this->db->get($this->db_table);

        if ($db->num_rows() > 0) {
            $user_data = $db->row_object();
        }

        $this->user_data = $user_data;
    }

    private function set_usaha_data() {
        $usaha_data = array();


        $this->db->where('id_usaha', $this->user_data->id_usaha);
        $db = $this->db->get('_usaha');

        if ($db->num_rows() > 0) {
            $usaha_data = $db->row_object();
        }

        $this->usaha_data = $usaha_data;
    }

    public function is_login() {
        $this->db->group_start();
        $this->db->or_where('username', $this->username);
        $this->db->or_where('email', $this->username);
        $this->db->group_end();

        $this->db->where('password', $this->password);

        $db = $this->db->get($this->db_table);

        if ($db->num_rows() < 1) {
            $this->logout();

            $this->session->set_flashdata('message_error', 'Maaf Anda Belum Login');
            redirect('login');
        } elseif (empty($db->row_object()->id_usaha)) {
            $this->logout();

            $this->session->set_flashdata('message_error', 'Maaf Usaha Tidak ditemukan');
            redirect('login');
        }

        $this->login_status = true;
    }

}
