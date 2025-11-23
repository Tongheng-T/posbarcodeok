<?php


if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "User") {

    header('location:../');
}



$aus = $_SESSION['aus'];

?>

<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- 
Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Total number of items purchased since the beginning</h1>
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
                        <h5 class="m-0">Earning By Date</h5>
                    </div>

                    <div class="row">

                        <?php
                        // Export to Excel (if user clicks export)
                        if (isset($_GET['export']) && $_GET['export'] == 'excel') {
                            header("Content-Type: application/vnd-ms-excel");
                            header("Content-Disposition: attachment; filename=stock_report.xls");
                        }
                        $filter_category = $_GET['filter_category'] ?? '';
                        $start_date = $_GET['start_date'] ?? '';
                        $end_date = $_GET['end_date'] ?? '';


                        $filter_sql = "";
                        if (!empty($filter_category)) {
                            $filter_sql = " AND p.category = '" . esc($filter_category) . "' ";
                        }
                        // Filter date range
                        if (!empty($start_date)) {
                            $start_date_safe = mysqli_real_escape_string($connection, $start_date);
                            $filter_sql .= " AND s.date >= '{$start_date_safe}' ";  // Assume 's.date' is your date column
                        }
                        if (!empty($end_date)) {
                            $end_date_safe = mysqli_real_escape_string($connection, $end_date);
                            $filter_sql .= " AND s.date <= '{$end_date_safe}' ";
                        }

                        $sql = "SELECT p.product AS product_name, p.barcode, p.category, p.purchaseprice, p.saleprice, p.image, s.product_id, 
                        SUM(s.stock) AS total_stock, 
                        SUM(s.stock * s.price) AS total_value,
                        SUM(s.stock * p.saleprice) AS total_saleprice
                        FROM tbl_product_stock s
                        JOIN tbl_product p ON s.product_id = p.pid
                        WHERE s.aus = '{$aus}'{$filter_sql}
                        GROUP BY s.product_id ORDER BY p.product ASC";
                        $selectstock = query($sql);

                        ?>


                        <div class="card-body">

                            <form method="get" class="form-inline mb-3 no-print">
                                <input type="hidden" name="table_stocklis" value="">

                                <!-- Category Filter -->
                                <div class="form-group mr-2">
                                    <label for="filter_category" class="mr-2">ជ្រើសប្រភេទផលិតផល:</label>
                                    <select name="filter_category" id="filter_category" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- ទាំងអស់ --</option>
                                        <?php
                                        $categories = query("SELECT DISTINCT category FROM tbl_product WHERE aus=$aus ORDER BY category ASC");
                                        $current = $_GET['filter_category'] ?? '';
                                        while ($cat = $categories->fetch_object()) {
                                            $selected = $current === $cat->category ? 'selected' : '';
                                            echo "<option value=\"{$cat->category}\" {$selected}>{$cat->category}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Start Date -->
                                <div class="form-group mr-2">
                                    <label for="start_date" class="mr-2">ចាប់ពីថ្ងៃ:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
                                </div>

                                <!-- End Date -->
                                <div class="form-group mr-2">
                                    <label for="end_date" class="mr-2">ដល់ថ្ងៃ:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </form>



                            <!-- Export to Excel Button -->
                            <p class="no-print">
                                <button onclick="window.print()" style="padding: 8px 12px; background-color: #2196f3; color: white; border: none; border-radius: 4px;">
                                    🖨 Print Table
                                </button>
                                <a href="?table_stocklis&export=excel" style="padding: 8px 12px; background-color: green; color: white; text-decoration: none; border-radius: 4px;">Export to Excel</a>
                            </p>

                            <table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
                                <thead style="background-color: #f2f2f2;">
                                    <tr>
                                        <th>ល.រ</th>
                                        <th>product_id</th>
                                        <th>ឈ្មោះផលិតផល</th>
                                        <th>Barcode</th>
                                        <th>Category</th>
                                        <th>Purchase Price</th>
                                        <th>Sale Price</th>
                                        <th>Image</th>
                                        <th>បរិមាណសរុប (Stock)</th>
                                        <th>តម្លៃសរុប (Stock × Price)</th>
                                        <th>(Stock × Sale)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $grand_total_value = 0;
                                    $saleprice_total = 0;

                                    while ($row = $selectstock->fetch_object()) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($i) . '</td>';
                                        echo '<td>' . htmlspecialchars($row->product_id) . '</td>';
                                        echo '<td>' . htmlspecialchars($row->product_name) . '</td>';
                                        echo '<td>' . htmlspecialchars($row->barcode) . '</td>';
                                        echo '<td>' . htmlspecialchars($row->category) . '</td>';
                                        echo '<td style="text-align: right;">$ ' . number_format($row->purchaseprice, 2) . '</td>';
                                        echo '<td style="text-align: right;">$ ' . number_format($row->saleprice, 2) . '</td>';

                                        // Image (if you store image URL or path in DB)
                                        echo '<td><img src="../productimages/' . htmlspecialchars($row->image) . '" alt="Product Image" style="width: 50px; height: auto;"></td>';

                                        echo '<td style="text-align: right;">' . number_format($row->total_stock) . '</td>';
                                        echo '<td style="text-align: right;">$ ' . number_format($row->total_value, 2) . '</td>';
                                        echo '<td style="text-align: right;">$ ' . number_format($row->total_saleprice, 2) . '</td>';
                                        echo '</tr>';

                                        $grand_total_value += $row->total_value;

                                        $saleprice_total += $row->total_saleprice;

                                        $tok = $saleprice_total - $grand_total_value;
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: bold; background-color: #f9f9f9;">
                                        <td colspan="2" style="text-align: right;">សរុបប្រាក់ចំណេញ (Profit Total):</td>
                                        <td style="text-align: right;">$ <?php echo number_format($tok, 2); ?></td>
                                        <td colspan="5" style="text-align: right;">សរុបតម្លៃ (Grand Total):</td>
                                        <td style="text-align: right;">$ <?php echo number_format($grand_total_value, 2); ?></td>
                                        <td style="text-align: right;">$ <?php echo number_format($saleprice_total, 2); ?></td>

                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <!--  -->



                    </div>

                </div>
            </div>

        </div>
        <!-- /.col-md-6 -->


    </div>
    <!-- /.row -->

</div>
<!-- /.content -->