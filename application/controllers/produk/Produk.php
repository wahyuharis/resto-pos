<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $table_name = 'it_produk';
    private $primary_key = 'id_produk';
    private $title = "Produk";

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();

        $this->load->model('Stok_model');
        $this->load->model('produk/Produk_model');
    }

    public function index() {
        $template = new Template();

        $template->set_title('Produk');
        $template->set_title_desc("Daftar " . $this->title);

        $outlet_ar = $this->db->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->get('m_outlet')
                ->result_array();


        $opt_outlet = dropdown_array($outlet_ar, 'id_outlet', 'nama_outlet', 'Pilih Outlet...', FALSE);


        $view_data = array(
            'table_header' => $this->table_header(),
            'opt_outlet' => $opt_outlet
        );

        $template->set_box(FALSE);
        $template->load_view('produk/produk/list', $view_data);

        $template->tampil();
    }

    private function table_header() {
        $produk = new Produk_model();

        $table_header = $produk->table_header_list();

        return $table_header;
    }

    private function orderby() {

        $produk = new Produk_model();

        $array = $produk->orderby_list();

        return $array;
    }

    private function sql() {

        $produk = new Produk_model();

        $sql = $produk->sql_list();

        return $sql;
    }

    private function callback_column($key, $col, $row) {
        $auth = new Auth_model();
        $enc = new Encryption_model();

        if ($key == 'aksi') {
            $col = "";
            $col .= '<a class="btn btn-info btn-xs"  href="' .
                    base_url() . $auth->get_url_controller() . 'edit/' . $enc->encode($row[$this->primary_key])
                    . '" ><i class="fa fa-pencil" ></i></a>';
            $col .= '&nbsp';

            $col .= '<a class="btn btn-danger btn-xs" onclick="delete_alert(' . "'" . $row[$this->primary_key] . "'" . ')" href="#" ><i class="fa fa-trash" ></i></a>';
        }

        if ($key == 'foto') {
            if (!empty($col)) {
                $col = '<img width="50" height="50" src="' . base_url('uploads/' . $col) . '" >';
            }
        }

        if ($key == 'harga') {
            $col = number_format($col, 2);
        }

        if ($key == 'stock_akhir') {
            if (intval($row['lacak_stok']) == 1) {
                $col = $col;
            } else {
                $col = '<label class="label label-default" >No Stok</label>';
            }
        }

        return $col;
    }

    // <editor-fold defaultstate="collapsed" desc="default">
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

    public function delete() {
        $message = '';
        $succes = false;
        $data = array();
        $error = array();

        $auth = new Auth_model();

        $primary_id = $this->input->post('delete_id');

        $this->db->set('status', 0);
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

// </editor-fold>

    public function add() {
        $auth = new Auth_model();
        $this->edit();
    }

    public function edit($primary_id = null) {

        $auth = new Auth_model();
        $template = new Template();
        $enc = new Encryption_model();

        $primary_id = $enc->decode($primary_id);

        if (!empty(trim($primary_id))) {
            $id_usaha = get_column($this->table_name, array($this->primary_key => $primary_id), 'id_usaha');
            if ($id_usaha != $this->id_usaha) {
                $this->session->set_flashdata('message_error', 'Data Tidak Ada');
                redirect($this->url_controller);
            }
        }


        $view_data = array();

        $form = array();
        $form['id_kategori'] = '';
        $form['nama_produk'] = '';
        $form['foto'] = '';
        $form['deskripsi'] = '';
        $form['harga'] = '';
        $form['sku'] = '';
        $form['stok_awal'] = 0;
        $form['lacak_stok'] = '';

        $form['id_outlet_arr'] = array();


        $kategori_ar = $this->db->where('id_usaha', $this->id_usaha)
                ->where('status', 1)
                ->get('it_kategori')
                ->result_array();

        $form['id_kategori_opt'] = $kategori_ar;

        $where = array(
            'status' => 1,
            'id_usaha' => $this->id_usaha,
        );


        $select = "m_outlet.*,"
                . "_f_stock_akhir(" . $this->id_usaha . ", m_outlet.id_outlet, '" . intval($primary_id) . "') AS `sisa`,
                _f_harga(m_outlet.id_outlet, '" . intval($primary_id) . "') AS harga";

        $outlet_ar = $this->db->where($where)
                ->select($select)
                ->get('m_outlet')
                ->result_array();

        $form['id_outlet_opt'] = $outlet_ar;

        if (!empty(trim($primary_id))) {
            $where = array(
                $this->primary_key => $primary_id
            );

            $id_usaha_get = get_column($this->table_name, $where, 'id_usaha');

            if ($id_usaha_get != $this->id_usaha) {
                $this->session->set_flashdata('message_error', 'Data Tidak Ditemukan');
                redirect($auth->get_url_controller());
            }

            $where[$this->primary_key] = $primary_id;
            $where['id_usaha'] = $this->id_usaha;
            $row = get_row('it_produk', $where);

            $this->db->where(array('id_produk' => $primary_id));
            $db = $this->db->get('rel_produk_outlet');

            $id_outlet_arr = array();

            foreach ($db->result_array() as $row2) {
                array_push($id_outlet_arr, $row2['id_outlet']);
            }

            $form['id_outlet_arr'] = $id_outlet_arr;

            $form['id_kategori'] = $row['id_kategori'];
            $form['nama_produk'] = $row['nama_produk'];
            $form['foto'] = $row['foto'];
            $form['sku'] = $row['sku'];
            $form['harga'] = $row['harga'];
            $form['deskripsi'] = $row['deskripsi'];
            $form['lacak_stok'] = $row['lacak_stok'];

            $template->set_title($this->title);
            $template->set_title_desc("Edit " . $this->title);
        } else {


            $template->set_title($this->title);
            $template->set_title_desc("Tambah " . $this->title);
        }

        $view_data = array(
            'primary_id' => $primary_id,
            'form' => $form,
        );

        $template->load_view('produk/produk/edit', $view_data);
        $template->tampil();
    }

    function detail($primary_id = null) {
        
    }

    public function submit() {
        $message = '';
        $succes = true;
        $data = array();
        $error = array();
        $error_tab = array();

        $post = $this->input->post();

        $it_variant_produk = array();
        if (isset($post['ko_output'])) {
            $ko_output = json_decode($post['ko_output'], true);

            if (isset($ko_output['it_variant_produk'])) {
                $it_variant_produk = $ko_output['it_variant_produk'];
            }
        }

        $this->load->library('form_validation');
        $this->form_validation->set_data($ko_output);


        $this->form_validation->set_rules('id_kategori', ucwords('kategori'), 'trim|required');
        $this->form_validation->set_rules('nama_produk', ucwords('nama_produk'), 'trim|required');



        if (!empty(trim($post['primary_id']))) {
            $db = $this->db->where('id_produk!=', $post['primary_id'])
                    ->where('id_usaha', $this->id_usaha)
                    ->where('sku', $ko_output['sku'])
                    ->get($this->table_name);

//            print_r2($db->num_rows() );

            if ($db->num_rows() > 0) {
                $succes = false;
                $error['sku'] = '<li>SKU harus diisi dengan kode unik</li>';
                $message .= '<li>SKU harus diisi dengan kode unik</li>';
            }
        } else {
//            $this->form_validation->set_rules('sku', ucwords('sku'), 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }

        if (count($ko_output['id_outlet_ar']) < 1) {
            $message .= "<p>Pilih Satu atau Lebih Outlet</p>";
            $succes = FALSE;
            $error['id_outlet_ar'] = "<p>Pilih Satu atau Lebih Outlet</p>";
        }


        foreach ($ko_output['harga_list'] as $harga_row) {
            if (floatval2($harga_row['harga']) < 100) {
                $succes = FALSE;
                $message .= "<p>Harga " . $harga_row['nama_outlet'] . " Kosong</p>";
                $error_tab['harga_tab'] = "<p>Harga Ada Yang Kosong</p>";
            }
        }


        if (!empty($post['primary_id'])) {
            $where = array(
                $this->primary_key => $post['primary_id']
            );
            $id_usaha_get = get_column($this->table_name, $where, 'id_usaha');
            if ($id_usaha_get != $this->id_usaha) {
                $succes = false;
                $message .= '<li>ID usaha tidak sama dengan session saat ini</li>';
            }
        }

        $insert = array();


        $foto_uploaded = '';
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|GIF|JPG|PNG';
        $this->load->library('upload', $config);
        if (!empty(trim($_FILES['foto']['name']))) {
            if ($this->upload->do_upload('foto')) {
                $foto_uploaded = $this->upload->data()['file_name'];

                $config2['image_library'] = 'gd2';
                $config2['source_image'] = './uploads/' . $foto_uploaded;
                $config2['create_thumb'] = FALSE;
                $config2['maintain_ratio'] = false;
                $config2['width'] = 150;
                $config2['height'] = 150;
                $this->load->library('image_lib', $config2);
                $this->image_lib->resize();
            } else {
                $succes = false;
                $message .= $this->upload->display_errors();
            }
        }


        $insert_id = null;

        $stock = new Stok_model();

        if ($succes) {

            if (!empty(trim($foto_uploaded))) {
                $foto_db = get_column($this->table_name, array('id_produk' => $post['primary_id']), 'foto');
                if (!empty(trim($foto_db))) {
                    error_reporting(0);
                    unlink('./uploads/' . $foto_db);
                    error_reporting(E_ALL);
                }

                $insert['foto'] = $foto_uploaded;
            }



            $insert['sku'] = $ko_output['sku'];
            $insert['nama_produk'] = $ko_output['nama_produk'];
            $insert['id_kategori'] = $ko_output['id_kategori'];
            $insert['harga'] = floatval2($ko_output['harga']);
            $insert['deskripsi'] = $ko_output['deskripsi'];
            $insert['lacak_stok'] = intval($ko_output['lacak_stok']);
            $insert['id_usaha'] = $this->id_usaha;

            if (empty(trim($post['primary_id']))) {
                $this->db->insert($this->table_name, $insert);
                $insert_id = $this->db->insert_id();

                $this->db->delete('rel_produk_outlet', array('id_produk' => $insert_id));
                foreach ($ko_output['id_outlet_ar'] as $row0) {
                    $insert_outlet['id_produk'] = $insert_id;
                    $insert_outlet['id_outlet'] = $row0;
                    $this->db->insert('rel_produk_outlet', $insert_outlet);
                }

                if ($insert['lacak_stok']) {
                    foreach ($ko_output['stock_list'] as $row) {
                        $stock->stok_adj($row['id_outlet'], $insert_id, $row['qty_adj']);
                    }
                }

                $this->db->delete('it_produk_harga', array('id_produk' => $insert_id));
                foreach ($ko_output['harga_list'] as $row2) {
                    $insert_harga['id_produk'] = $insert_id;
                    $insert_harga['id_outlet'] = $row2['id_outlet'];
                    $insert_harga['harga'] = floatval2($row2['harga']);
                    $this->db->insert('it_produk_harga', $insert_harga);
                }
            } else {
                $this->db->where($this->primary_key, $post['primary_id']);
                $this->db->set($insert);
                $this->db->update($this->table_name);

                $insert_id = $post['primary_id'];

                $this->db->delete('rel_produk_outlet', array('id_produk' => $insert_id));
                foreach ($ko_output['id_outlet_ar'] as $row0) {
                    $insert_outlet['id_produk'] = $insert_id;
                    $insert_outlet['id_outlet'] = $row0;
                    $this->db->insert('rel_produk_outlet', $insert_outlet);
                }

                if ($insert['lacak_stok']) {
                    foreach ($ko_output['stock_list'] as $row) {
                        $stock->stok_adj($row['id_outlet'], $insert_id, $row['qty_adj']);
                    }
                }

                $this->db->delete('it_produk_harga', array('id_produk' => $insert_id));
                foreach ($ko_output['harga_list'] as $row2) {
                    $insert_harga['id_produk'] = $insert_id;
                    $insert_harga['id_outlet'] = $row2['id_outlet'];
                    $insert_harga['harga'] = floatval2($row2['harga']);
                    $this->db->insert('it_produk_harga', $insert_harga);
                }
            }

            if (empty(trim($insert['sku']))) {
                $sku = 10000 + $insert_id;

                $this->db->update($this->table_name, array('sku' => $sku), array('id_produk' => $insert_id));
            }
        }


        $result = array(
            'error' => $error,
            'error_tab' => $error_tab,
            'message' => $message,
            'succes' => $succes,
            'data' => $data,
        );

        header_json();
        echo json_encode($result);
    }

    function _is_uniqe2() {
        
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
