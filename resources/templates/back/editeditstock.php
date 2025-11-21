<?php

if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "User") {

  header('location:../');
}




update_stock();

$aus = $_SESSION['aus'];
$id = $_GET['id'];

$select = query("SELECT * from tbl_product_stock where id=$id");
confirm($select);
$row = fetch_assoc($select);

$product_id = $row['product_id'];
$stock = $row['stock'];
$purchaseprice_db = $row['price'];

$selectp = query("SELECT * from tbl_product where pid=$product_id");
$roww = fetch_assoc($selectp);
$saleprice_db = $roww['saleprice'];
$barcode = $roww['barcode'];

$change = query("SELECT * from tbl_change where aus='$aus'");
confirm($change);
$row_exchange = fetch_object($change);
$exchange = $row_exchange->exchange;
$usd_or_real = $row_exchange->usd_or_real;

if ($usd_or_real == "usd") {
  $USD_usd = "$";
  $saleprice = $saleprice_db;
  $purchaseprice = $purchaseprice_db;
} else {
  $USD_usd = "៛";
  $saleprice = $saleprice_db * $exchange;
  $purchaseprice = $purchaseprice_db * $exchange;

}
?>



  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1 class="m-0">Admin Dashboard</h1> -->
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Starter Page</li> -->
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">


        <div class="card card-primary card-outline">
          <div class="card-header">
            <h5 class="m-0">Product</h5>
          </div>


          <form action="" method="post" enctype="multipart/form-data">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">

                  <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control barcode" placeholder="Enter Barcode" id="barcode" name="txtbarcode" value="<?php echo $barcode; ?>" disabled>
                  </div>

                  <div class="form-group">
                    <label>Product Name</label>
                    <select id="pname"  class="form-control select2s" name="txtselect_option" disabled>
                      <option value="" disabled selected>Select Product</option>

                      <?php
                      $selectt = query("SELECT * from tbl_product where aus=$aus order by pid desc");
                      confirm($selectt);

                      while ($rowp = fetch_assoc($selectt)) {
  
                      ?>
                         <option value="<?php echo $rowp['pid']; ?>" <?php if ($rowp['pid'] == $product_id) { ?> selected="selected" <?php } ?> ><?php echo $rowp['product']; ?></option>

                      <?php

                      }

                      ?>


                    </select>
                  </div>

                  <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" min="1" step="any" class="form-control" placeholder="Enter Stock" name="txtstock" autocomplete="off" value="<?php echo $stock; ?>" required >
                  </div>

                  <div class="form-group">
                    <label>Purchase Price (<?php echo $USD_usd?>)</label>
                    <input type="number" min="0" step="any" class="form-control" placeholder="Enter Stock" name="txtpurchaseprice" autocomplete="off" value="<?php echo $purchaseprice; ?>" required>
                  </div>
                  <div class="form-group">
                    <label>Sale Price (<?php echo $USD_usd?>)</label>
                    <input type="number" min="0.1" step="any" class="form-control" placeholder="Enter Stock" name="txtsaleprice" autocomplete="off" value="<?php echo $saleprice; ?>" required>
                  </div>


                </div>




                <div class="col-md-6">

                <div class="imgpro" id="showimg">
                 



                </div>




                </div>

              </div>
            </div>

            <div class="card-footer">
              <div class="text-center">
                <button type="submit" class="btn btn-primary" value="<?php echo $id; ?>" name="btnupdates">Update</button>
              </div>
            </div>

          </form>

        </div>

      </div>
      <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
  
</div>
  <!-- /.content -->



