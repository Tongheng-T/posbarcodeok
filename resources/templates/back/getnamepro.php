<?php

include_once '../../config.php';


$barcode = $_GET["id"] ?? null;
$aus = $_SESSION['aus'] ?? null;
$pid = null;

if ($barcode && $aus) {
    $query = query("SELECT * FROM tbl_product WHERE aus=? AND barcode=?", [$aus, $barcode]);
    if ($query && $query->rowCount() > 0) {
        $roww = fetch_assoc($query);
        $pid = $roww['pid'];
    }
}
?>

<option value="" disabled <?= !$pid ? "selected" : "" ?>>Select Product</option>

<?php
$select = query("SELECT * FROM tbl_product WHERE aus=? ORDER BY pid DESC", [$aus]);
while ($row = fetch_assoc($select)) {
    $selected = ($row['pid'] == $pid) ? "selected" : "";
    ?>
    <option value="<?= htmlspecialchars($row['pid']) ?>" <?= $selected ?>>
        <?= htmlspecialchars($row['product']) ?>
    </option>
    <?php
}
?>