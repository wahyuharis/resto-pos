<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    private $id_usaha = null;
    private $url_controller = '';
    private $primary_key = 'id_user';
    private $title = 'User';
    private $table_name = '_user';
    private $type_rekening = null;
    private $limit_user = null;

    public function __construct() {
        parent::__construct();

        $auth = new Auth_model();
        $auth->is_login();
        $this->id_usaha = $auth->get_user_data()->id_usaha;
        $this->url_controller = $auth->get_url_controller();

        $this->type_rekening = get_enum_values('m_rekening', 'type');

        $this->limit_user = get_column('_usaha', array('id_usaha' => $this->id_usaha), 'limit_user');
    }

    public function index() {
        $template = new Template();

        $template->set_title($this->title);
        $template->set_title_desc('Daftar ' . $this->title);

        $view_data['table_header'] = $this->table_header();

        $template->load_view('user/list', $view_data);

        $template->tampil();
    }

    private function table_header() {
        $table_header = array(
            'aksi',
            '_user.id_user',
            'nama outlet',
            'username',
            'email',
            'level',
            'verivikasi'
        );

        return $table_header;
    }

    private function orderby() {
        $array = array(
            'aksi',
            '_user.id_user',
            'm_outlet.nama_outlet',
            '_user.username',
            '_user.email',
            '_user.type',
            '_user.verifikasi'
        );

        return $array;
    }

    private function sql() {
        $sql = "SELECT 
            '' as aksi,
                _user.id_user,
                m_outlet.nama_outlet,
                _user.username,
                _user.email,
                _user.`type`,
                _user.verifikasi

                FROM _user

                LEFT JOIN m_outlet
                ON m_outlet.id_outlet=_user.id_outlet

                WHERE
                _user.id_usaha=" . $this->id_usaha . "
                AND
                _user.`status`=1";

        return $sql;
    }

    private function callback_column($key, $col, $row) {

        $auth = new Auth_model();
        $encrypt = new Encryption_model();

        if ($key == 'aksi') {
            $col = "";
            $col .= '<a class="btn btn-info btn-xs" '
                    . 'href="' . base_url() . $auth->get_url_controller() . 'edit/'
                    . $encrypt->encode($row[$this->primary_key]) . '" >'
                    . '<i class="fa fa-pencil" ></i>'
                    . '</a>';
            $col .= '&nbsp';
            $col .= '<a class="btn btn-danger btn-xs" onclick="delete_alert(' . "'" . $row[$this->primary_key] . "'" . ')" href="#" ><i class="fa fa-trash" ></i></a>';
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

    function total_row() {
        $this->db->where('id_usaha', $this->id_usaha);
        $this->db->where('status', 1);
        $db = $this->db->get($this->table_name);

        return $db->num_rows();
    }

    public function add($primary_id = null) {
        $this->edit($primary_id);

        if ($this->total_row() >= $this->limit_user) {
            redirect('tagihan/user');
        }
    }

    public function edit($primary_id = null) {
        $auth = new Auth_model();
        $enc = new Encryption_model();

        $primary_id = $enc->decode($primary_id);

        $template = new Template();

        $template->set_title($this->title);
        $template->set_title_desc('Tambah ' . $this->title);

        $view_data = array();
        $form = array();

        $buff = get_enum_values('_user', 'type');
        $opt_type = array();
        $opt_type[''] = 'Pilih Level...';
        foreach ($buff as $row0) {
            $opt_type[$row0] = $row0;
        }

        $opt_outlet = array();
        $buff = $this->db->where('status', 1)
                ->where('id_usaha', $this->id_usaha)
                ->get('m_outlet')
                ->result_array();
        $opt_outlet = dropdown_array($buff, 'id_outlet', 'nama_outlet', 'Pilih Outlet...');

        $form['username'] = '';
        $form['password'] = '';
        $form['email'] = '';
        $form['type'] = '';
        $form['id_outlet'] = '';
        $form['foto'] = '';

        $form['opt_outlet'] = $opt_outlet;
        $form['opt_type'] = $opt_type;

        if (!empty(trim($primary_id))) {
            $template->set_title_desc('Edit ' . $this->title);

            $where = array(
                'id_usaha' => $this->id_usaha,
                'id_user' => $primary_id,
            );
            $this->db->where($where);
            $db = $this->db->get($this->table_name);

            if ($db->num_rows() > 0) {
                $form['username'] = $db->row_object()->username;
                $form['password'] = $db->row_object()->password;
                $form['email'] = $db->row_object()->email;
                $form['type'] = $db->row_object()->type;
                $form['id_outlet'] = $db->row_object()->id_outlet;
                $form['foto'] = $db->row_object()->foto;
            }
        }


        $view_data['primary_id'] = $primary_id;
        $view_data['form'] = $form;
        $view_data['title'] = $this->title;


        $template->load_view('user/edit', $view_data);

        $template->tampil();
    }

    public function submit() {
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

        if (empty($primary_id)) {
            $this->form_validation->set_rules('username_frm', ucwords('username'), 'trim|required|is_unique[_user.username]');
            $this->form_validation->set_rules('email_frm', ucwords('email'), 'trim|valid_email|required|is_unique[_user.email]');
        }

        $this->form_validation->set_rules('password_frm', ucwords('password'), 'trim|required');

        $this->form_validation->set_rules('type', ucwords('type'), 'trim|required');
        if (!($post['type'] == 'admin')) {
            $this->form_validation->set_rules('id_outlet', ucwords('Outlet'), 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $message = validation_errors();
            $error = $this->form_validation->error_array();
            $succes = false;
        }

        if ($succes && (!empty(trim($primary_id)))) {

            $db = $this->db->where('username', trim($post['username_frm']))
                    ->where('id_user!=', $primary_id)
                    ->get($this->table_name);
            if ($db->num_rows() > 0) {
                $succes = FALSE;
                $message .= "<p>Username Sudah Terdaftar</p>";
                $error['email'] = '';
            }

            $db = $this->db->where('email', trim($post['email_frm']))
                    ->where('id_user!=', $primary_id)
                    ->get($this->table_name);
            if ($db->num_rows() > 0) {
                $succes = FALSE;
                $message .= "<p>Email Sudah Terdaftar</p>";
                $error['email'] = '';
            }
        }

        $foto_uploaded = '';
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|GIF|JPG|jpeg|PNG';
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


        if ($succes) {

            $insert['username'] = trim($post['username_frm']);
            $insert['password'] = trim($post['password_frm']);
            $insert['email'] = trim($post['email_frm']);
            $insert['type'] = $post['type'];
            $insert['id_outlet'] = $post['id_outlet'];

            $insert['foto'] = $foto_uploaded;

            $insert['id_usaha'] = $this->id_usaha;

            if (empty(trim($post['primary_id']))) {

                $this->db->insert($this->table_name, $insert);
                $message = "Data Berhasil disimpan";
            } else {

                $this->db->where($this->primary_key, $primary_id);
                $this->db->set($insert);
                $this->db->update($this->table_name);


                $message = "Data Berhasil disimpan";
            }
            $this->session->set_flashdata('message_succes', $message);
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
        $succes = false;
        $data = array();
        $error = array();

        $auth = new Auth_model();

        $primary_id = $this->input->post('delete_id');

        $this->db->set('status', 0);
        $this->db->where('id_usaha', $this->id_usaha);
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
