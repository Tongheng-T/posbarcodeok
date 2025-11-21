
<!-- Main Footer -->
<footer class="main-footer">
  <!-- To the right -->
  <div class="float-right d-none d-sm-inline">
    POS BARCODE INVENTORY SYSTEM V-2.0
  </div>
  <!-- Default to the left -->
  <strong>BY: <a href="https://web.facebook.com/TonghengCoding " target="_blank">Tongheng-T</a></strong>
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- <script src="../dist/js/jsll.js"></script> -->


 <!-- SweetAlert2 -->
 <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
$(document).ready(function () {

    function activateSelect2() {
        $('.unit_select').select2({
            tags: true,
            placeholder: "Select or type new unit",
            allowClear: true,
            width: '100%'
        });
    }

    activateSelect2();

    $("#add_unit").click(function () {

        let row = `
        <tr>
            <td>
                <select class="form-control unit_select" name="unit_name[]" required>
                    <?= fill_unit(); ?>
                </select>
            </td>
            <td><input type="number" class="form-control" name="unit_price[]" min="0.1" step="any" required></td>
            <td><button type="button" class="btn btn-danger btn-sm remove_unit">x</button></td>
        </tr>`;

        $("#unit_table tbody").append(row);

        activateSelect2();
    });

    $(document).on("click", ".remove_unit", function () {
        $(this).closest("tr").remove();
    });

});
</script>

 

<!-- pos order -->
<script src="../resources/templates/back/orderlistt.js"></script>
<?php require_once("jsl.php"); ?>

<?php require_once("edit_order.php"); ?>
<?php require_once("posjs.php"); ?>




<script>
  $(document).ready(function() {
    $('#table_category').DataTable();
  });
</script>
<script>
  $(document).ready(function() {
    $('#table_product').DataTable();
  });
</script>
<script>
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>



<!-- ........//////////////////productlist -->

<script>
  $(document).ready(function() {
    $('.btndelete').click(function() {
      var tdh = $(this);
      var id = $(this).attr("id");

      Swal.fire({
        title: 'Do you want to delete?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {

          $.ajax({
            url: '../resources/templates/back/productdelete.php',
            type: 'post',
            data: {
              pidd: id
            },
            success: function(data) {
              tdh.parents('tr').hide();
            }

          });

          Swal.fire(
            'Deleted!',
            'Your Product has been deleted.',
            'success'
          )
        }
      })

    });

  });

</script>


<!-- taxdis -->

<script>
  $(document).ready(function() {
    $('#table_tax').DataTable();
  });
</script>



<!-- tablereport -->

<!-- InputMask -->
<script src="../plugins/moment/moment.min.js"></script>

<!-- date-range-picker -->
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script>
  //Date picker
  $('#date_1').datetimepicker({
    format: 'YYYY-MM-DD'
  });



  //Date picker
  $('#date_2').datetimepicker({
    format: 'YYYY-MM-DD'
  });
  $('#date_11').datetimepicker({
    format: 'DD-MM-YYYY'
  });
</script>

<script>
  $(document).ready(function() {
    $('#table_report').DataTable({

      "order": [
        [0, "desc"]
      ]
    });
  });
</script>

<!-- dashboard -->
<script>
    $(document).ready(function() {
        $('#table_recentorder').DataTable({

            "order": [
                [0, "desc"]
            ]
        });
    });

    // $(document).ready(function() {
    //     $('#table_bestsellingproduct').DataTable({

           
    //     });
    // });
</script>


<script>
    $('.chang_branch').on('click', function() {
        var val = $(this).attr("value");
        var aus = $("#aus").val();
 
        $.ajax({
            url: "../resources/templates/back/update_real.php",
            method: "post",
            data: {
                val: val,
                aus: aus
               
            },
            success: function(data) {
                window.location.href = 'itemt?taxdis';
            }

        });
    })

    $('.ganeral').on('click', function() {

        $.ajax({
            url: "../resources/templates/back/unsetting.php",
        
            success: function(data) {
                window.location.href = 'itemt?settings';
            }

        });
    })

    // $('.ganeral').on('click', function() {
    //   document.location = '../resources/templates/back/unsetting.php';
      
    // })

</script>



</body>

</html>