<script>
  // Initialize Select2 Elements
$('.select2').select2();
$('.select2bs4').select2({ theme: 'bootstrap4' });

var productarr = [];

// ========= LOAD ORDER ITEMS ===========
$.ajax({
    url: "../resources/templates/back/getorderproduct.php",
    method: "GET",
    dataType: "json",
    data: { id: <?php echo $_GET['id'] ?> },
    success: function (data) {

        $.each(data, function (key, item) {

            // If product already exists in the table → increase qty only
            if (productarr.includes(item.product_id)) {

                let qty = parseInt($('#qty_id' + item.product_id).val()) + 1;
                $('#qty_id' + item.product_id).val(qty);

                let saleprice = qty * item.saleprice;

                let final_price = ('<?php echo $usd_or_real ?>' === 'usd')
                    ? saleprice
                    : saleprice * <?php echo $exchange ?>;

                $('#saleprice_id' + item.product_id).html(final_price);
                $('#saleprice_idd' + item.product_id).val(final_price);

                calculate(0, 0);
            }

            // Otherwise → add new row
            else {

                addTableRow(
                    item.product_id,
                    item.product_name,
                    item.qty,
                    item.rate,
                    item.saleprice,
                    item.stock,
                    item.barcode,
                    item.image,
                    item.units
                );

                productarr.push(item.product_id);
            }
        });

        $("#txtbarcode_id").val("");
    }
});


// ========= ADD ROW FUNCTION ===========
function addTableRow(product_id, product_name, qty, rate, saleprice, stock, barcode, image, units) {

    let unitOptions = "";

    // currency conversion
    let salepricee, ratee;

    if ('<?php echo $usd_or_real ?>' === 'usd') {
        salepricee = saleprice;
        ratee = rate;
    } else {
        salepricee = saleprice * <?php echo $exchange ?>;
        ratee = rate * <?php echo $exchange ?>;
    }

    // units dropdown
    if (units && units.length > 0) {
        units.forEach(u => {
            let unit_price = ('<?php echo $usd_or_real ?>' === 'usd')
                ? u.unit_price
                : u.unit_price * <?php echo $exchange ?>;

            unitOptions += `<option value="${unit_price}" data-usd="${u.unit_price}">${u.unit_name}</option>`;
        });
    }

    let row = `
        <tr>

            <input type="hidden" name="barcode_arr[]" value="${barcode}">

            <td style="font-size:17px;">
                <img src="../productimages/${image}" height="50">
                &nbsp;
                <span class="badge badge-dark">${product_name}</span>
                <input type="hidden" name="pid_arr[]" value="${product_id}">
                <input type="hidden" name="product_arr[]" value="${product_name}">
            </td>

            <td style="font-size:17px;">
                <span class="badge badge-primary stocklbl" id="stock_id${product_id}">${stock}</span>
                <input type="hidden" class="stock_c" id="stock_idd${product_id}" value="${stock}">
            </td>

            <td>
                <select class="form-control item_size" name="size_arr[]" data-pid="${product_id}">
                    ${unitOptions}
                </select>
            </td>

            <td style="font-size:17px;">
                <span class="badge badge-warning price" id="price_id${product_id}">${salepricee}</span>
                <input type="hidden" class="price_c" id="price_idd${product_id}" value="${salepricee}">
            </td>

            <td>
                <input type="text" class="form-control qty" name="quantity_arr[]" 
                       id="qty_id${product_id}" value="${qty}">
            </td>

            <td style="font-size:17px;">
                <span class="badge badge-success totalamt" id="saleprice_id${product_id}">
                    ${ratee * qty}
                </span>
                <input type="hidden" class="saleprice" id="saleprice_idd${product_id}" 
                       value="${ratee * qty}">
            </td>

            <td>
                <center>
                    <button type="button" class="btn btn-danger btn-sm btnremove" data-id="${product_id}">
                        <span class="fas fa-trash"></span>
                    </button>
                </center>
            </td>

        </tr>
    `;

    $('.details').append(row);
    calculate(0, 0);
}


// ========= QTY CHANGE EVENT ===========

$("#itemtable").delegate(".qty", "keyup change", function () {

    let tr = $(this).closest('tr');
    let qty = parseFloat($(this).val()) || 1;
    let stock = parseFloat(tr.find(".stock_c").val());
    let price = parseFloat(tr.find(".price").text());

    if (qty > stock) {
        Swal.fire("WARNING!", "SORRY! This Much Of Quantity Is Not Available", "warning");
        qty = 1;
        $(this).val(1);
    }

    let total = qty * price;

    tr.find(".totalamt").text(total);
    tr.find(".saleprice").val(total);

    calculate(0, 0);
    $("#txtpaid").val("");
    $("#txtdue").val("");
});


// ========= REMOVE ROW ===========
$(document).on('click', '.btnremove', function () {
    let id = $(this).attr("data-id");

    productarr = productarr.filter(p => p != id);

    $(this).closest('tr').remove();

    calculate(0, 0);
    $("#txtpaid").val("");
    $("#txtdue").val("");
});

</script>