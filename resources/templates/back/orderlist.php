<?php


if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "") {

  header('location:../');
}



?>



<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">OrderList</h1>
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


        <div class="card card-primary card-outline">
          <div class="card-header">
            <h5 class="m-0">Orders</h5>
          </div>
          <div class="card-body">

            <table class="table table-striped table-hover " id="table_orderlist">
              <thead>
                <tr>

                  <td>N.0</td>
                  <td>Invoice ID</td>
                  <td>Order Date</td>
                  <td>Total</td>
                  <td>Paid</td>
                  <td>Due</td>
                  <td>Payment Type</td>

                  <td>ActionIcons</td>

                </tr>

              </thead>


              <tbody>

                <?php
                $aus = $_SESSION['aus'];
                $change = query("SELECT * from tbl_change where aus='$aus'");
                confirm($change);
                $row_exchange = $change->fetch(PDO::FETCH_OBJ);
                $exchange = $row_exchange->exchange;
                $usd_or_real = $row_exchange->usd_or_real;
                $select = query("SELECT * from tbl_invoice where aus='$aus' order by invoice_id DESC");
                confirm($select);
                $no = 1;
                while ($row = $select->fetch(PDO::FETCH_OBJ)) {

                  if ($usd_or_real == "usd") {
                    $USD_usd = "$";
                    $totall = $row->total;
                    $total = number_format($totall, 2);
                  } else {
                    $USD_usd = "៛";
                    $totall = $row->total  * $exchange;
                    $total = number_format($totall);
                  }

                  echo '
                      <tr>      
                      <td>' . $no   . '</td>
                      <td>' . $row->receipt_id   . '</td>
                      <td>' . $row->order_date   . '</td>
                      <td>' . $total . $USD_usd . '</td>
                      <td>' . $row->paid . '</td>
                      <td>' . $row->due          . '</td>';
                  if ($row->payment_type == "Cash") {
                    echo '<td><span class="badge badge-warning">' . $row->payment_type . '</td></span></td>';
                  } else {
                    echo '<td><span class="badge badge-success">' . $row->payment_type . '</td></span></td>';
                  }

                  echo '
                    <td>
                    <div class="btn-group">
                    <a href="invoice_a5?id=' . $row->invoice_id . '" class="btn btn-warning " role="button" ><span class="fa fa-print" style="color:#ffffff" data-toggle="tooltip" title="Print Bill"></span></a>
                    <a href="itemt?editorderpos&id=' . $row->invoice_id . '" class="btn btn-info " role="button"><span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="Edit Order"></span></a>
                  
                    ' . show_delete($row->invoice_id) . '
                   
                    </div>
                    </td>
                    </tr>';
                  $no++;
                }

                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->