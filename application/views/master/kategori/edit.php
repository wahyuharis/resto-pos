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

<?= form_open_multipart('#', ' id="form-1" ') ?>
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
                    <label for="nama_gudang">nama kategori *</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" placeholder="nama kategori" value="<?= $form['nama_kategori'] ?>" class="form-control" >
                </div>


                <div class="form-group">
                    <label for="foto">foto</label>
                    <br>
                    <?php $url_foto = 'default_img/blank-image.png' ?>
                    <?php if (!empty(trim($form['foto']))) $url_foto = 'uploads/' . $form['foto'] ?>
                    <label for="foto" class="img-upload" >
                        <img id="foto-preview" src="<?= base_url($url_foto) ?>" >
                        <input type="file" name="foto" id="foto" value="upload" >
                        <div class="overlay-img">
                            Pilih File
                        </div>
                    </label>
                    <script>
                        function readURL(input) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    $('#foto-preview').attr('src', e.target.result);
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                        $("input[name=foto]").change(function () {
                            readURL(this);
                        });
                    </script>
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
<?= form_close() ?>

<?php require_once 'edit_script.php'; ?>