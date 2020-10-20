<?php

function get_enum_values( $table, $field )
{
    $ci=&get_instance();
    $type = $ci->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = explode("','", $matches[1]);
    
    $return=array();
    foreach($enum as $row){
        $return[$row]=$row;
    }
    
    return $return;
}

function waktu_ymd_to_dmy($waktu_str) {

    $var = date_create($waktu_str);
    return date_format($var, "d/m/Y");
}

function waktu_dmy_to_ymd($waktu_str) {
    error_reporting(0);
    if (!is_date_dmy($waktu_str)) {
        $waktu_str = '00/00/0000';
    }
    $var = date_create_from_format("d/m/Y", $waktu_str);

    error_reporting(E_ALL);
    return date_format($var, "Y-m-d");
}

function daterange_parse($tanggal) {
    $str_date = [];
    $str_date[0] = '';
    $str_date[1] = '';
    $str_date = explode('-', $tanggal);

//    error_reporting(0);

    if (!isset($str_date[0])) {
        $str_date[0] = "";
    }

    if (!isset($str_date[1])) {
        $str_date[1] = "";
    }

    $start = waktu_dmy_to_ymd(trim(($str_date[0])));
    $end = waktu_dmy_to_ymd(trim(($str_date[1])));

//    error_reporting(E_ALL);


    $tanggal_arr = (object) array(
                'start' => $start . " 00:00:00",
                'end' => $end . " 23:59:59",
    );

    return $tanggal_arr;
}

function print_r2($var) {
    echo "<pre>";
    print_r($var);
    die();
}

function intval2($str) {
    $int = str_replace(',', '', $str);
    $int = intval($int);
    return $int;
}

function is_float2($float) {
    $float = floatval($float) . "";
    $pos = strpos($float, '.');
    if ($pos > 0) {
        return true;
    } else {
        return false;
    }
}

function floatval2($str) {
    $int = str_replace(',', '', $str);
    $int = floatval($int);
    return $int;
}

function dropdown_array($result_array, $index, $label, $placeholder = "", $empty_opt = true) {
    $output = array();

    if ($empty_opt) {
        $output[''] = $placeholder;
    }

    foreach ($result_array as $row) {
        $output[$row[$index]] = $row[$label];
    }
    return $output;
}

function base_url_from_array($arr_url) {
    $ci = &get_instance();
    $ci->load->helper('url');
    $output = array();
    foreach ($arr_url as $row) {
        array_push($output, base_url() . $row);
    }
    return $output;
}


function mask_uriformat($var) {
    $output = urlencode(base64_encode($var));
    return $output;
}

function unmask_uriformat($var) {
    $output = base64_decode(urldecode($var));
    return $output;
}

function header_json() {
    header('Content-Type: application/json');
}

function header_text() {
    header("Content-Type: text/plain");
}

function header_cross_domain(){
    header("Access-Control-Allow-Origin: *");
}

function multidim_search($column, $value, $array) {
    foreach ($array as $key => $val) {
        if ($val[$column] == $value) {
            return $key;
        }
    }
    return null;
}

function set_datatype($str) {
    $arr0 = explode(',', $str);
    $arr1 = array();
    foreach ($arr0 as $key => $ls0) {
        $ls0 = trim($ls0);
        $ls0 = trim($ls0, '"');
        $ls0 = trim($ls0, "'");
        $ls0 = trim($ls0);

        $arr1[$ls0] = $ls0;
    }
    return $arr1;
}

function is_date_dmy($tgl, $spliter = "/") {
    $length = strlen($tgl);
    $bool = false;
    $split = str_split($tgl, 1);

    if ($length = 10 && $split[2] == $spliter && $split[5] == $spliter) {
        if (intval($split[0] . "" . $split[1]) <= 31) {
            if (intval($split[3] . "" . $split[4]) <= 12) {
                $bool = true;
            }
        }
    }
    return $bool;
}

function get_table_column($table, $column, $where = array()) {
    $ci = & get_instance();

    $ci->db->where($where);
    $ci->db->limit(1);
    $exc = $ci->db->get($table);

    $output = 0;
    if ($exc->num_rows() > 0) {
        $output = $exc->row_array()[$column];
    }
    return $output;
}

function generate_code($table, $column = 'kode', $label = 'KODE') {
    $ci = & get_instance();

    $primary = "";
    $fields = $ci->db->field_data($table);
    foreach ($fields as $field_list) {
        if ($field_list->primary_key == 1) {
            $primary = $field_list->name;
        }
    }

    if (empty(trim($primary))) {
        exit("Primary Key ga ketemu");
    }

    $ci->db->like($column, $label . "/" . date('y/m/d'));
    $ci->db->order_by($primary, 'desc');
    $ci->db->limit(1);
    $exc = $ci->db->get($table);

    $kode = "";
    if ($exc->num_rows() > 0) {
        $result = $exc->row_array()[$column];

        $explode = explode('/', $result);
        $idx = $explode[4];
        $kode = $label . "/" . date('y/m/d') . "/" . ( intval($idx) + 1 );
    } else {
        $kode = $label . "/" . date('y/m/d') . "/1";
    }

    return $kode;
}

function get_column($table, $where, $column) {
    $ci = &get_instance();

    $ci->db->where($where);
    $db = $ci->db->get($table);

    $output = null;
    if ($db->num_rows() > 0) {
        $output = $db->row_array()[$column];
    }

    return $output;
}

function get_row($table, $where) {
    $ci = &get_instance();

    $ci->db->where($where);
    $db = $ci->db->get($table);

    $output = array();
    if ($db->num_rows() > 0) {
        $output = $db->row_array();
    }

    return $output;
}
