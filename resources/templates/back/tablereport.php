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

<!-- daterange picker -->
<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">

<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">


<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Table Report</h1>
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


        <div class="card card-warning card-outline">
          <div class="card-header">
            <h5 class="m-0">FROM : <?php echo date('d-m-Y', strtotime($date_1)) ?> -- To : <?php echo date('d-m-Y', strtotime($date_2)) ?> </h5>
          </div>


          <form action="" method="post" name="">

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


              <br>

              <?php
              $tbl_product = query("SELECT sum(purchaseprice) as purchasepricee from tbl_product where aus='$aus'");
              confirm($tbl_product);
              $tbl_product = fetch_object($tbl_product);
              $purchaseprice   = $tbl_product->purchasepricee;
              // ////////

              $change = query("SELECT * from tbl_change where aus='$aus'");
              confirm($change);
              $row_exchange = fetch_object($change);
              $exchange = $row_exchange->exchange;
              $usd_or_real = $row_exchange->usd_or_real;
              $select = query("SELECT sum(total) as grandtotal , sum(subtotal) as stotal, count(invoice_id) as invoice , sum(discount) as discountd from tbl_invoice where aus='$aus' and order_date between '$date_1' AND '$date_2'");
              confirm($select);

              $row = fetch_object($select);

              $grand_totall = $row->grandtotal;
              $subtotall = $row->stotal;
              $invoice = $row->invoice;
              $discountdd = $row->discountd;

              if ($usd_or_real == "usd") {
                $grand_totalg = $grand_totall;
                $grand_total = number_format($grand_totalg, 2);
                $subtotalg = $subtotall;
                $subtotal = number_format($subtotalg, 2);
                $discountddd = $discountdd;
                $discountd = number_format($discountdd, 2);
                $USD_usd = "$";
                $USD_txt = "USD";
              } else {
                $grand_totalg = $grand_totall * $exchange;
                $grand_total = number_format($grand_totalg);
                $subtotalg = $subtotall * $exchange;
                $subtotal = number_format($subtotalg);
                $discountddd = $discountdd * $exchange;
                $discountd = number_format($discountddd);

                $USD_usd = "៛";
                $USD_txt = "KHR";
              }


              ?>



              <div class="row">
                <div class="col-12 col-sm-3 col-md-3">
                  <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">TOTAL DISCOUNT (<?php echo $USD_txt ?>)</span>
                      <span class="info-box-number">
                        <h2><?php echo $discountd; ?></h2>

                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-3 col-md-3">
                  <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">TOTAL DISCOUNT (<?php echo $USD_txt ?>)</span>
                      <span class="info-box-number">
                        <h2><?php echo $purchaseprice; ?></h2>

                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-3 col-md-3">
                  <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">SUBTOTAL (<?php echo $USD_txt ?>)</span>
                      <span class="info-box-number">
                        <h2><?php echo $subtotal; ?></h2>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-3 col-md-3">
                  <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">GRAND TOTAL (<?php echo $USD_txt ?>)</span>
                      <span class="info-box-number">
                        <h2><?php echo $grand_total; ?></h2>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
              </div>
              <!-- /.row -->


              <br>




              <table class="table table-striped table-hover " id="table_report">
                <thead>
                  <tr>

                    <td>#</td>

                    <td>Order Date</td>
                    <td>SubTotal</td>
                    <td>Discount(%)</td>
                    <td>Discount</td>
                    <td>Total</td>
                    <td>Paid</td>
                    <td>Due</td>
                    <td>Saler name</td>
                    <td>Payment Type</td>



                  </tr>

                </thead>


                <tbody>

                  <?php

                  $select = query("SELECT * from tbl_invoice where aus='$aus' and order_date between '$date_1' AND '$date_2'");
                  confirm($select);

                  $no = 1;
                  while ($row = fetch_object($select)) {

                    if ($usd_or_real == "usd") {
                      $totall = number_format($row->total, 2);

                      $subtotall = $row->subtotal;
                      $discountt = $row->discount;
                      $discount = number_format($discountt, 2);
                      $subtotal = number_format($subtotall, 2);
                    } else {
                      $totalll = $row->total * $exchange;
                      $totall = number_format($totalll);

                      $subtotall = $row->subtotal * $exchange;
                      $discountt = $row->discount * $exchange;
                      $subtotal = number_format($subtotall);

                      $discount = number_format($discountt);
                    }

                    echo '
                      <tr>
                        <td>' . $no . '</td>
                   
                        <td>' . date('d-m-Y', strtotime($row->order_date)) . '</td>
                        <td>' . $subtotall    . $USD_usd    . '</td>
                        <td>' . $row->discountp       . '%</td>
                        <td>' . $discount       . $USD_usd    . '</td>
                        <td><span class="badge badge-danger">' . $totall  . $USD_usd  . '</td></span></td>
                        <td>' . $row->paid         . '</td>
                        <td>' . $row->due          . '</td>
                        <td>' . $row->saler_name   . '</td>';

                    if ($row->payment_type == "Cash") {
                      echo '<td><span class="badge badge-warning">' . $row->payment_type . '</td></span></td>';
                    } else {
                      echo '<td><span class="badge badge-success">' . $row->payment_type . '</td></span></td>';
                    }
                    $no++;
                  }
                  ?>
                </tbody>
              </table>








            </div>


          </form>






        </div>
      </div>



    </div>
    <!-- /.col-md-6 -->
  </div>
  <!-- /.row -->
</div><!-- /.content-fluid -->