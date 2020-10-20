<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>
<div class="row" id="cetak_element">

    <div class="col-md-12" >
        <div class="col-md-6">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><?= $detail->tanggal ?></td>
                    </tr>
                    <tr>
                        <td>Outlet</td>
                        <td>:</td>
                        <td><?= $detail->nama_outlet ?></td>
                    </tr>

                    <tr>
                        <td>Keperluan</td>
                        <td>:</td>
                        <td><?= $detail->nama_kastrans_kategori ?></td>
                    </tr>

                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td><?= $detail->kode_kastrans ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp <?= number_format($detail->nominal, 2) ?></td>
                    </tr>
                    <tr>
                        <td>Nota Fisik</td>
                        <td>:</td>
                        <td>

                            <?php
                            if (!empty(trim($detail->foto_nota))) {
                                ?>
                                <a href="<?= base_url('uploads/' . $detail->foto_nota) ?>"></a>
                                <?php
                            }
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td><?= $detail->keterangan ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="col-md-6">
                    <a href="<?= base_url() . $auth->get_url_controller() ?>" class="btn btn-default" ><i class="fa fa-close"></i>  Batal</a>

        </div>
    </div>
</div>
