<script>
    $(window).on("load", function (e) {
<?php if (!empty($CI->session->flashdata('message_error'))) { ?>
            toastr.error("<?= addslashes($CI->session->flashdata('message_error')) ?>", 'Maaf');
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


        $('.select2-noajax').select2();

        $('.range-date').daterangepicker({
//            autoUpdateInput: false,
            singleDatePicker: false,
            showDropdowns: true,
            locale: {
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "Custom",
                format: 'DD/MM/YYYY',
                cancelLabel: 'Clear',
                "daysOfWeek": [
                    "Min",
                    "Sen",
                    "Sel",
                    "Rab",
                    "Kam",
                    "Jum",
                    "Sab"
                ],
                "monthNames": [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Augustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember"
                ],
            },
            ranges: {
                'Hari ini': [moment(), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
            }
        });


        format();
        $("body").on('DOMSubtreeModified', "form", function () {
            format();
        });
        function format() {
            $('.thousand').each(function () {
                num = $(this).val();
                num = numeral(num).format();
                $(this).val(num);
            });

            $('.thousand').keyup(function () {
                num = $(this).val();
                num = numeral(num).format();
                $(this).val(num);
            });


            $('.number').keypress(function (event) {
                num = $(this).val();
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            $('.number').focusout(function () {
                num = $(this).val();
                num = numeral(num).format('0,0.00');
                $(this).val(num);
            });

            $('.number').each(function () {
                num = $(this).val();
                num = numeral(num).format();
                $(this).val(num);
            });


        }


        $('input').keypress(function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        });

        $('.singgle-date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "Custom",
                format: 'DD/MM/YYYY',
                cancelLabel: 'Clear',
                "daysOfWeek": [
                    "Min",
                    "Sen",
                    "Sel",
                    "Rab",
                    "Kam",
                    "Jum",
                    "Sab"
                ],
                "monthNames": [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Augustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember"
                ],
            },
        });


        $('#content-isi').animate({
            opacity: 1,
        }, 1000);

//        content-header

        $('#content-header').animate({
            opacity: 1,
        }, 1000);



    });
</script>