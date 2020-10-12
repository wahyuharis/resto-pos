<?php

class Stok_model extends CI_Model {

    private $table_name = 'stok';
    private $primary_key = 'id_stok';
    private $id_usaha = null;

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
    }

    function stok_akhir($id_outlet, $id_item) {
        $sql = "SELECT `_f_stock_akhir`('" . $this->id_usaha . "', '" . $id_outlet . "', '" . $id_item . "') as stok_akhir";

        $db = $this->db->query($sql);
        $output = 0;
        if ($db->num_rows() > 0) {
            $output = $db->row_object()->stok_akhir;
            $output = floatval2($output);
        }

        return $output;
    }

    function stok_adj($id_outlet, $id_item, $qty) {
        
        $qty= floatval2($qty);

        $insert = array();
        
        $insert['id_usaha'] = $this->id_usaha;
        $insert['id_outlet'] = $id_outlet;
        $insert['id_produk'] = $id_item;
        $insert['qty_awal'] = $this->stok_akhir($id_outlet, $id_item);

        if ($insert['qty_awal'] > $qty) {
            $insert['qty_out'] = $insert['qty_awal'] - $qty;
        } else {
            $insert['qty_in'] = $qty - $insert['qty_awal'];
        }

        $insert['type'] = 'perbaikan';
        $insert['qty_akhir'] = $qty;

//        print_r2($qty);

        $this->db->insert('stok', $insert);
    }

}
