<?php

class Produk extends CI_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $succes = true;
        $data = array();
        $message = '';

        
        $ko_data = $this->input->post('ko_data');

        $this->db->limit(10);
        $data = $this->db->get('it_produk')->result_array();



        $output['succes'] = $succes;
        $output['data'] = $data;
        $output['message'] = $message;
        header_cross_domain();
        header_json();
        echo json_encode($output);
    }

}
