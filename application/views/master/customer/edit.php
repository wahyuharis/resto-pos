<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>
<style>
    label{
        text-transform: capitalize;
    }
</style>

<form role="form" id="form-1"  >
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    <?php
                    if (!isset($primary_id)) {
                        $primary_id = '';
                    }
                    ?>
                    <?= form_hidden('primary_id', $primary_id) ?>
                    <div class="form-group">
                        <label for="nama_gudang">nama customer *</label>
                        <input type="text" name="nama_customer" id="nama_customer" placeholder="nama customer" value="<?= $form['nama_customer'] ?>" class="form-control" >
                    </div>
                    
                    <div class="form-group">
                        <label for="telp">telp *</label>
                        <input type="text" name="telp" id="telp" placeholder="telp" value="<?= $form['telp'] ?>" class="form-control" >
                    </div>
                    
                    <div class="form-group">
                        <label for="telp">whatsapp *</label>
                        <input type="text" name="whatsapp" id="whatsapp" placeholder="whatsapp" value="<?= $form['whatsapp'] ?>" class="form-control" >
                    </div>
                    
                    <div class="form-group">
                        <label for="email">email *</label>
                        <input type="text" name="email" id="email" placeholder="email" value="<?= $form['email'] ?>" class="form-control" >
                    </div>
                </div>
                <div class="col-md-6">
                    
                     <div class="form-group">
                        <label for="tanggal_lahir">tanggal lahir *</label>
                        <input type="text" name="tanggal_lahir" id="tanggal_lahir" placeholder="tanggal lahir" value="<?= $form['tanggal_lahir'] ?>" class="form-control singgle-date" >
                    </div>
                    
                      <div class="form-group">
                        <label for="id_kota">Kota *</label>                            
                        <?= form_dropdown('id_kota', $form['id_kota_opt'], $form['id_kota'], ' class="form-control select2-noajax" ') ?>
                    </div>

                    <div class="form-group">
                        <label for="telp">alamat </label>
                        <textarea id="alamat" name="alamat"  class="form-control"  ><?= $form['alamat'] ?></textarea>
                    </div>

                </div>
                <div class="col-md-12">
                    <button id="submit-button" type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Simpan</button>
                    <a href="<?= base_url() . $auth->get_url_controller() ?>" class="btn btn-default" ><i class="fa fa-close"></i>  Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'edit_script.php'; ?>