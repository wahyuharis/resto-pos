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
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab"><b>Detail Produk</b></a></li>
                    <li><a id="harga_tab" href="#tab_3" data-toggle="tab"><b>Harga</b></a></li>
                    <li><a id="stok_tab" href="#tab_2" data-toggle="tab"><b>Stok</b></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                if (!isset($primary_id)) {
                                    $primary_id = '';
                                }
                                ?>
                                <?= form_hidden('primary_id', $primary_id) ?>

                                <div class="form-group">
                                    <label for="sku">sku </label>
                                    <input type="text" name="sku" id="sku" placeholder="jika tidak diisi akan digenerate otomatis" data-bind="textInput: sku" class="form-control" >
                                </div>

                                <div class="form-group">
                                    <label for="nama_produk">nama produk *</label>
                                    <input type="text" name="nama_produk" id="nama_produk" placeholder="nama produk" 
                                           data-bind=" textInput:nama_produk"
                                           class="form-control" >
                                </div>


                                <div class="form-group">
                                    <label for="id_kategori">kategori *</label>
                                    <select id="id_kategori" name="id_kategori" 
                                            data-bind="value:id_kategori ,
                            options: id_kategori_opt,
                           optionsText:'nama_kategori',
                           optionsValue:'id_kategori',
                           valueAllowUnset:true" class="form-control select2-noajax" ></select>
                                </div>

                                <div class="form-group">
                                    <label for="id_outlet_ar">Jual Di Outlet *</label>
                                    <select id="id_outlet_ar" name="id_outlet_ar" multiple="true"
                                            data-bind="
                                        selectedOptions:id_outlet_ar ,
                                        options: id_outlet_ar_opt,
                                        optionsText:'nama_outlet',
                                        optionsValue:'id_outlet',
                                        valueAllowUnset:true" class="form-control select2-noajax" ></select>
                                </div>

                                <div class="form-group">
                                    <label for="lacak_stok">Hitung Stok *</label>
                                    <input type="checkbox" name="lacak_stok" id="lacak_stok" placeholder="lacak_stok" 
                                           data-bind="checked: lacak_stok"
                                           >
                                </div>


                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="deskripsi">deskripsi </label>

                                    <textarea id="deskripsi" name="deskripsi" 
                                              data-bind="textInput:deskripsi"
                                              class="form-control"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="foto">foto</label>
                                    <br>
                                    
                                    <?php $url_foto='default_img/blank-image.png' ?>
                                    <?php if (!empty(trim($form['foto']))) $url_foto='uploads/'.$form['foto']  ?>
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
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Outlet</th>
                                    <th  style="width:30%">Harga</th>

                                </tr>
                            </thead>
                            <tbody data-bind="foreach: harga_list">
                                <tr>
                                    <td data-bind="text: $index()+1" ></td>
                                    <td><span data-bind="text: nama_outlet"></span></td>
                                    <td><input  data-bind="value: harga" type="text"  class="form-control thousand" ></span></td>

                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="tab_2">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Outlet</th>
<!--                                    <th data-bind="visible: $root.primary_id() > 0" style="width:30%">Sisa</th>
                                    <th data-bind="visible: $root.primary_id() < 1" style="width:30%">Awal</th>-->
                                    <th  style="width:30%">Sisa</th>
                                    <th  style="width:30%">Awal/Ubah</th>

                                </tr>
                            </thead>
                            <tbody data-bind="foreach: stock_list">
                                <tr>
                                    <td data-bind="text: $index()+1" ></td>
                                    <td><span data-bind="text: nama_outlet"></span></td>
<!--                                    <td data-bind="visible: $root.primary_id() > 0" ><span data-bind="text: qty_awal"></span></td>
                                    <td data-bind="visible: $root.primary_id() < 1"><input  data-bind="value: qty_adj" type="text"  class="form-control" ></span></td>
                                    -->
                                    <td><span data-bind="text: qty_awal"></span></td>
                                    <td><input  data-bind="value: qty_adj" type="text"  class="form-control number" ></span></td>

                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>

            </div>
            <div class="col-md-12">
                <textarea name="ko_output" class="form-control hidden" data-bind="value: ko.toJSON($root)" ></textarea>
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
<?php require_once 'edit_script_knockout.php'; ?>