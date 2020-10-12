<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_transaksi extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $primary_key = 'id_kastrans_kategori';
    private $title = 'Jenis Transaksi Kas';
    private $table_name = 'kastrans_kategori';
    private $type_trans = null;

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();

        $this->type_trans = get_enum_values('kastrans_kategori', 'type');
    }

    public function index() {
        $template = new Template();

        $template->set_title($this->title);
        $template->set_title_desc('Daftar ' . $this->title);

        $view_data['table_header'] = $this->table_header();

        $template->load_view('kas/jenis_transaksi/list', $view_data);

        $template->tampil();
    }

    private function table_header() {
        $table_header = array(
            'aksi',
            'id',
            'kode',
            'jenis transaksi',
            'type'
        );

        return $table_header;
    }

    private function orderby() {
        $array = array(
            'aksi',
            'kastrans_kategori.id_kastrans_kategori',
            'kastrans_kategori.kode_kastrans_kategori',
            'kastrans_kategori.nama_kastrans_kategori',
            'kastrans_kategori.`type`'
        );

        return $array;
    }

    private function sql() {
        $sql = "SELECT  
            '' as aksi,
            kastrans_kategori.id_kastrans_kategori,
            kastrans_kategori.kode_kastrans_kategori,
            kastrans_kategori.nama_kastrans_kategori,
            kastrans_kategori.`type`

            FROM kastrans_kategori
            WHERE kastrans_kategori.`status`=1
            AND
            kastrans_kategori.id_usaha=" . $this->id_usaha . "
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


        $form['kode_kastrans_kategori'] = '';
        $form['nama_kastrans_kategori'] = '';
        $form['type'] = '';

        $this->type_trans[''] = 'Pilih Type Transaksi...';

        $form['type_opt'] = $this->type_trans;


        if (!empty(trim($primary_id))) {
            $template->set_title_desc('Edit ' . $this->title);

            $where = array(
                'id_usaha' => $this->id_usaha,
                $this->primary_key => $primary_id,
            );
            $this->db->where($where);
            $db = $this->db->get($this->table_name);

            if ($db->num_rows() > 0) {
                $form['kode_kastrans_kategori'] = $db->row_object()->kode_kastrans_kategori;
                $form['nama_kastrans_kategori'] = $db->row_object()->nama_kastrans_kategori;
                $form['type'] = $db->row_object()->type;
            }
        }


        $view_data['primary_id'] = $primary_id;
        $view_data['form'] = $form;
        $view_data['title'] = $this->title;


        $template->load_view('kas/jenis_transaksi/edit', $view_data);

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
//kode_kastrans_kategori
//nama_kastrans_kategori
//type

        $this->form_validation->set_rules('nama_kastrans_kategori', ucwords('jenis transaksi'), 'trim|required');
        $this->form_validation->set_rules('type', ucwords('type'), 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }


        if ($succes) {

            $insert['kode_kastrans_kategori'] = $post['kode_kastrans_kategori'];
            $insert['nama_kastrans_kategori'] = $post['nama_kastrans_kategori'];
            $insert['type'] = $post['type'];
            $insert['id_usaha'] = $this->id_usaha;


            if (empty(trim($post['primary_id']))) {

                $this->db->insert($this->table_name, $insert);
                $message = "Data Berhasil disimpan";
                $insert_id = $this->db->insert_id();

                if (empty(trim($post['kode_kastrans_kategori']))) {



                    $kode = 1000 + $insert_id;
                    $this->db->where(array($this->primary_key => $insert_id));
                    $this->db->set(array('kode_kastrans_kategori' => $kode));
                    $this->db->update($this->table_name);

//                    print_r2($this->db->last_query());
                }
            } else {

                $this->db->where($this->primary_key, $primary_id);
                $this->db->set($insert);
                $this->db->update($this->table_name);

                $insert_id = $primary_id;

                if (empty(trim($post['kode_kastrans_kategori']))) {
                    $kode = 1000 + $insert_id;
                    $this->db->where(array($this->primary_key => $insert_id));
                    $this->db->set(array('kode_kastrans_kategori' => $kode));
                    $this->db->update($this->table_name);
                }


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
        $succes = true;
        $data = array();
        $error = array();

        $auth = new Auth_model();

        $primary_id = $this->input->post('delete_id');

        $lock = get_column($this->table_name, array($this->primary_key => $primary_id), 'lock_code');

        if (intval($lock) > 0) {
            $succes = FALSE;
            $message .= "Data Tersebut Dikunci";
        }

        if ($succes) {

            $this->db->set('status', 0);
            $this->db->where('id_usaha', $this->id_usaha);
            $this->db->where($this->primary_key, $primary_id);
            $this->db->update($this->table_name);
        }


        if ($succes) {
            $this->session->set_flashdata('message_succes', 'Data Berhasil Dihapus');
            $message = "Data Berhasil Dihapus";
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
