<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rekening extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $primary_key = 'id_rekening';
    private $title = 'Rekening & Pembayaran';
    private $table_name = 'm_rekening';
    private $type_rekening = null;

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();

        $this->type_rekening = get_enum_values('m_rekening', 'type');
    }

    public function index() {
        $template = new Template();

        $template->set_title($this->title);
        $template->set_title_desc('Daftar ' . $this->title);

        $view_data['table_header'] = $this->table_header();

        $template->load_view('master/rekening/list', $view_data);

        $template->tampil();
    }

    private function table_header() {
        $table_header = array(
            'aksi',
            'm_rekening.id_rekening',
            'rekening',
            'type'
        );

        return $table_header;
    }

    private function orderby() {
        $array = array(
            'aksi',
            'm_rekening.id_rekening',
            'm_rekening.nama_rekening',
            'm_rekening.type'
        );

        return $array;
    }

    private function sql() {
        $sql = "SELECT 
            '' as aksi,
            m_rekening.id_rekening,
            m_rekening.nama_rekening,
            m_rekening.`type`


            FROM m_rekening
            WHERE m_rekening.id_usaha=" . $this->id_usaha . "
            AND
            m_rekening.`status`=1
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

        $form['nama_rekening'] = '';
        $form['type'] = '';
        $form['deskripsi'] = '';

        $this->type_rekening[''] = 'Type Rekening...';

        $form['type_opt'] = $this->type_rekening;

        $db = $this->db->query($this->sql_alamat_kota());

        if (!empty(trim($primary_id))) {
            $template->set_title_desc('Edit ' . $this->title);

            $where = array(
                'id_usaha' => $this->id_usaha,
                'id_rekening' => $primary_id,
            );
            $this->db->where($where);
            $db = $this->db->get($this->table_name);

            if ($db->num_rows() > 0) {
                $form['nama_rekening'] = $db->row_object()->nama_rekening;
                $form['type'] = $db->row_object()->type;
                $form['deskripsi'] = $db->row_object()->deskripsi;
            }
        }


        $view_data['primary_id'] = $primary_id;
        $view_data['form'] = $form;
        $view_data['title'] = $this->title;


        $template->load_view('master/rekening/edit', $view_data);

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

        $this->load->library('form_validation');
        $this->form_validation->set_data($post);

        if (!empty(trim($primary_id))) {
            $id_usaha = get_column($this->table_name, array($this->primary_key => $primary_id), 'id_usaha');
            if ($id_usaha != $this->id_usaha) {
                $message .= '<p>Data Tidak Ditemukan</p>';
                $succes = FALSE;
            }
        }


        $this->form_validation->set_rules('nama_rekening', ucwords('nama rekening'), 'trim|required');
        $this->form_validation->set_rules('type', ucwords('type'), 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }


        $form['nama_rekening'] = '';
        $form['type'] = '';
        $form['deskripsi'] = '';

        if ($succes) {

            $insert['nama_rekening'] = $post['nama_rekening'];
            $insert['type'] = $post['type'];
            $insert['deskripsi'] = $post['deskripsi'];
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
