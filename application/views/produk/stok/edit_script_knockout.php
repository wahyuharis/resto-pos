<?php
$CI = &get_instance();
$CI->load->model('Auth_model');
$auth = new Auth_model();
?>
<script>
    function stok(id_outlet, nama_outlet, qty_awal, qty_adj) {
        var self = this;

        self.id_outlet = ko.observable(id_outlet);
        self.nama_outlet = ko.observable(nama_outlet);
        self.qty_awal = ko.observable(qty_awal);
        self.qty_adj = ko.observable(qty_adj);
    }

    function harga(id_outlet, nama_outlet, harga) {
        var self = this;

        self.id_outlet = ko.observable(id_outlet);
        self.nama_outlet = ko.observable(nama_outlet);
        self.harga = ko.observable(harga);
    }



    function Produk_model() {
        var self = this;

        self.primary_id = ko.observable('<?= intval($primary_id) ?>');

        self.lacak_stok = ko.observable(1);

        <?php if(!empty( trim($primary_id))){ ?>
                self.lacak_stok(<?=$form['lacak_stok']?>);
        <?php } ?>
        
        self.stock_list = ko.observableArray([]);

        self.harga_list = ko.observableArray([]);

        self.id_outlet_ar_opt = ko.observableArray(<?= json_encode($form['id_outlet_opt']) ?>)
        self.id_outlet_ar = ko.observableArray(<?= json_encode($form['id_outlet_arr'])?>);

<?php foreach ($form['id_outlet_opt'] as $row) { ?>
            var push = new harga('<?= $row['id_outlet'] ?>', '<?= $row['nama_outlet'] ?>', '<?= $row['harga'] ?>');
            self.harga_list.push(push);
<?php } ?>

<?php foreach ($form['id_outlet_opt'] as $row) { ?>
            var push = new stok('<?= $row['id_outlet'] ?>', '<?= $row['nama_outlet'] ?>', '<?= $row['sisa'] ?>', '<?= $row['sisa'] ?>');
            self.stock_list.push(push);
<?php } ?>

        self.id_kategori_opt = ko.observableArray(<?= json_encode($form['id_kategori_opt']) ?>)

        self.sku = ko.observable('<?= $form['sku'] ?>');
        self.nama_produk = ko.observable('<?= $form['nama_produk'] ?>');
        self.id_kategori = ko.observable('<?= $form['id_kategori'] ?>');
        self.harga = ko.observable('<?= $form['harga'] ?>');
        self.stok_awal = ko.observable('<?= $form['stok_awal'] ?>');
        self.deskripsi = ko.observable('<?= $form['deskripsi'] ?>');

        self.lacak_stok.subscribe(function (val) {
            console.log(val);

            if (!val) {
                $('#stok_tab').removeAttr('data-toggle')
            } else {
                $('#stok_tab').attr('data-toggle', 'tab');
            }

        });

    }

    ko.applyBindings(new Produk_model(), document.getElementById("form-1"));
</script>