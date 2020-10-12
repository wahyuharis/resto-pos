<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>
<style>
    .space-row{
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>
<div class="box">
    <div class="box-body">
        <div class="col-md-6" >
            <div class="row col-md-12">
                <label class="col-sm-4 space-row">SKU</label>
                <label class="col-sm-4 space-row">:</label>
                <label class="col-sm-4 space-row"><?= $produk['sku'] ?></label>
            </div>

            <div class="row col-md-12">
                <label class="col-sm-4 space-row">Nama Produk</label>
                <label class="col-sm-4 space-row">:</label>
                <label class="col-sm-4 space-row"><?= $produk['nama_produk'] ?></label>
            </div>
        </div>
        <div class="col-md-6" >

            <div class="row col-md-12">
                <label class="col-sm-4 space-row">Harga</label>
                <label class="col-sm-4 space-row">:</label>
                <label class="col-sm-4 space-row"><?= number_format($produk['harga'] ,2) ?></label>
            </div>
        </div>
        <div class="col-md-12 text-right">
                            <a class="btn btn-default" href="<?= base_url() . 'produk/stok/' ?>"><i class="fa fa-arrow-circle-left "></i> Kembali</a>

        </div>
    </div>
</div>


<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
            </div>

            <div class="col-md-6">
                <div class="btn-group pull-right">
                    <!--<a class="btn btn-danger " target="_blank" href="<?= base_url() . $auth->get_url_controller() . "pdf/" ?>"><i class="fa fa-file"></i> Pdf</a>-->
                    <a id="export_xls" class="btn btn-info " target="_blank" href="<?= base_url() . $auth->get_url_controller() . "xls/" ?>"><i class="fa fa-table"></i> Excel</a>
                    <a id="refresh_table" class="btn btn-default"  href="#"><i class="fa fa-refresh"></i> Refresh</a>
                </div>
            </div>

        </div>
        <div class="col-sm-12" style="margin-bottom: 20px"></div>
        <div class="row">
            <div class="col-md-12">
                <table id="table-1" style="width:100%" class="table table-striped table-hover">
                    <thead class="">
                        <tr>
                            <?php foreach ($table_header as $head) { ?>
                                <th><?= ucwords($head) ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>


<div class="modal fade" id="modal-delete">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="delete_post" method="post" action="">
                <div class="modal-body">
                    <p>Yakin Menghapus ?</p>
                    <?= form_hidden('delete_id', '') ?>
                </div>
                <div class="modal-footer justify-content-between">
                    <button id="delete_submit" type="submit" class="btn btn-danger">Ya</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>


<script>

    function delete_alert(delete_id) {
        $('input[name=delete_id]').val(delete_id);
        $('#modal-delete').modal('show');
    }




    $(document).ready(function () {

        var param = '';

        var id_produk = '<?= urlencode($id_produk) ?>';
        var id_outlet = '<?= urlencode($id_outlet) ?>';
        var date_range = '<?= urlencode($date_range) ?>';

        var param = '?id_produk=' + id_produk + '&id_outlet=' + id_outlet + '&date_range=' + date_range;

        var url_export = '<?= base_url() . $auth->get_url_controller() . "xls/" ?>' + param;

        $('#export_xls').attr('href', url_export);

        var table = $('#table-1').DataTable({
            'ajax': {
                'url': '<?= base_url() . $auth->get_url_controller() . 'datatables' ?>' + param,
                "complete": function (data, type) {

                },
            },
            "scrollX": true,
            "pagingType": "full",
            "order": [[4, "asc"]],
            "serverSide": true,
            "processing": true,
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                    "width": "25",
                    "visible": false,
                    "searchable": false
                },
            ],
            "initComplete": function (settings, json) {
                console.log('init');
            }
        });

        $('#form-filter').submit(function (e) {
            e.preventDefault();
            filter = $(this).serialize();

            var url = '<?= base_url() . $auth->get_url_controller() . 'datatables' ?>?' + filter;
            table.ajax.url(url).load();
        });

        $('#form-filter').on('keyup change paste', 'input, select, textarea', function () {
            $(this).submit();
        });

        $('#refresh_table').click(function () {
            table.ajax.reload(null, false);
        });

        $('#modal-edit').on('hide.bs.modal', function (e) {
            //Loading = new SubmitLoading('#form-modal');
            //Loading.write_html();

            table.ajax.reload(null, false);
        });


        $('#delete_post').submit(function (e) {
            e.preventDefault();
            Pace.stop();
            Pace.start();

            $('#delete_submit').prop('disabled', true);
            $.ajax({
                url: '<?= base_url() . $auth->get_url_controller() . "/delete/" ?>', // Url to which the request is send
                type: "POST", // Type of request to be send, called as method
                data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false
                success: function (data) // A function to be called if request succeeds
                {
                    table.ajax.reload(null, false);
                    $('#delete_submit').prop('disabled', false);

                    $('#modal-delete').modal('hide');
                }, error: function (err) {
                    toastr.error("Terjadi Kesalahan");
                }
            });
        });

    });
</script>