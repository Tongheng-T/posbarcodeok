<?php require_once("../resources/config.php"); ?>

<?php

if ($_SESSION['role'] == "Admin") {
    include_once(TEMPLATE_BACK . "/header.php");
} else {
    include_once(TEMPLATE_BACK . "/headeruser.php");
}
?>


<?php check_login();

if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "User") {
    header("Location: ../");
}

if (!empty($_SESSION['message'])) {
    display_message();
}
?>


<div class="content-wrapper">


    <?php

    if ($_SERVER['REQUEST_URI'] == "/ui/" || $_SERVER['REQUEST_URI'] == "/ui/itemt") {

        include(TEMPLATE_BACK . "/dashboard.php");
    }

    if (isset($_GET['dashboard'])) {

        include(TEMPLATE_BACK . "/dashboard.php");
    }
    if (isset($_GET['category'])) {

        include(TEMPLATE_BACK . "/category.php");
    }
    if (isset($_GET['addproduct'])) {

        include(TEMPLATE_BACK . "/addproduct.php");
    }
    if (isset($_GET['Add_stock'])) {

        include(TEMPLATE_BACK . "/Add_stock.php");
    }
    if (isset($_GET['stock_list'])) {

        include(TEMPLATE_BACK . "/stock_list.php");
    }
    if (isset($_GET['productlist'])) {

        include(TEMPLATE_BACK . "/productlist.php");
    }
    if (isset($_GET['viewproduct'])) {

        include(TEMPLATE_BACK . "/viewproduct.php");
    }
    if (isset($_GET['editproduct'])) {

        include(TEMPLATE_BACK . "/editproduct.php");
    }
    if (isset($_GET['printbarcode'])) {

        include(TEMPLATE_BACK . "/printbarcode.php");
    }
    if (isset($_GET['taxdis'])) {

        include(TEMPLATE_BACK . "/taxdis.php");
    }

    if (isset($_GET['pos'])) {

        include(TEMPLATE_BACK . "/pos.php");
    }
    if (isset($_GET['orderlist'])) {

        include(TEMPLATE_BACK . "/orderlist.php");
    }

    if (isset($_GET['editorderpos'])) {

        include(TEMPLATE_BACK . "/editorderpos.php");
    }
    if (isset($_GET['editstock'])) {

        include(TEMPLATE_BACK . "/editeditstock.php");
    }
    if (isset($_GET['tablereport'])) {

        include(TEMPLATE_BACK . "/tablereport.php");
    }
    if (isset($_GET['graphreport'])) {

        include(TEMPLATE_BACK . "/graphreport.php");
    }
    if (isset($_GET['orderlist_user'])) {

        include(TEMPLATE_BACK . "/orderlist copy.php");
    }



    if (isset($_GET['registration'])) {
        include(TEMPLATE_BACK . "/registration.php");
    }
    if (isset($_GET['settings'])) {

        include(TEMPLATE_BACK . "/settings.php");
    }
    if (isset($_GET['logout'])) {

        include(TEMPLATE_BACK . "/logout.php");
    }
    
    ?>



</div>


<!-- /#page-wrapper -->
<?php include(TEMPLATE_BACK . "/footer.php"); ?>