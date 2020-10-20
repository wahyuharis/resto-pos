<?php

class Reg_trans_model extends CI_Model {

    //put your code here

    public function __construct() {
        parent::__construct();
    }

    function get($id, $object = false) {

        $sql = "SELECT 
             kastrans_reg.*,
            
             m_outlet.nama_outlet,
            kastrans_kategori.nama_kastrans_kategori,
            DATE_FORMAT(kastrans_reg.tanggal,'%d/%m/%Y') AS tanggal
           

            FROM kastrans_reg

            LEFT JOIN kastrans_kategori 
            ON kastrans_kategori.id_kastrans_kategori=kastrans_reg.id_kastrans_kategori
            
            LEFT JOIN m_outlet
            ON m_outlet.id_outlet=kastrans_reg.id_outlet

            WHERE kastrans_reg.`status`=1
            AND kastrans_reg.id_usaha=1
            AND kastrans_reg.id_kastrans_reg=" . $this->db->escape($id) . " ";
        $db = $this->db->query($sql);

        $return = false;
        if ($db->num_rows() > 0) {
            $return = $db->row_array();

            if ($object) {
                $return = $db->row_object();
            }
        }

        return $return;
    }

}
