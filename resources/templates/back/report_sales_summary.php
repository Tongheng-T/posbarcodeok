<?php


if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "User") {

    header('location:../');
}



$aus = $_SESSION['aus'];

?>


<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- 
Content Header (Page header) -->

<!-- /.content -->

<!-- Filter Form -->
<div class="card card-primary card-outline no-print">
    <div class="card-header">
        <h3 class="card-title">Sales Summary Report</h3>
    </div>

    <div class="card-body">
        <form method="get" class="form-inline">
            <input type="hidden" name="report_sales_summary" value="">
            <div class="form-group mr-3">
                <label for="start_date" class="mr-2">ចាប់ពីថ្ងៃ:</label>
                <input type="date" name="start_date" class="form-control" value="<?= $_GET['start_date'] ?? '' ?>">
            </div>
            <div class="form-group mr-3">
                <label for="end_date" class="mr-2">ដល់ថ្ងៃ:</label>
                <input type="date" name="end_date" class="form-control" value="<?= $_GET['end_date'] ?? '' ?>">
            </div>
            <div class="form-group mr-3">
                <label for="saler_id" class="mr-2">អ្នកលក់:</label>
                <select name="saler_id" class="form-control">
                    <option value="">-- ទាំងអស់ --</option>
                    <?php
                    $salers = query(" SELECT DISTINCT saler_id, saler_name FROM tbl_invoice WHERE aus =?", [$aus]);
                    foreach ($salers as $s): ?>
                        <option value="<?= $s->saler_id ?>" <?= ($_GET['saler_id'] ?? '') == $s->saler_id ? 'selected' : '' ?>>
                            <?= $s->saler_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary ml-2">🔍 Filter</button>
        </form>
    </div>


    <?php
    // $start_date = $_GET['start_date'] ?? '';
    // $end_date = $_GET['end_date'] ?? '';
    // $saler_id = $_GET['saler_id'] ?? '';

    // $where = "WHERE i.aus = '$aus'";
    // if ($start_date)
    //     $where .= " AND DATE(i.order_date) >= '$start_date'";
    // if ($end_date)
    //     $where .= " AND DATE(i.order_date) <= '$end_date'";
    // if ($saler_id)
    //     $where .= " AND i.saler_id = '$saler_id'";



    // $summary = query("SELECT 
    //     SUM(d.qty * d.original_price) AS total_cost,
    //     SUM(d.saleprice) AS total_sale,
    //     SUM(d.saleprice - d.qty * d.original_price) AS gross_profit,
    //     (
    //         SELECT SUM(discount + discount_h) 
    //         FROM tbl_invoice 
    //         WHERE aus = '$aus'
    //         " . ($start_date ? " AND DATE(order_date) >= '$start_date'" : "") . "
    //         " . ($end_date ? " AND DATE(order_date) <= '$end_date'" : "") . "
    //         " . ($saler_id ? " AND saler_id = '$saler_id'" : "") . "
    //     ) AS total_discount
    // FROM tbl_invoice_details d
    // JOIN tbl_invoice i ON i.invoice_id = d.invoice_id
    // WHERE i.aus = '$aus'
    // " . ($start_date ? " AND DATE(i.order_date) >= '$start_date'" : "") . "
    // " . ($end_date ? " AND DATE(i.order_date) <= '$end_date'" : "") . "
    // " . ($saler_id ? " AND i.saler_id = '$saler_id'" : "") . "")->fetch_assoc();

    // Net sale = Sale - Discount
    
    $sql = "SELECT 
        SUM(d.qty * d.original_price) AS total_cost,
        SUM(d.saleprice) AS total_sale,
        SUM(d.saleprice - d.qty * d.original_price) AS gross_profit,

        (
            SELECT SUM(discount + discount_h)
            FROM tbl_invoice
            WHERE aus = :aus
            " . ($start_date ? " AND DATE(order_date) >= :start_date" : "") . "
            " . ($end_date ? " AND DATE(order_date) <= :end_date" : "") . "
            " . ($saler_id ? " AND saler_id = :saler_id" : "") . "
        ) AS total_discount

    FROM tbl_invoice_details d
    JOIN tbl_invoice i ON i.invoice_id = d.invoice_id
    WHERE i.aus = :aus
    " . ($start_date ? " AND DATE(i.order_date) >= :start_date" : "") . "
    " . ($end_date ? " AND DATE(i.order_date) <= :end_date" : "") . "
    " . ($saler_id ? " AND i.saler_id = :saler_id" : "") . "
