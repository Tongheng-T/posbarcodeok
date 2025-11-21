<?php

check_admin_session();

display_message();

$aus = $_SESSION['aus'];
?>



<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">

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
            <h5 class="m-0">Product List :</h5>
            <form method="get" class="form-inline mb-3">
              <input type="hidden" name="stock_list">
              <label for="date" class="mr-2">ជ្រើសរើសថ្ងៃ:</label>
              <input type="text" id="date" name="date" class="form-control"
                value="<?php echo $_GET['date'] ?? date('d-m-Y'); ?>">

              <button type="submit" class="btn btn-primary">Filter</button>
            </form>
          </div>
          <?php
          // ទាញថ្ងៃដែលមានទិន្នន័យ
          $dates = query("SELECT DISTINCT DATE(date) as d ,aus
                FROM tbl_product_stock  WHERE aus = ? ORDER BY d DESC", [$aus]);
          $available_dates = [];
          while ($row = $dates->fetch(PDO::FETCH_ASSOC)) {
            $available_dates[] = date("d-m-Y", strtotime($row['d']));
          }

          ?>



          <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

          <script>
            var availableDates = <?= json_encode($available_dates) ?>;

            flatpickr("#date", {
              dateFormat: "d-m-Y",
              onDayCreate: function (dObj, dStr, fp, dayElem) {
                let date = fp.formatDate(dayElem.dateObj, "d-m-Y");
                if (availableDates.includes(date)) {
                  // បន្ថែមចំណុចនៅក្រោមថ្ងៃ
                  dayElem.innerHTML += "<span style='color:red;'>•</span>";
                }
              }
            });
          </script>

            <!-- table-hover -->
          <div class="card-body">
            <table id="table_product" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <td>No</td>
                  <td>Id</td>
                  <td>Product</td>
                  <td>Date</td>
                  <td>Stock</td>
                  <td>PurchasePrice</td>
                  <td>Total</td>
                  <td>Image</td>
                  <td>ActionIcons</td>

                </tr>

              </thead>

              <tbody>

                <?php stock_list(); ?>

              </tbody>

            </table>


          </div>
        </div>

      </div>
      <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

 <script src="../plugins/jquery/jquery.min.js"></script>


<script>
  $(document).ready(function() {
    $('#table_product').DataTable();
  });
</script>