<?php



if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "User") {

  header('location:../');
}


display_message();
addstock();

$aus = $_SESSION['aus'];

$stmt = query("SELECT exchange, usd_or_real FROM tbl_change WHERE aus=? LIMIT 1", [$aus]);
$row_exchange = fetchOneObj($stmt);
$exchange = $row_exchange->exchange;
$usd_or_real = $row_exchange->usd_or_real;

// សញ្ញា currency
$USD_usd = ($usd_or_real == "usd") ? "$" : "៛";
?>
<style>
  .imgpro {
    border: 1px solid #3175b9;
    border-radius: 5px;

  }

  .imgpro img {
    margin-left: 36px;
    /* filter: blur(5px);
    filter: brightness(0.5);
    filter: contrast(200%);
    filter: grayscale(100%);
    filter: hue-rotate(90deg);
    filter: invert(75%);
    filter: opacity(35%); */
  }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Add Stock</h1>
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
                    <input type="text" class="form-control barcode" placeholder="Enter Barcode" id="barcode"
                      name="txtbarcode" autocomplete="off">
                  </div>

                  <div class="form-group">
                    <label>Product Name</label>
                    <select id="pname" class="form-control select2s" name="txtselect_option" required>
                      <option value="" disabled selected>Select Product</option>

                      <?php
                      $select = query("SELECT * FROM tbl_product WHERE aus=? ORDER BY pid DESC", [$aus]);
                      while ($row = fetch_assoc($select)) {


                        ?>
                        <option value="<?php echo $row['pid']; ?>">
                          <?php echo htmlspecialchars($row['product']); ?>
                        </option>
                        
                        <?php

                      }

                      ?>


                    </select>
                  </div>

                  <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" min="1" step="any" class="form-control" placeholder="Enter Stock"
                      name="txtstock" autocomplete="off" required>
                  </div>

                  <div class="form-group">
                    <label>Purchase Price (<?php echo $USD_usd ?>)</label>
                    <input type="number" min="0" step="any" class="form-control" placeholder="Enter Stock"
                      name="txtpurchaseprice" id="purchaseprice" autocomplete="off" required>
                  </div>
                  <div class="form-group">
                    <label>Sale Price (<?php echo $USD_usd ?>)</label>
                    <input type="number" min="0.1" step="any" class="form-control" placeholder="Enter Stock"
                      name="txtsaleprice" id="saleprice" autocomplete="off" required>
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
                <button type="submit" class="btn btn-primary" name="btnaddpro">Add Product</button>
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