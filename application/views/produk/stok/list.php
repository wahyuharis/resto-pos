<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--<div class="col-md-12" style="height: 20px"></div>-->
                <form id="form-filter">
                    <div class="col-md-3" >
                        <div class="form-group">
                            <label for="">Outlet</label>
                            <?= form_dropdown('outlet', $opt_outlet, '', ' class="form-control" ') ?>
                        </div>
                    </div>
                    <div class="col-md-3" >
                        <div class="form-group">
                            <label for="">range tanggal</label>
                            <input type="text" name="range_tanggal" value="<?= $tanggal_val ?>" class="form-control range-date" placeholder="range tanggal">
                        </div>

                    </div>
                    <div class="col-md-3" >
                        <div class="form-group">
                            <label for="">sku</label>
                            <input type="text" name="sku" value="" class="form-control" placeholder="sku">
                        </div>

                    </div>
                    <div class="col-md-3" >
                        <div class="form-group">
                            <label for="">Nama produk</label>
                            <input type="text" name="nama_produk" value="" class="form-control" placeholder="nama produk">
                        </div>


                    </div>
                    <div class="col-md-3" >



                    </div>


                    <div class="col-md-12 text-right" >
                        <a href="<?= base_url() . $auth->get_url_controller() ?>" class="btn btn-default"><i class="fa fa-eraser"></i>
                            Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                            Cari
                        </button>
                    </div>
                </form>
            </div>
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
                    <a id="export_xls" class="btn btn-info " target="_blank" href="#"><i class="fa fa-table"></i> Excel</a>
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

<script>

    function delete_alert(delete_id) {
        $('input[name=delete_id]').val(delete_id);
        $('#modal-delete').modal('show');
    }

    function edit_modal(primary_id) {
        var jqxhr = $.get('<?= base_url() . $auth->get_url_controller() . "/edit_modal/" ?>' + primary_id, function () {
        }).done(function (data) {
            $('#modal-edit').modal('show');
            $('#form-modal').html(data);
        }).fail(function () {
            toastr.error('Terjadi Kesalahan Cek koneksi');
        });
    }



    $(document).ready(function () {

        var param = '';
        param = $('#form-filter').serialize();
        var url_export = '<?= base_url() . $auth->get_url_controller() . "xls/" ?>?' + param;

        $('#export_xls').attr('href', url_export);


        var table = $('#table-1').DataTable({
            'ajax': {
                'url': '<?= base_url() . $auth->get_url_controller() . 'datatables?' ?>' + param,
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
                    "targets": 1,
                    "orderable": false,
                    "width": "25",
                    "visible": false,
                    "searchable": false
                },
            ],
            "initComplete": function (settings, json) {
                console.log(settings);
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

            var param = '';
            param = $('#form-filter').serialize();
            var url_export = '<?= base_url() . $auth->get_url_controller() . "xls/" ?>?' + param;

            $('#export_xls').attr('href', url_export);

        });

        $('#refresh_table').click(function () {
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