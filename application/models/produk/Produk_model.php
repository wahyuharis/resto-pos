<?php

class Produk_model extends CI_Model {
    
    private $id_usaha = null;
    private $table_name = 'it_produk';
    private $primary_key = 'id_produk';
    private $title = "Produk";
    private $id_outlet=null;


    public function __construct() {
        parent::__construct();
        
        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->id_outlet= $this->input->get('outlet');
    }
    
    function table_header_list(){
        $table_header = array(
            'aksi',
            'it_produk.id_produk',
            'kategori',
            'sku',
            'nama produk',
            'foto',
            'stok',
            'harga'
        );

        return $table_header;
    }
    
     function orderby_list() {
        $array = array(
            'aksi',
            'it_produk.id_produk',
            'it_kategori.nama_kategori',
            'sku',
            'nama_produk',
            'sku',
            'it_produk.foto',
            "_f_stock_akhir(".$this->id_usaha.", ".$this->id_outlet.", it_produk.id_produk)",
            "_f_harga(".$this->id_outlet.", it_produk.id_produk)"
        );

        return $array;
    }
            
    function sql_list(){

        $sql = "
           SELECT 
            '' AS aksi,
            it_produk.id_produk,
            it_kategori.nama_kategori,
            sku,
            nama_produk,
            it_produk.foto,
				_f_stock_akhir(it_produk.id_usaha, m_outlet.id_outlet, it_produk.id_produk) AS stock_akhir,
            _f_harga(m_outlet.id_outlet, it_produk.id_produk) AS harga,
            it_produk.lacak_stok,
            m_outlet.id_outlet
            
            FROM it_produk

            LEFT JOIN it_kategori ON it_kategori.id_kategori=it_produk.id_kategori
	    JOIN m_outlet

            WHERE 
            it_produk.`status`=1
            AND
            it_produk.id_usaha=" . $this->id_usaha . "
            and
            m_outlet.id_outlet=" . $this->id_outlet . " ";
        
        $kategori= $this->input->get('kategori');
        if(!empty(trim($kategori))){
            $sql.=" and it_kategori.nama_kategori like '%".$this->db->escape_str(trim($kategori))."%' ";
        }
        
        $nama_produk= $this->input->get('nama_produk');
        if(!empty(trim($nama_produk))){
            $sql.=" and  nama_produk like '%".$this->db->escape_str(trim($nama_produk))."%' ";
        }
        
        $sku= $this->input->get('sku');
        if(!empty(trim($sku))){
            $sql.=" and  sku like '%".$this->db->escape_str(trim($sku))."%' ";
        }

        return $sql;
        
    }
    
    function harga($id_produk) {
        $sql=" SELECT `_f_harga`('1', '1') as harga ";
        $db= $this->db->query($sql);
        $harga=0;
        
        if($db->num_rows()>0){
            $harga=$db->row_object()->harga;
        }
        
        return $harga;
    }
}
