<?php
$level1 = $CI->db->order_by('urutan', 'asc')->get_where('_menu', array('level' => '0'))->result_array();

$level2 = $CI->db->order_by('urutan', 'asc')->get_where('_menu', array('level' => '1'))->result_array();

$level3 = $CI->db->order_by('urutan', 'asc')->get_where('_menu', array('level' => '2'))->result_array();

//$url_controller_active=$auth->get_url_controller();


?>




<?php foreach ($level1 as $row0) { ?>
    <?php
    $has_treeview0 = "";
    if (!is_null(multidim_search('parent', $row0['id_menu'], $level2))) {
        $has_treeview0 = "treeview";
    }


    $url_controller = "#";
    if (!empty(trim($row0['url_controller']))) {
        $url_controller = base_url() . $row0['url_controller'];
    }

    $active0 = "";
    if (rtrim($row0['url_controller'], "/") . "/" == $url_controller_active) {
        $active0 = "active";
    }
    ?>
    <li class="<?= $has_treeview0 ?> <?=$active0?>">
        <a href="<?= $url_controller ?>">
            <i class="<?= $row0['logo'] ?>"></i> <span><?= $row0['nama'] ?></span>
    <?php if (!empty($has_treeview0)) { ?> 
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
    <?php } ?>
        </a>
            <?php $display_none1 = 'display: none;'; ?>
        <?php if (!empty($has_treeview0)) { ?>
            <?php
            $display_none1 = 'display: none;';

            foreach ($level2 as $cari1) {
                if ($cari1['parent'] == $row0['id_menu']) {
                    if (rtrim($cari1['url_controller'], "/") . "/" == $url_controller_active) {
                        $display_none1 = 'display: block;';
                    }
                }
            }
            foreach ($level2 as $cari11) {
                foreach ($level3 as $cari22) {
                    if ($cari11['parent'] == $row0['id_menu']) {
                        if ($cari22['parent'] == $cari11['id_menu']) {
                            if (rtrim($cari22['url_controller'], "/") . "/" == $url_controller_active) {
                                $display_none1 = 'display: block;';
                            }
                        }
                    }
                }
            }
            ?>
        <?php } ?>

        <ul class="treeview-menu" style="<?= $display_none1 ?>">
    <?php foreach ($level2 as $row1) { ?>
                <?php if ($row1['parent'] == $row0['id_menu']) { ?>
                    <?php
                    $has_treeview1 = "";
                    if (!is_null(multidim_search('parent', $row1['id_menu'], $level3))) {
                        $has_treeview1 = "treeview";
                    }
                    $url_controller = "#";
                    if (!empty(trim($row1['url_controller']))) {
                        $url_controller = base_url() . $row1['url_controller'];
                    }

                    $active1 = "";
                    if (rtrim($row1['url_controller'], "/") . "/" == $url_controller_active) {
                        $active1 = "active";
                    }
                    ?>
                    <li class="<?= $has_treeview1 ?> <?= $active1 ?>">
                        <a href="<?= $url_controller ?>">
                            <i class="<?= $row1['logo'] ?>"></i> 
            <?= $row1['nama'] ?>
                            <?php if (!empty($has_treeview1)) { ?> 
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
            <?php } ?>
                        </a>
                            <?php if (!empty($has_treeview1)) { ?> 
                            <?php
                            $display_none2 = 'display: none;';
                            foreach ($level3 as $cari2) {
                                if ($cari2['parent'] == $row1['id_menu']) {
                                    if (rtrim($cari2['url_controller'], "/") . "/" == $url_controller_active) {
                                        $display_none2 = "display: block;";
                                    }
                                }
                            }
                            ?>
                            <ul class="treeview-menu" style="<?= $display_none2 ?>">
                            <?php foreach ($level3 as $row2) { ?>
                                    <?php if ($row2['parent'] == $row1['id_menu']) { ?>
                                        <?php
                                        $active2 = "";
                                        if (rtrim($row2['url_controller'], "/") . "/" == $url_controller_active) {
                                            $active2 = "active";
                                        }
                                        $url_controller = "#";
                                        if (!empty(trim($row2['url_controller']))) {
                                            $url_controller = base_url() . $row2['url_controller'];
                                        }
                                        ?>
                                        <li class="<?= $active2 ?>">
                                            <a href="<?= $url_controller ?>"><i class="<?= $row2['logo'] ?>"></i> <?= $row2['nama'] ?></a>
                                        </li>
                    <?php } ?>
                                <?php } ?>

                            </ul>
            <?php } ?>
                    </li>
                    <?php } ?>
            <?php } ?>
        </ul>
    </li>
<?php } ?>



