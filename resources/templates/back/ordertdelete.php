<?php

include_once '../../config.php';

if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "User") {
    header("Location: ../../../");
}


$id = $_POST['piddd'];


$select = query("SELECT * from tbl_invoice_details where invoice_id='$id'");
confirm($select);

foreach ($select as $product_invoice_details) {

    $updateproduct_stock = query("UPDATE tbl_product set stock=stock+" . $product_invoice_details['qty'] . " where pid='" . $product_invoice_details['product_id'] . "'");
    confirm($updateproduct_stock);
}


$delete = query("DELETE tbl_invoice, tbl_invoice_details from tbl_invoice INNER JOIN tbl_invoice_details ON tbl_invoice.invoice_id = tbl_invoice_details.invoice_id WHERE tbl_invoice.invoice_id='$id'");
confirm($delete);


if ($delete) {
} else {

    echo 'error: Failed to delete';
}
