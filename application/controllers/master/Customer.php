<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $primary_key = 'id_customer';
    private $title = 'Customer';
    private $table_name = 'm_customer';

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();
    }

    public function index() {
        $template = new Template();

        $template->set_title($this->title);
        $template->set_title_desc('Daftar ' . $this->title);

        $view_data['table_header'] = $this->table_header();

        $template->load_view('master/customer/list', $view_data);

        $template->tampil();
    }

    private function table_header() {
        $table_header = array(
            'aksi',
            'id_customer',
            'nama customer',
            'telp',
            'email',
            'kota',
            'alamat',
            'tanggal lahir'
        );

        return $table_header;
    }

    private function orderby() {
        $array = array(
            'aksi',
            'id_customer',
            'nama_customer',
            'telp',
            'email',
            'alamat_kota.name',
            'alamat',
            "DATE_FORMAT(tanggal_lahir,'%d/%m/%Y')"
        );

        return $array;
    }

    private function sql() {
        $sql = "SELECT 
        '' AS aksi,
        id_customer,
        nama_customer,
        telp,
        email,
        alamat_kota.name AS kota,
        alamat,
        DATE_FORMAT(tanggal_lahir,'%d/%m/%Y') as tanggal_lahir
        FROM m_customer

        LEFT JOIN alamat_kota ON alamat_kota.id=m_customer.id_kota

            WHERE
            m_customer.`status`=1
            AND
            m_customer.id_usaha=" . $this->id_usaha . "
                ";

        return $sql;
    }

    private function callback_column($key, $col, $row) {

        $auth = new Auth_model();
        $encrypt = new Encryption_model();

        if ($key == 'aksi') {
            $col = "";
            $col .= '<a class="btn btn-info btn-xs" '
                    . 'href="' . base_url() . $auth->get_url_controller() . 'edit/'
                    . $encrypt->encode($row[$this->primary_key]) . '" >'
                    . '<i class="fa fa-pencil" ></i>'
                    . '</a>';
            $col .= '&nbsp';
            $col .= '<a class="btn btn-danger btn-xs" onclick="delete_alert(' . "'" . $row[$this->primary_key] . "'" . ')" href="#" ><i class="fa fa-trash" ></i></a>';
        }

        return $col;
    }

    // <editor-fold defaultstate="collapsed" desc="Datatables">
    public function datatables() {
        $sql = $this->sql();

        $search_get = $this->input->get('search');
        $cari = $search_get['value'];
        if (strlen(trim($cari)) > 0) {
            $sql .= " and (" . "\n";
            $i = 0;
            foreach ($this->orderby() as $search_key => $search) {
                if ($i > 1) {
                    $sql .= " or ";
                }
                if ($i > 0) {
                    $sql .= " " . $search . " like " . "'%" . $cari . "%' " . "\n";
                }
                $i++;
            }
            $sql .= ")" . "\n";
        }

        foreach ($this->orderby() as $order_key => $order) {
            $order_get = $this->input->get('order');
            if (is_array($order_get) && count($order_get)) {
                if ($order_get[0]['column'] == $order_key) {
                    $sql .= "\n" . " order by " . $order . " " . $order_get[0]['dir'] . " ";
                }
            }
        }

        $total_row = $this->db->query("select count(*) as total from (" . $sql . ") as tb ")->row_array()['total'];


        $sql .= " limit " . intval($this->input->get('start')) . "," . intval($this->input->get('length')) . " ";
        $result = $this->db->query($sql)->result_array();


        $datatables_format = array(
            'data' => array(),
            'recordsTotal' => $total_row,
            'recordsFiltered' => $total_row,
        );

        foreach ($result as $row) {
            $buffer = array();
            foreach ($row as $key => $col) {
                $col = $this->callback_column($key, $col, $row);
                array_push($buffer, $col);
            }
            array_push($datatables_format['data'], $buffer);
        }
        header('Content-Type: application/json');
        echo json_encode($datatables_format);
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="alamat kota">
    private function sql_alamat_kota() {
        $sql = "SELECT  
        alamat_kota.id,
        CONCAT( alamat_provinsi.name,' - ',alamat_kota.name ) AS text
        FROM alamat_kota
        JOIN alamat_provinsi ON alamat_provinsi.id=alamat_kota.province_id";
        return $sql;
    }

    // </editor-fold>
    public function add($primary_id = null) {
        $this->edit($primary_id);
    }

    public function edit($primary_id = null) {
        $auth = new Auth_model();
        $enc = new Encryption_model();

        $primary_id = $enc->decode($primary_id);

        if (!empty(trim($primary_id))) {
            $id_usaha = get_column($this->table_name, array($this->primary_key => $primary_id), 'id_usaha');
            if ($id_usaha != $this->id_usaha) {
                $this->session->set_flashdata('message_error', 'Data Tidak Ada');
                redirect($this->url_controller);
            }
        }

        $template = new Template();

        $template->set_title($this->title);
        $template->set_title_desc('Tambah ' . $this->title);

        $view_data = array();
        $form = array();

        $form['nama_customer'] = '';
        $form['telp'] = '';
        $form['whatsapp'] = '';
        $form['email'] = '';
        $form['alamat'] = '';
        $form['tanggal_lahir'] = '';
        $form['id_kota'] = '';
        $form['id_kota_opt'] = array();

        $db = $this->db->query($this->sql_alamat_kota());
        $form['id_kota_opt'] = dropdown_array($db->result_array(), 'id', 'text', 'pilih kota');


        if (!empty(trim($primary_id))) {
            $template->set_title_desc('Edit ' . $this->title);


            $where = array(
                'id_usaha' => $this->id_usaha,
                'id_customer' => $primary_id,
            );
            $this->db->where($where);
            $db = $this->db->get($this->table_name);

            if ($db->num_rows() > 0) {

                $form['nama_customer'] = $db->row_object()->nama_customer;
                $form['telp'] = $db->row_object()->telp;
                $form['whatsapp'] = $db->row_object()->whatsapp;
                $form['email'] = $db->row_object()->email;
                $form['alamat'] = $db->row_object()->alamat;
                $form['id_kota'] = $db->row_object()->id_kota;
                $form['tanggal_lahir'] = waktu_ymd_to_dmy($db->row_object()->tanggal_lahir);
            }
        }


        $view_data['primary_id'] = $primary_id;
        $view_data['form'] = $form;
        $view_data['title'] = $this->title;


        $template->load_view('master/customer/edit', $view_data);

        $template->tampil();
    }

    public function submit() {
        $auth = new Auth_model();
        $enc = new Encryption_model();
        $userdata = $auth->get_user_data();


        $message = '';
        $succes = true;
        $data = array();
        $error = array();
        $post = $this->input->post();

        $primary_id = ($post['primary_id']);

        if (!empty(trim($primary_id))) {
            $id_usaha = get_column($this->table_name, array($this->primary_key => $primary_id), 'id_usaha');
            if ($id_usaha != $this->id_usaha) {
                $message .= '<p>Data Tidak Ditemukan</p>';
                $succes = FALSE;
            }
        }

        $this->load->library('form_validation');
        $this->form_validation->set_data($post);


        $this->form_validation->set_rules('nama_customer', ucwords('nama outlet'), 'trim|required');
        $this->form_validation->set_rules('telp', ucwords('Kota'), 'trim|required');
        $this->form_validation->set_rules('email', ucwords('telp'), 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }


        if ($succes) {

            $insert['nama_customer'] = $post['nama_customer'];
            $insert['telp'] = $post['telp'];
            $insert['email'] = $post['email'];
            $insert['alamat'] = $post['alamat'];
            $insert['id_kota'] = $post['id_kota'];
            $insert['tanggal_lahir'] = waktu_dmy_to_ymd($post['tanggal_lahir']);
            $insert['id_usaha'] = $this->id_usaha;


            if (empty(trim($post['primary_id']))) {

                $this->db->insert($this->table_name, $insert);
                $message = "Data Berhasil disimpan";
            } else {
                $this->db->where($this->primary_key, $primary_id);
                $this->db->set($insert);
                $this->db->update($this->table_name);

                $message = "Data Berhasil disimpan";
            }
            $this->session->set_flashdata('message_succes', $message);
        }




        $result = array(
            'error' => $error,
            'message' => $message,
            'succes' => $succes,
            'data' => $data,
        );

        header_json();
        echo json_encode($result);
    }

    public function delete() {
        $message = '';
        $succes = false;
        $data = array();
        $error = array();

        $auth = new Auth_model();

        $primary_id = $this->input->post('delete_id');

        $this->db->set('status', 0);
        $this->db->where('id_usaha', $this->id_usaha);
        $this->db->where($this->primary_key, $primary_id);
        $this->db->update($this->table_name);

        $this->session->set_flashdata('message_succes', 'Data Berhasil Dihapus');
        $message = "Data Berhasil Dihapus";

        $result = array(
            'error' => $error,
            'message' => $message,
            'succes' => TRUE,
            'data' => $data,
        );

        header_json();
        echo json_encode($result);
    }

    public function xls() {
        $data_view = array();

        $this->load->library('table');

        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );
        $this->table->set_template($template);

        $this->table->set_heading($this->table_header());

        $list = $this->db->query($this->sql())->result_array();

        foreach ($list as $row) {
            $this->table->add_row($row);
        }

        $data_view['table'] = $this->table->generate();
        $data_view['title'] = $this->title;

        $content = $this->load->view('master/outlet/export', $data_view, true);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=" . trim($this->title) . "_" . uniqid() . ".xls");
        echo $content;
    }

}
