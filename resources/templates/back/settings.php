<?php
$aus = $_SESSION['aus'];
$user = $_SESSION['userid'];
$selectk = query("SELECT * from tbl_user where user_id = '$user'");
$rows = $selectk->fetch(PDO::FETCH_OBJ);
$username = $rows->username;
$useremail = $rows->useremail;
$createdate = $rows->createdate;
$aus = $rows->aus;
$tim = $rows->tim;
$img = $rows->img;
$lat = $rows->lat;
$lng = $rows->lng;

$date_1 = date("d-m-Y", strtotime($rows->birthday));

if (isset($_POST['savemap'])) {
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    if (!empty($lng)) {
        $update = query("UPDATE tbl_user set lat='$lat', lng='$lng' where user_id='$user'");
        confirm($update);
        if ($update) {

            set_message(' <script>
            Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your work has been saved",
            showConfirmButton: false,
            timer: 1500
            });
          </script>');
            redirect('itemt?settings');
        }
    } else {

        redirect('itemt?settings');
    }
}

save_st();
// display_message();
changepasswordd();

$change = "";
if (empty($_SESSION['change'])) {
    $cc = '';
} else {
    $cc = $_SESSION['change'];
}

if (($cc == 'active')) {
    $change = 'active';
    $changee = 'active show';
} else {
    $general = 'active';
    $generall = 'active show';
}

?>


