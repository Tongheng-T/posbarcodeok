<!-- Content Header (Page header) -->

<?php if ($_SESSION['useremail'] == "User") {
    header('location:../');
};?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Registration</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Admin Page</li> -->
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<?php 
display_message();
registration(); 
?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0">Registration</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php edit_registration() ?>

                    <div class="col-md-8">
                        <form action="" method="post">
                            <table class="table table-striped table-hover ">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Name</td>
                                        <td>Email</td>
                                        <td>Password</td>
                                        <td>Role</td>
                                        <td>Edit</td>
                                        <td>Delete</td>
                                    </tr>

                                </thead>


                                <tbody>

                                    <?php
                                    $aus = $_SESSION['aus'];
                                    $selectt = query("SELECT min(user_id) as user from tbl_user where aus= '$aus' ");
                                    $roww = fetch_object($selectt);
                                    $user = $roww->user;
                                    $selectk = query("SELECT * from tbl_user where user_id = '$user'");
                                    $rows = fetch_object($selectk);
                                    $useremail = $rows->useremail;
                                    $select = query("SELECT * from tbl_user where aus= '$aus' order by user_id ASC");
                                    confirm($select);

                                    $admin = 'Admin';
                                    $user = 'User';
                                    $no = 1;
                                    while ($row = fetch_object($select)) {
                                        if ($row->role == $admin or $row->role == $user) {
                                            $password = "********";
                                        } else {
                                            $password = $row->password;
                                        }
                                        if ($row->useremail == $useremail) {
                                            $delete = '';
                                        } else {

                                            $delete = '
                                            <td><button type="submit" class="btn btn-primary" value="' . $row->user_id . '" name="btnedit">Edit</button></td>
                                            <td><a href="../resources/templates/back/delete_user.php?id=' . $row->user_id . '" class="btn btn-danger"><i class="fa fa-trash-alt"></i></a></td> ';
                                        }
                                        echo '
                                       <tr>
                                       <td>' . $no . '</td>
                                       <td> <img height="50" src="../resources/images/userpic/' . $row->img . '" alt="" class="img-circle"> ' . $row->username . '</td>
                                       <td>' . $row->useremail . '</td>
                                       <td>' . $password . '</td>  
                                       <td>' . $row->role . '</td>
                                       ' . $delete . '
                                       </tr>';
                                        $no++;
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </form>
                    </div>

                </div>
            </div>
        </div>


    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<script>
   
    function show() {
            var p = document.getElementById('pwd');
            var eyeicon = document.getElementById('eyeicon');
            p.setAttribute('type', 'text');
            eyeicon.classList = 'fas fa-eye';
        }

        function hide() {
            var p = document.getElementById('pwd');
            var eyeicon = document.getElementById('eyeicon');
            p.setAttribute('type', 'password');
            eyeicon.classList = 'fas fa-eye-slash';
        }

        var pwShown = 0;

        document.getElementById("eye").addEventListener("click", function() {
            if (pwShown == 0) {
                pwShown = 1;
                show();
            } else {
                pwShown = 0;
                hide();
            }
        }, false);
    </script>
</script>