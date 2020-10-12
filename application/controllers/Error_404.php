<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Error_404 extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $template = new Template();

        $template->set_title('404 Error Page');


        $view_data = array();

        $template->set_box(FALSE);
        $template->load_view('404', $view_data);

        $template->tampil();
    }

}
