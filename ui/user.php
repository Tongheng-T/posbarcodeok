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

if ($_SERVER['REQUEST_URI'] == "/posbarcode/ui/" || $_SERVER['REQUEST_URI'] == "/posbarcode/ui/itemt") {

    include(TEMPLATE_BACK . "/dashboard.php");
}

if (isset($_GET['dashboard'])) {

    include(TEMPLATE_BACK . "/dashboard.php");
}
if (isset($_GET['category'])) {

    include(TEMPLATE_BACK . "/category.php");
}

if (isset($_GET['registration'])) {

    include(TEMPLATE_BACK . "/registration.php");
}
if (isset($_GET['logout'])) {

    include(TEMPLATE_BACK . "/logout.php");
}

    
    ?>



</div>


<!-- /#page-wrapper -->
<?php include(TEMPLATE_BACK . "/footer.php"); ?>