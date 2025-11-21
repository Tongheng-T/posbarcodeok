<?php

if (empty($_SESSION['useremail']) || empty($_SESSION['role'])) {
  redirect('../');
}




$aus = $_SESSION['aus'];

if (isset($_POST['btnsaveorder'])) {

  $orderdate = date('Y-m-d');
  $subtotall = $_POST['txtsubtotal'];
  $discount = $_POST['txtdiscount'];
  $discountp = $_POST['txtdiscountp'];
  $discount_h = $_POST['discount_h'] ?? 0;
  $free = $_POST['free'] ?? 0;
  $totall = $_POST['txttotal'];
  $payment_type = $_POST['rb'];
  $due = $_POST['txtdue'];
  $paid = $_POST['txtpaid'];
  $buyer = $_POST['txtname'];

  // ARRAYS
  $arr_pid = $_POST['pid_arr'];
  $arr_barcode = $_POST['barcode_arr'];
  $arr_name = $_POST['product_arr'];
  $arr_stock = $_POST['stock_c_arr'];
  $arr_qty = $_POST['quantity_arr'];
  $arr_pricee = $_POST['price_c_arr'];
  $arr_totall = $_POST['saleprice_arr'];

  $saler_name = $_SESSION['username'];
  $saler_id = $_SESSION['userid'];


  $change = query(
    "SELECT * FROM tbl_change WHERE aus = ?",
    [$aus]
  );
  $row_exchange = fetch_object($change);

  $exchange = $row_exchange->exchange;
  $usd_or_real = $row_exchange->usd_or_real;


  if ($usd_or_real === "usd") {
    $subtotal = $subtotall;
    $total = $totall;
    $discountKHRk = $discount;
    $exchangee = 1;
  } else {
    $subtotal = $subtotall / $exchange;
    $total = $totall / $exchange;
    $discountKHRk = $discount / $exchange;
    $exchangee = $exchange;
  }


  $tbl_invoice = query(
    "SELECT MAX(receipt_id) as receipt_num FROM tbl_invoice WHERE aus = ?",
    [$aus]
  );

  $row = fetch_object($tbl_invoice);
  $receipt_id = ($row->receipt_num ?? 0) + 1;


  $insert = query(
    "INSERT INTO tbl_invoice 
    (receipt_id, order_date, subtotal, discount, discountp, free, discount_h, total,
     payment_type, due, paid, saler_id, saler_name, buyer, aus)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    [
      $receipt_id,
      $orderdate,
      $subtotal,
      $discountKHRk,
      $discountp,
      $free,
      $discount_h,
      $total,
      $payment_type,
      $due,
      $paid,
      $saler_id,
      $saler_name,
      $buyer,
      $aus
    ]
  );

  $invoice_id = last_id();


  if ($invoice_id) {

    for ($i = 0; $i < count($arr_pid); $i++) {

      $arr_price = $arr_pricee[$i] / $exchangee;
      $arr_total = $arr_totall[$i] / $exchangee;
      $rem_qty = $arr_stock[$i] - $arr_qty[$i];

      if ($rem_qty < 0) {
        set_message("Order not completed, insufficient stock!");
        redirect("pos");
      }

      // UPDATE STOCK
      query("UPDATE tbl_product SET stock = ? WHERE pid = ?", [
        $rem_qty,
        $arr_pid[$i]
      ]);

      // INSERT DETAIL
      query(
        "INSERT INTO tbl_invoice_details (invoice_id, barcode, product_id, product_name, qty, rate, saleprice, order_date, saler_id, aus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
          $invoice_id,
          $arr_barcode[$i],
          $arr_pid[$i],
          $arr_name[$i],
          $arr_qty[$i],
          $arr_price,
          $arr_total,
          $orderdate,
          $saler_id,
          $aus
        ]
      );
    }

    redirect('invoice_a5?id=' . $invoice_id);
  }




  //var_dump($arr_total);

}


ob_end_flush();

$select = query("SELECT * from tbl_taxdis where aus =?", [$aus]);
confirm($select);
$row = fetch_object($select);

$change = query("SELECT * from tbl_change where aus=?", [$aus]);
confirm($change);
$row_exchange = fetch_object($change);
$exchange = $row_exchange->exchange;
$usd_or_real = $row_exchange->usd_or_real;

if ($usd_or_real == "usd") {
  $dddd = '$<span id="txttotal">0</span>';
  $USD_usd = " $";
  $USD_txt = "USD";
  $Change_rea = "៛";
  $Change = "KHR";
} else {
  $dddd = '<span id="txttotal">0</span>៛';
  $USD_usd = " ៛";
  $Change = "USD";
  $Change_rea = "$";
  $USD_txt = "KHR";
}

?>


