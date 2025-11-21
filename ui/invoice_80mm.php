<?php require_once("../resources/config.php"); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="../dist/css/print80mmm.css">

    <title>RECEIPT : <?php show_customer_name(); ?></title>
</head>
<?php
$id = $_GET['id'];
$select = query("SELECT * from tbl_invoice where invoice_id =$id");
confirm($select);
$row = $select->fetch_object();
$dd = $row->order_date;

function show_customer_name()
{
    $id = $_GET['id'];
    $select = query("select * from tbl_invoice where invoice_id = $id");
    confirm($select);
    $row = $select->fetch_object();
    $order_date = $row->order_date;
    $invoice_id = $row->invoice_id;
    echo 'N0 ' . $invoice_id . ' _ ' . $order_date;
}

?>

<body>
    <div class="ticket">
        <div class="logo">
            <img src="../ui/logo/logo.png" alt="Logo" class="img-fluid">
            <h5 class="h5">TONGHENG_T MART</h5>
        </div>

        <p class="centered">
            <br>Address: Dambea - Tboung Khmum Province
            <br>Email Address: Tongheng@mgail.com
            <!-- <br>fackbook: សហគមន៍កសិកម្មតំបែររុងរឿង -->
            <br>Phone Number: 00718989726
            <br>
        </p>
        <hr>
        <h3 class="RECEIPT">RECEIPT</h3>
        </p>
        <p class="h_p">INVOICE N0:
        <p class="top_p"><?php echo $row->invoice_id ?></p>
        </p>
        <p class="h_p">Date:
        <p class="top_p"><?php echo date('d-m-Y', strtotime($dd)) ?></p>
        </p>
        <table class="table">
            <thead>
                <tr>
                    <th>PRODUCT</th>
                    <th>QTY</th>
                    <th>PRICE</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select = query("SELECT * from tbl_invoice_details where invoice_id =$id");
                confirm($select);
                while ($item = $select->fetch_object()) {

                    echo '
                  <tr>
                  <td>' . $item->product_name . '</td>
                  <td>' . $item->qty . '</td>
                  <td>' . $item->rate . '</td>
                  <td>' . $item->rate * $item->qty . ' <b style="font-size: 16px;">&#x17DB </b></td>
                 </tr>
                    
                  ';
                }
                $discount_rs = $row->discount / 100;
                $discount_rs = $discount_rs * $row->subtotal;

                $sgst_rs = $row->sgst / 100;                // 2.5/100
                $sgst_rs = $sgst_rs * $row->subtotal;

                $cgst_rs = $row->cgst / 100;
                $cgst_rs = $cgst_rs * $row->subtotal;

                ?>


                <tr>
                    <td></td>
                    <th colspan="2">SUBTOTAL</th>
                    <th><?php echo number_format($row->subtotal) ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
                <tr>
                    <td></td>
                    <th colspan="2">DISCOUNT %</th>
                    <th><?php echo number_format($row->discount) ?> % </th>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">DISCOUNT (KHR)</td>
                    <td><?php echo number_format($discount_rs) ?> <b style="font-size: 16px;">&#x17DB </b></td>

                </tr>
                <!-- <tr>
                    <td></td>
                    <th colspan="2">SGST %</th>
                    <th><?php echo number_format($row->sgst) ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
                <tr>
                    <td></td>
                    <th colspan="2">CGST %</th>
                    <th><?php echo number_format($row->cgst) ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
                <tr>
                    <td></td>
                    <th colspan="2">SGST(KHR)</th>
                    <th><?php echo number_format($sgst_rs) ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
                <tr>
                    <td></td>
                    <th colspan="2">CGST(KHR)</th>
                    <th><?php echo number_format($cgst_rs) ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr> -->
                <tr>
                    <td></td>
                    <th colspan="2">TOTAL (HKR)</th>
                    <th><?php echo $row->total ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
                <tr>
                    <td></td>
                    <th colspan="2">ប្រាក់បានទទួល</th>
                    <th><?php echo $row->paid ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
                <tr>
                    <td></td>
                    <th colspan="2">ប្រាក់អាប់ឫខ្វះ</th>
                    <th><?php echo $row->due ?> <b style="font-size: 16px;">&#x17DB </b></th>
                </tr>
            </tbody>
        </table>

        <b>Importan Notice:</b> <br>
        <p>No item will be replaced or refunded if you don have the invoice with you. </p>
    </div>

    <script>
        window.print();
    </script>

</body>

</html>