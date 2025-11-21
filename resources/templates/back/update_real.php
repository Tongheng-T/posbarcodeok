<?php

require_once("../../config.php");
?>




<div class="card card-success card-outline">

  <div class="card-body">

    <?php


    if (isset($_POST['val'])) {
      $id_branch = $_POST['val'];
      $aus = $_POST['aus'];
      $id = 1;
      $change = query("SELECT * from tbl_change where aus='$aus'");
      confirm($change);
      $row_exchange = fetch_object($change);
      $exchange = $row_exchange->exchange;
      $usd_or_real = $row_exchange->usd_or_real;


      // if ($id_branch == "usd") {
    
      //   $update_product = query("UPDATE tbl_product set saleprice=saleprice/$exchange,m_price=m_price/$exchange where aus='$aus'");
      //   confirm($update_product);
      // } else {
    
      //   $update_product = query("UPDATE tbl_product set saleprice=saleprice*$exchange ,m_price=m_price*$exchange where aus='$aus'");
      //   confirm($update_product);
      // }
    

      $update = query("UPDATE tbl_change set usd_or_real= '$id_branch' where aus=" . $aus);
      confirm($update);
    }
    ?>


  </div>
</div>