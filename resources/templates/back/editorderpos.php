<?php


if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "") {

  header('location:../');
}


$id = $_GET["id"];
$aus = $_SESSION['aus'];

$select = query("SELECT * from tbl_invoice where aus='$aus' and invoice_id =$id");
confirm($select);
$row = fetch_assoc($select);

$order_date = date('Y-m-d', strtotime($row['order_date']));
$subtotalll     = $row['subtotal'];
$discount     = $row['discount'];
$discountp     = $row['discountp'];
$totalll        = $row['total'];
$paid         = $row['paid'];
$due          = $row['due'];
$payment_type = $row['payment_type'];


$select = query("SELECT * from tbl_invoice_details where aus='$aus' and  invoice_id=$id");
confirm($select);
$row_invoice_details = fetchAllObj($select);


if (isset($_POST['btnupdateorder'])) {

  //Steps for btnupdateorder button.

  // 1) Get values from text feilds and from array in variables.

  $txt_orderdate     = date('Y-m-d');
  $txt_subtotall      = $_POST['txtsubtotal'];
  $txt_discountp      = $_POST['txtdiscountp'];
  $txt_discount      = $_POST['txtdiscount'];
  $discount_h      = $_POST['discount_h'];


  $txt_totall         = $_POST['txttotal'];
  $txt_payment_type  = $_POST['rb'];
  $txt_due           = $_POST['txtdue'];
  $txt_paid          = $_POST['txtpaid'];

  /////


  $arr_pid     = $_POST['pid_arr'];
  $arr_barcode = $_POST['barcode_arr'];
  $arr_name    = $_POST['product_arr'];
  $arr_stock   = $_POST['stock_c_arr'];
  $arr_qty     = $_POST['quantity_arr'];
  $arr_price   = $_POST['price_c_arr'];
  $arr_total   = $_POST['saleprice_arr'];

  $saler_name = $_SESSION['username'];
  $saler_id = $_SESSION['userid'];

  $change = query("SELECT * from tbl_change where aus='$aus'");
  confirm($change);
  $row_exchange = fetch_object($change);
  $exchange = $row_exchange->exchange;
  $usd_or_real = $row_exchange->usd_or_real;
  if ($usd_or_real == "usd") {
    $txt_subtotal = $txt_subtotall;
    $txt_total = $txt_totall;
    $discountKHRk = $discount;
    // $arr_price = $arr_pricee;
    // $arr_total = $arr_totall;
    $exchangee = 1;
  } else {
    $txt_subtotal = $txt_subtotall / $exchange;
    $txt_total = $txt_totall / $exchange;
    $discountKHRk = $discount / $exchange;
    // $arr_price = $arr_pricee / $exchange;
    // $arr_total = $arr_totall / $exchange;
    $exchangee = $exchange;
  }

  // 2) Write update query for tbl_product add stock.

  foreach ($select as $product_invoice_details) {

    $updateproduct_stock = query("UPDATE tbl_product set stock=stock+" . $product_invoice_details['qty'] . " where pid='" . $product_invoice_details['product_id'] . "'");
    confirm($updateproduct_stock);
  }

  // 3) Write delete query for tbl_invoice_details table data where invoice_id =$id .

  $delete_invoice_details = query("DELETE from tbl_invoice_details where invoice_id =$id");
  confirm($delete_invoice_details);


  // 4) Write update query for tbl_invoice table data.

  $update_tbl_invoice = query("UPDATE tbl_invoice SET order_date='{$txt_orderdate}',subtotal='{$txt_subtotal}',discount='{$txt_discount}',discountp='{$txt_discountp}',discount_h='{$discount_h}',total='{$txt_total}',payment_type='{$txt_payment_type}',due='{$txt_due}',paid='{$txt_paid}',edit='{$saler_name}' where invoice_id=$id");
  confirm($update_tbl_invoice);
  $invoice_id = $id;

  if ($invoice_id != null) {

    // 5) Write select query for tbl_product table to get out stock value.
    for ($i = 0; $i < count($arr_pid); $i++) {

      $selectpdt = query("SELECT * from tbl_product where pid='" . $arr_pid[$i] . "'");
      confirm($selectpdt);

      while ($rowpdt = fetch_object($selectpdt)) {


        $db_stock[$i] = $rowpdt->stock;

        $rem_qty = $db_stock[$i] - $arr_qty[$i];

        if ($rem_qty < 0) {
          return "Order is not completed";
        } else {


          // 6) Write update query for tbl_product table to update stock values.
          $update = query("UPDATE tbl_product SET stock='$rem_qty' where pid='" . $arr_pid[$i] . "'");
          confirm($update);
        } //else end here

      }



      // 7) Write insert query for tbl_invoice_details for insert new records.

      $insert = query("INSERT into tbl_invoice_details (invoice_id,barcode,product_id,product_name,qty,rate,saleprice,order_date) values ('{$id}','{$arr_barcode[$i]}','{$arr_pid[$i]}','{$arr_name[$i]}','{$arr_qty[$i]}','{$arr_price[$i]}','{$arr_total[$i]}','{$txt_orderdate}')");
      confirm($insert);
    } //end for loop

    header('location:itemt?orderlist');
  } //1st if end
  //var_dump($arr_total);

}
ob_end_flush();


