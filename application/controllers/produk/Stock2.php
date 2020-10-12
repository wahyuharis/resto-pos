<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $table_name = 'stok';
    private $primary_key = 'id_stok';
    private $title = "Stock";

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();
    }

    public function index() {
        $template = new Template();

        $template->set_title('Produk');

        $view_data = array(
            'table_header' => $this->table_header(),
        );

        $template->load_view('produk/produk/list', $view_data);

        $template->tampil();
    }

    private function table_header() {
        $table_header = array(
            'aksi',
            'id_produk',
            'kategori',
            'nama produk',
            'foto',
        );

        return $table_header;
    }

    private function orderby() {
        $array = array(
            'aksi',
            'id_produk',
            'id_kategori',
            'nama_produk',
            'foto',
        );

        return $array;
    }

    private function sql() {

        $sql = "
            SELECT 
                m_outlet.nama_outlet,
                it_produk.sku,
                it_produk.nama_produk,
                _f_stock_akhir(it_produk.id_usaha, m_outlet.id_outlet, it_produk.id_produk) AS stok_akhir

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
                m_outlet.id_outlet=1
 ";

        return $sql;
    }

    private function callback_column($key, $col, $row) {
        $auth = new Auth_model();

        if ($key == 'aksi') {
            $col = "";
            $col .= '<a class="btn btn-info btn-xs"  href="' .
                    base_url() . $auth->get_url_controller() . 'edit/' . $row[$this->primary_key]
                    . '" ><i class="fa fa-pencil" ></i></a>';
            $col .= '&nbsp';

            $col .= '<a class="btn btn-danger btn-xs" onclick="delete_alert(' . "'" . $row[$this->primary_key] . "'" . ')" href="#" ><i class="fa fa-trash" ></i></a>';
        }

        if ($key == 'foto') {
            if (!empty($col))
                $col = '<img width="50" height="50" src="' . base_url('uploads/' . $col) . '" >';
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
        $view_data = array();

        $form = array();
        $form['id_kategori'] = '';
        $form['nama_produk'] = '';
        $form['foto'] = '';
        $form['deskripsi'] = '';
        $form['harga'] = '';

        $kategori_ar = $this->db->where('id_usaha', $this->id_usaha)
                ->where('status', 1)
                ->get('it_kategori')
                ->result_array();

        $form['id_kategori_opt'] = $kategori_ar;



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


            $form['id_kategori'] = $row['id_kategori'];
            $form['nama_produk'] = $row['nama_produk'];
            $form['foto'] = $row['foto'];
            $form['deskripsi'] = $row['deskripsi'];


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

    public function submit() {
        $message = '';
        $succes = false;
        $data = array();
        $error = array();

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
        $this->form_validation->set_rules('harga', ucwords('harga'), 'trim|required');

        $succes = true;
        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
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

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|GIF|JPG|PNG';
        $this->load->library('upload', $config);

        if (!empty(trim($_FILES['foto']['name']))) {
            if ($this->upload->do_upload('foto')) {
                $insert['foto'] = $this->upload->data()['file_name'];
            } else {
                $succes = false;
                $message .= $this->upload->display_errors();
            }
        }


        $insert_id = null;


        if ($succes) {

//            print_r2($ko_output);

            $insert['foto'] = $insert['foto'];
            $insert['nama_produk'] = $ko_output['nama_produk'];
            $insert['id_kategori'] = $ko_output['id_kategori'];
            $insert['deskripsi'] = $ko_output['deskripsi'];
            $insert['id_usaha'] = $this->id_usaha;

            if (empty(trim($post['primary_id']))) {
                $this->db->insert($this->table_name, $insert);
                $insert_id = $this->db->insert_id();

                $insert_variant = array();
                $insert_variant['id_usaha'] = $this->id_usaha;
                $insert_variant['sku'] = $ko_output['sku'];
                $insert_variant['id_produk'] = $insert_id;
                $insert_variant['nama_variant'] = $insert['nama_produk'];
                $insert_variant['harga'] = floatval2($ko_output['harga']);

                $this->db->insert('it_produk_variant', $insert_variant);
                $insert_id2 = $this->db->insert_id();

                if (empty(trim($ko_output['sku']))) {
                    $sku_variant = $insert_id2 + 1000;
                    $this->db->update('it_produk_variant', array('sku' => $sku_variant), //set
                            array('id_produk_variant' => $insert_id2) //where
                    );
                }

                foreach ($it_variant_produk as $row_var) {
                    $insert_variant = array();

                    $insert_variant['id_usaha'] = $this->id_usaha;
                    $insert_variant['sku'] = $row_var['sku'];
                    $insert_variant['id_produk'] = $insert_id;
                    $insert_variant['nama_variant'] = $row_var['nama_variant'];
                    $insert_variant['harga'] = floatval2($row_var['harga']);

                    $this->db->insert('it_produk_variant', $insert_variant);
                    $insert_id2 = $this->db->insert_id();

                    if (empty(trim($ko_output['sku']))) {
                        $sku_variant = $insert_id2 + 1000;
                        $this->db->update('it_produk_variant', array('sku' => $sku_variant), //set
                                array('id_produk_variant' => $insert_id2) //where
                        );
                    }
                }
            } else {

                $this->db->where($this->primary_key, $post['primary_id']);
                $this->db->set($insert);
                $this->db->update($this->table_name);

                $insert_id = $post['primary_id'];
            }
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

}
