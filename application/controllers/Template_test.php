<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Template_test extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->library('Template');
        $this->load->library('grocery_CRUD');


        $template = new Template();

        $crud = new grocery_CRUD();
        $crud->unset_bootstrap();
        $crud->unset_jquery();

        $crud->set_theme('bootstrap');
        $crud->set_language('indonesian');

        $crud->set_table('offices');
        $crud->set_subject('Office');
        $crud->required_fields('city');
        $crud->columns('city', 'country', 'phone', 'addressLine1', 'postalCode');

        $output = $crud->render();
        
        $template->set_Grocery_output($output);
        $template->tampil();
    }

}
