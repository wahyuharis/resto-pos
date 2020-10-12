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
                        <label for="username">username *</label>
                        <input value="<?= $form['username'] ?>" type="text" name="username_frm" id="username_frm" placeholder="username"  class="form-control" >
                    </div>

                    <div class="form-group">
                        <label for="username">email *</label>
                        <input value="<?= $form['email'] ?>" type="text" name="email_frm" id="email_frm" placeholder="email"  class="form-control" >
                    </div>

                    <div class="form-group">
                        <label for="password">password *</label>
                        <input value="<?= $form['password'] ?>" type="text" name="password_frm" id="password_frm" placeholder="password"  class="form-control" style="text-security:disc; -webkit-text-security:disc;" >
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">level user *</label>
                        <?= form_dropdown('type', $form['opt_type'], $form['type'], ' class="form-control select2-noajax" ') ?>
                    </div>

                    <div class="form-group">
                        <label for="id_outlet">outlet *</label>
                        <?= form_dropdown('id_outlet', $form['opt_outlet'], $form['id_outlet'], ' class="form-control select2-noajax" ') ?>
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
                <div class="col-md-12">
                    <button id="submit-button" type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Simpan</button>
                    <a href="<?= base_url() . $auth->get_url_controller() ?>" class="btn btn-default" ><i class="fa fa-close"></i>  Batal</a>
                </div>
            </div>
        </div>
    </div>
<?= form_close() ?>


<?php require_once 'edit_script.php'; ?>