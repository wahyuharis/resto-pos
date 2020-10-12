<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<link rel="stylesheet" href="<?= $lte_url ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>bower_components/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>bower_components/Ionicons/css/ionicons.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>plugins/pace/pace.min.css">


<link rel="stylesheet" href="<?= $lte_url ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>bower_components/datatables.net-bs/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?= $lte_url ?>bower_components/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="<?= $lte_url ?>dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="<?= $lte_url ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link href="<?= base_url() ?>node_modules/toastr/build/toastr.min.css" rel="stylesheet" type="text/css"/>
<link href="<?= base_url() ?>asset_custom/custom.css" rel="stylesheet" type="text/css"/>
<?php if (isset($css_files)) { ?>
    <?php foreach ($css_files as $css) { ?>
        <link href="<?= $css ?>" rel="stylesheet">
    <?php } ?>
<?php } ?>

<script src="<?= base_url() ?>node_modules/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>node_modules/numeral/min/numeral.min.js" type="text/javascript"></script>

<script src="<?= $lte_url ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= $lte_url ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= $lte_url ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?= $lte_url ?>bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="<?= $lte_url ?>bower_components/moment/min/moment.min.js"></script>
<script src="<?= $lte_url ?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="<?= $lte_url ?>plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="<?= $lte_url ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?= $lte_url ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!--<script src="<?= $lte_url ?>bower_components/fastclick/lib/fastclick.js"></script>-->
<script src="<?= $lte_url ?>bower_components/PACE/pace.min.js"></script>
<script src="<?= base_url() ?>node_modules/toastr/build/toastr.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>asset_custom/knockout-3.5.1.js" type="text/javascript"></script>
<script src="<?= base_url() ?>asset_custom/JQGrid.js" type="text/javascript"></script>
<script src="<?= base_url() ?>asset_custom/custom.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="<?= $lte_url ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!--<script src="<?= $lte_url ?>dist/js/demo.js"></script>-->
<?php if (isset($js_files)) { ?>
    <?php foreach ($js_files as $js) { ?>
        <script src="<?= $js ?>"></script>
    <?php } ?>
<?php } ?>

<script>
    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "sEmptyTable": 'Data Masih Kosong',
            "sProcessing": 'Loading...',
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "<span class='btn btn-default btn-sm'><i class='fa fa-search'></i></span>",
            "sUrl": "",
            "paginate": {
                "previous": "<i class='fa fa-chevron-left'></i>",
                "next": "<i class='fa fa-chevron-right'></i>",
                "first": "<i class='fa fa-chevron-left'></i><i class='fa fa-chevron-left'></i>",
                "last": "<i class='fa fa-chevron-right'></i><i class='fa fa-chevron-right'></i>",
            },
        }
    });
</script>