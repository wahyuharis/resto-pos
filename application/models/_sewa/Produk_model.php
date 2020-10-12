<?php

class Sewa extends CI_Model {
    

    public function __construct() {
        parent::__construct();

    }
    
    public function sewa(){
        $sql="SELECT 
_usaha.id_usaha,
_usaha.nama_usaha,
_usaha.tanggal_daftar,
_usaha_limit.limit_outlet,
_usaha_limit.limit_user,
period_diff(date_format(now(), '%Y%m'), DATE_FORMAT( _usaha.tanggal_daftar , '%Y%m')) AS jml_periode,

DATE_ADD(_usaha.tanggal_daftar,INTERVAL 
period_diff(date_format(now(), '%Y%m'), DATE_FORMAT( _usaha.tanggal_daftar , '%Y%m'))
 MONTH) AS tgl_tagih,
 
(SELECT COUNT(_usaha_bayar.id_usaha_bayar) FROM _usaha_bayar
WHERE _usaha_bayar.id_usaha=_usaha.id_usaha)
AS kali_bayar,

(SELECT sum(_usaha_bayar.nominal) FROM _usaha_bayar
WHERE _usaha_bayar.id_usaha=_usaha.id_usaha)
AS jml_bayar

FROM _usaha
LEFT JOIN _usaha_limit
ON _usaha_limit.id_usaha=_usaha.id_usaha
WHERE _usaha.status=1";
    }
    
    
}
