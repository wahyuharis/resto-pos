var table_index = '.table-mov';
$("body").on('DOMSubtreeModified', table_index, function () {
    table_mov();
});


$(document).ready(function () {
    table_mov();
});

function table_mov() {
    var table_index = table_index;
    $('body').append('<div id="key-alert" style="display:none;bottom:0;right:0;position:fixed;" >asdas</div>');
    var righted = 0;
    var lefted = 0
    $(table_index + ' tbody tr td input').on('keyup', function (e) {
        var row_index = $(this).closest("tr").index();
        var col_index = $(this).closest("td").index();


        if (e.keyCode == 39) {
            position = e.target.selectionStart;
            length = $(this).val().length;
            if (length == position) {
                righted++;
                $('#key-alert').html("Press right again...");
                $('#key-alert').show();
            }
            if (righted == 2) {
                $(table_index + ' tbody tr:eq(' + row_index + ') td:eq(' + (col_index + 1) + ')').find('input').focus();
                righted = 0;
                $('#key-alert').fadeOut('slow');
            }
        }

        if (e.keyCode == 37) {
            position = e.target.selectionStart;
            length = 0;
            if (length == position) {
                lefted++;
                $('#key-alert').html("Press left again...");
                $('#key-alert').show();
            }
            if (lefted == 2) {
                $(table_index + ' tbody tr:eq(' + row_index + ') td:eq(' + (col_index - 1) + ')').find('input').focus();
                lefted = 0;
                $('#key-alert').fadeOut('slow');
            }
        }
        if (e.keyCode == 38) {
            $(table_index + ' tbody tr:eq(' + (row_index - 1) + ') td:eq(' + (col_index) + ')').find('input').focus();
        }
        if (e.keyCode == 40) {
            $(table_index + ' tbody tr:eq(' + (row_index + 1) + ') td:eq(' + (col_index) + ')').find('input').focus();
        }
    });
}