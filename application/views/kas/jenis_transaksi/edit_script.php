<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>
<script>
    $('#form-1').ready(function () {


        $('#form-1').submit(function (e) {
            e.preventDefault();
            Loading = new SubmitLoading('#submit-button');
            Loading.write();

            $.ajax({
                url: "<?= base_url() . $auth->get_url_controller() ?>submit/", // Url to which the request is send
                type: "POST", // Type of request to be send, called as method
                data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false
                success: function (data) // A function to be called if request succeeds
                {
//                    console.log(data);

                    if (!data.succes) {
                        toastr.error(data.message);

                        error = data.error;
                        petik = '"';

                        for (var key in error) {
                            console.log(key + " " + error[key]);
                            $('input[name=' + petik + key + petik + '], select[name=' + petik + key + petik + '],textarea[name=' + petik + key + petik + ' ]').parent().addClass('has-error');

                            $('input[name=' + petik + key + petik + '], select[name=' + petik + key + petik + '],textarea[name=' + petik + key + petik + ' ]').focusout(function () {
                                var input = $(this).val();
                                if (input.length > 0) {
                                    $(this).parent().removeClass('has-error');
                                }
                            });
                        }

                    } else if (data.succes) {
                        primary = '';
                        if (primary.trim().length > 0) {
                            toastr.info(data.message, 'informasi');
                        } else {
                            window.location.href = '<?= base_url() . $auth->get_url_controller() ?>';
                        }
                    } else {
                        toastr.error("Terjadi Kesalahan");
                    }

                    setTimeout(function () {
                        Loading.rewrite();
                    }, 500);

                },
                error: function (err) {
                    Loading.rewrite();

                    toastr.error("Terjadi Kesalahan Cek Koneksi");
                    console.log(err);
                }
            });
        });
    });
</script>