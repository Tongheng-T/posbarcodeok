<?php require_once("../resources/config.php"); ?>

<?php

if ($_SESSION['role'] == "Admin") {
    include_once(TEMPLATE_BACK . "/header.php");
} else {
    include_once(TEMPLATE_BACK . "/headeruser.php");
}
?>


<?php check_login();
if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "Admin") {
    header("Location: ../");
}
?>


<div class="content-wrapper">


    <?php

if ($_SERVER['REQUEST_URI'] == "/posbarcode/user/" || $_SERVER['REQUEST_URI'] == "/posbarcode/user/itemt") {

    include(TEMPLATE_BACK . "/pos.php");
}

if (isset($_GET['category'])) {

    include(TEMPLATE_BACK . "/category.php");
}
if (isset($_GET['pos'])) {

    include(TEMPLATE_BACK . "/pos.php");
}
if (isset($_GET['orderlist'])) {

    include(TEMPLATE_BACK . "/orderlist.php");
}

if (isset($_GET['registration'])) {

    include(TEMPLATE_BACK . "/registration.php");
}
if (isset($_GET['changepassword'])) {

    include(TEMPLATE_BACK . "/changepassword.php");
}
if (isset($_GET['logout'])) {

    include(TEMPLATE_BACK . "/logout.php");
}
if (isset($_GET['editorderpos'])) {

    include(TEMPLATE_BACK . "/editorderpos.php");
}

    
    ?>



</div>


<!-- /#page-wrapper -->
<?php include(TEMPLATE_BACK . "/footer.php"); ?>