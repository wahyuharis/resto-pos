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
                        <label for="nama_gudang">Nomor Meja *</label>
                        <input type="text" name="nomor_meja" id="nomor_meja" placeholder="Nomor Meja" value="<?= $form['nomor_meja'] ?>" class="form-control" >
                    </div>

                    <div class="form-group">
                        <label for="id_kota">Outlet *</label>                            
                        <?= form_dropdown('id_outlet', $form['opt_outlet'], $form['id_outlet'], ' class="form-control select2-noajax" ') ?>
                    </div>


                </div>
                <div class="col-md-6">


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