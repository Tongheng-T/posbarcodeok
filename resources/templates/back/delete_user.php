<?php
include_once '../../config.php';
$id = $_GET['id'];

if (isset($id)) {

    $delete = query("DELETE from tbl_user where user_id =" . $id);
    confirm($delete);
    if ($delete) {

        set_message(' <script>
        Swal.fire({
        icon: "success",
        title: "Account deleted successfully"
        });
       </script>');
        redirect('../../../ui/itemt?registration');
    } else {
        set_message(' <script>
        Swal.fire({
        icon: "warning",
        title: "Account Is Not Deleted"
        });
       </script>');
        redirect('../../../ui/itemt?registration');
       
    }
}
