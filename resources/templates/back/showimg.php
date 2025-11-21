<?php

include_once '../../config.php';

$aus = $_SESSION['aus'];
if (!empty($_GET["id"])) {
    $barcode = $_GET["id"];
    $query = query("SELECT * FROM tbl_product WHERE barcode=? AND aus=?", [$barcode, $aus]);

    if ($query->rowCount() == 1) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        $img = $row->image;
        echo '<img width="300" src="../productimages/' . htmlspecialchars($img) . '" alt="">';
    }

} elseif (!empty($_GET["pid"])) {

    $id = $_GET["pid"];
    $query = query("SELECT * FROM tbl_product WHERE pid=? AND aus=?", [$id, $aus]);

    if ($query->rowCount() == 1) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        $img = $row->image;
        echo '<img width="300" src="../productimages/' . htmlspecialchars($img) . '" alt="">';
    }
}