<section class="content container-fluid">
    <div class="box box-warning">
        <div class="card card-danger card-outline">
            <div class="card-header with-border">
                <h5 class="box-title">Account settings</h5>
            </div>
            <!-- daterange picker -->
            <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">

            <!-- Tempusdominus Bootstrap 4 -->
            <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

            <div class="card overflow">
                <div class="row no-gutters  row-border-light">
                    <div class="col-md-3 pt-0">
                        <div class="list-group ">
                            <a class="list-group-item list-group-item-action ganeral <?php echo $general ?>" data-toggle="list" href="#account-general">General </a>
                            <a class="list-group-item list-group-item-action <?php echo $change ?>" data-toggle="list" href="#account-change-password">Change password</a>
                            <!-- <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-social-links">Location</a> -->

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">

                            <div class="tab-pane fade <?php echo $generall ?>" id="account-general">
                                <form action="" method="post" name="formeditproduct" enctype="multipart/form-data">
                                    <div class="card-body media align-items-center">
                                        <div class="form-group">
                                            <label>រូបថត រូបភាព</label>
                                            <input type="file" class="input-group" name="file" onchange="displayImage(this)" id="profilImg">
                                            <img src="../resources/images/userpic/<?php echo $img ?> " onclick="triggerClick()" id="profiledisplay">
                                        </div>
                                        <div class="media-body ml-4">

                                            <div class="text-lightt small mt-1">Allowed JPG, GIF or PNG. Max size of 800K</div>
                                            <?php echo $img ?>
                                        </div>
                                    </div>
                                    <hr class="border-light m-0">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control mb-1" name="username" value="<?php echo $username ?>">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">E-mail</label>
                                            <input type="text" class="form-control mb-1" readonly value="<?php echo $useremail ?>">

                                        </div>


                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="form-label">Birthday</label>
                                                <div class="input-group date" id="date_11" data-target-input="nearest">
                                                    <input type="text" class="form-control date_11" data-target="#date_11" name="birthday" value="<?php echo $date_1; ?>" data-date-format="dd-mm-yyyy">
                                                    <div class="input-group-append" data-target="#date_11" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Company</label>

                                            <select class="form-control select2" data-dropdown-css-class="select2-purple" name="province" style="width: 100%;">
                                                <option>Select OR Search</option><?php echo ket(); ?>

                                                <?php
                                                $a = array("ខេត្តបាត់ដំបង", "ខេត្តកំពង់ចាម", "ខេត្តកំពង់ឆ្នាំង", "ខេត្តព្រះសីហនុ (កំពង់សោម)", "ខេត្តកំពង់ស្ពឺ", "ខេត្តកំពង់ធំ", "ខេត្តកំពត", "ខេត្តកោះកុង", "ខេត្តក្រចេះ", "ខេត្តប៉ៃលិន", "រាជធានីភ្នំពេញ", "ខេត្តតាកែវ", "ខេត្តព្រៃវែង", "ខេត្តពោធិ៍សាត់", "ខេត្តសៀមរាប", "ខេត្តស្ទឹងត្រែង", "ខេត្តស្វាយរៀង", "ខេត្តឧត្ដរមានជ័យ", "ខេត្តព្រះវិហារ", "ខេត្តបន្ទាយមានជ័យ", "ខេត្តកណ្តាល", "ខេត្តរតនៈគិរី", "ខេត្តមណ្ឌលគិរី", "ខេត្តត្បូងឃ្មុំ", "ខេត្តកែប");

                                                foreach ($a as $key => $value):

                                                ?>
                                                    <option <?php if ($key == $rows->location) { ?> selected="selected" <?php } ?> value="<?php echo $key ?>"><?php echo $value ?></option>

                                                <?php endforeach; ?>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary" name="save_st">Save changes</button>&nbsp;
                                        <button type="button" class="btn btn-default">Cancel</button>
                                    </div>

                                </form>
                            </div>

                            <div class="tab-pane fade <?php echo $changee ?>" id="account-change-password">
                                <form class="needs-validation form-horizontal" novalidate action="" method="post">
                                    <div class="card-body pb-2">
                                        <div class="form-group">
                                            <label for="validationCustom" class="form-label">Current password</label>
                                            <input type="password" class="form-control" placeholder=" Old Password" name="txt_oldpassword" id="validationCustom" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="validationCustom01" class="form-label">New password</label>
                                            <input type="password" class="form-control" placeholder="New Password" name="txt_newpassword" id="validationCustom01" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid Name.
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="validationCustom02" class="form-label">Repeat new password</label>
                                            <input type="password" class="form-control" placeholder="Repeate New Password" name="txt_rnewpassword" id="validationCustom02" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid Name.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary" name="btnupdate">Save changes</button>&nbsp;
                                        <button type="button" class="btn btn-default">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <!-- <div class="tab-pane fade" id="account-social-links">
                                <div class="card-body pb-2">
                                    <div class="form-group">
                                        <label class="form-label">Location</label>
                                        <div class="content">
                                            <form action="" method="post">
                                                <div class="mapform">
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <input type="text" class="form-control" placeholder="lat" value="" name="lat" id="lat">
                                                        </div>
                                                        <div class="col-5">
                                                            <input type="text" class="form-control" placeholder="lng" value="" name="lng" id="lng">
                                                        </div>
                                                    </div>

                                                    <div id="map" style="height:400px; width: 800px;" class="my-3"></div>

                                                    <script>
                                                        let map;

                                                        function initMap() {
                                                            map = new google.maps.Map(document.getElementById("map"), {
                                                                center: {
                                                                    lat: <?php echo $lat ?>,
                                                                    lng: <?php echo $lng ?>
                                                                },
                                                                zoom: 8,
                                                                scrollwheel: true,
                                                            });

                                                            const uluru = {
                                                                lat: <?php echo $lat ?>,
                                                                lng: <?php echo $lng ?>
                                                            };
                                                            let marker = new google.maps.Marker({
                                                                position: uluru,
                                                                map: map,
                                                                draggable: true
                                                            });

                                                            google.maps.event.addListener(marker, 'position_changed',
                                                                function() {
                                                                    let lat = marker.position.lat()
                                                                    let lng = marker.position.lng()
                                                                    $('#lat').val(lat)
                                                                    $('#lng').val(lng)
                                                                })

                                                            google.maps.event.addListener(map, 'click',
                                                                function(event) {
                                                                    pos = event.latLng
                                                                    marker.setPosition(pos)
                                                                })
                                                        }
                                                    </script>

                                                    <script async defer
                                                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCR2d87gn8Dbb-gxbP-HSau30w8tKv9pqc&callback=initMap">
                                                    </script>
                                                </div>

                                                <input type="submit" class="btn btn-primary" name="savemap">
                                            </form>


                                        </div>
                                    </div>

                                </div>
                            </div> -->



                        </div>
                    </div>
                </div>
            </div>

            <br>
        </div>
    </div>
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