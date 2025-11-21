<?php
$upload_directory = "uploads";

// helper function

function check_admin_session()
{
    if (empty($_SESSION['useremail']) || $_SESSION['role'] === "User") {
        header("Location: ../");
        exit;
    }
}
function convert_number_kh($day)
{
    $kh_day = ["០", "១", "២", "៣", "៤", "៥", "៦", "៧", "៨", "៩"];
    for ($i = 0; $i <= 9; $i++) {
        $day = str_replace($i, $kh_day[$i], $day);
    }
    return $day;
}

function last_id()
{
    global $pdo;
    return $pdo->lastInsertId();
}

function set_message($msg)
{
    if (!empty($msg)) {
        $_SESSION['message'] = $msg;
    } else {
        $msg = "";
    }
}

function display_message()
{
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}
function actr($path)
{
    if (isset($_GET[$path])) {
        $active = "active";
        echo $active;
    }
}

function redirect($location)
{
    header("Location: $location");
}

function query($sql, $params = [])
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function confirm($stmt)
{
    if (!$stmt) {
        die("Database query failed.");
    }
    return true;
}

// Fetch as associative array
function fetch_assoc($stmt)
{
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Escape string (PDO handles prepare, but for display use this)
function escape_string($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/*********************************FRONT END FUNCTIONS************************************/

function login_user()
{
    if (isset($_POST['btn_login'])) {

        $useremail = trim($_POST['txt_email'] ?? '');
        $password = trim($_POST['txt_password'] ?? '');

        // Query by email only (NOT with password)
        $query = query("SELECT * FROM tbl_user WHERE useremail = :email", [':email' => $useremail]);

        if ($query->rowCount() == 0) {
            // អ៊ីមែលមិនមាន
            set_message("
                <script>
                    $(function() {
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        Toast.fire({
                            icon: 'error',
                            title: 'អ៊ីមែលនេះមិនមានក្នុងប្រព័ន្ធ!'
                        })
                    });
                </script>");
            redirect("");
            return;
        }

        $row = $query->fetch(PDO::FETCH_ASSOC);

        // Verify password (សំខាន់)
        if (!password_verify($password, $row['userpassword'])) {
            set_message("
                <script>
                    $(function() {
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        Toast.fire({
                            icon: 'error',
                            title: 'ពាក្យសម្ងាត់មិនត្រឹមត្រូវ!'
                        })
                    });
                </script>");
            redirect("");
            return;
        }

        // If verified → Save session
        $_SESSION['userid'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['useremail'] = $row['useremail'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['aus'] = $row['aus'];

        // Update last login + online time
        $date = new DateTime('now', new DateTimeZone('Asia/Bangkok'));
        $datee = $date->format('Y-m-d H:i:s');
        $time = time() + 10;

        query(
            "UPDATE tbl_user SET login_online = :online, last_login = :last WHERE user_id = :id",
            [':online' => $time, ':last' => $datee, ':id' => $_SESSION['userid']]
        );

        // Role check
        if ($row['role'] === 'Admin') {
            set_message("
                <script>
                    $(function() {
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Login success By Admin'
                        })
                    });
                </script>");
            header('refresh:2;ui/');
        } else {
            set_message("
                <script>
                    $(function() {
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Login success By User'
                        })
                    });
                </script>");
            header('refresh:2;user/');
        }
    }
}


function set_message_signin($msgg)
{
    if (!empty($msgg)) {
        $_SESSION['messagee'] = $msgg;
    } else {
        $msgg = "";
    }
}

function check_login()
{
    if (isset($_SESSION['userid'])) {
        $id = $_SESSION['userid'];

        // Prepared statement with limit 1
        $stmt = query("SELECT * FROM tbl_user WHERE user_id = ? LIMIT 1", [$id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            return $user_data; // return array with user info
        }
    }

    // Redirect to login if not found
    header("Location: ../");
    exit;
}

function name_user()
{
    if (isset($_SESSION['userid'])) {
        $id = $_SESSION['userid'];

        // Query with prepared statement
        $stmt = query("SELECT username FROM tbl_user WHERE user_id = ?", [$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['username'])) {
            return $row['username'];  // Return username
        } else {
            return "Hello";           // Default fallback
        }
    } else {
        return "Hello";               // No user logged in
    }
}


/*********************************BACK END FUNCTIONS************************************/

function changepassword()
{
    if (isset($_POST['btnupdate'])) {

        $oldpassword_txt = md5($_POST['txt_oldpassword']);
        $newpassword_txt = md5($_POST['txt_newpassword']);
        $rnewpassword_txt = md5($_POST['txt_rnewpassword']);

        //echo $oldpassword_txt."-".$newpassword_txt."-".$rnewpassword_txt;


        // 2 Step) Using of select Query we will get out database records according to useremail.

        $email = $_SESSION['useremail'];

        $select = query("SELECT * from tbl_user where useremail='$email'");
        confirm($select);

        $row = fetch_assoc($select);

        $useremail_db = $row['useremail'];
        $password_db = $row['userpassword'];



        // 3 Step) We will compare the user inputs values to database values.

        if ($oldpassword_txt == $password_db) {

            if ($newpassword_txt == $rnewpassword_txt) {

                // 4 Step) If values will match then we will run update Query. 


                $update = query("UPDATE tbl_user set userpassword='$rnewpassword_txt' where useremail='$email'");
                confirm($update);

                if ($update) {
                    set_message(' <script>
                            Swal.fire({
                              icon: "success",
                              title: "Password Updated successfully"
                            });
                          </script>');
                    redirect('itemt?changepassword');
                } else {
                    set_message(' <script>
                    Swal.fire({
                      icon: "error",
                      title: "Password Not Updated successfully"
                    });
                  </script>');
                    redirect('itemt?changepassword');
                }

                // $_SESSION['status']="New Password Matched";
                // $_SESSION['status_code']="success";

            } else {
                set_message(' <script>
                    Swal.fire({
                      icon: "error",
                      title: "New Password Deos Not Matched"
                    });
                  </script>');
                redirect('itemt?changepassword');
            }
        } else {

            set_message(' <script>
            Swal.fire({
              icon: "error",
              title: "Password Deos Not Matched"
            });
          </script>');
            redirect('itemt?changepassword');
        }
    }
}



function registration()
{
    if (isset($_POST['btnsave']) || isset($_POST['btnupdate'])) {

        $username = trim($_POST['txtname']);
        $useremail = trim($_POST['txtemail']);
        $userrole = $_POST['txtselect_option'];
        $password = $_POST['txtpassword'] ?? '';

        $id = $_POST['btnupdate'] ?? null;
        $isUpdate = isset($_POST['btnupdate']);

        $image = 'user.png';
        $uploadOk = true;

        $f_name = $_FILES['file']['name'] ?? '';
        if (!empty($f_name)) {
            $f_extension = strtolower(pathinfo($f_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $f_size = $_FILES['file']['size'];

            if (!in_array($f_extension, $allowed_ext)) {
                set_message('<script>Swal.fire({icon:"warning", title:"Only jpg, jpeg, png, gif allowed"});</script>');
                redirect('itemt?registration');
                $uploadOk = false;
            } elseif ($f_size > 1000000) {
                set_message('<script>Swal.fire({icon:"warning", title:"Max file size 1MB"});</script>');
                redirect('itemt?registration');
                $uploadOk = false;
            } else {
                $user_photo = uniqid() . '.' . $f_extension;
                move_uploaded_file($_FILES['file']['tmp_name'], UPLOAD_DIRECTORY_UDER . DS . $user_photo);
                $image = $user_photo;
            }
        }

        if ($uploadOk) {
            $aus = $_SESSION['aus'];
            $time = new DateTime('now', new DateTimeZone('Asia/Bangkok'));
            $datee = $time->format('Y-m-d H:i:s');

            $select_admin = query("SELECT * FROM tbl_user WHERE aus='$aus' LIMIT 1");
            confirm($select_admin);
            $rowadmin = fetchOneObj($select_admin);
            $date_new = $rowadmin->date_new ?? $datee;
            $tim = $rowadmin->tim ?? '';

            if ($isUpdate) {
                // --- UPDATE USER ---
                $id = (int) $id;
                $select_img = query("SELECT img FROM tbl_user WHERE user_id = $id");
                confirm($select_img);
                $row = fetch_assoc($select_img);
                $dbimage = $row['img'];

                if (!empty($f_name) && $dbimage != 'user.png') {
                    @unlink(UPLOAD_DIRECTORY_UDER . DS . $dbimage);
                }

                if (!empty($password)) {
                    $userpassword = password_hash($password, PASSWORD_DEFAULT);
                    $update = query("UPDATE tbl_user 
                        SET username='$username', useremail='$useremail', userpassword='$userpassword', img='$image', role='$userrole' 
                        WHERE user_id='$id'");
                } else {
                    $update = query("UPDATE tbl_user 
                        SET username='$username', useremail='$useremail', img='$image', role='$userrole' 
                        WHERE user_id='$id'");
                }

                confirm($update);
                if ($update) {
                    set_message('<script>Swal.fire({icon:"success", title:"User updated successfully"});</script>');
                } else {
                    set_message('<script>Swal.fire({icon:"error", title:"Error updating user"});</script>');
                }
                redirect('itemt?registration');

            } else {
                // --- NEW USER ---
                $check_email = query("SELECT useremail FROM tbl_user WHERE useremail='$useremail'");
                confirm($check_email);
                if ($check_email->rowCount() > 0) {
                    set_message('<script>Swal.fire({icon:"warning", title:"Email already exists"});</script>');
                    redirect('itemt?registration');
                }

                $userpassword = password_hash($password, PASSWORD_DEFAULT);
                $insert = query("INSERT INTO tbl_user (username,useremail,userpassword,img,role,createdate,date_new,verified,aus,tim)
                                 VALUES ('$username','$useremail','$userpassword','$image','$userrole','$datee','$date_new',1,'$aus','$tim')");
                confirm($insert);

                if ($insert) {
                    set_message('<script>Swal.fire({icon:"success", title:"User added successfully"});</script>');
                } else {
                    set_message('<script>Swal.fire({icon:"error", title:"Error inserting user"});</script>');
                }
                redirect('itemt?registration');
            }
        }
    }
}


function edit_category()
{
    if (isset($_POST['btnedit'])) {

        $select = query("SELECT * from tbl_category where catid =" . $_POST['btnedit']);
        confirm($select);

        if ($select) {
            $row = fetchOneObj($select);

            echo '<div class="col-md-4">

                <div class="form-group">
               <label for="exampleInputEmail1">Category</label>

               <input type="hidden" class="form-control" placeholder="Enter Category"  value="' . $row->catid . '" name="txtcatid" >

               <input type="text" class="form-control" placeholder="Enter Category"  value="' . $row->category . '" name="txtcategory" >
               </div>


               <div class="card-footer">
               <button type="submit" class="btn btn-info" name="btnupdate">Update</button>
               </div>
               </div>';
        }
    } else {

        echo '<div class="col-md-4">

         <div class="form-group">
         <label for="exampleInputEmail1">Category</label>
         <input type="text" class="form-control" placeholder="Enter Category"  name="txtcategory" >
         </div>
         <div class="card-footer">
         <button type="submit" class="btn btn-warning" name="btnsave">Save</button>
         </div>

         </div>';
    }
}

function query_category()
{
    $aus = $_SESSION['aus'];
    $select = query("SELECT * from tbl_category where aus=$aus order by catid ASC");
    confirm($select);
    $no = 1;
    while ($row = fetchOneObj($select)) {

        echo '
        <tr>
        <td>' . $no . '</td>
        <td>' . $row->category . '</td>
        <td>
        <button type="submit" class="btn btn-primary" value="' . $row->catid . '" name="btnedit">Edit</button>
        </td>
        <td>
        <button type="submit" class="btn btn-danger" value="' . $row->catid . '" name="btndelete">Delete</button>
       </td>
      </tr>';
        $no++;
    }
}


function insert_update_delete()
{
    $aus = $_SESSION['aus'];
    if (isset($_POST['btnsave'])) {

        $category = $_POST['txtcategory'];

        if (empty($category)) {
            set_message(' <script>
            Swal.fire({
            icon: "warning",
            title: "Category Feild is Empty"
            });
           </script>');
            redirect('itemt?category');
        } else {

            $insert = query("INSERT into tbl_category (category,aus) values('{$category}','{$aus}')");
            confirm($insert);
            if ($insert) {
                set_message(' <script>
                Swal.fire({
                icon: "success",
                title: "Category Added successfully"
                });
               </script>');
                redirect('itemt?category');
            } else {
                set_message(' <script>
                Swal.fire({
                icon: "warning",
                title: "Category Added Failed"
                });
               </script>');
                redirect('itemt?category');
            }
        }
    }



    if (isset($_POST['btnupdate'])) {

        $category = $_POST['txtcategory'];
        $id = $_POST['txtcatid'];

        if (empty($category)) {
            set_message(' <script>
            Swal.fire({
            icon: "warning",
            title: "Category Feild is Empty"
            });
           </script>');
            redirect('itemt?category');
        } else {

            $update = query("UPDATE tbl_category set category='$category' where catid=" . $id);
            confirm($update);

            if ($update) {
                set_message(' <script>
                Swal.fire({
                icon: "success",
                title: "Category Update successfully"
                });
               </script>');
                redirect('itemt?category');
            } else {
                set_message(' <script>
                Swal.fire({
                icon: "warning",
                title: "Category Update Failed"
                });
               </script>');
                redirect('itemt?category');
            }
        }
    }


    if (isset($_POST['btndelete'])) {

        $delete = query("DELETE from tbl_category where catid=" . $_POST['btndelete']);
        confirm($delete);
        if ($delete) {
            set_message(' <script>
            Swal.fire({
            icon: "success",
            title: "Deleted"
            });
           </script>');
            redirect('itemt?category');
        } else {
            set_message(' <script>
            Swal.fire({
            icon: "warning",
            title: "Delete Failed"
            });
           </script>');
            redirect('itemt?category');
        }
    } else {
    }
}


function addProduct()
{

    if (!isset($_POST['btnsave']))
        return;
    $productname = $_POST['txtproductname'];
    $aus = $_SESSION['aus'] ?? '';
    $barcode = trim($_POST['txtbarcode'] ?? '');
    $product = trim($_POST['txtproductname'] ?? '');
    $category = trim($_POST['txtselect_option'] ?? '');
    $desc = trim($_POST['txtdescription'] ?? '');
    $stock = (int) ($_POST['txtstock'] ?? 0);
    $purchase = (float) ($_POST['txtpurchaseprice'] ?? 0);
    $sale = (float) ($_POST['txtsaleprice'] ?? 0);

    // Exchange Rate
    $stmt = query("SELECT exchange, usd_or_real FROM tbl_change WHERE aus=? LIMIT 1", [$aus]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    if ($row && $row->usd_or_real !== "usd") {
        $purchase = $purchase / $row->exchange;
        $sale = $sale / $row->exchange;
    }

    // File Upload
    $file = $_FILES['myfile'] ?? null;
    $newName = "display.jpg";

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = uniqid() . '.' . $ext;
        $path = "../productimages/" . $newName;

        if (!in_array($ext, $allowed)) {
            set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "only jpg, jpeg, png and gif can be upload"
            });
          </script>');
            redirect('itemt?addproduct');
            return;
        }

        if ($file['size'] > 1000000) {
            set_message('<script>
                    Swal.fire({
                      icon: "warning",
                      title: "Max file should be 1MB"
                    });
                </script>');
            redirect('itemt?addproduct');
            return;
        }

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            set_message(' <script>
                            Swal.fire({
                              icon: "error",
                              title: "Product Inserted Failed"
                            });
                          </script>');
            redirect('itemt?addproduct');
            return;
        }
    }

    // Insert Product
    $stmt = query("INSERT INTO tbl_product (barcode, product, category, description, stock, purchaseprice, saleprice, image, aus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $barcode ?: null,
        $product,
        $category,
        $desc,
        $stock,
        $purchase,
        $sale,
        $newName,
        $aus
    ]);

    $pid = last_id();

    // Auto-generate barcode if empty
    if (empty($barcode)) {
        date_default_timezone_set("Asia/Bangkok");
        $newbarcode = $pid . date('his');
        $newBarcode = str_pad($pid, 13, date('his'), STR_PAD_LEFT);
        query("UPDATE tbl_product SET barcode=? WHERE pid=?", [$newBarcode, $pid]);
    }

    // Insert Stock
    query("INSERT INTO tbl_product_stock (product_id, stock, price, aus) VALUES (?, ?, ?, ?)", [
        $pid,
        $stock,
        $purchase,
        $aus
    ]);

    // Save units
    foreach ($_POST['unit_name'] as $index => $uname) {

        // Skip empty rows
        if (trim($uname) == "")
            continue;
        $uprice = $_POST['unit_price'][$index];

        // Convert KHR → USD only if system currency = KHR
        if ($row && $row->usd_or_real !== "usd") {
            $uprice = $uprice / floatval($row->exchange);
        }

        query("INSERT INTO tbl_product_unit (pid, unit_name, unit_price, aus)
               VALUES (?,?,?,?)",
            [$pid, $uname, $uprice, $aus]
        );

        // Auto save new units into tbl_unit
        query("INSERT IGNORE INTO tbl_unit (unit_name, aus) VALUES (?, ?)", [
            $uname,
            $aus
        ]);
    }
    set_message(' <script>
                            Swal.fire({
                              icon: "success",
                              title: "Product Inserted Successfully"
                            });
                          </script>');
    redirect('itemt?addproduct');
}



function productlist()
{
    $aus = $_SESSION['aus'];
    $select = query("SELECT * from tbl_product where aus=$aus order by pid ASC");
    confirm($select);

    $change = query("SELECT * from tbl_change where aus='$aus'");
    confirm($change);
    $row_exchange = fetchOneObj($change);
    $exchange = $row_exchange->exchange;
    $usd_or_real = $row_exchange->usd_or_real;

    while ($row = fetchOneObj($select)) {

        if ($usd_or_real == "usd") {
            $USD_usd = "$";
            // $m_price = number_format($row->m_price, 2) . $USD_usd;
            $saleprice = number_format($row->saleprice, 2) . $USD_usd;
            $purchaseprice = number_format($row->purchaseprice, 2) . $USD_usd;
        } else {
            $USD_usd = "៛";
            // $m_pricee = $row->m_price * $exchange;
            $salepricee = $row->saleprice * $exchange;
            $purchaseprice = $row->purchaseprice * $exchange;
            // $m_price = number_format($m_pricee) . $USD_usd;
            $saleprice = number_format($salepricee) . $USD_usd;
            $purchaseprice = number_format($purchaseprice) . $USD_usd;
        }

        echo '
           <tr>
           <td>' . $row->barcode . '</td>
           <td>' . $row->product . '</td>
           <td>' . $row->category . '</td>
           <td>' . $row->description . '</td>
           <td>' . $row->stock . '</td>
           
           <td>' . $purchaseprice . '</td>
           <td>' . $saleprice . '</td>
           
           <td><image src="../productimages/' . $row->image . '" class="img-rounded" width="40px" height="40px/"></td>
           
           <td>
           
           <div class="btn-group">
           
           <a href="itemt?printbarcode&id=' . $row->pid . '" class="btn btn-dark btn-xs" role="button"><span class="fa fa-barcode" style="color:#ffffff" data-toggle="tooltip" title="PrintBarcode"></span></a>
           
           
           <a href="itemt?viewproduct&id=' . $row->pid . '" class="btn btn-warning btn-xs" role="button"><span class="fa fa-eye" style="color:#ffffff" data-toggle="tooltip" title="View Product"></span></a>
           
           
           <a href="itemt?editproduct&id=' . $row->pid . '" class="btn btn-success btn-xs" role="button"><span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="Edit Product"></span></a>
           
           <button id=' . $row->pid . '  class="btn btn-danger btn-xs btndelete"><span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="Delete Product"></span></button>
           
           </div>
           
           </td>
           
           </tr>';
    }
}

function viewproduct()
{
    $id = $_GET['id'];

    $select = query("SELECT * from tbl_product where pid = $id");
    confirm($select);

    while ($row = fetchOneObj($select)) {

        echo '
<div class="row">
<div class="col-md-6">

<ul class="list-group">

<center><p class="list-group-item list-group-item-info"><b>PRODUCT DETAILS</b></p></center>  

  <li class="list-group-item"><b>Barcode</b> <span class="badge badge-light float-right">' . bar128($row->barcode) . '</span></li>
  <li class="list-group-item"><b>Product Name</b><span class="badge badge-warning float-right">' . $row->product . '</span></li>
  <li class="list-group-item"><b>Category</b> <span class="badge badge-success float-right">' . $row->category . '</span></li>
  <li class="list-group-item"><b>Description </b><span class="badge badge-primary float-right">' . $row->description . '</span></li>
  <li class="list-group-item"><b>Stock</b> <span class="badge badge-danger float-right">' . $row->stock . '</span></li>
  <li class="list-group-item"><b>Purchase Price </b><span class="badge badge-secondary float-right">' . $row->purchaseprice . '</span></li>
  <li class="list-group-item"><b>Sale Price</b> <span class="badge badge-dark float-right">' . $row->saleprice . '</span></li>
  <li class="list-group-item"><b>Product Profit</b> <span class="badge badge-success float-right">' . ($row->saleprice - $row->purchaseprice) . '</span></li>
</ul>
</div>

<div class="col-md-6">
<ul class="list-group">
<center><p class="list-group-item list-group-item-info"><b>PRODUCT IMAGE</b></p></center>  
<img src="../productimages/' . $row->image . '" class="img-thumbnail"/>
</ul>
</div>
</div>



';
    }
}

function fill_product()
{
    $aus = $_SESSION['aus'];
    $output = '';
    $select = query("SELECT * from tbl_product where aus=$aus order by product asc");
    confirm($select);

    foreach ($select as $row) {
        $output .= '<option value="' . $row["pid"] . '">' . $row["product"] . '</option>';
    }

    return $output;
}

function edit_product()
{

    $id = $_GET['id'];

    $select = query("SELECT * from tbl_product where pid=$id");
    confirm($select);

    $row = fetch_assoc($select);

    $image_db = $row['image'];


    if (isset($_POST['btneditproduct'])) {

        // $barcode_txt       =$_POST['txtbarcode'];
        $product_txt = $_POST['txtproductname'];
        $category_txt = $_POST['txtselect_option'];
        $description_txt = $_POST['txtdescription'];
        $stock_txt = $_POST['txtstock'];
        $purchaseprice_txt = $_POST['txtpurchaseprice'];
        $saleprice_txt = $_POST['txtsaleprice'];

        //Image Code or File Code Start Here..
        $f_name = $_FILES['myfile']['name'];

        $aus = $_SESSION['aus'];

        $change = query("SELECT * from tbl_change where aus='$aus'");
        confirm($change);
        $row_exchange = fetchOneObj($change);
        $exchange = $row_exchange->exchange;
        $usd_or_real = $row_exchange->usd_or_real;
        if ($usd_or_real == "usd") {
            $saleprice_txtt = $saleprice_txt;
            $purchaseprice_txtt = $purchaseprice_txt;
        } else {
            $saleprice_txtt = $saleprice_txt / $exchange;
            $purchaseprice_txtt = $purchaseprice_txt / $exchange;
        }

        if (!empty($f_name)) {

            $f_tmp = $_FILES['myfile']['tmp_name'];
            $f_size = $_FILES['myfile']['size'];
            $f_extension = explode('.', $f_name);
            $f_extension = strtolower(end($f_extension));
            $f_newfile = uniqid() . '.' . $f_extension;

            $store = "../productimages/" . $f_newfile;

            if ($f_extension == 'jpg' || $f_extension == 'jpeg' || $f_extension == 'png' || $f_extension == 'gif') {

                if ($f_size >= 1000000) {


                    set_message(' <script>
        Swal.fire({
          icon: "warning",
          title: "Max file should be 1MB"
        });
      </script>');
                    redirect('itemt?productlist');
                } else {

                    if (move_uploaded_file($f_tmp, $store)) {

                        $f_newfile;
                        unlink("../productimages/$image_db");

                        $update = query("UPDATE tbl_product set product='$product_txt' , category='$category_txt' , description='$description_txt' , stock='$stock_txt' , purchaseprice='$purchaseprice_txtt' , saleprice='$saleprice_txtt' , image='$f_newfile' where pid=$id");
                        confirm($update);

                        if ($update) {
                            set_message(' <script>
            Swal.fire({
              icon: "success",
              title: "Product Updated Successfully With New Image"
            });
          </script>');
                            redirect('itemt?productlist');
                        } else {
                            set_message(' <script>
           Swal.fire({
          icon: "error",
          title: "Product Update Failed"
           });
         </script>');
                            redirect('itemt?productlist');
                        }
                    }
                }
            } else {
                set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "only jpg, jpeg, png and gif can be upload"
            });
          </script>');
                redirect('itemt?productlist');
            }
        } else {

            $update = query("UPDATE tbl_product set product='$product_txt' , category='$category_txt' , description='$description_txt' , stock='$stock_txt' , purchaseprice='$purchaseprice_txtt' , saleprice='$saleprice_txtt' , image='$image_db' where pid=$id");
            confirm($update);
            if ($update) {

                set_message(' <script>
      Swal.fire({
     icon: "success",
     title: "Product Updated Successfully"
      });
    </script>');
                redirect('itemt?productlist');
            } else {
                set_message(' <script>
      Swal.fire({
     icon: "error",
     title: "Product Update Failed"
      });
    </script>');
                redirect('itemt?productlist');
            }
        }
    }
}


function show_name_category($catid)
{
    $tblcategory = query("SELECT * from tbl_user where user_id = '$catid'");
    $row = fetchOneObj($tblcategory);
    echo '
    <option selected  value="' . $row->user_id . '">' . $row->username . '</option>
';
}

function show_name_product($id)
{
    $stmt = query("SELECT product FROM tbl_product WHERE pid = ? LIMIT 1", [$id]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    return $row->product ?? '';
}


// /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hang()
{

    if (isset($_POST['btnedit_ch'])) {

        $id = $_POST['btnedit_ch'];

        $select = query("SELECT * from tbl_logo where id=$id");
        confirm($select);
        $row = fetch_assoc($select);
        $image_db = $row['img'];

        // $barcode_txt       =$_POST['txtbarcode'];

        $txtname = $_POST['txtname'];
        $txtaddres = $_POST['txtaddres'];
        $txtrod = $_POST['txtrod'];
        $txtphone = $_POST['txtphone'];


        //Image Code or File Code Start Here..
        $f_name = $_FILES['file']['name'];

        if (!empty($f_name)) {

            $f_tmp = $_FILES['file']['tmp_name'];
            $f_size = $_FILES['file']['size'];
            $f_extension = explode('.', $f_name);
            $f_extension = strtolower(end($f_extension));
            $f_newfile = uniqid() . '.' . $f_extension;

            $store = "../productimages/logo/" . $f_newfile;

            if ($f_extension == 'jpg' || $f_extension == 'jpeg' || $f_extension == 'png' || $f_extension == 'gif') {

                if ($f_size >= 1000000) {


                    set_message(' <script>
                          Swal.fire({
                            icon: "warning",
                            title: "Max file should be 1MB"
                          });
                        </script>');
                    redirect('itemt?taxdis');
                } else {

                    if (move_uploaded_file($f_tmp, $store)) {
                        $f_newfile;
                        if ($image_db != 'logo.png') {
                            unlink("../productimages/logo/$image_db");
                        }

                        $update = query("UPDATE tbl_logo set  name='$txtname',addres='$txtaddres' ,img='$f_newfile',road='$txtrod',phone='$txtphone' where id=$id");
                        confirm($update);

                        if ($update) {
                            set_message(' <script>
            Swal.fire({
              icon: "success",
              title: " Updated Successfully With New Image"
            });
          </script>');
                            redirect('itemt?taxdis');
                        } else {
                            set_message(' <script>
           Swal.fire({
          icon: "error",
          title: " Update Failed"
           });
         </script>');
                            redirect('itemt?taxdis');
                        }
                    }
                }
            } else {
                set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "only jpg, jpeg, png and gif can be upload"
            });
          </script>');
                redirect('itemt?taxdis');
            }
        } else {

            $update = query("UPDATE tbl_logo set name='$txtname',addres='$txtaddres',road='$txtrod',phone='$txtphone'  where id=$id");
            confirm($update);
            if ($update) {

                set_message(' <script>
      Swal.fire({
     icon: "success",
     title: " Updated Successfully"
      });
    </script>');
                redirect('itemt?taxdis');
            } else {
                set_message(' <script>
      Swal.fire({
     icon: "error",
     title: " Update Failed"
      });
    </script>');
                redirect('itemt?taxdis');
            }
        }
    }
}


function edit_registration()
{
    if (isset($_POST['btnedit'])) {
        $aus = $_SESSION['aus'];
        $select = query("SELECT * from tbl_user where user_id =" . $_POST['btnedit']);
        confirm($select);

        $selectt = query("SELECT min(user_id) as user from tbl_user where aus= '$aus' ");
        $roww = fetchOneObj($selectt);
        $user = $roww->user;
        $selectk = query("SELECT * from tbl_user where user_id = '$user'");
        $rows = fetchOneObj($selectk);
        $useremail = $rows->useremail;

        if ($select) {
            $row = fetchOneObj($select);

            if ($row->useremail == $useremail) {
                $default = 'defaultt';
            } else {
                $default = '';
            }

            echo ' <div class="col-md-4">
               <form action="" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label>រូបថត រូបភាព</label>
                <input type="file" class="input-group" name="file" onchange="displayImage(this)" id="profilImg">
                <img  src="../resources/images/userpic/' . $row->img . ' " onclick="triggerClick()" id="profiledisplay">
               </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" class="form-control" placeholder="Enter Name" name="txtname" value="' . $row->username . '" required>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" placeholder="Enter email" name="txtemail" value="' . $row->useremail . '" required>
                </div>
                <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <div class="input-group">
                    <input type="password" placeholder="Password" id="pwd" class="form-control" placeholder="Password" name="txtpassword" >
                    <button type="button" class="input-group-text" id="eye">
                    <span class="fas fa-eye-slash" id="eyeicon"></span>
                   </button>
                </div>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control ' . $default . '" name="txtselect_option" required>
                        <option value="" disabled selected>Select Role</option>
                        <option selected>' . $row->role . '</option>
                        <option>Admin</option>
                        <option>User</option>

                    </select>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-info" value="' . $row->user_id . '" name="btnupdate">Update</button>
                </div>
            </form>

        </div>';
        }
    } else {
        echo '<div class="col-md-4">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
               <label>រូបថត រូបភាព</label>
               <input type="file" class="input-group" name="file" onchange="displayImage(this)" id="profilImg">
               <img  src="../productimages/user/display.jpg " onclick="triggerClick()" id="profiledisplay">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Name</label>
                <input type="text" class="form-control" placeholder="Enter Name" name="txtname" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" placeholder="Enter email" name="txtemail" required>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" placeholder="Password" name="txtpassword" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select class="form-control" name="txtselect_option" required>
                    <option value="" disabled selected>Select Role</option>
                    <option>Admin</option>
                    <option>User</option>

                </select>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" name="btnsave">Save</button>
            </div>
        </form>

    </div>';
    }
}

function update_discount()
{
    if (isset($_POST['btnupdate'])) {

        $discount = $_POST['txtdiscount'];


        $id = $_POST['txtid'];

        if ($discount < 0) {
            set_message(' <script>
          Swal.fire({
            icon: "warning",
            title: "Feild is Empty"
          });
        </script>');
            redirect('itemt?taxdis');
        } else {

            $update = query("UPDATE tbl_taxdis set discount='{$discount}' where taxdis_id =" . $id);
            confirm($update);

            if ($update) {
                set_message(' <script>
            Swal.fire({
              icon: "success",
              title: "Discount Update successfully"
            });
          </script>');
                redirect('itemt?taxdis');
            } else {
                set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "Failed"
            });
          </script>');
                redirect('itemt?taxdis');
            }
        }
    }
}


function ket()
{
    $a = array("ខេត្តបាត់ដំបង", "ខេត្តកំពង់ចាម", "ខេត្តកំពង់ឆ្នាំង", "ខេត្តព្រះសីហនុ (កំពង់សោម)", "ខេត្តកំពង់ស្ពឺ", "ខេត្តកំពង់ធំ", "ខេត្តកំពត", "ខេត្តកោះកុង", "ខេត្តក្រចេះ", "ខេត្តប៉ៃលិន", "រាជធានីភ្នំពេញ", "ខេត្តតាកែវ", "ខេត្តព្រៃវែង", "ខេត្តពោធិ៍សាត់", "ខេត្តសៀមរាប", "ខេត្តស្ទឹងត្រែង", "ខេត្តស្វាយរៀង", "ខេត្តឧត្ដរមានជ័យ", "ខេត្តព្រះវិហារ", "ខេត្តបន្ទាយមានជ័យ", "ខេត្តកណ្តាល", "ខេត្តរតនៈគិរី", "ខេត្តមណ្ឌលគិរី", "ខេត្តត្បូងឃ្មុំ", "ខេត្តកែប");

    foreach ($a as $key => $value):

        echo '<option value="' . $key . '">' . $value . '</option>';
    endforeach;
}

function changepasswordd()
{
    if (isset($_POST['btnupdate'])) {

        $oldpassword_txt = md5($_POST['txt_oldpassword']);
        $newpassword_txt = md5($_POST['txt_newpassword']);
        $rnewpassword_txt = md5($_POST['txt_rnewpassword']);

        //echo $oldpassword_txt."-".$newpassword_txt."-".$rnewpassword_txt;


        // 2 Step) Using of select Query we will get out database records according to useremail.

        $email = $_SESSION['useremail'];

        $select = query("SELECT * from tbl_user where useremail='$email'");
        confirm($select);

        $row = fetch_assoc($select);

        $useremail_db = $row['useremail'];
        $password_db = $row['userpassword'];



        // 3 Step) We will compare the user inputs values to database values.

        if ($oldpassword_txt == $password_db) {

            if ($newpassword_txt == $rnewpassword_txt) {

                // 4 Step) If values will match then we will run update Query. 


                $update = query("UPDATE tbl_user set userpassword='$rnewpassword_txt' where useremail='$email'");
                confirm($update);
                $_SESSION['change'] = 'active';

                if ($update) {
                    set_message(' <script>
                            Swal.fire({
                              icon: "success",
                              title: "Password Updated successfully"
                            });
                          </script>');
                    redirect('itemt?settings');
                } else {
                    set_message(' <script>
                    Swal.fire({
                      icon: "error",
                      title: "Password Not Updated successfully"
                    });
                  </script>');
                    redirect('itemt?settings');
                }

                // $_SESSION['status']="New Password Matched";
                // $_SESSION['status_code']="success";

            } else {
                set_message(' <script>
                    Swal.fire({
                      icon: "error",
                      title: "New Password Deos Not Matched"
                    });
                  </script>');
                redirect('itemt?settings');
            }
        } else {
            $_SESSION['change'] = 'active';


            set_message(' <script>
            Swal.fire({
              icon: "error",
              title: "Password Deos Not Matched"
            });
          </script>');
            redirect('itemt?settings');
        }
    }
}

function save_st()
{

    if (isset($_POST['save_st'])) {

        $id = $_SESSION['userid'];

        $select = query("SELECT * from tbl_user where user_id=$id");
        confirm($select);
        $row = fetch_assoc($select);
        $image_db = $row['img'];

        // $barcode_txt       =$_POST['txtbarcode'];

        $birthdayy = $_POST['birthday'];
        $birthday = date("Y-m-d", strtotime($birthdayy));
        $province = $_POST['province'];
        $username = $_POST['username'];


        //Image Code or File Code Start Here..
        $f_name = $_FILES['file']['name'];

        if (!empty($f_name)) {

            $f_tmp = $_FILES['file']['tmp_name'];
            $f_size = $_FILES['file']['size'];
            $f_extension = explode('.', $f_name);
            $f_extension = strtolower(end($f_extension));
            $f_newfile = uniqid() . '.' . $f_extension;

            $store = "../resources/images/userpic/" . $f_newfile;

            if ($f_extension == 'jpg' || $f_extension == 'jpeg' || $f_extension == 'png' || $f_extension == 'gif') {

                if ($f_size >= 1000000) {


                    set_message(' <script>
                          Swal.fire({
                            icon: "warning",
                            title: "Max file should be 1MB"
                          });
                        </script>');
                    redirect('itemt?settings');
                } else {

                    if (move_uploaded_file($f_tmp, $store)) {
                        $f_newfile;
                        if ($image_db != 'user.png') {
                            unlink("../resources/images/userpic/$image_db");
                        }

                        $update = query("UPDATE tbl_user set  username='$username',img='$f_newfile',location='$province' ,birthday='$birthday' where user_id=$id");
                        confirm($update);

                        if ($update) {
                            set_message(' <script>
            Swal.fire({
              icon: "success",
              title: "General Updated Successfully With New Image"
            });
          </script>');
                            redirect('itemt?settings');
                        } else {
                            set_message(' <script>
           Swal.fire({
          icon: "error",
          title: "General Update Failed"
           });
         </script>');
                            redirect('itemt?settings');
                        }
                    }
                }
            } else {
                set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "only jpg, jpeg, png and gif can be upload"
            });
          </script>');
                redirect('itemt?settings');
            }
        } else {

            $update = query("UPDATE tbl_user set  username='$username',location='$province' ,birthday='$birthday' where user_id=$id");
            confirm($update);
            if ($update) {

                set_message(' <script>
      Swal.fire({
     icon: "success",
     title: "General Updated Successfully"
      });
    </script>');
                redirect('itemt?settings');
            } else {
                set_message(' <script>
      Swal.fire({
     icon: "error",
     title: "General Update Failed"
      });
    </script>');
                redirect('itemt?settings');
            }
        }
    }
}


function addstock()
{
    if (isset($_POST['btnaddpro'])) {

        $txtbarcode = $_POST['txtbarcode'];
        $txtselect_option = $_POST['txtselect_option'];
        $txtstock = $_POST['txtstock'];
        $txtpurchaseprice = $_POST['txtpurchaseprice'];
        $txtsaleprice = $_POST['txtsaleprice'];
        $aus = $_SESSION['aus'];
        $change = query("SELECT * from tbl_change where aus='$aus'");
        confirm($change);
        $row_exchange = fetchOneObj($change);
        $exchange = $row_exchange->exchange;
        $usd_or_real = $row_exchange->usd_or_real;
        if ($usd_or_real == "usd") {
            $saleprice_txtt = $txtsaleprice;
            $purchaseprice_txtt = $txtpurchaseprice;
        } else {
            $saleprice_txtt = $txtsaleprice / $exchange;
            $purchaseprice_txtt = $txtpurchaseprice / $exchange;
        }

        $date = new DateTime('now', new DateTimeZone('Asia/bangkok'));
        $new_date = $date->format('Y-m-d');
        $update = query("UPDATE tbl_product set stock=stock+'{$txtstock}',purchaseprice='{$purchaseprice_txtt}' ,saleprice='{$saleprice_txtt}' where barcode=" . $txtbarcode);
        confirm($update);

        $inserts = query("INSERT into tbl_product_stock ( product_id,stock,price,date,aus) values('{$txtselect_option}','{$txtstock}','{$purchaseprice_txtt}','{$new_date}','{$aus}')");
        if ($update) {
            set_message(' <script>
            Swal.fire({
              icon: "success",
              title: "Add Stock successfully"
            });
          </script>');
            redirect('itemt?Add_stock');
        } else {
            set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "Failed"
            });
          </script>');
            redirect('itemt?Add_stock');
        }
    }
}


function stock_liset()
{
    $aus = $_SESSION['aus'];
    $select = query("SELECT * from tbl_product_stock where aus=$aus order by id desc");
    confirm($select);
    $change = query("SELECT * from tbl_change where aus='$aus'");
    confirm($change);
    $row_exchange = fetchOneObj($change);
    $exchange = $row_exchange->exchange;
    $usd_or_real = $row_exchange->usd_or_real;
    $subtotal = 0;
    $totals = 0;
    $subtotall = 0;
    $totalss = 0;
    $no = 1;
    while ($row = fetchOneObj($select)) {
        $selectt = query("SELECT * from tbl_product where pid= $row->product_id ");
        $roww = fetchOneObj($selectt);

        if ($usd_or_real == "usd") {
            $USD_usd = "$";
            $price = number_format($row->price, 2) . $USD_usd;
            $total = number_format($row->price * $row->stock) . $USD_usd;
            $totall = $row->price * $row->stock;
            $subtotal += $row->price;
            $totals += $totall;
            $totalss = number_format($totals, 2) . $USD_usd;
            $subtotall = number_format($subtotal, 2) . $USD_usd;
        } else {
            $USD_usd = "៛";
            $pricee = $row->price * $exchange;
            $price = number_format($pricee);
            $total = number_format($pricee * $row->stock);
            $totall = $pricee * $row->stock;
            $subtotal += $pricee;
            $totals += $totall;
            $totalss = number_format($totals) . $USD_usd;
            $subtotall = number_format($subtotal) . $USD_usd;
        }

        echo '
           <tr>
           <td>' . $no . '</td>
           <td>' . $row->id . '</td>
           <td>' . show_name_product($row->product_id) . '</td>
           <td>' . $row->stock . '</td>
           <td>' . $price . '</td>
           <td>' . $total . '</td>
           
           <td><image src="../productimages/' . $roww->image . '" class="img-rounded" width="40px" height="40px/"></td>
           
           <td>
           
           <div class="btn-group">
                                         
           <a href="itemt?editstock&id=' . $row->id . '" class="btn btn-success " role="button"><span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="Edit Product"></span></a>
           
           <button id=' . $row->id . '  class="btn btn-danger  btndelete"><span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="Delete Product"></span></button>
           
           </div>
           
           </td>
           
           </tr> 
           
           ';
        $no++;
    }

    echo '<tr>
            <td></td>
            <td></td>
            <td></td>
            <th>សរុប</th>
            <td><span class="badge badgeth badge-danger">' . $subtotall . '</span></td>
            <td><span class="badge badgeth badge-success">' . $totalss . '</span></td>
            <td></td>
            <td></td>
           </tr>';
}



//
function stock_list()
{
    $aus = $_SESSION['aus'];
    $selected_date = $_GET['date'] ?? null;

    // Get stock list (JOIN with product)
    if ($selected_date) {
        $selected_datee = date("Y-m-d", strtotime($selected_date));
        $stmt = query("SELECT ps.*, p.image, p.product 
            FROM tbl_product_stock ps
            JOIN tbl_product p ON ps.product_id = p.pid
            WHERE ps.aus = ? AND DATE(ps.date) = ?
            ORDER BY ps.id DESC
        ", [$aus, $selected_datee]);
    } else {
        $stmt = query("SELECT ps.*, p.image, p.product
            FROM tbl_product_stock ps
            JOIN tbl_product p ON ps.product_id = p.pid
            WHERE ps.aus = ?
            ORDER BY ps.product_id DESC
        ", [$aus]);
    }

    $stocks = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Get exchange rate
    $stmt2 = query("SELECT * FROM tbl_change WHERE aus = ? LIMIT 1", [$aus]);
    $row_exchange = $stmt2->fetch(PDO::FETCH_OBJ);

    $exchange = $row_exchange->exchange ?? 4000;
    $usd_or_real = $row_exchange->usd_or_real ?? "usd";

    // 👉 កំណត់ symbol តាមលំនាំដើម
    if ($usd_or_real === "usd") {
        $symbol = "$";
    } else {
        $symbol = "៛";
    }

    $subtotal = 0;
    $totals = 0;
    $no = 1;

    foreach ($stocks as $row) {
        // កំណត់តម្លៃ
        if ($usd_or_real === "usd") {
            $unit_price = $row->price;
        } else {
            $unit_price = $row->price * $exchange;
        }

        $total_price = $unit_price * $row->stock;

        $subtotal += $unit_price;
        $totals += $total_price;

        echo "
           <tr>
             <td>{$no}</td>
             <td>{$row->id}</td>
             <td>" . show_name_product($row->product_id) . "</td>
             <td>" . $row->date . "</td>
             <td>{$row->stock}</td>
             <td>" . number_format($unit_price, $usd_or_real === "usd" ? 2 : 0) . "{$symbol}</td>
             <td>" . number_format($total_price, $usd_or_real === "usd" ? 2 : 0) . "{$symbol}</td>
             <td><img src='../productimages/{$row->image}' class='img-rounded' width='40' height='40'></td>
             <td>
               <div class='btn-group'>
                 <a href='itemt?editstock&id={$row->id}' class='btn btn-success'>
                   <span class='fa fa-edit' style='color:#fff' data-toggle='tooltip' title='Edit Product'></span>
                 </a>
                 <button id='{$row->id}' class='btn btn-danger btndelete'>
                   <span class='fa fa-trash' style='color:#fff' data-toggle='tooltip' title='Delete Product'></span>
                 </button>
               </div>
             </td>
           </tr>";
        $no++;
    }

    // សរុប
    $subtotall = number_format($subtotal, $usd_or_real === "usd" ? 2 : 0) . $symbol;
    $totalss = number_format($totals, $usd_or_real === "usd" ? 2 : 0) . $symbol;

    echo "
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <th>សរុប</th>
          <td><span class='badge badgeth badge-danger'>{$subtotall}</span></td>
          <td><span class='badge badgeth badge-success'>{$totalss}</span></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>";
}






function update_stock()
{
    if (isset($_POST['btnupdates'])) {
        $idp = $_POST['btnupdates'];
        $aus = $_SESSION['aus'];
        $selects = query("SELECT * from tbl_product_stock where id= $idp");
        $row = fetchOneObj($selects);
        $dbstock = $row->stock;
        $dbid = $row->product_id;
        // $selectp = query("SELECT * from tbl_product where pid= $idp");

        $txtbarcode = $_POST['txtbarcode'];
        $txtselect_option = $_POST['txtselect_option'];
        $txtstock = $_POST['txtstock'];
        $txtpurchaseprice = $_POST['txtpurchaseprice'];
        $txtsaleprice = $_POST['txtsaleprice'];

        $change = query("SELECT * from tbl_change where aus='$aus'");
        confirm($change);
        $row_exchange = fetchOneObj($change);
        $exchange = $row_exchange->exchange;
        $usd_or_real = $row_exchange->usd_or_real;
        if ($usd_or_real == "usd") {
            $saleprice_txtt = $txtsaleprice;
            $purchaseprice_txtt = $txtpurchaseprice;
        } else {
            $saleprice_txtt = $txtsaleprice / $exchange;
            $purchaseprice_txtt = $txtpurchaseprice / $exchange;
        }

        $update = query("UPDATE tbl_product set stock=stock-'{$dbstock}'+'$txtstock',purchaseprice='{$purchaseprice_txtt}',saleprice='{$saleprice_txtt}' where pid =" . $dbid);

        $update = query("UPDATE tbl_product_stock set stock='{$txtstock}',price='{$purchaseprice_txtt}' where id =" . $idp);
        confirm($update);

        if ($update) {
            set_message(' <script>
            Swal.fire({
              icon: "success",
              title: "Stock Update successfully"
            });
          </script>');
            redirect('itemt?stock_list');
        } else {
            set_message(' <script>
            Swal.fire({
              icon: "warning",
              title: "Failed"
            });
          </script>');
            redirect('itemt?stock_list');
        }
    }
}


function show_delete($invoice_id)
{
    if ($_SESSION['useremail'] == "" or $_SESSION['role'] == "Admin") {

        return '
        <button id=' . $invoice_id . ' class="btn btn-danger  btndelete_orderlist"><span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="Delete Order"></span></button>
        ';
    }
}


function show_usd()
{
    $aus = $_SESSION['aus'];
    $change = query("SELECT * from tbl_change where aus='$aus'");
    confirm($change);
    $row_exchange = fetchOneObj($change);
    $exchange = $row_exchange->usd_or_real;
    if ($exchange == "usd") {
        echo '$';
    } else {
        echo '៛';
    }
}

function img_user()
{
    if (isset($_SESSION['userid'])) {
        $id = $_SESSION['userid'];

        // Query with prepared statement
        $stmt = query("SELECT img FROM tbl_user WHERE user_id = ?", [$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['img'])) {
            return $row['img'];   // Return image filename from DB
        } else {
            return "Hello.png";   // Default fallback
        }
    } else {
        return "Hello.png";       // No user logged in
    }
}

function fill_unit()
{
    $aus = $_SESSION['aus'];
    $select = query("SELECT unit_name FROM tbl_unit WHERE aus = ?", [$aus]);

    $options = "";
    foreach ($select as $unit) {
        $options .= '<option value="' . htmlspecialchars($unit['unit_name']) . '">' .
            htmlspecialchars($unit['unit_name']) . '</option>';
    }

    return $options;
}

function unit_name($id)
{
    $aus = $_SESSION['aus'];
    $select = query("SELECT unit_name FROM tbl_unit WHERE pid = ? and aus = ?", [$id,$aus]);

    $options = "";
    foreach ($select as $unit) {
        $options .= '<option value="' . htmlspecialchars($unit['unit_name']) . '">' .
            htmlspecialchars($unit['unit_name']) . '</option>';
    }

    return $options;
}


function display_messag_signin()
{
    if (isset($_SESSION['messagee'])) {
        echo $_SESSION['messagee'];
    }
}
function fetchAllObj($stmt)
{
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function fetchOneObj($stmt)
{
    return $stmt->fetch(PDO::FETCH_OBJ);
}
function fetch_object($stmt)
{
    return $stmt->fetch(PDO::FETCH_OBJ);
}
