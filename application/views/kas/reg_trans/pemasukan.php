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
    <?= form_open_multipart('#', ' id="form-1"   ') ?>
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
                        <label for="tanggal">tanggal *</label>
                        <input type="text" name="tanggal" id="tanggal" placeholder="tanggal" value="<?= $form['tanggal'] ?>" class="form-control singgle-date" >
                    </div>


                    <div class="form-group">
                        <label for="id_kastrans_kategori">jenis transaksi *</label>
                        <?= form_dropdown('id_kastrans_kategori', $form['opt_pemasukan'], $form['id_kastrans_kategori'], ' class="form-control" ') ?>
                    </div>

                    <div class="form-group">
                        <label for="id_outlet">outlet *</label>
                        <?= form_dropdown('id_outlet', $form['opt_outlet'], $form['id_outlet'], ' class="form-control select2-noajax" ') ?>
                    </div>

                    <div class="form-group">
                        <label for="kode_kastrans">kode </label>
                        <input type="text" name="kode_kastrans" id="kode_kastrans" placeholder="kode" value="<?= $form['kode_kastrans'] ?>" class="form-control" >
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nominal">nominal *</label>

                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1"><b>Rp.</b> </span>
                            <input type="text" name="nominal" id="nominal" placeholder="nominal" value="<?= $form['nominal'] ?>" class="form-control thousand" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto"> bukti nota fisik  </label>                        <span>*.Jpg, *.Png, *.pdf</span>

                        <input type="file" id="foto" name="foto" class="form-control" >
                        <span class="text-danger">*max 512 kB</span>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">keterangan </label>
                        <textarea id="keterangan" name="keterangan" class="form-control" ><?= $form['keterangan'] ?></textarea>
                    </div>

                </div>
                <div class="col-md-12">
                    <button id="submit-button" type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Simpan</button>
                    <a href="<?= base_url() . $auth->get_url_controller() ?>" class="btn btn-default" ><i class="fa fa-close"></i>  Batal</a>
                </div>
            </div>
        </div>
    </div>
    <?= form_close() ?>
    <?php require_once 'pemasukan_script.php'; ?>