<script>
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    })


    var productarr = [];

    $(function () {

        $('#txtbarcode_id').on('change', function () {


            var barcode = $("#txtbarcode_id").val();

            $.ajax({
                url: "../resources/templates/back/getproduct.php",
                method: "get",
                dataType: "json",
                data: {
                    id: barcode
                },
                success: function (data) {

                    if (!data || !data.pid) {
                        Swal.fire("Not Found", "Product not found!", "warning");
                        $("#txtbarcode_id").val("");
                        return;
                    }

                    if (jQuery.inArray(data["pid"], productarr) !== -1) {

                        var actualqty = parseInt($('#qty_id' + data["pid"]).val()) + 1;
                        $('#qty_id' + data["pid"]).val(actualqty);

                        var saleprice = parseInt(actualqty) * data["saleprice"];

                        $('#saleprice_id' + data["pid"]).html(saleprice);
                        $('#saleprice_idd' + data["pid"]).val(saleprice);

                        // $("#txtbarcode_id").val("");
                        calculate(0, 0);

                    } else {

                        addrow(data["pid"], data["product"], data["saleprice"], data["stock"], data["barcode"], data["image"]);

                        productarr.push(data["pid"]);

                        //$("#txtbarcode_id").val("");

                        function addrow(pid, product, saleprice, stock, barcode, image) {

                            <?php
                            $change = query("SELECT * from tbl_change where aus = '$aus'");
                            confirm($change);
                            $row_exchange = fetch_object($change);
                            $exchange = $row_exchange->exchange;
                            $usd_or_real = $row_exchange->usd_or_real;
                            ?>

                            if ('<?php echo $usd_or_real ?>' == 'usd') {
                                salepricee = saleprice;
                            } else {
                                salepricee = saleprice * <?php echo $exchange ?>;

                            }

                            var tr = '<tr>' +

                                '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id' + barcode + '" value="' + barcode + '" >' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><img src="../productimages/' + image + ' " alt="" height=50 >  &nbsp; <class="form-control product_c" name="product_arr[]" <span class="badge badge-dark">' + product + '</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="' + pid + '" ><input type="hidden" class="form-control product" name="product_arr[]" value="' + product + '" >  </td>' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id' + pid + '">' + stock + '</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + pid + '" value="' + stock + '"></td>' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + pid + '">' + salepricee + '</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + pid + '" value="' + saleprice + '"></td>' +

                                '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id' + pid + '" value="' + 1 + '" size="1"></td>' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="saleprice_id' + pid + '">' + salepricee + '</span><input type="hidden" class="form-control saleprice" name="saleprice_arr[]" id="saleprice_idd' + pid + '" value="' + saleprice + '"></td>' +

                                //remove button code start here

                                // '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+pid+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+
                                // '</tr>';

                                '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="' + pid + '"><span class="fas fa-trash"></span></center></td>' +


                                '</tr>';

                            $('.details').append(tr);
                            calculate(0, 0);
                        } //end function addrow

                    }
                    $("#txtbarcode_id").val("");
                } // end of success function
            }) // end of ajax request
        }) // end of onchange function
    }); // end of main function





    var productarr = [];

    $(function () {

        $('#product_id').on('change', function () {
            let id = $(this).val();

            if (!id) return;

            $.ajax({
                url: "../resources/templates/back/getproduct.php",
                method: "GET",
                data: { id: id },
                dataType: "json",
                success: function (data) {
                    if (!data || !data.pid) {
                        Swal.fire("Not Found", "Product not found!", "warning");
                        return;
                    }

                    if (jQuery.inArray(data["pid"], productarr) !== -1) {

                        var actualqty = parseInt($('#qty_id' + data["pid"]).val()) + 1;
                        $('#qty_id' + data["pid"]).val(actualqty);

                        var saleprice = parseInt(actualqty) * data["saleprice"];
                        <?php
                        $change = query("SELECT * from tbl_change where aus = '$aus'");
                        confirm($change);
                        $row_exchange = fetch_object($change);
                        $exchange = $row_exchange->exchange;
                        $usd_or_real = $row_exchange->usd_or_real;
                        ?>

                        if ('<?php echo $usd_or_real ?>' == 'usd') {
                            salepricee = saleprice;
                        } else {
                            salepricee = saleprice * <?php echo $exchange ?>;

                        }

                        $('#saleprice_id' + data["pid"]).html(salepricee);
                        $('#saleprice_idd' + data["pid"]).val(salepricee);

                        //$("#txtbarcode_id").val("");

                        calculate(0, 0);
                    } else {

                        addrow(data["pid"], data["product"], data["saleprice"], data["stock"], data["barcode"], data["image"], data["units"]);

                        productarr.push(data["pid"]);

                        //$("#txtbarcode_id").val("");

                        function addrow(pid, product, saleprice, stock, barcode, image, units) {
                            let unitOptions = '<option value="">Select Size</option>';

                            <?php
                            $change = query("SELECT * from tbl_change where aus = '$aus'");
                            confirm($change);
                            $row_exchange = fetch_object($change);
                            $exchange = $row_exchange->exchange;
                            $usd_or_real = $row_exchange->usd_or_real;
                            ?>

                            if ('<?php echo $usd_or_real ?>' == 'usd') {
                                salepricee = saleprice;
                            } else {
                                salepricee = saleprice * <?php echo $exchange ?>;

                            }

                            if (units && units.length > 0) {
                                units.forEach(u => {
                                    let unit_price = 0;
                                    if ('<?php echo $usd_or_real ?>' === 'usd') {
                                        // keep USD from database
                                        unit_price = u.unit_price;
                                    } else {
                                        // convert USD → KHR
                                        unit_price = u.unit_price * <?php echo $exchange ?>;
                                    }

                                    unitOptions += `<option value="${unit_price}" data-usd="${u.unit_price}">${u.unit_name}</option>`;
                                });
                            }


                            var tr = '<tr>' +
                                '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id' + barcode + '" value="' + barcode + '" >' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><img src="../productimages/' + image + ' " alt="" height=50 >  &nbsp; <class="form-control product_c" name="product_arr[]" <span class="badge badge-dark">' + product + '</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="' + pid + '" ><input type="hidden" class="form-control product" name="product_arr[]" value="' + product + '" >  </td>' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id' + pid + '">' + stock + '</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + pid + '" value="' + stock + '"></td>' +
                                '<td>' +
                                '<select class="form-control item_size" name="size_arr[]" data-pid="' + pid + '">' +
                                unitOptions +
                                '</select>' +
                                '</td>' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + pid + '">' + salepricee + '</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + pid + '" value="' + salepricee + '"></td>' +

                                '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id' + pid + '" value="' + 1 + '" size="1"></td>' +

                                '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="saleprice_id' + pid + '">' + salepricee + '</span><input type="hidden" class="form-control saleprice" name="saleprice_arr[]" id="saleprice_idd' + pid + '" value="' + salepricee + '"></td>' +

                                //remove button code start here

                                // '<td style="text-align:center; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+pid+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+

                                '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="' + pid + '"><span class="fas fa-trash"></span></center></td>' +


                                '</tr>';


                            $('.details').append(tr);
                            calculate(0, 0);

                        } //end function addrow

                    }
                    $("#txtbarcode_id").val("");
                } // end of success function
            }) // end of ajax request
        }) // end of onchange function
    }); // end of main function

    $("#itemtable").delegate(".qty", "keyup change", function () {

        var quantity = $(this);
        var tr = $(this).parent().parent();

        if ((quantity.val() - 0) > (tr.find(".stock_c").val() - 0)) {

            Swal.fire("WARNING!", "SORRY! This Much Of Quantity Is Not Available", "warning");
            quantity.val(1);

            tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());

            tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
            calculate(0, 0);
        } else {
            tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());

            tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
            calculate(0, 0);
        }
    });
    $("#itemtable").on("click", ".btn-size", function () {


        var discountt = $(this).val();

        calculate(0, 0);

    });


    function calculate(dis, paid) {

        var subtotal = 0;
        var discount = dis;

        var total = 0;
        var paid_amt = paid;
        var due = 0;

        $(".saleprice").each(function () {

            subtotal = subtotal + ($(this).val() * 1);
        });

        $("#txtsubtotal_id").val(subtotal.toFixed(2));

        discount = parseFloat($("#txtdiscount_p").val());

        discount = discount / 100;
        discount = discount * subtotal;

        var dd = '';
        total = subtotal - discount;
        due = paid_amt - total;

        if ('<?php echo $usd_or_real ?>' == 'usd') {
            subtotal = total * <?php echo $exchange ?>;
            var dd = 2;
            var khc = '';

        } else {
            var khc = 2;
            subtotal = total / <?php echo $exchange ?>

        }
        total_khr = subtotal;

        $("#txtdiscount_n").val(discount.toFixed(dd));
        $("#txttotall_khr").val(total_khr.toFixed(khc));

        $("#txttotall").val(total.toFixed(dd));

        //  $("#txtdue").val(due);


    } //end calculate function


    $("#discount_h").keyup(function () {

        var discountt = $(this).val();

        calculate(0, 0);

    });

    $("#txtpaid").keyup(function () {
        txtdiscount_n

        // var paid = $(this).val();
        // var discount = $("#txtdiscount_p").val();
        // calculate(discount, paid);

        var totalAmount = $("#txttotall").val();
        var recievedAmount = $(this).val();
        var changeAmount = recievedAmount - totalAmount;

        if ('<?php echo $usd_or_real ?>' == 'usd') {
            var chkh = changeAmount * <?php echo $exchange ?>;
            var dd = 2;
            var ddkh = '';
        } else {
            var chkh = changeAmount / <?php echo $exchange ?>;
            var dd = '';
            var ddkh = 2;

        }

        $("#txtduekh").val(chkh.toFixed(ddkh));
        $("#txtdue").val(changeAmount.toFixed(dd));

    });



    $(document).on('click', '.btnremove', function () {

        var removed = $(this).attr("data-id");
        productarr = jQuery.grep(productarr, function (value) {

            return value != removed;
            calculate(0, 0);
        })

        $(this).closest('tr').remove();
        calculate(0, 0);
    });

    $(document).on('change', '.item_size', function () {

        let price = parseFloat($(this).val());
        let tr = $(this).closest('tr');

        // Update price display
        tr.find('.price').text(price);
        tr.find('.price_c').val(price);

        // Recalculate total for this row
        let qty = parseFloat(tr.find('.qty').val());
        let total = qty * price;

        tr.find('.totalamt').text(total);
        tr.find('.saleprice').val(total);

        calculate(0, 0);
    });

</script>