<?php require_once("../resources/config.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt - SaleID : <?php show_customer_name(); ?></title>
  <link rel='shortcut icon' href="../ui/logo/256.ico" type="image/x-icon">
  <link rel="icon" href="../ui/logo/32.ico" sizes="32x32">
  <link rel="icon" href="../ui/logo/48.ico" sizes="48x48">
  <link rel="icon" href="../ui/logo/96.ico" sizes="96x96">
  <link rel="icon" href="../ui/logo/256.ico" sizes="144x144">
  <link type="text/css" rel="stylesheet" href="../dist/css/receiptt.css" media="all">
  <link type="text/css" rel="stylesheet" href="../dist/css/no-printt.css" media="print">
</head>

<body>
  <?php
  $id = $_GET['id'];
  $aus = $_SESSION['aus'];
  $select = query("SELECT * from tbl_invoice where aus='$aus' and invoice_id =$id");
  confirm($select);
  $row = $select->fetch_object();
  $dd = $row->order_date;
  $invoice_id = $row->receipt_id;
  function show_customer_name()
  {
    $id = $_GET['id'];
    $select = query("select * from tbl_invoice where invoice_id = $id");
    confirm($select);
    $row = $select->fetch_object();
    $order_date = $row->order_date;
    $invoice_id = $row->invoice_id;
    echo  $invoice_id . ' _ ' . $order_date;
  }
  $select_logo = query("SELECT * from tbl_logo where aus='$aus'");
  $rowg = $select_logo->fetch_object();
  $name = $rowg->name;

  ?>

  <div id="wrapper">
    <div id="receipt-header">
      <br>
      <div class="logo">
      <img src="../productimages/logo/<?php echo $rowg->img ?> " alt="Logo" class="img-fluid">
      </div>
      <h3 id="resturant-name"><?php echo $name ?></h3>
      <p>Address: <?php echo $rowg->addres ?></p>
      <p>ផ្លូវលេខ <?php echo $rowg->road ?></p>
      <p>Tel: <?php echo $rowg->phone ?></p>
      <p>Reference Receipt: <strong><?php echo "00" . $invoice_id  ?></strong></p>
      <p>Date: <strong><?php echo date('d-m-Y', strtotime($dd)) ?></strong></p>
    </div>
    <div id="receipt-body">
      <table class="tb-sale-detail">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>


          <?php


          $change = query("SELECT * from tbl_change");
          confirm($change);
          $row_exchange = $change->fetch_object();
          $exchange = $row_exchange->exchange;
          $usd_or_real = $row_exchange->usd_or_real;

          if ($usd_or_real == "usd") {
            $USD_usd = "$";
            $Change_rea = "៛";

            $kh_or_us = number_format($row->total * $exchange) . $Change_rea;
            $kh_or_ustit = "សរុបរួមជារៀល";

            $subtotal = $USD_usd . number_format($row->subtotal, 2);
            $total = $USD_usd . number_format($row->total, 2);
            $paid = number_format($row->paid, 2);
            $due = number_format($row->due, 2);
          } else {
            $USD_usd = "៛";
            $subtotal = number_format($row->subtotal * $exchange) . $USD_usd;
            $total = number_format($row->total * $exchange) . $USD_usd;
            $paid = number_format($row->paid);
            
            $due = number_format($row->due);
            $Change = "$";
            $Change_rea = "$";
            $kh_or_ustit = "សរុបរួមជាដុល្លារ";
            $kh_or_us = $Change_rea . number_format($row->total, 2);
          }




          $select = query("SELECT * from tbl_invoice_details where invoice_id =$id");
          confirm($select);
          $countqty = 0;
          $no = 1;
          while ($item = $select->fetch_object()) {
            if ($usd_or_real == "usd") {
              $USD_usd = "$";
              $totaldb = $USD_usd . number_format($item->rate * $item->qty, 2);
              $saleprice = $item->rate;
            } else {
              $USD_usd = "៛";
              $totaldbb = $item->rate * $item->qty * $exchange;
              $totaldb = number_format($totaldbb) . $USD_usd;

              $saleprice = $item->rate * $exchange;
            }


            echo '
                <tr>
                <td width="30"> ' . $no . '</td>
                <td width="190"> ' . $item->product_name . '</td>
                <td width="40"> ' . $item->qty . '</td>
                <td width="55"> ' . $saleprice . '</td>
                <td width="65"> ' . $totaldb . ' </td>
               </tr>
                ';
            $no++;
            $countqty += $item->qty;
          }
          $discount_rs = $row->discountp / 100;
          $discount_rs = $discount_rs * $row->subtotal;

          ?>
        </tbody>
      </table>
      <table class="tb-sale-total">
        <tbody>
          <tr>
            <td>ចំនួនសរុប</td>
            <td><?php echo $countqty ?></td>
            <td>Total</td>
            <td><b><?php echo $subtotal ?> <span class="bb"></span></b></td>
          </tr>
          <tr>
            <td colspan="2">Discount %</td>
            <td colspan="2"><?php echo $row->discountp ?> % </span></td>
          </tr>
          <tr>
            <td colspan="2"><b style="font-size: 15px">សរុបរួម</b></td>
            <td colspan="2"><b style="font-size: 15px"><?php echo $total ?> </b></td>
          </tr>
          <tr>
            <td colspan="2"><b><?php echo $kh_or_ustit ?></b></td>
            <td colspan="2"><b><?php echo $kh_or_us ?> <span class="bb"> </span></b></td>
          </tr>
          <tr>
            <td colspan="2">ទឹកប្រាក់ទទួល</td>
            <td colspan="2"><?php echo $paid ?> <span class="bb"><?php echo $USD_usd ?> </span></td>
          </tr>
          <tr>
            <td colspan="2">ប្រាក់់អាប់</td>
            <td colspan="2"><?php echo $due ?> <span class="bb"><?php echo $USD_usd ?> </span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="receipt-footer">
      <p class="foorece">Thank You!!!</>
      <p>Power by thpos.store</p>
    </div>
    <div id="buttons">
      <a href="/posbarcode/user/itemt?pos">
        <button class="btn btn-back">
          Back to Cashier
        </button>
      </a>
      <button class="btn btn-print" type="button" onclick="window.print(); return false;">
        Print
      </button>
    </div>
  </div>
</body>

</html>