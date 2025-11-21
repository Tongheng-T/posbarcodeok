<script>
  //Initialize Select2 Elements
  $('.select2').select2()

  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })


  var productarr = [];

  $.ajax({
    url: "../resources/templates/back/getorderproduct.php",
    method: "get",
    dataType: "json",
    data: {
      id: <?php echo $_GET['id'] ?>
    },
    success: function(data) {
      //alert("pid");n

      //console.log(data);

      $.each(data, function(key, data) {
        if (jQuery.inArray(data["product_id"], productarr) !== -1) {

          var actualqty = parseInt($('#qty_id' + data["product_id"]).val()) + 1;
          $('#qty_id' + data["product_id"]).val(actualqty);

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


          $('#saleprice_id' + data["product_id"]).html(salepricee);
          $('#saleprice_idd' + data["product_id"]).val(salepricee);

          // $("#txtbarcode_id").val("");
          calculate(0, 0);

        } else {

          addrow(data["product_id"], data["product_name"], data["qty"], data["rate"], data["saleprice"], data["stock"], data["barcode"], data["image"]);

          productarr.push(data["product_id"]);

          //$("#txtbarcode_id").val("");

          function addrow(product_id, product_name, qty, rate, saleprice, stock, barcode, image) {

            if ('<?php echo $usd_or_real ?>' == 'usd') {
              salepricee = saleprice;
              ratee = rate;

            } else {
              salepricee = rate * <?php echo $exchange ?>;
              ratee = rate * <?php echo $exchange ?>;

            }

            var tr = '<tr>' +

              '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id' + barcode + '" value="' + barcode + '" >' +

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><img src="../productimages/' + image + ' " alt="" height=50 >  &nbsp; <class="form-control product_c" name="product_arr[]" <span class="badge badge-dark"> ' + product_name + '</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="' + product_id + '" ><input type="hidden" class="form-control product" name="product_arr[]" value="' + product_name + '" >  </td>' +

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id' + product_id + '">' + stock + '</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + product_id + '" value="' + stock + '"></td>' +

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + product_id + '">' + salepricee + '</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + product_id + '" value="' + salepricee + '"></td>' +

              '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id' + product_id + '" value="' + qty + '" size="1"></td>' +

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="saleprice_id' + product_id + '">' + ratee * qty + '</span><input type="hidden" class="form-control saleprice" name="saleprice_arr[]" id="saleprice_idd' + product_id + '" value="' + ratee * qty + '"></td>' +

              //remove button code start here

              // '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+pid+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+
              // '</tr>';

              '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="' + product_id + '"><span class="fas fa-trash"></span></center></td>' +


              '</tr>';

            $('.details').append(tr);
            calculate(0, 0);
          } //end function addrow

        }
      }); //end function each
      $("#txtbarcode_id").val("");
    } // end of success function
  }) // end of ajax request















  $("#itemtable").delegate(".qty", "keyup change", function() {

    var quantity = $(this);
    var tr = $(this).parent().parent();

    if ((quantity.val() - 0) > (tr.find(".stock_c").val() - 0)) {

      Swal.fire("WARNING!", "SORRY! This Much Of Quantity Is Not Available", "warning");
      quantity.val(1);

      tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());

      tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
      calculate(0, 0);
      $("#txtpaid").val("");
      $("#txtdue").val("");
    } else {
      tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());

      tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
      calculate(0, 0);
      $("#txtpaid").val("");
      $("#txtdue").val("");
    }
  });



  // function calculate(dis, paid) {

  //   var subtotal = 0;
  //   var discount = dis;
  //   var sgst = 0;
  //   var cgst = 0;
  //   var total = 0;
  //   var paid_amt = paid;
  //   var due = 0;

  //   $(".saleprice").each(function() {

  //     subtotal = subtotal + ($(this).val() * 1);
  //   });

  //   $("#txtsubtotal_id").val(subtotal.toFixed(2));

  //   sgst = parseFloat($("#txtsgst_id_p").val());

  //   cgst = parseFloat($("#txtcgst_id_p").val());

  //   discount = parseFloat($("#txtdiscount_p").val());

  //   sgst = sgst / 100;
  //   sgst = sgst * subtotal;

  //   cgst = cgst / 100;
  //   cgst = cgst * subtotal;

  //   discount = discount / 100;
  //   discount = discount * subtotal;

  //   $("#txtsgst_id_n").val(sgst.toFixed(2));

  //   $("#txtcgst_id_n").val(cgst.toFixed(2));

  //   $("#txtdiscount_n").val(discount.toFixed(2));


  //   total = sgst + cgst + subtotal - discount;
  //   due = total - paid_amt;


  //   $("#txttotal").val(total.toFixed(2));

  //   paid_db = parseFloat($("#txtpaid").val());
  //   due_db = paid_db - total;

  //   $("#txtdue").val(due_db.toFixed(2));

  // } //end calculate function


  // $("#txtdiscount_p").keyup(function() {

  //   var discount = $(this).val();

  //   calculate(discount, 0);

  // });

  // $("#txtpaid").keyup(function() {

  //   var paid = $(this).val();
  //   var discount = $("#txtdiscount_p").val();
  //   calculate(discount, paid);

  // });


  // $(document).on('click', '.btnremove', function() {

  //   var removed = $(this).attr("data-id");
  //   productarr = jQuery.grep(productarr, function(value) {

  //     return value != removed;
  //     calculate(0, 0);
  //     $("#txtpaid").val("");
  //     $("#txtdue").val("");
  //   })

  //   $(this).closest('tr').remove();
  //   calculate(0, 0);
  //   $("#txtpaid").val("");
  //   $("#txtdue").val("");
  // });
</script>