<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function create_acc()
{
    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';
    if (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $email = $_POST['ap_email'];
        $password = $_POST['password'];
        $password_re = $_POST['password_re'];

        $lat = $_POST['latitude'];
        $lng = $_POST['longitude'];

        $email_check = query("SELECT * FROM tbl_user WHERE useremail = '$email'");
        confirm($email_check);
        $username_check = query("SELECT * FROM tbl_user WHERE username = '$username'");
        confirm($username_check);

        if (strlen($username) < 5) {
            set_message("<div class='alert alert-danger text-center'>
            <p>Your username must be at least 5 characters!</p></div>");
        } elseif ($password_re != $password) {
            set_message("<div class='alert alert-danger text-center'>
            <p>Your passwords do not match!</p></div>");

        } elseif ($email_check->rowCount() > 0) {
            set_message("<div class='alert alert-danger text-center'>
            <p>Email that you have entered is already exist!</p></div>");
        } elseif ($username_check->rowCount() > 0) {
            set_message("<div class='alert alert-danger text-center'>
            <p>username that you have entered is already exist!</p></div>");
        } else {
            $tbl_date_free = query("SELECT * FROM tbl_date_free");
            confirm($tbl_date_free);
            $row = fetch_assoc($tbl_date_free);
            $numdatefree = $row['number_of_date'];

            $vkey = rand(999999, 111111);
            $password = password_hash($password, PASSWORD_DEFAULT);
            $numtimm = time();

            $passwordd = $vkey + $numtimm;
            $user_id = $passwordd;

            $useraus_check = query("SELECT * FROM tbl_user WHERE aus = '$user_id'");
            confirm($useraus_check);
            if ($username_check->rowCount() > 0) {
            }

            $date = new DateTime('now', new DateTimeZone('Asia/bangkok'));
            $datee = $date->format('Y-m-d H:i:s');

            $query = query("INSERT INTO tbl_user (username,useremail,userpassword,createdate,date_new,code,aus,tim,lat,lng) VALUES('{$username}','{$email}','{$password}','{$datee}','{$datee}','{$vkey}','{$user_id}','{$numdatefree}','{$lat}','{$lng}')");
            confirm($query);
            $aus = last_id();
            // $ress = query("UPDATE tbl_user set lat='$lat', lng='$lng' where user_id=" . $_SESSION['userid']);

            $query_change = query("INSERT INTO tbl_change (aus) VALUES('{$user_id}')");
            $query_taxdis = query("INSERT INTO tbl_taxdis (aus) VALUES('{$user_id}')");
            $tbl_logo = query("INSERT INTO tbl_logo (name,img,aus) VALUES('TH POS','logo.png','{$user_id}')");
            confirm($query_change);

            // $to = $email;
            // $subject = "Email Verification";
            // $message = "Your verification code is: <h2>$vkey</h2>";
            // $headers = "From: mrrbean88@gmail.com \r\n";
            // $headers .= "MIME-Version: 1.0" . "\r\n";
            // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            $_SESSION['useremail'] = $email;
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thpos.store@gmail.com';
            $mail->Password = 'amob htaq yvew gjev';
            $mail->Port = 587;

            $mail->setFrom('thpos.store@gmail.com');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'THPOS ';
            $mail->Body = "Your verification code is: <h2>$vkey</h2>";
            $mail->send();


            if ($mail) {
                redirect("verify");
                set_message_signin("<div class='alert alert-success text-center'>
                We've sent a verification code to your email - $email</div>");
            }
        }
    }
}

function verify()
{
    if (isset($_POST['cerify_code'])) {
        $vkey = $_POST['code'] ?? '';

        if (empty($_SESSION['useremail'])) {
            redirect("-");
            exit;
        }

        $email = $_SESSION['useremail'];

        // បញ្ជាក់អីមែល និងកូដសម្រាប់ស្វែងរកអ្នកប្រើ
        $query = query("SELECT verified, code, useremail FROM tbl_user WHERE code = ? AND useremail = ?", [$vkey, $email]);
        $queryt = query("SELECT * FROM tbl_user WHERE code = 0");

        if ($queryt->rowCount() > 0) { // កែចេញពី <0 → >0
            set_message_signin("<div class='alert alert-danger text-center'>
                <p>Your account has been verified already.</p></div>");
            redirect("-");
        } elseif ($query->rowCount() > 0) {

            $fetch_data = $query->fetch(PDO::FETCH_ASSOC);
            $fetch_code = $fetch_data['code'];
            $email = $fetch_data['useremail'];
            $code = 0;
            $verified = 1;

            $update = query("UPDATE tbl_user SET code = ?, verified = ? WHERE code = ?", [$code, $verified, $fetch_code]);

            if ($update->rowCount() > 0) {
                set_message_signin("<script>
                    $(function() {
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Verify code success. Now you can login.'
                        })
                    });
                </script>");
                redirect("../-");
            } else {
                set_message_signin("<div class='alert alert-danger text-center'>
                    <p>Failed while updating code!</p></div>");
            }
        } else {
            set_message_signin("<div class='alert alert-danger text-center'>
                You've entered incorrect code!</div>");
        }
    }
}




function forgot_pass()
{
    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    if (isset($_POST['check-email'])) {
        $email = trim($_POST['email']);

        // ✅ ប្រើ PDO query()
        $check_email = query("SELECT * FROM tbl_user WHERE useremail = :email", [':email' => $email]);

        if ($check_email->rowCount() > 0) {
            $code = rand(111111, 999999); // rand(min, max)

            // ✅ update code with PDO
            $insert_code = query("UPDATE tbl_user SET code = :code WHERE useremail = :email", [
                ':code' => $code,
                ':email' => $email
            ]);

            if ($insert_code) {
                $_SESSION['useremail'] = $email;

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'thpos.store@gmail.com';
                $mail->Password = 'mkuq bumn cjzc pkyf'; // ✅ ត្រូវប្រុងប្រយ័ត្ន កុំឲ្យ password លេចសាធារណៈ
                $mail->SMTPSecure = 'tls'; // ✅ ចូលបន្ថែម security
                $mail->Port = 587;

                $mail->setFrom('thpos.store@gmail.com', 'THPOS');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'THPOS Password Reset Code';
                $mail->Body = "Your password reset code is: <h2>$code</h2>";

                if ($mail->send()) {
                    set_message_signin("<script>
                        $(function() {
                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 5000
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'We sent a password reset OTP to your email $email'
                            })
                        });
                    </script>");

                    $_SESSION['useremail'] = $email;
                    header('location: reset-code');
                    exit();
                } else {
                    set_message_signin("<script>
                        $(function() {
                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 5000
                            });
                            Toast.fire({
                                icon: 'error',
                                title: 'Failed while sending code!'
                            })
                        });
                    </script>");
                }
            } else {
                set_message_signin("<script>
                    $(function() {
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        Toast.fire({
                            icon: 'warning',
                            title: 'Something went wrong!'
                        })
                    });
                </script>");
            }
        } else {
            set_message_signin("<script>
                $(function() {
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 5000
                    });
                    Toast.fire({
                        icon: 'info',
                        title: 'This email address does not exist!'
                    })
                });
            </script>");
        }
    }
}


function reset_code()
{
    $email = $_SESSION['useremail'];
    //if user click check reset otp button
    if (isset($_POST['check-reset-otp'])) {

        $otp_code = escape_string($_POST['otp']);
        $check_code = query("SELECT * FROM tbl_user WHERE code = $otp_code and useremail = '$email'");
        if ($check_code->rowCount() > 0) {
            $fetch_data = fetch_assoc($check_code);
            $email = $fetch_data['useremail'];
            $_SESSION['useremail'] = $email;
 
            set_message_signin(" <script>
            $(function() {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 5000
                });
                Toast.fire({
                    icon: 'info',
                    title: 'Please create a new password.'
                })
            });
          </script>");
            header('location: new-password');
            exit();
        } else {
            set_message_signin(" <script>
            $(function() {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 5000
                });
                Toast.fire({
                    icon: 'error',
                    title: 'You entered incorrect code!'
                })
            });
          </script>");
        }
    }
}



function change_pass()
{
    if (isset($_POST['change-password'])) {
        $password = escape_string($_POST['password']);
        $cpassword = escape_string($_POST['cpassword']);
        if ($password !== $cpassword) {
            set_message_signin(" <script>
            $(function() {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 5000
                });
                Toast.fire({
                    icon: 'error',
                    title: 'Confirm password not matched!'
                })
            });
          </script>");
        } else {
            $code = 0;
            $email = $_SESSION['useremail']; //getting this email using session
            $password = md5($password);
            $update_pass = query("UPDATE tbl_user SET code = $code, userpassword = '$password' WHERE useremail = '$email'");
            if ($update_pass) {

                set_message_signin(" <script>
            $(function() {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 5000
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Your password changed. Now you can login with your new password.'
                })
            });
          </script>");
                header('Location: ../-');
            } else {
                $errors['db-error'] = "Failed to change your password!";
            }
        }
    }
}



// ////////////////////////////////////////

function savepay()
{
    if (isset($_POST['payuser'])) {

        $numberidpay = $_POST['numberidpay'];
        $id_service = $_POST['payuser'];

        $f_name = $_FILES['file']['name'];
        $image_temp_location = $_FILES['file']['tmp_name'];
        $aus = $_SESSION['aus'];
        $username = $_SESSION['username'];
        $f_extension = explode('.', $f_name);
        $f_extension = strtolower(end($f_extension));
        $f_newfile = uniqid() . '.' . $f_extension;

        $select = query("SELECT * from tbl_service where id='$id_service'");
        confirm($select);
        $row = fetch_assoc($select);
        $tim = $row['tim'];
        $num_month = $row['num_month'];

        if (!empty($f_name)) {
            // move_uploaded_file($image_temp_location, "../resources/images/userpic/" . $f_name);
            move_uploaded_file($image_temp_location, UPLOAD_DIRECTORY_IDPAY . DS . $f_newfile);
            $image = $f_newfile;
            $insert = query("INSERT INTO tbl_payment (user_name,id_service,numberidpay,img,num_month,tim,aus) values('{$username}','{$id_service}','{$numberidpay}','{$image}','{$num_month}','{$tim}','{$aus}')");
            confirm($insert);

            $id = $_SESSION['userid'];
            $query = query("SELECT * from tbl_user where user_id = '$id' limit 1");
            $row = fetch_object($query);
            $showdate = $row->date_new;
            $date = new DateTime('now', new DateTimeZone('Asia/bangkok'));
            $new_date = $date->format('Y-m-d');
            $datetime4 = new DateTime($new_date);
            $datetime3 = new DateTime($showdate);
            $intervall = $datetime3->diff($datetime4);
            $texttt = $intervall->format('%a');
            $numdatee = $texttt - $row->tim;

            $update = query("UPDATE tbl_user set date_new='$new_date', tim='1' where user_id='$id'");
            confirm($update);
            if ($insert) {

                set_message(' <script>
                Swal.fire({
                  title: "សូមរង់ចាំ... ",
                  text: "អ្នកអាចប្រើបណ្ដោះអាសន្នបាន ប្រព័ន្ធកំពុងធ្វើការអនុម័ត។ លទ្ធផលនឹងត្រូវបានផ្ដល់ឲ្យដឹងក្នុងរយៈពេល១៥នាទីបន្ទាប់។ សូមអរគុណ!",
                  width: 600,
                  padding: "3em",
                  color: "#716add",
                  background: "#fff url(https://sweetalert2.github.io/images/trees.png)",
                  backdrop: `
                    rgba(0,0,123,0.4)
                    url("https://sweetalert2.github.io/images/nyan-cat.gif")
                    left top
                    no-repeat
                  `
                 });
                </script>');
                redirect('itemt?pos');
            }
        } else {
            set_message(' <script>
                Swal.fire({
                  icon: "warning",
                  title: "សូមបញ្ចូលរូបភាពបង់ប្រាក់"
                });
               </script>');
            redirect('itemt?pos');
        }
    }
}


function service_list()
{
    $aus = $_SESSION['aus'];
    $select = query("SELECT * from tbl_service ");
    confirm($select);

    while ($row = fetch_object($select)) {

        if ($row->free == 0) {
            $free = '<span>ឥតគិតថ្លៃ</span>
             <i class="fas fa-times"></i>';
        } else {
            $free = '<span>ឥតគិតថ្លៃ ' . convert_number_kh($row->free) . 'ខែ</span>
             <i class="fas fa-check"></i>';
        }

        echo '<div class="card ' . $row->class . '">
             <div class="top">
                 <div class="title">' . convert_number_kh($row->num_month) . 'ខែ</div>	
                 <div class="price-sec">
                     <span class="dollar">$</span>
                     <span class="price">' . $row->price_show . '</span>
                     <span class="decimal">.99</span>
                 </div>
             </div>
             <div class="info">' . $row->text . '</div>
             <div class="details">
                 <div class="one">
                     <span>' . $row->expires . '</span>
                     <i class="fas fa-check"></i>
                 </div>
                 <div class="one">
                     ' . $free . '
                 </div>

                 <button id=' . $row->id . ' type="button" class="id" data-toggle="modal" data-target="#exampleModalpay">Purchase</button>
             </div>
         </div>';
    }
}


function num_message()
{

    if (!empty($_SESSION['aus'])) {
        $aus = $_SESSION['aus'];
        $select = query("SELECT count(viw) as num from tbl_message where aus='$aus' and viw = 0");
        confirm($select);
        $row = fetch_object($select);
        if ($row->num == 0) {
            $num = '';
        } else {
            $num = $row->num;
        }

        return $num;
    }
}



function timeago($date, $tense = 'ago')
{
    date_default_timezone_set("asia/phnom_penh");
    $time = date($date);
    static $periods = array('year', 'month', 'day', 'hour', 'minute', 'second');
    if (!(strtotime($time) > 0)) {
        return trigger_error("wrong time format: '$time'", E_USER_ERROR);
    }
    $now = new DateTime('now', new DateTimeZone('Asia/bangkok'));
    $time = new DateTime($time);
    $diff = $now->diff($time)->format('%y %m %d %h %i %s');
    $diff = explode(' ', $diff);
    $diff = array_combine($periods, $diff);
    $diff = array_filter($diff);

    $period = key($diff);
    $value = current($diff);
    if (!$value) {
        $period = '';
        $tense = '';
        $value = 'just now';
    } else {
        if ($period == 'day' && $value >= 7) {
            $period = 'week';
            $value = floor($value / 7);
        }
        if ($value > 1) {
            $period .= 's';
        }
    }
    return "$value $period $tense";
}


function show_message_pay()
{
    if (!empty($_SESSION['aus'])) {
        $aus = $_SESSION['aus'];
        $select = query("SELECT * from tbl_message where aus='$aus' order by id DESC");
        confirm($select);
        while ($row = fetch_object($select)) {
            $active = '';
            $messages = $row->messages;
            $danger = '';
            if ($messages == "ការទូទាត់បរាជ័យ") {
                $danger = 'text-danger';
            }
            if ($row->viw == 0) {
                $active = '<i class="fa fa-circle text-success"></i>';
                $messages = '<b>' . $row->messages . '</b>';
                $text = '<p></p>';
            }

            echo '   
        <button id=' . $row->id . ' class=" dropdown-item viw ' . $danger . '" type="button">
        <i class="fas fa-envelope mr-2"></i> ' . $messages . '
        <span class="float-right text-muted text-sm">' . $active . ' ' . timeago($row->date) . '</span>
      </button>';
        }
    }
}
function service_list_dom()
{

    $select = query("SELECT * from tbl_service ");
    confirm($select);
    $i = 4;
    while ($row = fetch_object($select)) {

        if ($row->free == 0) {
            $free = '<i class="ri-close-circle-line"></i>
                    ឥតគិតថ្លៃ';
        } else {
            $free = '<i class="ri-checkbox-circle-line"></i>
                    ឥតគិតថ្លៃ ' . convert_number_kh($row->free) . 'ខែ';
        }

        echo '<div class="card">
            <div class="content">
                <h4 class="fonkh">' . convert_number_kh($row->num_month) . 'ខែ</h4>
                <h3>$' . $row->price_show . '.99</h3>
                <p class="fonkh">
                    <i class="ri-checkbox-circle-line"></i>
                    សុពលភាព ១ខែ $9.99/mo.
                </p>
                <p class="fonkh">
                    ' . $free . '
                </p>
            </div>
            <button id="link' . $i . '" class="btn">Join Now</button>
        </div>';
        $i++;
    }
}
