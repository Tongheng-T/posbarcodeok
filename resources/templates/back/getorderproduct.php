<?php
include_once '../../config.php';

$id = $_GET['id'];
$rows = [];
$select = query("SELECT * from tbl_invoice_details a INNER JOIN tbl_product b ON a.product_id = b.pid where a.invoice_id='$id'");
confirm($select);

while($row_invoice_details = fetch_assoc($select)) {
    $rows[] = $row_invoice_details;
}

header('Content-Type: application/json');

echo json_encode($rows);

?>