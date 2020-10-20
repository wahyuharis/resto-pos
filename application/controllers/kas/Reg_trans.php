<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reg_trans extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $primary_key = 'id_kastrans_reg';
    private $title = 'Transaksi Kas';
    private $table_name = 'kastrans_reg';

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

        $outlet_ar = $this->db->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->get('m_outlet')
                ->result_array();


        $opt_outlet = dropdown_array($outlet_ar, 'id_outlet', 'nama_outlet', 'Pilih Outlet...', FALSE);


        $tanggal_val = date('01/m/Y') . " - " . date('t/m/Y');


        $view_data = array(
            'table_header' => $this->table_header(),
            'opt_outlet' => $opt_outlet,
            'tanggal_val' => $tanggal_val,
        );

        $template->load_view('kas/reg_trans/list', $view_data);

        $template->set_box(false);

        $template->tampil();
    }

    private function table_header() {
        $table_header = array(
            'aksi',
            'id_kastrans_reg',
            'kode',
            'outlet',
            'jenis transaksi',
            'tanggal',
            'nominal'
        );

        return $table_header;
    }

    private function orderby() {
        $array = array(
            'aksi',
            'kastrans_reg.id_kastrans_reg',
            'kastrans_reg.kode_kastrans',
            'm_outlet.nama_outlet',
            'kastrans_kategori.nama_kastrans_kategori',
            'kastrans_reg.tanggal',
            'kastrans_reg.nominal'
        );

        return $array;
    }

    private function sql() {
        $sql = "SELECT 
                        '' as aksi,
            kastrans_reg.id_kastrans_reg,
            kastrans_reg.kode_kastrans,
            m_outlet.nama_outlet,
            kastrans_kategori.nama_kastrans_kategori,
            DATE_FORMAT(kastrans_reg.tanggal,'%d/%m/%Y') AS tanggal,
            kastrans_reg.nominal

            FROM kastrans_reg

            LEFT JOIN kastrans_kategori 
            ON kastrans_kategori.id_kastrans_kategori=kastrans_reg.id_kastrans_kategori
            
            LEFT JOIN m_outlet
            ON m_outlet.id_outlet=kastrans_reg.id_outlet

            WHERE kastrans_reg.`status`=1
            AND kastrans_reg.id_usaha=" . $this->id_usaha . "
                ";

        return $sql;
    }

    private function callback_column($key, $col, $row) {

        $auth = new Auth_model();
        $encrypt = new Encryption_model();

        if ($key == 'aksi') {
            $col = "";
            $col .= '<a class="btn btn-info btn-xs" '
                    . 'href="' . base_url() . $auth->get_url_controller() . 'detail/'
                    . $encrypt->encode($row[$this->primary_key]) . '" >'
                    . '<i class="fa fa-list" ></i> Detail'
                    . '</a>';
            $col .= '&nbsp';
        }

        if ($key == 'nominal') {
            $col = "Rp " . number_format($row['nominal'], 2);
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



    public function pengeluaran($primary_id = null) {
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

        $template->set_title("Pengeluaran Kas");
        $template->set_title_desc('Tambah ' . $this->title);

        $view_data = array();
        $form = array();

        $form['kode_kastrans'] = '';
        $form['id_kastrans_kategori'] = '';
        $form['id_outlet'] = '';
        $form['tanggal'] = '';
        $form['nominal'] = '';
        $form['foto_nota'] = '';
        $form['keterangan'] = '';

        $pengeluaran_ar = $this->db->select('kastrans_kategori.*,'
                        . "concat(kastrans_kategori.kode_kastrans_kategori,' - ', kastrans_kategori.nama_kastrans_kategori) as opsi")
                ->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->where('type', 'pengeluaran')
                ->get('kastrans_kategori')
                ->result_array();

        $form['opt_pengeluaran'] = dropdown_array($pengeluaran_ar, 'id_kastrans_kategori', 'opsi', 'Tulis/Pilih jenis transaksi kas...');


        $outlet_ar = $this->db->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->get('m_outlet')
                ->result_array();

        $opt_outlet = dropdown_array($outlet_ar, 'id_outlet', 'nama_outlet', 'Pilih Outlet...');

        $form['opt_outlet'] = $opt_outlet;

        if (!empty(trim($primary_id))) {
            $template->set_title_desc('Edit ' . $this->title);

            $where = array(
                'id_usaha' => $this->id_usaha,
                $this->primary_key => $primary_id,
            );
            $this->db->where($where);
            $db = $this->db->get($this->table_name);

            if ($db->num_rows() > 0) {
                $form['kode_kastrans'] = $db->row_object()->kode_kastrans;
                $form['id_kastrans_kategori'] = $db->row_object()->id_kastrans_kategori;
                $form['id_outlet'] = $db->row_object()->id_outlet;
                $form['tanggal'] = $db->row_object()->tanggal;
                $form['nominal'] = $db->row_object()->nominal;
                $form['foto_nota'] = $db->row_object()->foto_nota;
                $form['keterangan'] = $db->row_object()->keterangan;
            }
        }


        $view_data['primary_id'] = $primary_id;
        $view_data['form'] = $form;
        $view_data['title'] = $this->title;


        $template->load_view('kas/reg_trans/pengeluaran', $view_data);

        $template->tampil();
    }

    public function submit_pengeluaran() {
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

//        print_r2($post);

        if (!empty(trim($primary_id))) {
            $id_usaha = get_column($this->table_name, array($this->primary_key => $primary_id), 'id_usaha');
            if ($id_usaha != $this->id_usaha) {
                $message .= '<p>Data Tidak Ditemukan</p>';
                $succes = FALSE;
            }
        }

        $this->form_validation->set_rules('tanggal', ucwords('tanggal'), 'trim|required');
        $this->form_validation->set_rules('id_kastrans_kategori', ucwords('jenis transaksi'), 'trim|required');
        $this->form_validation->set_rules('id_outlet', ucwords('outlet'), 'trim|required');
//        $this->form_validation->set_rules('kode_kastrans', ucwords('kode'), 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }

        if ($succes) {
            $nominal = floatval2($post['nominal']);
            if ($nominal < 100) {
                $succes = false;
                $message = '<p>nominal tidak boleh kosong</p>';
                $error['nominal'] = '<p>nominal tidak boleh kosong</p>';
            }
        }

        $foto_uploaded = '';
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|GIF|JPG|jpeg|PNG|pdf|PDF';
        $config['max_size'] = 512;
        $this->load->library('upload', $config);

        if (!empty(trim($_FILES['foto']['name']))) {
            if ($this->upload->do_upload('foto')) {
                $foto_uploaded = $this->upload->data()['file_name'];
            } else {
                $succes = false;
                $message .= $this->upload->display_errors();
            }
        }



        if ($succes) {
            $insert_id = null;
            $id_kastrans_kategori = null;
            if (!is_numeric($post['id_kastrans_kategori'])) {

                $insert0['nama_kastrans_kategori'] = $post['id_kastrans_kategori'];
                $insert0['type'] = 'pengeluaran';
                $insert0['id_usaha'] = $this->id_usaha;

                $this->db->insert('kastrans_kategori', $insert0);
                $insert_id0 = $this->db->insert_id();

                $kode0 = 1000 + $insert_id0;
                $this->db->where(array('id_kastrans_kategori' => $insert_id0));
                $this->db->set(array('kode_kastrans_kategori' => $kode0));
                $this->db->update('kastrans_kategori');

                $id_kastrans_kategori = $insert_id0;
            } else {
                $id_kastrans_kategori = $post['id_kastrans_kategori'];
            }

            $insert['tanggal'] = waktu_dmy_to_ymd($post['tanggal']);
            $insert['id_kastrans_kategori'] = $id_kastrans_kategori;
            $insert['id_outlet'] = $post['id_outlet'];
            $insert['kode_kastrans'] = $post['kode_kastrans'];
            $insert['nominal'] = floatval2($post['nominal']) * -1;
            $insert['keterangan'] = $post['keterangan'];
            $insert['foto_nota'] = $foto_uploaded;
            $insert['id_usaha'] = $this->id_usaha;

            if (empty(trim($post['primary_id']))) {

                $this->db->insert($this->table_name, $insert);
                $message = "Data Berhasil disimpan";
                $insert_id = $this->db->insert_id();

                if (empty(trim($post['kode_kastrans']))) {
                    $kode = 1000 + $insert_id;
                    $this->db->where(array($this->primary_key => $insert_id));
                    $this->db->set(array('kode_kastrans' => $kode));
                    $this->db->update($this->table_name);
                }
            }
            $this->session->set_flashdata('message_succes', $message);
            $data['primary_id'] = $enc->encode($insert_id);
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

    public function pemasukan($primary_id = null) {
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

        $template->set_title("Pengeluaran Kas");
        $template->set_title_desc('Tambah ' . $this->title);

        $view_data = array();
        $form = array();

        $form['kode_kastrans'] = '';
        $form['id_kastrans_kategori'] = '';
        $form['id_outlet'] = '';
        $form['tanggal'] = '';
        $form['nominal'] = '';
        $form['foto_nota'] = '';
        $form['keterangan'] = '';

        $pengeluaran_ar = $this->db->select('kastrans_kategori.*,'
                        . "concat(kastrans_kategori.kode_kastrans_kategori,' - ', kastrans_kategori.nama_kastrans_kategori) as opsi")
                ->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->where('type', 'pemasukan')
                ->get('kastrans_kategori')
                ->result_array();

        $form['opt_pemasukan'] = dropdown_array($pengeluaran_ar, 'id_kastrans_kategori', 'opsi', 'Tulis/Pilih jenis transaksi kas...');


        $outlet_ar = $this->db->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->get('m_outlet')
                ->result_array();

        $opt_outlet = dropdown_array($outlet_ar, 'id_outlet', 'nama_outlet', 'Pilih Outlet...');

        $form['opt_outlet'] = $opt_outlet;

        if (!empty(trim($primary_id))) {
            $template->set_title_desc('Edit ' . $this->title);

            $where = array(
                'id_usaha' => $this->id_usaha,
                $this->primary_key => $primary_id,
            );
            $this->db->where($where);
            $db = $this->db->get($this->table_name);

            if ($db->num_rows() > 0) {
                $form['kode_kastrans'] = $db->row_object()->kode_kastrans;
                $form['id_kastrans_kategori'] = $db->row_object()->id_kastrans_kategori;
                $form['id_outlet'] = $db->row_object()->id_outlet;
                $form['tanggal'] = $db->row_object()->tanggal;
                $form['nominal'] = $db->row_object()->nominal;
                $form['foto_nota'] = $db->row_object()->foto_nota;
                $form['keterangan'] = $db->row_object()->keterangan;
            }
        }


        $view_data['primary_id'] = $primary_id;
        $view_data['form'] = $form;
        $view_data['title'] = $this->title;


        $template->load_view('kas/reg_trans/pemasukan', $view_data);

        $template->tampil();
    }

    public function submit_pemasukan() {
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

        $this->form_validation->set_rules('tanggal', ucwords('tanggal'), 'trim|required');
        $this->form_validation->set_rules('id_kastrans_kategori', ucwords('jenis transaksi'), 'trim|required');
        $this->form_validation->set_rules('id_outlet', ucwords('outlet'), 'trim|required');
//        $this->form_validation->set_rules('kode_kastrans', ucwords('kode'), 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }

        if ($succes) {
            $nominal = floatval2($post['nominal']);
            if ($nominal < 100) {
                $succes = false;
                $message = '<p>nominal tidak boleh kosong</p>';
                $error['nominal'] = '<p>nominal tidak boleh kosong</p>';
            }
        }

        $foto_uploaded = '';
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|GIF|JPG|jpeg|PNG|pdf|PDF';
        $config['max_size'] = 512;
        $this->load->library('upload', $config);

        if (!empty(trim($_FILES['foto']['name']))) {
            if ($this->upload->do_upload('foto')) {
                $foto_uploaded = $this->upload->data()['file_name'];
            } else {
                $succes = false;
                $message .= $this->upload->display_errors();
            }
        }



        if ($succes) {
            $insert_id = null;
            $id_kastrans_kategori = null;
            if (!is_numeric($post['id_kastrans_kategori'])) {

                $insert0['nama_kastrans_kategori'] = $post['id_kastrans_kategori'];
                $insert0['type'] = 'pengeluaran';
                $insert0['id_usaha'] = $this->id_usaha;

                $this->db->insert('kastrans_kategori', $insert0);
                $insert_id0 = $this->db->insert_id();

                $kode0 = 1000 + $insert_id0;
                $this->db->where(array('id_kastrans_kategori' => $insert_id0));
                $this->db->set(array('kode_kastrans_kategori' => $kode0));
                $this->db->update('kastrans_kategori');

                $id_kastrans_kategori = $insert_id0;
            } else {
                $id_kastrans_kategori = $post['id_kastrans_kategori'];
            }

            $insert['tanggal'] = waktu_dmy_to_ymd($post['tanggal']);
            $insert['id_kastrans_kategori'] = $id_kastrans_kategori;
            $insert['id_outlet'] = $post['id_outlet'];
            $insert['kode_kastrans'] = $post['kode_kastrans'];
            $insert['nominal'] = floatval2($post['nominal']);
            $insert['keterangan'] = $post['keterangan'];
            $insert['foto_nota'] = $foto_uploaded;
            $insert['id_usaha'] = $this->id_usaha;

            if (empty(trim($post['primary_id']))) {

                $this->db->insert($this->table_name, $insert);
                $message = "Data Berhasil disimpan";
                $insert_id = $this->db->insert_id();

                if (empty(trim($post['kode_kastrans']))) {
                    $kode = 1000 + $insert_id;
                    $this->db->where(array($this->primary_key => $insert_id));
                    $this->db->set(array('kode_kastrans' => $kode));
                    $this->db->update($this->table_name);
                }
            }
            $this->session->set_flashdata('message_succes', $message);
            $data['primary_id'] = $enc->encode($insert_id);
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
//
//        $auth = new Auth_model();
//
//        $primary_id = $this->input->post('delete_id');
//
//        $lock = get_column($this->table_name, array($this->primary_key => $primary_id), 'lock_code');
//
//        if (intval($lock) > 0) {
//            $succes = FALSE;
//            $message .= "Data Tersebut Dikunci";
//        }
//
//        if ($succes) {
//
//            $this->db->set('status', 0);
//            $this->db->where('id_usaha', $this->id_usaha);
//            $this->db->where($this->primary_key, $primary_id);
//            $this->db->update($this->table_name);
//        }
//
//
//        if ($succes) {
//            $this->session->set_flashdata('message_succes', 'Data Berhasil Dihapus');
//            $message = "Data Berhasil Dihapus";
//        }


        $result = array(
            'error' => $error,
            'message' => $message,
            'succes' => $succes,
            'data' => $data,
        );

        header_json();
        echo json_encode($result);
    }

    function detail($primary_id) {

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

        $this->load->model('kas/Reg_trans_model');
        $reg_trans_model = new Reg_trans_model();

        $detail = $reg_trans_model->get($primary_id, true);

        $template = new Template();

        $template->set_title("Detail Transaksi Kas");
        $template->set_title_desc('Detail Transaksi Kas');

//        print_r2($detail);


        $view_data['primary_id'] = $primary_id;
        $view_data['title'] = $this->title;
        $view_data['detail'] = $detail;


        $template->load_view('kas/reg_trans/detail', $view_data);

        $template->tampil();
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
