<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Autentikasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        redirect('Autentikasi/login');
    }

    public function login() {
//        echo $this->session->flashdata('message');
//        die();
//        
        $template=new Template();
        
        $template->tampil_login();
    }
    
    public function login_submit() {
        $succes = true;
        $message = "";

        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));
        
        $auth=new Auth_model();
        $data=$auth->login($username, $password);

        header_json();
        $output = array(
            'succes' => $data['succes'],
            'message' => $data['message'],
        );
        echo json_encode($output);
        
    }

    public function logout(){
        $this->session->set_flashdata('message','berhasil logout');
        
        $auth=new Auth_model();
        $auth->logout();
        
        redirect('autentikasi/login');
    }
    
    
}
