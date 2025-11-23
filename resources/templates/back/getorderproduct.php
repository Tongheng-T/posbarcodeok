<?php
include_once '../../config.php';

header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? '';

if (!$id || !ctype_digit((string)$id)) {
    echo json_encode(["error" => "invalid id"]);
    exit;
}

$invoice_id = (int)$id;

// 1) Get invoice items + product info
$stmt = query("
    SELECT a.*, 
           b.product, 
           b.barcode,
           b.image,
           b.stock,
           b.pid
    FROM tbl_invoice_details a
    INNER JOIN tbl_product b ON a.product_id = b.pid
    WHERE a.invoice_id = ?
", [$invoice_id]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    // no items
    echo json_encode([]);
    exit;
}

// 2) collect unique pids to fetch units in one query (avoid N+1)
$pids = array_values(array_unique(array_column($rows, 'pid')));

// prepare placeholders for IN(...)
$placeholders = implode(',', array_fill(0, count($pids), '?'));

// 3) fetch all units for these pids
$unit_map = []; // pid => [ {unit_name, unit_price}, ... ]
if (count($pids) > 0) {
    $unit_stmt = query("
        SELECT pid, unit_name, unit_price
        FROM tbl_product_unit
        WHERE pid IN ($placeholders)
    ", $pids);

    $all_units = $unit_stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($all_units as $u) {
        $pid_key = $u['pid'];
        if (!isset($unit_map[$pid_key])) $unit_map[$pid_key] = [];
        $unit_map[$pid_key][] = [
            'unit_name'  => $u['unit_name'],
            'unit_price' => (float)$u['unit_price']
        ];
    }
}

// 4) attach units to each invoice row
foreach ($rows as &$row) {
    $pid = $row['pid'];
    $row['units'] = $unit_map[$pid] ?? [];
    // normalize numeric fields if you want (optional)
    if (isset($row['qty']))  $row['qty']  = (float)$row['qty'];
    if (isset($row['rate'])) $row['rate'] = (float)$row['rate'];
    if (isset($row['saleprice'])) $row['saleprice'] = (float)$row['saleprice'];
}

// 5) output JSON
$json = json_encode($rows, JSON_UNESCAPED_UNICODE);

if ($json === false) {
    // fallback error
    echo json_encode(["error" => "json_encode failed"]);
    exit;
}

echo $json;
