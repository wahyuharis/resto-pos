<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>

        <div class="row">
            <div class="col-md-6">
                <a class="btn btn-primary" href="<?= base_url().$auth->get_url_controller().'add' ?>"><i class="fa fa-plus"></i> Tambah</a>
            </div>

            <div class="col-md-6">
                <div class="btn-group pull-right">
                    <!--<a class="btn btn-danger " target="_blank" href="<?= base_url() . $auth->get_url_controller() . "pdf/" ?>"><i class="fa fa-file"></i> Pdf</a>-->
                    <a class="btn btn-info " target="_blank" href="<?= base_url() . $auth->get_url_controller() . "xls/" ?>"><i class="fa fa-table"></i> Excel</a>
                    <a id="refresh_table" class="btn btn-default"  href="#"><i class="fa fa-refresh"></i> Refresh</a>
                </div>
            </div>

        </div>
        <div class="col-sm-12" style="margin-bottom: 20px"></div>
        <div class="row">
            <div class="col-md-12">
                <table id="table-1" class="table table-striped table-hover">
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


<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="form-modal">

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
        var table = $('#table-1').DataTable({
            'ajax': {
                'url': '<?= base_url() . $auth->get_url_controller() . 'datatables' ?>',
                "complete": function (data, type) {
//                    json = data.responseJSON;
//                    console.log(json);
//                    $('#total_row').html(json.recordsTotal);
                },
            },
//            "scrollX": true,
            "pagingType": "full",
            "order": [[1, "desc"]],
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
                console.log('init');
                $('#table-1').wrap("<div class='table-responsive'></div>");
            }
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
                    
                    
                    if (data.succes) {
                        toastr.success(data.message, 'Info ');

                    } else {
                        toastr.error(data.message, 'Maaf ');

                    }
                    
                }, error: function (err) {
                    toastr.error("Terjadi Kesalahan");
                }
            });
        });

    });
</script>