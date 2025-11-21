<?php

if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "User") {

  header('location:../');
}




edit_product();

$id = $_GET['id'];
$aus = $_SESSION['aus'];
$select = query("SELECT * from tbl_product where pid=$id");
confirm($select);

$row = fetch_assoc($select);

$id_db = $row['pid'];

$barcode_db = $row['barcode'];
$product_db = $row['product'];
$category_db = $row['category'];
$description_db = $row['description'];
$stock_db = $row['stock'];
$purchaseprice_db = $row['purchaseprice'];
$saleprice_db = $row['saleprice'];
$image_db = $row['image'];

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


        <div class="card card-success card-outline">
          <div class="card-header">
            <h5 class="m-0">Edit Product</h5>
          </div>

          <form action="" method="post" name="formeditproduct" enctype="multipart/form-data">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">

                  <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control" value="<?php echo $barcode_db; ?>" placeholder="Enter Barcode" name="txtbarcode" autocomplete="off" disabled>
                  </div>

                  <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" class="form-control" value="<?php echo $product_db; ?>" placeholder="Enter Name" name="txtproductname" autocomplete="off" required>
                  </div>

                  <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" name="txtselect_option" required>
                      <option value="" disabled selected>Select Category</option>

                      <?php
                      $select = query("SELECT * from tbl_category order by catid desc");
                      confirm($select);

                      while ($row = fetch_assoc($select)) {
                        extract($row);

                      ?>
                        <option <?php if ($row['category'] == $category_db) { ?> selected="selected" <?php } ?>><?php echo $row['category']; ?></option>

                      <?php

                      }

                      ?>


                    </select>
                  </div>


                  <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" placeholder="Enter Description" name="txtdescription" rows="4" required><?php echo $description_db; ?> </textarea>
                  </div>

                </div>


                <div class="col-md-6">


                  <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" min="1" step="any" class="form-control" value="<?php echo $stock_db; ?>" placeholder="Enter Stock" name="txtstock" autocomplete="off" required>
                  </div>


                  <div class="form-group">
                    <label>Purchase Price (<?php echo $USD_usd?>)</label>
                    <input type="number" min="0" step="any" class="form-control" value="<?php echo $purchaseprice; ?>" placeholder="Enter Stock" name="txtpurchaseprice" autocomplete="off" required>
                  </div>

                  <div class="form-group">
                    <label>Sale Price (<?php echo $USD_usd?>)</label>
                    <input type="number" min="0.1" step="any" class="form-control" value="<?php echo $saleprice; ?>" placeholder="Enter Stock" name="txtsaleprice" autocomplete="off" required>
                  </div>


                  <div class="form-group">
                    <label>Product image</label><br />
                    <image src="../productimages/<?php echo $image_db; ?>" class="img-rounded" width="50px" height="50px/">


                      <input type="file" class="input-group" name="myfile">
                      <p>Upload image</p>
                  </div>

                </div>


              </div>

            </div>

            <div class="card-footer">
              <div class="text-center">
                <button type="submit" class="btn btn-success" name="btneditproduct">Update Product</button>
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