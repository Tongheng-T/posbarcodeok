<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS BARCODE | SYSTEMS | By:TH</title>
    <link rel='shortcut icon' href="../ui/logo/256.ico" type="image/x-icon">
    <link rel="icon" href="../ui/logo/32.ico" sizes="32x32">
    <link rel="icon" href="../ui/logo/48.ico" sizes="48x48">
    <link rel="icon" href="../ui/logo/96.ico" sizes="96x96">
    <link rel="icon" href="../ui/logo/256.ico" sizes="144x144">
    <link rel="manifest" href="../manifest.json">
    <meta name="theme-color" content="#000000">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../dist/css/castermll.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="../plugins/toastr/toastr.min.css">

    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="../plugins/toastr/toastr.min.js"></script>
    <script src="../dist/js/scripts.js"></script>



</head>
<style>
    @font-face {
        font-family: "OSbattambang";
        src: url(../fone/KhmerOSbattambang.ttf)format("truetype");
    }

    * {
        font-family: "OSbattambang";
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="itemt?dashboard" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link"></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li> -->
            </ul>
        </nav>
        <!-- /.navbar -->
        <?php
        $aus = $_SESSION['aus'];
        $select_logo = query("SELECT * FROM tbl_logo WHERE aus = :aus", [':aus' => $aus]);
        $rowg = $select_logo->fetch(PDO::FETCH_OBJ);

        $logo = $rowg->img ?? 'default.png'; // fallback if null
        ?>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="itemt?dashboard" class="brand-link">
                <img src="../productimages/logo/<?php echo $logo ?>" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">POS BARCODE</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="../resources/images/userpic/<?php echo img_user() ?>" class="img-circle elevation-2"
                            alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"><?php htmlspecialchars(name_user()); ?></a>
                    </div>
                </div>


                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                        <li class="nav-item">
                            <a href="itemt?dashboard" class="nav-link <?php actr("dashboard"); ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?category" class="nav-link <?php actr("category"); ?>">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Category
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?addproduct" class="nav-link <?php actr("addproduct"); ?>">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>
                                    Product

                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?Add_stock" class="nav-link <?php actr("Add_stock"); ?>">
                                <i class="nav-icon fas fa-cart-plus"></i>
                                <p>
                                    Add_stock

                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?stock_list" class="nav-link <?php actr("stock_list"); ?>">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>
                                    Stock List

                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="itemt?productlist" class="nav-link <?php actr("productlist"); ?>">
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Product List

                                </p>
                            </a>
                        </li>





                        <li class="nav-item">
                            <a href="itemt?pos" class="nav-link <?php actr("pos"); ?>">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    POS

                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?orderlist" class="nav-link <?php actr("orderlist"); ?>">
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    OrderList

                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="itemt?" class="nav-link <?php actr("tablereport");
                            actr("graphreport"); ?>">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>
                                    Sales Report
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="itemt?tablereport" class="nav-link <?php actr("tablereport"); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Table Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="itemt?graphreport" class="nav-link <?php actr("graphreport"); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Graph Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="itemt?orderlist_user" class="nav-link <?php actr("orderlist_user"); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Orderlist_user</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="itemt?report_sales_summary"
                                        class="nav-link <?php actr("report_sales_summary"); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>របាយការណ៍ចំណូល/ចំណាយ</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="itemt?taxdis" class="nav-link <?php actr("taxdis"); ?>">
                                <i class="nav-icon fa fa-cog"></i>
                                <p>
                                    Settings
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?registration" class="nav-link <?php actr("registration"); ?>">
                                <i class="nav-icon far fa-plus-square"></i>
                                <p>
                                    Registration
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?settings" class="nav-link <?php actr("settings"); ?>">
                                <i class="nav-icon fas fa-user-lock"></i>
                                <p>
                                    Admin
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itemt?logout" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->