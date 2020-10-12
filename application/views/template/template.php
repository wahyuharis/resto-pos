<?php
$lte_url = LTE_URL;
$auth = new Auth_model();
$CI = &get_instance();


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $app_name . ' - ' . $title ?></title>
        <link rel="icon" href="<?= base_url() . $logo_app ?>" />
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <?php require_once 'template_asset.php'; ?>

    </head>
    <body class="hold-transition skin-blue fixed sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="<?= $lte_url ?>index2.html" class="logo" style="">
                    <span class="logo-mini">                        
                        <img class="img-circle" style="height: 30px;width: 30px" src="<?= base_url() . $logo_usaha ?>">
                    </span>
                    <span class="logo-lg" style="margin-left: -40px;">
                        <img class="img-circle" style="height: 30px;width: 30px" src="<?= base_url() . $logo_usaha ?>">
                        <?= $nama_usaha ?>
                    </span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">

                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?= base_url($image_profile) ?>" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?= $fullname ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="<?= base_url($image_profile) ?>" class="img-circle" alt="User Image">

                                        <p>
                                            <?= $fullname ?>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">
                                        <div class="row">
                                            <div class="col-xs-4 text-center">
                                            </div>
                                            <div class="col-xs-4 text-center">
                                            </div>
                                            <div class="col-xs-4 text-center">
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?= base_url('akun') ?>" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= base_url('logout') ?>" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->

                        </ul>
                    </div>
                </nav>
            </header>

            <!-- =============================================== -->

            <!-- Left side column. contains the sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?= base_url($image_profile) ?>" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p><?= $fullname ?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="header">MENU</li>

                        <?php
                        $act = "";
                        if ($auth->get_url_controller() == 'app/home/') {
                            $act = 'active';
                        }
                        ?>
                        <li class="<?= $act ?>">
                            <a href="<?= base_url('app/home/') ?>">
                                <i class="fa fa-home"></i> <span>Beranda</span>
                            </a>
                        </li>


                        <?= require_once 'template_menu.php'; ?>


                        <li class="header"></li>
                        <li><br></li>
                        <li><br></li>
                        <li><br></li>
                        <li><br></li>
                        <li><br></li>

                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- =============================================== -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?= $title ?>
                        <small><?= $title_desc ?></small>
                    </h1>
                    <ol class="breadcrumb">
<!--                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="#">Examples</a></li>
                        <li class="active">Blank page</li>-->
                    </ol>
                </section>

                <!-- Main content -->
                <section id="content-isi" style="opacity: 0" class="content">

                    
                    <?php if($box){ ?>
                     <div class="box">
                        <div class="box-body">
                            <?= $content ?>
                        </div>
                    </div>
                    <?php }else{ ?>
                    <?= $content ?>
                    <?php } ?>

                </section>
            </div>

            <footer class="main-footer" >
                <div class="pull-right hidden-xs">
                    <b>Version</b> 1.0
                </div>
                <?= $app_name ?>
            </footer>

        </div>
        <script>
            $(document).ready(function () {
                $('.sidebar-menu').tree();

            })
        </script>
        <?php require_once 'template_script.php'; ?>

    </body>
</html>
