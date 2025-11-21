<?php


if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "User") {

  header('location:../');
}



if (isset($_POST['date_1'])) {
  $_SESSION['date'] = $_POST['date_1'];
  $date_1 = $_POST['date_1'];
  $date_2 = $_POST['date_2'];

  $_SESSION['date_1'] = $date_1;
  $_SESSION['date_2'] = $date_2;
} else {
  $date_1 = date('Y-m-01');
  $date_2 = date("Y-m-d");
  $_SESSION['date'] = $date_1;
  $_SESSION['date_1'] = $date_1;
  $_SESSION['date_2'] = $date_2;
}
$aus = $_SESSION['aus'];
?>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>



<!-- daterange picker -->
<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">

<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Graph Report</h1>
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

        <form method="post" action="" name="">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">FROM : <?php echo date('d-m-Y', strtotime($date_1)) ?> -- To : <?php echo date('d-m-Y', strtotime($date_2)) ?> </h5>
            </div>
            <div class="card-body">
              <div class="row">

                <div class="col-md-5">
                  <div class="form-group">
                    <!-- <label>Date:</label> -->
                    <div class="input-group date" id="date_1" data-target-input="nearest">
                      <input type="text" class="form-control date_1" data-target="#date_1" name="date_1" value="<?php echo $date_1; ?>" data-date-format="yyyy-mm-dd">
                      <div class="input-group-append" data-target="#date_1" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="col-md-5">
                  <div class="form-group">
                    <!-- <label>Date:</label> -->
                    <div class="input-group date" id="date_2" data-target-input="nearest">
                      <input type="text" class="form-control date_2" data-target="#date_2" name="date_2" value="<?php echo $date_2; ?>" data-date-format="yyyy-mm-dd">
                      <div class="input-group-append" data-target="#date_2" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>



                </div>

                <div class="col-md-2">

                  <div class="text-center">
                    <button type="submit" class="btn btn-warning" name="btnfilter">Filter Records</button>
                  </div>
                </div>

              </div>


              <?php
              $change = query("SELECT * from tbl_change where aus='$aus'");
              confirm($change);
              $row_exchange = fetch_object($change);
              $exchange = $row_exchange->exchange;
              $usd_or_real = $row_exchange->usd_or_real;
              $select = query("SELECT order_date , sum(total) as grandtotal from tbl_invoice where aus='$aus' and order_date between '$date_1' AND '$date_2' group by order_date");
              confirm($select);

              $total = [];
              $date = [];

              while ($row = fetch_assoc($select)) {
                extract($row);
                
                $date[] = $order_date;

                if ($usd_or_real == "usd") {
                  $total[] = $grandtotal;
                  $USD_usd = " $";
                  $USD_txt = "USD";
                  $total[] = $grandtotal;
                } else {
                  $total[] = $grandtotal * $exchange;
                  $USD_usd = " ៛";
                  $USD_txt = "KHR";
                }
              }

              

              // echo json_encode($total);


              ?>

              <div>
                <canvas id="myChart" style="height:250px"></canvas>
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
                    data: <?php echo json_encode($total); ?>,
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


            <?php

            $select = query("SELECT product_name , sum(qty) as q from tbl_invoice_details where aus='$aus' and order_date between '$date_1' AND '$date_2' group by product_id");
            confirm($select);

            $pname = [];
            $qty = [];

            while ($row = fetch_assoc($select)) {
              extract($row);

              $pname[] = $product_name;
              $qty[] = $q;
            }

            // echo json_encode($total);

            ?>

            <div>
              <canvas id="bestsellingproduct" style="height:250px"></canvas>
            </div>


          </div>

          <script>
            const ctx1 = document.getElementById('bestsellingproduct');

            new Chart(ctx1, {
              type: 'line',
              data: {
                labels: <?php echo json_encode($pname); ?>,
                datasets: [{
                  label: 'Product Quantity',
                  backgroundColor: 'rgb(102,255,102)',
                  borderColor: 'rgb(0,102,0)',
                  data: <?php echo json_encode($qty); ?>,
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
      </form>

    </div>
    <!-- /.col-md-6 -->
  </div>
  <!-- /.row -->
</div>
<!-- /.content -->