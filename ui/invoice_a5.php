<?php require_once("../resources/config.php"); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../dist/css/printa5s.css">
    <link type="text/css" rel="stylesheet" href="../dist/css/no-print.css" media="print">
    <title>វិក័យប័ត្រ - ស្រូយ លាងសួគ៌: <?php show_customer_name(); ?></title>
</head>

<?php
$id = $_GET['id'];
$aus = $_SESSION['aus'];
$invoice = query("SELECT * FROM tbl_invoice WHERE aus = ? and invoice_id = ?", [$aus, $id]);
$row = $invoice->fetch(PDO::FETCH_OBJ);
function show_customer_name()
{
    $id = $_GET['id'];
    $select = query("SELECT * from tbl_invoice where invoice_id = $id");
    confirm($select);
    $row = $select->fetch(PDO::FETCH_OBJ);
    $order_date = $row->order_date;
    $invoice_id = $row->receipt_id;
    $name = $row->buyer;
    echo $invoice_id . ' _ ' . $order_date . '_' . $name;
}
$select_logo = query("SELECT * from tbl_logo where aus='$aus'");
$rowg = $select_logo->fetch(PDO::FETCH_OBJ);
?>

<body>
    <div class="ticket">
        <div class="header">
            <div class="logos">
                <img src="../ui/logo/scgs.png" class="logo-left" alt="SCG Logo">
                <h2 class="store-name">ស្រូយ លាងសួគ៌</h2>

                <img src="../ui/logo/toas.png" class="logo-right" alt="TOA Logo">

            </div>
            <p class="store-desc">មានលក់ គ្រឿងសំណង់ គ្រប់ប្រភេទ</p>
            <p class="left">
                <br>ភូមិកំពង់រាំង ឃុំសេដា
                <br>ស្រុកតំបែរ ខេត្តត្បូងឃ្មុំ

            </p>
            <div class="store-contact">
                <p><span class="label">Tel:</span> 097 78 88 781</p>
                <p><span class="label"> :</span> 097 96 67 067</p>
                <p><span class="label"> :</span> 071 34 33 434</p>
            </div>

        </div>

        <div class="invoice-title">
            <h3>វិក័យប័ត្រ<br><span>INVOICE</span></h3>
        </div>

        <div class="info-row">
            <p>ឈ្មោះអតិថិជន: <span><?php echo htmlspecialchars($row->buyer ?? ''); ?></span></p>
            <p>កាលបរិច្ឆេទ: <span><?php echo date('d/m/Y', strtotime($row->order_date)); ?></span></p>
            <p>លេខវិក័យប័ត្រ: <span><?php echo $row->receipt_id; ?></span></p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ល.រ<br><small>No.</small></th>
                    <th>មុខទំនិញ<br><small>Items</small></th>
                    <th>បរិមាណ<br><small>Qty</small></th>
                    <th>តម្លៃ<br><small>Unit Price</small></th>
                    <th>សរុប<br><small>Amount</small></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $change = query("SELECT * from tbl_change where aus='$aus'");
                confirm($change);
                $row_exchange = $change->fetch(PDO::FETCH_OBJ);
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
                    $discount_rs = $row->discountp / 100;
                    $discount_rs = $discount_rs * $row->subtotal;
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

                    $discount_rs = $row->discountp / 100;
                    $discount_rs = $discount_rs * $row->subtotal;
                    $discount_rs = $discount_rs * $exchange;
                }

                $details = query("SELECT * FROM tbl_invoice_details WHERE invoice_id = ?", [$id]);
                $no = 1;
                while ($item = $details->fetch(PDO::FETCH_OBJ)) {
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
                    echo "
                     <tr>
                       <td>{$no}</td>
                       <td>{$item->product_name}</td>
                       <td>{$item->qty}</td>
                       <td>" . $saleprice . "</td>
                       <td>" . $totaldb . "</td>
                     </tr>
                   ";
                    $no++;
                }

                $sho_dis = $discount_rs + $row->discount_h;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">សរុប / Total</th>
                    <th><?php echo $sho_dis; ?><?php echo $USD_usd ?></th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">សរុប / Total</th>
                    <th><?php echo $total; ?> <?php echo $USD_usd ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="buyer">
                អ្នកទិញ / The Buyer
                <div class="sign-line"></div>
            </div>
            <div class="seller">
                អ្នកលក់ / The Seller
                <div class="sign-line"></div>
            </div>
        </div>
        <div id="receipt-footer">
            <p class="foorece">Thank You!!!</>
            <p>Power by thpos.store</p>
        </div>
        <div id="buttons">
            <a href="/restaurant_pos/posbarcode%20-%20Copy/ui/itemt?pos">
                <button class="btn btn-back">
                    Back to Cashier
                </button>
            </a>
            <button class="btn btn-print" type="button" onclick="window.print(); return false;">
                Print
            </button>
        </div>
    </div>

    <!-- <script>window.print();</script> -->
</body>
    <script>
        window.print();
    </script>

</html>