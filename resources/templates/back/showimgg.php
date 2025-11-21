<?php

include_once '../../config.php';


$aus = $_SESSION['aus'];
$response = [];

// Find product by barcode or pid
if (!empty($_GET["id"])) {
    $barcode = $_GET["id"];
    $query = query("SELECT * FROM tbl_product WHERE barcode=? AND aus=?", [$barcode , $aus]);

    if ($query->rowCount() == 1) {
        $response = $query->fetch(PDO::FETCH_ASSOC);
    }

} elseif (!empty($_GET["pid"])) {
    $id = $_GET["pid"];
    $query = query("SELECT * FROM tbl_product WHERE pid=? AND aus=?", [$id ,$aus]);

    if ($query->rowCount() == 1) {
        $response = $query->fetch(PDO::FETCH_ASSOC);
    }
}

// get exchange info
$change = query("SELECT * FROM tbl_change WHERE aus = ?", [$aus]);
if ($change->rowCount() > 0) {
    $row_exchange = $change->fetch(PDO::FETCH_ASSOC);
    $response["exchange"] = $row_exchange["exchange"];
    $response["usd_or_real"] = $row_exchange["usd_or_real"];
} else {
    $response["exchange"] = null;
    $response["usd_or_real"] = null;
}

// return JSON
header('Content-Type: application/json');
echo json_encode($response);