";

    $stmt = $pdo->prepare($sql);

    $params = [
        ':aus' => $aus
    ];
    if ($start_date)
        $params[':start_date'] = $start_date;
    if ($end_date)
        $params[':end_date'] = $end_date;
    if ($saler_id)
        $params[':saler_id'] = $saler_id;

    $stmt->execute($params);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);




    $total_free = query("SELECT SUM(free) AS total_free FROM tbl_invoice WHERE aus = '$aus'")->fetch_assoc();


    $free_items = query("SELECT 
        d.product_name,
        d.qty,
        d.original_price,
        d.saleprice,
        d.rate,
        i.invoice_id,
        i.free,
        i.order_date
    FROM tbl_invoice_details d
    JOIN tbl_invoice i ON i.invoice_id = d.invoice_id
    WHERE 
        i.aus = '$aus'
        AND i.free > 0 AND d.saleprice = i.free
        " . ($start_date ? " AND DATE(i.order_date) >= '$start_date'" : "") . "
        " . ($end_date ? " AND DATE(i.order_date) <= '$end_date'" : "") . "
        " . ($saler_id ? " AND i.saler_id = '$saler_id'" : "") . "
    ORDER BY i.order_date DESC
");



    ?>

    <div class="card card-warning card-outline mt-4">
        <div class="card-header">
            <h3 class="card-title">📦 បញ្ជីទំនិញឥតគិតថ្លៃ (Saleprice = 0)</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="bg-light">
                    <tr>
                        <th>ល.រ</th>
                        <th>ថ្ងៃ</th>
                        <th>លេខវិក័យប័ត្រ</th>
                        <th>ឈ្មោះទំនិញ</th>
                        <th>ចំនួន</th>
                        <th>តម្លៃដើម</th>
                        <th>តម្លៃលក់</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total_free_qty = 0;
                    $total_free_cost = 0;
                    $total_free_saleprice = 0;

                    while ($row = $free_items->fetch_assoc()):
                        $total_free_qty += $row['qty'];
                        $total_free_cost += $row['qty'] * $row['original_price'];
                        $total_free_saleprice += $row['qty'] * $row['rate'];
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $row['order_date'] ?></td>
                            <td><?= $row['invoice_id'] ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= $row['qty'] ?></td>
                            <td>$ <?= number_format(($row['original_price'] ?? 0), 2) ?></td>
                            <td class="text-danger">$ <?= number_format($row['saleprice'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-right">សរុប:</td>
                        <td><?= $total_free_qty ?></td>
                        <td>$ <?= number_format($total_free_cost, 2) ?> (តម្លៃដើម)</td>
                        <td>$ <?= number_format($total_free_saleprice, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php
    // Net sale = Sale - Discount
    
    $net_profit = $summary['gross_profit'] - $summary['total_discount'] - $total_free_saleprice;
    $net_sale = $summary['total_sale'] - $summary['total_discount'] - $total_free_saleprice;
    ?>
    <div class="col-lg-12">


        <div class="row">
            <div class="col-md-4">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">លក់សរុប</span>
                        <span class="info-box-number">$ <?= number_format($summary['total_sale'], 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">ចំណាយសរុប</span>
                        <span class="info-box-number">$ <?= number_format($summary['total_cost'], 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">ចំណេញសរុប (មុនបញ្ចុះ)</span>
                        <span class="info-box-number">$ <?= number_format($summary['gross_profit'], 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">បញ្ចុះតម្លៃសរុប</span>
                        <span class="info-box-number">$
                            <?php echo number_format($summary['total_discount'], 2); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-gift"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">ប្រាក់ឥតគិតថ្លៃ</span>
                        <span class="info-box-number">$ <?= number_format($total_free['total_free'], 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">លក់សុទ្ធបន្ទាប់ពីបញ្ចុះតម្លៃ-ឥតគិតថ្លៃ</span>
                        <span class="info-box-number">$ <?php echo number_format($net_sale, 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">ប្រាក់ចំណេញបន្ទាប់ពីបញ្ចុះ</span>
                        <span class="info-box-number">$ <?= number_format($net_profit, 2); ?></span>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<div class="mb-3 no-print">
    <a href="?report_sales_summary=1&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&saler_id=<?= urlencode($saler_id) ?>&export=excel"
        class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export to Excel
    </a>
    <button onclick="window.print();" class="btn btn-info">
        <i class="fas fa-print"></i> Print Report
    </button>
</div>
<?php
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=sales_summary_" . date("Ymd") . ".xls");
}

?>
<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ល.រ</th>
            <th>ឈ្មោះទំនិញ</th>
            <th>ចំនួនលក់</th>
            <th>តម្លៃនាំចូល</th>
            <th>តម្លៃលក់ (rate)</th>
            <th>ចំណាយសរុប</th>
            <th>លក់សរុប</th>
            <th>ចំណេញ</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        $detail = query("SELECT 
            d.product_name,
            SUM(d.qty) AS qty_sold,
            d.original_price,
            d.rate,
            SUM(d.qty * d.original_price) AS total_cost,
            SUM(d.saleprice) AS total_sale,
            SUM(d.saleprice - d.qty * d.original_price) AS profit
            FROM tbl_invoice_details d
            JOIN tbl_invoice i ON i.invoice_id = d.invoice_id
            $where
            GROUP BY d.product_id
            ORDER BY qty_sold DESC
        ");

        while ($row = $detail->fetch_object()) {
            echo "<tr>";
            echo "<td>{$i}</td>";
            echo "<td>{$row->product_name}</td>";
            echo "<td>{$row->qty_sold}</td>";
            echo "<td>$ " . number_format(($row->original_price ?? 0), 2) . "</td>";
            echo "<td>$ " . number_format($row->rate, 2) . "</td>";
            echo "<td>$ " . number_format(($row->total_cost ?? 0), 2) . "</td>";
            echo "<td>$ " . number_format($row->total_sale, 2) . "</td>";
            echo "<td style='color:green;'>$ " . number_format(($row->profit ?? 0), 2) . "</td>";
            echo "</tr>";
            $i++;
        }
        ?>
        <?php $service_summary = query("SELECT 
        SUM(d.saleprice) AS total_sale,
        SUM((d.saleprice - d.original_price)) AS total_profit
        FROM tbl_invoice_details d
        WHERE d.product_name LIKE '%ឈ្នួលជាង%'
        AND d.original_price = 0 AND d.aus='$aus' 
         " . ($start_date ? " AND DATE(d.order_date) >= '$start_date'" : "") . "
         " . ($end_date ? " AND DATE(d.order_date) <= '$end_date'" : "") . "
         " . ($saler_id ? " AND d.saler_id = '$saler_id'" : "") . "")->fetch_assoc();
        ?>

    <tfoot style="font-weight:bold; background-color:#f9f9f9;">
        <?php
        // Reuse already calculated $summary values
        ?>
        <tr>
            <td colspan="2" style="text-align: right;">សរុប:</td>
            <td>
                <?= number_format(array_sum(array_column(iterator_to_array($detail), 'qty_sold'))) ?>
            </td>
            <td></td>
            <td></td>
            <td>$ <?= number_format($summary['total_cost'], 2) ?></td>
            <td>$ <?= number_format($summary['total_sale'], 2) ?></td>
            <td style="color:green;">$ <?= number_format($summary['gross_profit'], 2) ?></td>
        </tr>
        <tr>
            <th colspan="2"> </th>
            <th style="color:red;"> ឈ្នួលជាង</th>
            <th style="color:red;">$ <?= number_format($service_summary['total_profit'], 2) ?></th>
            <th colspan="2" style="text-align:right;">បញ្ចុះតម្លៃសរុប:</th>

            <th style="color:red;">$ <?= number_format($summary['total_discount'], 2) ?></th>
            <th style="color:blue;">$ <?= number_format($net_profit, 2) ?></th>

        </tr>
        <tr>
            <th colspan="6" style="text-align:right;">លក់សុទ្ធបន្ទាប់ពីបញ្ចុះតម្លៃ:</th>
            <th style="color:blue;">$ <?= number_format($summary['total_sale'] - $summary['total_discount'], 2) ?></th>
            <th style="color:blue;">$ <?= number_format($net_profit - $service_summary['total_profit'], 2) ?></th>
        </tr>

    </tfoot>
    </tbody>
</table>
<!-- Auto print when page loads -->
<script>
    function autoPrintIfNeeded() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.print();
        }
    }
    window.onload = autoPrintIfNeeded;
</script>



<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">បង្ហាញស្ថានភាពស្តុក</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="bg-primary text-white">
                    <th>ល.រ</th>
                    <th>ឈ្មោះផលិតផល</th>
                    <th>ស្តុកបញ្ចូល</th>
                    <th>ចំនួនលក់</th>
                    <th>ស្តុកនៅសល់</th>
                    <th>តម្លៃនាំចូលសរុប</th>
                    <th>ចំនួនលុយឥវ៉ាន់នៅសល់</th> <!-- ✅ ថ្មី -->
                </tr>
            </thead>
            <tbody>
                <?php
                $total_cost_sold_all = 0;


                $i = 1;
                $grand_import_cost = 0;
                $grand_stock_value = 0;

                $products = query("SELECT pid, product, stock FROM tbl_product WHERE aus='$aus' ORDER BY product ASC");
                while ($p = $products->fetch_object()) {

                    // Stock imported
                    $import = query("SELECT SUM(stock) AS total_in, SUM(stock * price) AS total_cost, price
                                     FROM tbl_product_stock WHERE product_id = '$p->pid'");
                    $in = $import->fetch_assoc();
                    $total_in = $in['total_in'] ?? 0;
                    $total_cost = $in['total_cost'] ?? 0;

                    // Price per unit (if different prices were used, you might want to average them)
                    $unit_price = $total_in > 0 ? $total_cost / $total_in : 0;

                    // Stock sold
                    $sold = query("SELECT SUM(qty) AS total_out
                                   FROM tbl_invoice_details WHERE product_id = '$p->pid'");
                    $out = $sold->fetch_assoc();
                    $total_out = $out['total_out'] ?? 0;

                    // Current stock from tbl_product
                    $current_stock = $p->stock;

                    // Money value of remaining stock
                    $stock_value = $unit_price * $current_stock;

                    $grand_import_cost += $total_cost;
                    $grand_stock_value += $stock_value;
                    $total_cost_sold = $unit_price * $total_out;
                    $total_cost_sold_all += $total_cost_sold;

                    echo "<tr>";
                    echo "<td>" . ($i++) . "</td>";
                    echo "<td>$p->product</td>";
                    echo "<td>$total_in</td>";
                    echo "<td>$total_out</td>";
                    echo "<td class='text-success'>$current_stock</td>";
                    echo "<td>$ " . number_format($total_cost, 2) . "</td>";
                    echo "<td>$ " . number_format($stock_value, 2) . "</td>"; // ✅ ថ្មី
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold;" class="bg-light">
                    <td colspan="5" class="text-right">សរុប:</td>
                    <td>$ <?php echo number_format($grand_import_cost, 2); ?></td>
                    <td>$ <?php echo number_format($grand_stock_value, 2); ?></td>
                </tr>
                <tr style="font-weight: bold;" class="bg-warning text-dark">
                    <td colspan="6" class="text-right">តម្លៃនាំចូលសម្រាប់ផលិតផលដែលបានលក់:</td>
                    <td>$ <?php echo number_format($total_cost_sold_all, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>




<div class="card card-danger card-outline">
    <div class="card-header">
        <h3 class="card-title">ប្រាក់ចំណេញសេវា "ឈ្នួលជាង"</h3>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between">
                <span>តម្លៃលក់សរុប:</span>
                <strong>$ <?php echo number_format($service_summary['total_sale'], 2); ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>ប្រាក់ចំណេញសរុប:</span>
                <strong class="text-success">$
                    <?php echo number_format($service_summary['total_profit'], 2); ?></strong>
            </li>
        </ul>
    </div>
</div>





<?php


$result = query("SELECT 
    p.pid AS product_id,
    p.product AS product_name,
    p.image AS product_image, -- ✅ រូបភាព
    p.stock AS product_stock,
    IFNULL(stock_in.total_qty, 0) AS total_stock_in,
    IFNULL(stock_out.total_qty, 0) AS total_stock_out,
    (IFNULL(stock_in.total_qty, 0) - IFNULL(stock_out.total_qty, 0)) AS calculated_stock,
    
    p.purchaseprice AS product_purchase_price,
    last_stock.price AS last_stock_price,
    
    p.stock * p.purchaseprice AS total_stock_value_product,
    (IFNULL(stock_in.total_qty, 0) - IFNULL(stock_out.total_qty, 0)) * p.purchaseprice AS total_stock_value_real,

    CASE 
        WHEN p.stock != (IFNULL(stock_in.total_qty, 0) - IFNULL(stock_out.total_qty, 0))
            THEN 'Stock count mismatch'
        WHEN p.purchaseprice != last_stock.price
            THEN 'Purchase price mismatch'
        ELSE NULL
    END AS issue

FROM tbl_product p

LEFT JOIN (
    SELECT product_id, SUM(stock) AS total_qty
    FROM tbl_product_stock
    GROUP BY product_id
) stock_in ON stock_in.product_id = p.pid

LEFT JOIN (
    SELECT product_id, SUM(qty) AS total_qty
    FROM tbl_invoice_details
    GROUP BY product_id
) stock_out ON stock_out.product_id = p.pid

LEFT JOIN (
    SELECT ps.product_id, ps.price
    FROM tbl_product_stock ps
    JOIN (
        SELECT product_id, MAX(date) AS last_date
        FROM tbl_product_stock
        GROUP BY product_id
    ) latest ON latest.product_id = ps.product_id AND latest.last_date = ps.date
) last_stock ON last_stock.product_id = p.pid

WHERE 
    ( p.stock != (IFNULL(stock_in.total_qty, 0) - IFNULL(stock_out.total_qty, 0))
      OR p.purchaseprice != last_stock.price )
    AND p.aus = '$aus' ORDER BY p.pid DESC
"); // ⬅️ ដាក់ SQL ពីខាងលើ


?>

<h3>🔍 បញ្ជីទំនិញមានបញ្ហា</h3>
<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>រូបភាព</th> <!-- ✅ រូបភាព -->
            <th>ឈ្មោះ</th>
            <th>Stock</th>
            <th>Stock IN</th>
            <th>Stock OUT</th>
            <th>គណនាស្តុក</th>
            <th>តម្លៃដើម</th>
            <th>តម្លៃចុងក្រោយ</th>
            <th>សរុបតម្លៃ</th>
            <th>បញ្ហា</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="<?= $row['issue'] ? 'error' : '' ?>">
                <td><?= $row['product_id'] ?></td>
                <td>
                    <?php if (!empty($row['product_image'])): ?>
                        <img src="../productimages/<?= $row['product_image'] ?>" alt="img" style="width: 50px; height: 50px;">
                    <?php else: ?>
                        <span>—</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['product_stock'] ?></td>
                <td><?= $row['total_stock_in'] ?></td>
                <td><?= $row['total_stock_out'] ?></td>
                <td><?= $row['calculated_stock'] ?></td>
                <td><?= number_format($row['product_purchase_price'], 2) ?></td>
                <td><?= number_format($row['last_stock_price'], 2) ?></td>
                <td><?= number_format($row['total_stock_value_product'], 2) ?></td>
                <td><strong><?= $row['issue'] ?></strong></td>
            </tr>

        <?php endwhile; ?>
    </tbody>
</table>