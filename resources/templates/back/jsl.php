<script>
  $(function () {

    $('.select2s').on('change', function () {

      var bacodeid = $(".select2s").val();


      $.ajax({
        url: "../resources/templates/back/getprobacode.php",
        method: "get",
        dataType: "json",
        data: {
          id: bacodeid

        },
        success: function (data) {

          // alert(data["sj_price"])
          var bacode = data["bacode"];
          // $("#txtbarcode_id").val("");
          var show_bacode = document.getElementById('barcode');
          show_bacode.value = bacode;

        }
      });
    })


    $(".barcode").keyup(function () {

      var bacodeid = $('.barcode').val();


      $.ajax({
        url: "../resources/templates/back/getnamepro.php",
        method: "get",
        data: {
          id: bacodeid

        },
        success: function (data) {
          $('#pname').html(data);
          $('#pname').append(data.htmlresponse);

        }
      })
    })



    $(".barcode").keyup(function () {
      var bacodeid = $(".barcode").val();

      $.ajax({
        url: "../resources/templates/back/showimgg.php",
        method: "get",
        dataType: "json",
        data: { id: bacodeid },
        success: function (data) {
          if (!data || !data.image) {
            $("#showimg").html("<p style='color:red'>Not Found</p>");
            return;
          }

          let purchaseprice, saleprice;

          if (data["usd_or_real"] === "usd") {
            purchaseprice = data["purchaseprice"];
            saleprice = data["saleprice"];
          } else {
            purchaseprice = data["purchaseprice"] * data["exchange"];
            saleprice = data["saleprice"] * data["exchange"];
          }

          $("#purchaseprice").val(purchaseprice);
          $("#saleprice").val(saleprice);
          $("#showimg").html(
            '<img width="300" src="../productimages/' + data["image"] + '" alt="">'
          );
        },
      });
    });


    $('.select2s').on('change', function () {
      var bacodeid = $(".select2s").val();

      $.ajax({
        url: "../resources/templates/back/showimgg.php",
        method: "get",
        dataType: "json",
        data: { pid: bacodeid },
        success: function (data) {
          let purchaseprice, saleprice;

          if (data["usd_or_real"] === "usd") {
            purchaseprice = data["purchaseprice"];
            saleprice = data["saleprice"];
          } else {
            purchaseprice = data["purchaseprice"] * data["exchange"];
            saleprice = data["saleprice"] * data["exchange"];
          }

          $("#purchaseprice").val(purchaseprice);
          $('#saleprice').val(saleprice);
          $('#showimg').html('<img width="300" src="../productimages/' + data["image"] + '" alt="">');
        }
      });
    });

    

  });
</script>