$select = query("SELECT * from tbl_taxdis where aus = '$aus'");
confirm($select);
$row = fetch_object($select);

$change = query("SELECT * from tbl_change where aus='$aus' ");
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
  $dueekk = $due / $exchange;
  $dueek = number_format($dueekk, 2);
  $duee = $due ;
} else {
  $dddd = '<span id="txttotal">0</span>៛';
  $USD_usd = " ៛";
  $Change = "USD";
  $Change_rea = "$";
  $USD_txt = "KHR";
  $dueee = $due / $exchange;
  $dueek = $due;
  $duee = number_format($dueee, 2);
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






<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">


        <div class="card card-danger card-outline">
          <div class="card-header">
            <h5 class="m-0">Edit Order POS</h5>
          </div>



          <div class="card-body">


            <div class="row">

              <div class="col-md-8">

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                  </div>
                  <input type="text" class="form-control" placeholder="Scan Barcode" autocomplete="off" name="txtbarcode" id="txtbarcode_id">
                </div>


                <form action="" method="post" name="">


                  <select id="product_id" class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;">
                    <option>Select OR Search</option><?php echo fill_product(); ?>

                  </select>
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
                    <span class="input-group-text">SUBTOTAL(<?php echo $USD_txt ?>) </span>
                  </div>
                  <input type="text" class="form-control" name="txtsubtotal" id="txtsubtotal_id" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa" style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>


                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT(%)</span>
                  </div>
                  <input type="text" class="form-control" name="txtdiscountp" id="txtdiscount_p" value=" <?php echo $row->discount ?> " readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT</span>
                  </div>
                  <input type="text" class="form-control" name="discount_h" id="discount_h"  >
                  <div class="input-group-append">
                    <span class="input-group-text"><?php echo $USD_txt ?></span>
                  </div>
                </div>


                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtdiscount" id="txtdiscount_n" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa" style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>

                <hr style="height:2px; border-width:0; color:black; background-color:black;">

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TOTAL(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" name="txttotal" id="txttotall" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa" style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TOTAL(<?php echo $Change ?>)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" name="txttotalkhr" id="txttotall_khr" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa" style="font-size: 24px;"><?php echo $Change_rea ?></i></span>
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
                  <input type="text" class="form-control" name="txtdue" id="txtdue" readonly value="<?php echo $dueek ?>">
                  <div class="input-group-append">
                    <span class="input-group-text"> <i class="fa" style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Change(<?php echo $Change ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtduekh" id="txtduekh" readonly value="<?php echo $duee ?>">
                  <div class="input-group-append">
                    <span class="input-group-text"> <i class="fa" style="font-size: 24px;"><?php echo $Change_rea ?></i></span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Paid Amount(<?php echo $USD_txt ?>)</span>
                  </div>
                  <input type="text" class="form-control" name="txtpaid" id="txtpaid" value="<?php echo $paid ?>">
                  <div class="input-group-append">
                    <span class="input-group-text"> <i class="fa" style="font-size: 24px;"><?php echo $USD_usd ?></i></span>
                  </div>
                </div>
                <hr style="height:2px; border-width:0; color:black; background-color:black;">

                <div class="card-footer">



                  <div class="text-center">
                    <button type="submit" class="btn btn-info" name="btnupdateorder">Update order</button>
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
</div><!-- /.container-fluid -->