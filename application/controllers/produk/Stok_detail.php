<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_detail extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $table_name = 'stok';
    private $primary_key = 'id_stok';
    private $title = "Stok";
    private $id_produk = null;
    private $date_range = null;
    private $id_outlet = null;

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $enc = new Encryption_model();

        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();

        $this->load->model('Stok_model');
        $this->load->model('produk/Produk_model');




        $this->id_produk = $this->input->get('id_produk_encoded');
        $this->id_outlet = $this->input->get('id_outlet');
        $this->date_range = $this->input->get('range_tanggal');
    }

    public function index() {
        $enc = new Encryption_model();
        $auth = new Auth_model();

        $template = new Template();
        
        $id_produk= base64_decode($this->id_produk);
        
        $where=array(
            'id_produk'=>$id_produk,
            'id_usaha'=> $this->id_usaha,
            'status'=>1
        );
        
        $produk= get_row('it_produk', $where);
        
        $produk_model=new Produk_model();
        
        $produk['harga']=$produk_model->harga($produk['id_produk']);

        $template->set_title($this->title);
        $template->set_title_desc("Detail Stok Produk");

        $view_data = array(
            'produk'=> $produk,
            'table_header' => $this->table_header(),
            'id_produk' => $this->id_produk,
            'id_outlet' => $this->id_outlet,
            'date_range' => $this->date_range
        );

        $template->setUrl_controller_active('produk/stok/');



        $template->set_box(FALSE);
        $template->load_view('produk/stok_detail/list', $view_data);


        $template->tampil();
    }

    private function table_header() {

        $table_header = array(
            //  'aksi',
            'stok.id_stok',
            'tanggal',
            'qty_awal',
            'qty_in',
            'qty_out',
            'qty_akhir',
            'type trans stok'
        );

        return $table_header;
    }

    private function orderby() {

        $array = array(
            // 'aksi',
            'stok.id_stok',
            'stok.tanggal',
            'stok.qty_awal',
            'stok.qty_in',
            'stok.qty_out',
            'stok.qty_akhir',
            'stok.type'
        );

        return $array;
    }

    private function sql() {

        $id_produk = $this->input->get('id_produk');
        $id_produk = base64_decode($id_produk);

        $id_outlet = $this->input->get('id_outlet');
        $id_outlet = base64_decode($id_outlet);

        $date_range = $this->input->get('date_range');
        $date_range = daterange_parse($date_range);


        $sql = "
            SELECT  
               # '' AS aksi,
                stok.id_stok,
                DATE_FORMAT(stok.tanggal,'%d/%m/%Y %H:%i:%s') AS tanggal,
                stok.qty_awal,
                stok.qty_in,
                stok.qty_out,
                stok.qty_akhir,
                stok.`type`,
                stok.id_outlet,
                stok.id_produk

            FROM stok
            LEFT JOIN it_produk
            on stok.id_produk=it_produk.id_produk

            WHERE 
            stok.id_produk=" . $id_produk . "
            and
            stok.id_outlet=" . $id_outlet . "
            AND
            stok.tanggal BETWEEN '" . $date_range->start . "' AND '" . $date_range->end . "'
            AND
            stok.id_usaha=" . $this->id_usaha . "
 ";


        return $sql;
    }

    private function callback_column($key, $col, $row) {
        $auth = new Auth_model();
        $enc = new Encryption_model();



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
