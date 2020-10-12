<?php

class Encryption_model extends CI_Model {

    private $driver = 'openssl';
    private $cipher = 'aes-256';
    private $mode = 'ctr';

    public function __construct() {
        parent::__construct();
        $this->load->library('encryption');
        $this->encryption->initialize(
                array(
                    'cipher' => $this->cipher,
                    'driver' => $this->driver,
                    'mode' => $this->mode,
                )
        );
    }

    function encrypt($text) {
        $ciphertext = $this->encryption->encrypt($text);
        return $ciphertext;
    }

    function decrypt($text) {
        $decrypted = $this->encryption->decrypt($text);
        return $decrypted;
    }

    function encode($id){
        $encoded= urlencode(base64_encode($id));
        return $encoded;
    }
    
    function decode($id){
        $encoded= base64_decode(urldecode($id));
        return $encoded;
    }
    
}
