<?php

class Template {

    //put your code here
    private $app_name = 'Rengganis POS';
    private $logo_app = 'default_img/toppng.com-money-bag-icon-euros-377x376.png';
    private $logo_usaha = 'default_img/toppng.com-money-bag-icon-euros-377x376.png';
    private $nama_usaha = 'Nama Bisnis';
    private $username = 'username';
    private $fullname = 'fullname';
    private $image_profile = 'default_img/user7-128x128.jpg';
    private $title = 'Title';
    private $title_desc = '';
//Grocery crud
    private $Grocery_output = null;
    private $css_files = array();
    private $js_files = array();
//Grocery crud

    private $content = '';
    private $box=true;
    
    private $url_controller_active='';

    public function __construct() {
        $ci = &get_instance();
        $ci->load->helper('haris');
        $ci->load->model('Auth_model');
        $ci->load->library('session');

        $auth = new Auth_model();

        if ($auth->login_status) {
            $this->nama_usaha = $auth->get_usaha_data()->nama_usaha;
            $this->logo_usaha = 'logo_usaha/' . $auth->get_usaha_data()->logo;
            $this->fullname = $auth->get_user_data()->username;
            $this->username = $auth->get_user_data()->username;
            $this->url_controller_active=$auth->get_url_controller();
        }
    }
    
    function setUrl_controller_active($url_controller_active) {
        $this->url_controller_active = $url_controller_active;
        return $this;
    }

        
    function set_box($box) {
        $this->box = $box;
         return $this;
    }

    
    function set_app_name($app_name) {
        $this->app_name = $app_name;
        return $this;
    }

    function set_logo_usaha($logo_usaha) {
        $this->logo_usaha = $logo_usaha;
        return $this;
    }

    function set_nama_usaha($nama_usaha) {
        $this->nama_usaha = $nama_usaha;
        return $this;
    }

    function set_title($title) {
        $this->title = $title;
        return $this;
    }

    function set_username($username) {
        $this->username = $username;
        return $this;
    }

    function set_fullname($fullname) {
        $this->fullname = $fullname;
        return $this;
    }

    function set_image_profile($image_profile) {
        $this->image_profile = $image_profile;
        return $this;
    }

    function set_Grocery_output($Grocery_output) {
        $this->Grocery_output = $Grocery_output;
        return $this;
    }

    function set_css_files($css_files) {
        $this->css_files = $css_files;
        return $this;
    }

    function set_js_files($js_files) {
        $this->js_files = $js_files;
        return $this;
    }

    function set_content($content) {
        $this->content = $content;
        return $this;
    }

    function set_title_desc($title_desc) {
        $this->title_desc = $title_desc;
        return $this;
    }

    function load_view($view,$view_data) {
        $ci=&get_instance();

        $this->content=$ci->load->view($view, $view_data, true);
        return $this;
    }

    private function tampil_init() {

        if (!is_null($this->Grocery_output)) {
            $this->content = $this->Grocery_output->output;
            $this->css_files = $this->Grocery_output->css_files;
            $this->js_files = $this->Grocery_output->js_files;
        }
    }

    private function tampil_view_data() {
        $view_data = array(
            'app_name' => $this->app_name,
            'logo_app' => $this->logo_app,
            'logo_usaha' => $this->logo_usaha,
            'nama_usaha' => $this->nama_usaha,
            'title' => $this->title,
            'title_desc' => $this->title_desc,
            'username' => $this->username,
            'fullname' => $this->fullname,
            'image_profile' => $this->image_profile,
            'css_files' => $this->css_files,
            'js_files' => $this->js_files,
            'content' => $this->content,
            'box'=> $this->box,
            'url_controller_active'=> $this->url_controller_active,
        );

        return $view_data;
    }

    function tampil() {
        $ci = &get_instance();

        $this->tampil_init();

        $ci->load->view('template/template', $this->tampil_view_data());
    }

    function tampil_login() {
        $ci = &get_instance();

        $this->tampil_init();

        $ci->load->view('template/login', $this->tampil_view_data());
    }

}
