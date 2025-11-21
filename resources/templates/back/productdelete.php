<?php
include_once '../../config.php';

if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "User") {
    header("Location: ../../");
}

$id = $_POST['pidd'];
$select = query("SELECT * FROM tbl_product where pid='$id'");
confirm($select);
$row =  fetch_object($select);
$image = $row->image;
$delete = query("DELETE from tbl_product where pid =$id");
confirm($delete);

if ($delete) {
    unlink("../../../productimages/$image");
} else {

    echo "Error in deleting product";
}

?>