<style type="text/css">
  .tableFixHead {
    overflow: scroll;
    height: 520px;
  }

  .tableFixHead thead th {
    position: sticky;
    top: 0;
    z-index: 1;
  }

  table {
    border-collapse: collapse;
    width: 100px;
  }

  th,
  td {
    padding: 8px 16px;
  }

  th {
    background: #eee;
  }
</style>







<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!-- <h1 class="m-0">Point Of Sale</h1> -->
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
            <h5 class="m-0">POS</h5>
          </div>



          <div class="card-body">


            <div class="row">

              <div class="col-md-8">

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                  </div>
                  <input type="text" class="form-control" placeholder="Scan Barcode" autocomplete="off"
                    name="txtbarcode" id="txtbarcode_id">
                </div>


                <form id="posForm" method="post" autocomplete="off">



                  <select id="product_id" class="form-control select2" data-dropdown-css-class="select2-purple"
                    style="width: 100%;">
                    <option>Select OR Search</option><?php echo fill_product(); ?>
                    <option>Select OR Search</option>
                  </select>
                  <!-- <img src="productimages/639a07f9bd5d4.jpg" height="50" alt=""> -->
                  </br>
                  <div class="tableFixHead">


                    <table id="producttable" class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Product</th>
                          <th>Stock </th>
                          <th>Unit / Size </th>

                          <th>price </th>
                          <th>QTY </th>
                          <th>Total </th>
                          <th>Del </th>
                        </tr>

                      </thead>


                      <tbody class="details" id="itemtable">
                        <tr data-widget="expandable-table" aria-expanded="false">

                        </tr>
                      </tbody>
                    </table>



                  </div>



              </div>


              <div class="col-md-4">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">ឈ្មោះអ្នកទិញ </span>
                  </div>
                  <input type="text" class="form-control" name="txtname" id="txtname" placeholder="បញ្ចូល ឈ្មោះអ្នកទិញ">
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa"
                        style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">SUBTOTAL(<?php echo $USD_txt ?>) </span>
                  </div>
                  <input type="text" class="form-control" name="txtsubtotal" id="txtsubtotal_id" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa"
                        style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>


                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT(%)</span>
                  </div>
                  <input type="text" class="form-control" name="txtdiscountp" id="txtdiscount_p"
                    value=" <?php echo $row->discount ?> " readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>

                <!-- <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT</span>
                  </div>
                  <input type="text" class="form-control" name="discount_h" id="discount_h"   >
                  <div class="input-group-append">
                    <span class="input-group-text"><?php echo $USD_txt ?></span>
                  </div>
                </div> -->

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtdiscount" id="txtdiscount_n" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa"
                        style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>


                <hr style="height:2px; border-width:0; color:black; background-color:black;">

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TOTAL(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" name="txttotal" id="txttotall" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa"
                        style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TOTAL(<?php echo $Change ?>)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" name="txttotalkhr" id="txttotall_khr"
                    readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa"
                        style="font-size: 24px;"><?php echo $Change_rea ?></i></span>
                  </div>
                </div>

                <hr style="height:2px; border-width:0; color:black; background-color:black;">

                <div class="icheck-success d-inline">
                  <input type="radio" name="rb" value="Cash" checked id="radioSuccess1">
                  <label for="radioSuccess1">
                    CASH
                  </label>
                </div>
                <div class="icheck-primary d-inline">
                  <input type="radio" name="rb" value="QR" id="radioSuccess2">
                  <label for="radioSuccess2">
                    QR <i class="fas fa-qrcode"></i>
                  </label>
                </div>
                <!-- <div class="icheck-danger d-inline">
                          <input type="radio" name="rb" value="Check" id="radioSuccess3">
                          <label for="radioSuccess3">
                            CHECK
                          </label>
                        </div> -->
                <hr style="height:2px; border-width:0; color:black; background-color:black;">


                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Change(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtdue" id="txtdue" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"> <i class="fa"
                        style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Change(<?php echo $Change ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtduekh" id="txtduekh" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"> <i class="fa"
                        style="font-size: 24px;"><?php echo $Change_rea ?></i></span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Paid Amount(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtpaid" id="txtpaid" value="0">
                  <div class="input-group-append">
                    <span class="input-group-text"> <i class="fa"
                        style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>
                <hr style="height:2px; border-width:0; color:black; background-color:black;">

                <div class="card-footer">

                  <div class="text-center">
                    <button type="submit" class="btn btn-success" name="btnsaveorder">Save order</button>
                  </div>
                </div>

              </div>

            </div>
          </div>



        </div>


      </div>

      </form>

    </div>
    <!-- /.col-md-6 -->
  </div>
  <!-- /.row -->
</div><!-- /.content-fluid -->