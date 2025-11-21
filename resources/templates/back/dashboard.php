<?php


if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "User") {

    header('location:../');
}


$aus = $_SESSION['aus'] ?? null;

if ($aus) {
    // Exchange
    $stmt = query("SELECT * FROM tbl_change WHERE aus = ?", [$aus]);
    $row_exchange = $stmt->fetch(PDO::FETCH_OBJ);

    if ($row_exchange) {
        $exchange = $row_exchange->exchange;
        $usd_or_real = $row_exchange->usd_or_real;
    }

    // Invoice
    $stmt = query("SELECT SUM(total) AS gt, COUNT(invoice_id) AS invoice 
                   FROM tbl_invoice 
                   WHERE aus = ?", [$aus]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    $total_order = $row->invoice ?? 0;
    $grand_total = $row->gt ?? 0;

    if ($usd_or_real === "usd") {
        $total_revn = $grand_total;
        $USD_usd = " $";
        $USD_txt = "USD";
    } else {
        $total_revn = $grand_total * $exchange;
        $USD_usd = " ៛";
        $USD_txt = "KHR";
    }

    // Product
    $stmt = query("SELECT COUNT(product) AS pname FROM tbl_product WHERE aus = ?", [$aus]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    $total_product = $row->pname ?? 0;

    // Category
    $stmt = query("SELECT COUNT(category) AS cate FROM tbl_category WHERE aus = ?", [$aus]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    $total_category = $row->cate ?? 0;
}





?>

<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Admin Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Starter Page</li> -->
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">



                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $total_order; ?></h3>

                                <p>TOTAL INVOICE</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo number_format($grand_total, 2); ?></h3>

                                <p>TOTAL REVENUE(<?php echo $USD_txt ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $total_product; ?></h3>

                                <p>TOTAL PRODUCT</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $total_category; ?></h3>

                                <p>TOTAL CATEGORY</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->


                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Earning By Date</h5>
                    </div>
                    <div class="card-body">

                        <?php

                        $select = query("SELECT order_date, total FROM tbl_invoice WHERE aus = ? GROUP BY order_date LIMIT 50", [$aus]);

                        $ttl = [];
                        $date = [];

                        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                            $order_date = $row['order_date'];
                            $total = $row['total'];

                            $date[] = $order_date;

                            if ($usd_or_real == "usd") {
                                $ttl[] = $total;
                            } else {
                                $ttl[] = $total * $exchange;
                            }
                        }

                        // echo json_encode($total);
                        
                        ?>

                        <div>
                            <canvas id="myChart" style="height: 250px"></canvas>
                        </div>

                    </div>

                    <script>
                        const ctx = document.getElementById('myChart');

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($date); ?>,
                                datasets: [{
                                    label: 'Total Earning',
                                    backgroundColor: 'rgb(255,99,132)',
                                    borderColor: 'rgb(255,99,132)',
                                    data: <?php echo json_encode($ttl); ?>,
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true

                                    }
                                }
                            }
                        });
                    </script>


                </div>

            </div>


        </div>
        <!-- /.col-md-6 -->


        <div class="row">


            <div class="col-md-6">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">BEST SELLING PRODUCT</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover " id="table_bestsellingproduct">
                            <thead>
                                <tr>

                                    <td>N0</td>
                                    <td>Product Name</td>

                                    <td>QTY</td>
                                    <td>rate</td>
                                    <td>Total</td>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $select = query("SELECT product_id, product_name, rate, SUM(qty) AS q, SUM(saleprice) AS total FROM tbl_invoice_details WHERE aus = ? GROUP BY product_id ORDER BY SUM(qty) DESC LIMIT 12", [$aus]);

                                $no = 1;
                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    echo '
                                     <tr>
                                         <td>' . $no. '</td>
                                         <td><span class="badge badge-dark">' . $row->product_name . '</span></td>
                                         <td><span class="badge badge-success">' . $row->q . '</span></td>
                                         <td><span class="badge badge-primary">' . $row->rate . '</span></td>
                                         <td><span class="badge badge-danger">' . $row->total . '</span></td>
                                     </tr>';
                                     $no++;
                                }

                                ?>
                            </tbody>
                        </table>



                    </div>
                </div>

            </div>


            <div class="col-md-6">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Earning By Date</h5>
                    </div>
                    <div class="card-body">

                        <table class="table table-striped table-hover " id="table_recentorder">
                            <thead>
                                <tr>

                                    <td>Invoice ID</td>
                                    <td>Order Date</td>

                                    <td>Total</td>
                                    <td>Paymrnt Type</td>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $select = query("SELECT * FROM tbl_invoice WHERE aus = ? ORDER BY invoice_id DESC LIMIT 30", [$aus]);

                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {

                                    echo '
                                     <tr>
                                         <td>' . $row->invoice_id . '</td>
                                         <td><span class="badge badge-dark">' . $row->order_date . '</span></td>
                                         <td><span class="badge badge-danger">' . $row->total . '</span></td>';

                                    if ($row->payment_type == "Cash") {
                                        echo '<td><span class="badge badge-warning">' . $row->payment_type . '</span></td>';
                                    } else {
                                        echo '<td><span class="badge badge-success">' . $row->payment_type . '</span></td>';
                                    }

                                    echo '</tr>';
                                }

                                ?>
                            </tbody>
                        </table>



                    </div>
                </div>

            </div>

        </div>

    </div>
    <!-- /.row -->

</div>
<!-- /.content -->