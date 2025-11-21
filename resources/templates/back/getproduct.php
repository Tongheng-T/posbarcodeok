<?php


include_once '../../config.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(["error" => "no id"]);
    exit;
}

// Get main product
$stmt = query("SELECT * FROM tbl_product WHERE pid = ? OR barcode = ? LIMIT 1", [
    $id, 
    $id
]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode([]);
    exit;
}

$pid = $product['pid'];

// Get units for the product
$unit_stmt = query("SELECT unit_name, unit_price FROM tbl_product_unit WHERE pid = ?", [$pid]);
$units = $unit_stmt->fetchAll(PDO::FETCH_ASSOC);

// Attach units to product JSON
$product['units'] = $units;

// Return JSON
echo json_encode($product);
