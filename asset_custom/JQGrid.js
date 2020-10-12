function JQgrid(body_id, data_arr) {
    this.body_id = body_id;
    this.data = data_arr;
    this.html = "";
    this.output_id = "";
    this.remove_attr = '';

    this.output_id = function (output_id = "") {
        this.output_id = output_id;
    }

    this.remove_attribute = function (attr) {
        this.remove_attr = attr;
    }


    this.callback = function (cols, row) {
        return cols;
    }

    this.get_data = function () {
        return this.data;
    }

    this.init = function () {
        this.html = $(this.body_id).children().eq(0)[0].outerHTML;
        $(this.body_id).html('');
    }

    this.run = function () {
        this.init();

        var i;
        var i2;
        var i3;
        var i4;
        data_arr = this.data;
        var rows = [];
        var self = this;


        for (i = 0; i < this.data.length; i++) {
            row = this.data[i];
            $(this.body_id).append(this.html);
            rows = self.callback(rows, row);
            row_select = $(this.body_id).children().eq(i);


            for (i2 = 0; i2 < rows.length; i2++) {
                value = 0;
                value = row[rows[i2].key];
                if (rows[i2].value != null || rows[i2].value != undefined) {
                    value = rows[i2].value;
                }
                row_select.find(rows[i2].input).val(value);
                row_select.find(rows[i2].input).html(value);
            }
        }
        for (i = 0; i < data_arr.length; i++) {
            row_select = $(self.body_id).children().eq(i);
            row = {};
            rows = self.callback(rows, data_arr[i]);
            for (i2 = 0; i2 < rows.length; i2++) {
                row[rows[i2].key] = row_select.find(rows[i2].input).val();
            }
            self.data[i] = row;

        }

        $(this.body_id).children().each(function (i) {
            $(this).find(self.remove_attr).click(function () {
                self.remove_row(i);
            });
        });



        $(this.output_id).val(JSON.stringify(this.data));


        containerid = this.body_id;
        data_arr = this.data;
        var i;
        var output = [];

        $(containerid).on('change paste keyup', function () {
            for (i = 0; i < data_arr.length; i++) {
                row_select = $(self.body_id).children().eq(i);
                row = {};
                rows = self.callback(rows, data_arr[i]);
                for (i2 = 0; i2 < rows.length; i2++) {
                    row[rows[i2].key] = row_select.find(rows[i2].input).val();
                }
                self.data[i] = row;
            }

            for (i = 0; i < self.data.length; i++) {
                row = self.data[i];
                rows = self.callback(rows, row);
                row_select = $(self.body_id).children().eq(i);

                for (i2 = 0; i2 < rows.length; i2++) {
                    value = 0;
                    value = row[rows[i2].key];
                    if (rows[i2].value != null || rows[i2].value != undefined) {
                        value = rows[i2].value;
                    }
                    row_select.find(rows[i2].input).val(value);
                    row_select.find(rows[i2].input).html(value);
                }
            }

            for (i = 0; i < data_arr.length; i++) {
                row_select = $(self.body_id).children().eq(i);
                row = {};
                rows = self.callback(rows, data_arr[i]);
                for (i2 = 0; i2 < rows.length; i2++) {
                    row[rows[i2].key] = row_select.find(rows[i2].input).val();
                }
                self.data[i] = row;

            }

            $(self.output_id).val(JSON.stringify(self.data));
        });
    }




    this.push_row = function (row) {
        this.data.push(row);
        $(this.body_id).html('');
        self = this;

        for (i = 0; i < this.data.length; i++) {
            row = this.data[i];
            $(this.body_id).append(this.html);
            rows = [];
            rows = self.callback(rows, row);
            row_select = $(this.body_id).children().eq(i);

            for (i2 = 0; i2 < rows.length; i2++) {
                value = 0;
                value = row[rows[i2].key];
                if (rows[i2].value != null || rows[i2].value != undefined) {
                    value = rows[i2].value;
                }
                row_select.find(rows[i2].input).val(value);
                row_select.find(rows[i2].input).html(value);
            }
        }

        for (i = 0; i < data_arr.length; i++) {
            row_select = $(self.body_id).children().eq(i);
            row = {};
            rows = self.callback(rows, data_arr[i]);
            for (i2 = 0; i2 < rows.length; i2++) {
                row[rows[i2].key] = row_select.find(rows[i2].input).val();
            }
            self.data[i] = row;

        }
        $(this.output_id).val(JSON.stringify(this.data));

        $(this.body_id).children().each(function (i) {
            $(this).find(self.remove_attr).click(function () {
                self.remove_row(i);
            });
        });
    }

    this.remove_row = function (i) {
        data_arr = this.data;
        var rows = [];
        var self = this;

        self.data.splice(i, 1);
        $(this.body_id).html('');

        for (i = 0; i < this.data.length; i++) {
            row = this.data[i];
            $(this.body_id).append(this.html);
            rows = self.callback(rows, row);
            row_select = $(this.body_id).children().eq(i);


            for (i2 = 0; i2 < rows.length; i2++) {
                value = 0;
                value = row[rows[i2].key];
                if (rows[i2].value != null || rows[i2].value != undefined) {
                    value = rows[i2].value;
                }
                row_select.find(rows[i2].input).val(value);
                row_select.find(rows[i2].input).html(value);
            }
        }
        for (i = 0; i < data_arr.length; i++) {
            row_select = $(self.body_id).children().eq(i);
            row = {};
            rows = self.callback(rows, data_arr[i]);
            for (i2 = 0; i2 < rows.length; i2++) {
                row[rows[i2].key] = row_select.find(rows[i2].input).val();
            }
            self.data[i] = row;
        }
        $(self.output_id).val(JSON.stringify(self.data));
        $(this.body_id).children().each(function (i) {
            $(this).find(self.remove_attr).click(function () {
                self.remove_row(i);
            });
        });
    }

}


function curency_to_float(str) {
    var myNumeral2 = numeral(str);

    return parseFloat(myNumeral2.value());
}

function float_to_currency(floatval) {
    floatval = parseFloat(floatval);
    return numeral(floatval).format("0,0.00");
}