<?php


if ($_SESSION['useremail'] == ""  or $_SESSION['role'] == "User") {

    header('location:../');
}

display_message();

?>





<!-- ======================================================= -->


<?php

update_discount();


if (isset($_POST['btnupdate_ch'])) {


    $exchange = $_POST['txtchange'];

    $id = $_POST['txtid_ch'];

    if ($exchange < 0) {

        set_message(' <script>
    Swal.fire({
    icon: "warning",
    title: "Feild is Empty!"
    });
   </script>');

        redirect('itemt?taxdis');
    } else {
        $update = query("UPDATE tbl_change set exchange='{$exchange}' where id =" . $id);
        confirm($update);

        if ($update) {

            set_message(' <script>
    Swal.fire({
    icon: "success",
    title: "Exchange Update successfully",
    });
   </script>');

            redirect('itemt?taxdis');
        } else {

            set_message(' <script>
    Swal.fire({
    icon: "warning",
    title: "Failed",
    });
   </script>');
            redirect('itemt?taxdis');
        }
    }
}


?>



<section class="content container-fluid">
    <div class="box box-warning">

        <div class="row">
            <div class="col-md-3">
                <div class="card card-success card-outline">
                    <div class="card-header with-border">
                        <h5 class="box-title">Discount(%) Form</h5>
                    </div>


                    <form action="" method="post">
                        <div class="card-body">

                            <?php
                            $aus = $_SESSION['aus'];

                            $select = query("SELECT * from tbl_taxdis where aus='$aus'");
                            confirm($select);
                            if ($select) {
                                $row = fetchOneObj($select);

                                echo '
        
                               <div class="form-group">
                               
                                 <input type="hidden" class="form-control" placeholder="Enter Category"  value="' . $row->taxdis_id . '" name="txtid" >
                  
                               <div class="form-group">
                                 <label for="exampleInputEmail1">Discount(%)(៛)</label>
                                 <input type="text" class="form-control" placeholder="Enter Exchange" value="' . $row->discount . '" name="txtdiscount" >
                               </div>                
                               </div>
                               <div class="card-footer">
                               <button type="submit" class="btn btn-success" name="btnupdate"><i class="fas fa-save"></i> Save</button>
                               </div>

                               ';
                            }


                            ?>



                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-header with-border">
                        <h5 class="box-title">Exchange Form</h5>
                    </div>


                    <form action="" method="post">
                        <div class="card-body">

                            <?php
                            $aus = $_SESSION['aus'];

                            $select = query("SELECT * from tbl_change where aus='$aus'");
                            confirm($select);
                            if ($select) {
                                $row = fetchOneObj($select);

                                echo '
        
                               <div class="form-group">
                               
                                 <input type="hidden" class="form-control" placeholder="Enter Category"  value="' . $row->id . '" name="txtid_ch" >
                  
                               <div class="form-group">
                                 <label for="exampleInputEmail1">Exchange(៛)</label>
                                 <input type="text" class="form-control" placeholder="Enter Exchange" value="' . $row->exchange . '" name="txtchange" >
                               </div>                
                               </div>
                               <div class="card-footer">
                               <button type="submit" class="btn btn-info" name="btnupdate_ch">Update</button>
                               </div>

                               ';
                            }


                            ?>



                        </div>
                    </form>
                </div>
            </div>


            <div class="col-md-4">


                <div class="card card-danger card-outline">
                    <div class="card-header with-border">
                        <h5 class="box-title">USD OR LEAR</h5>
                    </div>


                    <div class="col-md-10">
                        <div class="text-center" style="padding: 10px;">
                            <div class="row">
                                <?php
                                function active_usd($active)
                                {
                                    $aus = $_SESSION['aus'];
                                    $change = query("SELECT * from tbl_change where aus='$aus'");
                                    confirm($change);
                                    $row_exchange = fetchOneObj($change);
                                    $exchange = $row_exchange->usd_or_real;
                                    if ($active == $exchange) {
                                        echo "active";
                                    }
                                }
                                function active_check($active)
                                {
                                    $aus = $_SESSION['aus'];
                                    $change = query("SELECT * from tbl_change where aus='$aus'");
                                    confirm($change);
                                    $row_exchange = fetchOneObj($change);
                                    $exchange = $row_exchange->usd_or_real;
                                    if ($active == $exchange) {
                                        echo '<i class="fa fa-check" aria-hidden="true"></i>';
                                    }
                                }

                                ?>
                                <input type="hidden" class="form-control" placeholder="Enter Category" value="<?php echo $aus ?>" id="aus">
                                <div class="col-md-6">
                                    <button value="usd" class="btn btn-primary chang_branch <?php active_usd("usd") ?>"><?php active_check("usd") ?> USD($)</button>
                                </div>
                                <div class="col-md-6">
                                    <button value="real" class="btn btn-primary chang_branch <?php active_usd("real") ?>"><?php active_check("real") ?> រៀល(៛)</button>
                                </div>
                            </div>
                        </div>




                    </div>



                </div>


            </div>


        </div><!-- /.container-fluid -->
    </div>
</section>







<!-- ======================================================= -->







<section class="content container-fluid">
    <div class="box box-warning">
        <div class="card card-warning card-outline">
            <div class="card-header with-border">
                <h5 class="m-0">ឈ្មោះនិង logo ហាង</h5>
            </div>
            <?php hang(); ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $aus = $_SESSION['aus'];
                        $select = query("SELECT * from tbl_logo where aus='$aus'");
                        confirm($select);

                        $row = fetchOneObj($select);


                        ?>
                        <form class="needs-validation" novalidate action="" method="post" name="formeditproduct" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="col-md-2 mb-3">
                                    <label for="validationCustom">logo</label>
                                    <input type="file" class="input-group" name="file" onchange="displayImage(this)" id="profilImg">
                                    <img src="../productimages/logo/<?php echo $row->img ?> " onclick="triggerClick()" id="profiledisplay">
                                    <div class="invalid-feedback">
                                        Please provide a valid Logo.
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="validationCustom01">ឈ្មោះហាង</label>
                                    <textarea type="text" class="form-control" placeholder="Enter Name" name="txtname" id="validationCustom01" required><?php echo $row->name ?></textarea>
                                    <div class="invalid-feedback">
                                        Please provide a valid Name.
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="validationCustom02">អាស័យដ្ឋាន</label>
                                    <textarea type="text" class="form-control" placeholder="Enter Addres" name="txtaddres" id="validationCustom02" required><?php echo $row->addres ?></textarea>
                                    <div class="invalid-feedback">
                                        Please provide a valid Addres.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label for="validationCustom03">ផ្លូវលេខ</label>
                                    <input class="form-control" placeholder="Enter Road" value="<?php echo $row->road ?>" name="txtrod" id="validationCustom03" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid Road.
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="validationCustom04">លេខទូរស័ព្ទ</label>
                                    <input type="text" class="form-control" placeholder="Enter Phone number" value="<?php echo $row->phone ?>" name="txtphone" id="validationCustom04" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid phone.
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">

                            </div>
                            <button type="submit" class="btn btn-primary" value="<?php echo $row->id ?>" name="btnedit_ch">Save</button>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    
</script>
