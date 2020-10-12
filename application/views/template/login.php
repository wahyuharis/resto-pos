<?php
$lte_url = LTE_URL;
$CI = &get_instance();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $app_name ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <link rel="icon" href="<?= base_url() . $logo_app ?>" />
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="stylesheet" href="<?= $lte_url ?>bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?= $lte_url ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?= $lte_url ?>bower_components/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?= $lte_url ?>dist/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="<?= $lte_url ?>plugins/iCheck/square/blue.css">
        <link href="<?= base_url() ?>node_modules/toastr/build/toastr.min.css" rel="stylesheet" type="text/css"/>

    </head>
    <body>

        <div class='slider'>
            <div class='slide1'></div>
            <div class='slide2'></div>
            <div class='slide3'></div>

            <div class="center-container">

                <div class="login-box">
                    <div class="login-box-body">
                        <div class="text-center">
                            <img src="<?= base_url() . $logo_app ?>" style="width: 50px;height: 50px">
                            <h3><?= $app_name ?></h3>
                        </div>

                        <p class="login-box-msg">
                            Sign in to start your session
                        </p>

                        <form id="login_form" action="#" method="post">
                            <div class="form-group has-feedback">
                                <label for="username" >Username / Email</label>
                                <input autocomplete="false" type="text" class="form-control" name="username" id="username" placeholder="Email atau Username" >
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="password" >Password</label>
                                <input autocomplete="false"  type="password" name="password" id="password" class="form-control" placeholder="Password" >
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="row">
                                <div class="col-xs-8">
                                    <div class="">
                                        <label>
                                            <input id="show_password" value="" type="checkbox"> Tampilkan Password
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <button id="btn_submit" type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>




        <!-- jQuery 3 -->
        <script src="<?= $lte_url ?>bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="<?= $lte_url ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="<?= $lte_url ?>plugins/iCheck/icheck.min.js"></script>
        <script src="<?= base_url() ?>node_modules/toastr/build/toastr.min.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>asset_custom/custom.js" type="text/javascript"></script>
        <script>
            $(function () {
//                $('input').iCheck({
//                    checkboxClass: 'icheckbox_square-blue',
//                    radioClass: 'iradio_square-blue',
//                    increaseArea: '20%' // optional
//                });

                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "5000",
                    "timeOut": "10000",
                    "extendedTimeOut": "3000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }

            });
        </script>

        <script>
            $('#login_form').submit(function (e) {
                e.preventDefault();
//                $('#btn_submit').prop('disabled', true);
//                $('#btn_submit').html('<i class="fas fa-circle-notch fa-spin" ></i>');

                Loading = new SubmitLoading('#btn_submit');
                Loading.write();

                $.ajax({
                    url: "<?= base_url('Autentikasi/login_submit') ?>", // Url to which the request is send
                    type: "POST", // Type of request to be send, called as method
                    data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    contentType: false, // The content type used when sending data to the server.
                    cache: false, // To unable request pages to be cached
                    processData: false, // To send DOMDocument or non processed data file it is set to false
                    success: function (data) // A function to be called if request succeeds
                    {
                        if (data['succes']) {
                            window.location = '<?= base_url('home') ?>';
                        } else {
//                            $('#alert').html(data['message']);
//                            setTimeout(function () {
//                                $('#alert').html('');
//                            }, 5000);

                            toastr.error(data['message'], "Maaf");

                            Loading.rewrite();
                        }


                    },
                    error: function (err) {
//                        $('#btn_submit').prop('disabled', false);
//                        $('#btn_submit').html('Login');
//console.log(err);
                        toastr.error('error code ' + err.status, "Maaf Periksa Koneksi Jaringan anda");
                        Loading.rewrite();

                    }
                });
            });

            $(document).ready(function (e) {
                $('#show_password').change(function(){
                    var checked=($(this).is(':checked') );
                    if(checked){
                        $('#password').attr('type','text');
                    }else{
                                                $('#password').attr('type','password');

                    }
                });

            });

            $(window).on("load", function (e) {
<?php if (!empty($CI->session->flashdata('message_error'))) { ?>
                    toastr["error"]("<?= addslashes($CI->session->flashdata('message_error')) ?>", 'Maaf');
<?php } ?>

<?php if (!empty($CI->session->flashdata('message'))) { ?>
                    toastr.success("<?= addslashes($CI->session->flashdata('message')) ?>", 'Informasi');
<?php } ?>

<?php if (!empty($CI->session->flashdata('message_succes'))) { ?>
                    toastr.success("<?= addslashes($CI->session->flashdata('message_succes')) ?>", 'Informasi');
<?php } ?>

<?php if (!empty($CI->session->flashdata('message_warning'))) { ?>
                    toastr["warning"]("<?= addslashes($CI->session->flashdata('message_warning')) ?>", 'Informasi');
<?php } ?>
            });

        </script>

    </body>
</html>
