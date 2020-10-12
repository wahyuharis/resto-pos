<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stok extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $table_name = 'it_produk';
    private $primary_key = 'id_produk';
    private $title = "Stok";
    private $range_tanggal=null;

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();

        $this->load->model('Stok_model');
        $this->load->model('produk/Produk_model');
        
        $this->range_tanggal=$this->input->get('range_tanggal');
    }

    public function index() {
        $template = new Template();

        $template->set_title($this->title);

        $outlet_ar = $this->db->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->get('m_outlet')
                ->result_array();


        $opt_outlet = dropdown_array($outlet_ar, 'id_outlet', 'nama_outlet', 'Pilih Outlet...', FALSE);

        $tanggal_val=date('01/m/Y')." - ".date('t/m/Y');
        
        $view_data = array(
            'table_header' => $this->table_header(),
            'opt_outlet' => $opt_outlet,
            'tanggal_val'=>$tanggal_val,
        );
        
        $template->set_title_desc("Daftar Stok Produk");

        $template->set_box(FALSE);
        $template->load_view('produk/stok/list', $view_data);

        $template->tampil();
    }

    private function table_header() {

        $table_header = array(
            'detail',
            'it_produk.id_produk',
            'nama outlet',
            'sku',
            'nama produk',
            'Stok'
        );

        return $table_header;
    }

    private function orderby() {

        $array = array(
            'aksi',
            'it_produk.id_produk',
            'm_outlet.nama_outlet',
            'it_produk.sku',
            'it_produk.nama_produk',
            " ".$this->sql_daterange()." "
        );

        return $array;
    }

    function sql_daterange() {
        $sql_date_range = '_f_stock_akhir(it_produk.id_usaha, m_outlet.id_outlet, it_produk.id_produk)';
        if (!empty(trim($this->input->get('range_tanggal')))) {
            $tanggal_arr = daterange_parse($this->input->get('range_tanggal'));

            $sql_date_range = "_f_stock_akhir_date(it_produk.id_usaha, m_outlet.id_outlet, it_produk.id_produk,'".$tanggal_arr->start."','".$tanggal_arr->end."' )";
        }

        return $sql_date_range;
    }

    private function sql() {
        $id_outlet = $this->input->get('outlet');

        $sql = "
            SELECT 
            '' as aksi,
                it_produk.id_produk,
                m_outlet.nama_outlet,
                it_produk.sku,
                it_produk.nama_produk,
                ".$this->sql_daterange()." AS stok_akhir,
                m_outlet.id_outlet
            FROM
                it_produk
                JOIN m_outlet

            WHERE
                it_produk.`status`=1
            AND
                m_outlet.`status`=1
            AND
                it_produk.id_usaha=" . $this->id_usaha . " 
            AND
                m_outlet.id_usaha=" . $this->id_usaha . " 
            AND
                m_outlet.id_outlet=" . $this->db->escape($id_outlet) . "
 ";

        $nama_produk = $this->input->get('nama_produk');
        if (!empty(trim($nama_produk))) {
            $sql .= " and  nama_produk like '%" . $this->db->escape_str($nama_produk) . "%' ";
        }

        $sku = $this->input->get('sku');
        if (!empty(trim($sku))) {
            $sql .= " and  sku like '%" . $this->db->escape_str($sku) . "%' ";
        }

        return $sql;
    }

    private function callback_column($key, $col, $row) {
        $auth = new Auth_model();
        $enc = new Encryption_model();
                
        $range_tanggal= urlencode($this->range_tanggal);

        if ($key == 'aksi') {
            $col = "";
            $col .= '<a class="btn btn-info btn-xs"  href="' .
                    base_url() . 'produk/stok_detail/' 
                    . '?range_tanggal='.$range_tanggal
                    . '&id_produk_encoded='.$enc->encode($row[$this->primary_key])
                    . '&id_outlet='.$enc->encode($row['id_outlet'])
                    . '" ><i class="fa fa-list" ></i></a>';
            $col .= '&nbsp';
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

// </editor-fold>


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
            $this->form_validation->set_rules('sku', ucwords('sku'), 'trim|required');
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
