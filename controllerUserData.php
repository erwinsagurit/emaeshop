<?php 
session_start();
require "connection/connect.php";
$username = "";
$email = "";
$f_name = "";
$l_name = "";
$errors = array();

    //if user click continue button in forgot password form
    if(isset($_POST['check-email'])){
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $check_email = "SELECT * FROM users
         WHERE email='$email'";
        $run_sql = mysqli_query($db, $check_email);
        if(mysqli_num_rows($run_sql) > 0){
            $code = rand(999999, 111111);
            $insert_code = "UPDATE users SET code = $code WHERE email = '$email'";
            $run_query =  mysqli_query($db, $insert_code);
            if($run_query){
                $subject = "Password Reset Code";
                $message = "Your password reset code is $code";
                $sender = "From: dagupion2017@gmail.com";
                if(mail($email, $subject, $message, $sender)){
                    $info = "We've sent a passwrod reset otp to your email - $email";
                    $_SESSION['info'] = $info;
                    $_SESSION['email'] = $email;
                    header('location: reset-code.php');
                    exit();
                }else{
                    $errors['otp-error'] = "Failed while sending code!";
                }
            }else{
                $errors['db-error'] = "Something went wrong!";
            }
        }else{
            $errors['email'] = "This email address does not exist!";
        }
    }

        //if user click check reset otp button
        if(isset($_POST['check-reset-otp'])){
            $_SESSION['info'] = "";
            $otp_code = mysqli_real_escape_string($db, $_POST['otp']);
            $check_code = "SELECT * FROM users WHERE code = $otp_code";
            $code_res = mysqli_query($db, $check_code);
            if(mysqli_num_rows($code_res) > 0){
                $fetch_data = mysqli_fetch_assoc($code_res);
                $email = $fetch_data['email'];
                $_SESSION['email'] = $email;
                $info = "Please create a new password that you don't use on any other site.";
                $_SESSION['info'] = $info;
                header('location: new-password.php');
                exit();
            }else{
                $errors['otp-error'] = "You've entered incorrect code!";
            }
        }
        //if user click change password button
        if(isset($_POST['change-password'])){
            $_SESSION['info'] = "";
            $password = mysqli_real_escape_string($db, $_POST['password']);
            $cpassword = mysqli_real_escape_string($db, $_POST['cpassword']);
            if($password !== $cpassword){
                $errors['password'] = "Confirm password not matched!";
            }else{
                $code = 0;
                $email = $_SESSION['email']; //getting this email using session
                $encpass = md5($password);
                $update_pass = "UPDATE users SET code = $code, password = '$encpass' WHERE email = '$email'";
                $run_query = mysqli_query($db, $update_pass);
                if($run_query){
                    $info = "Your password changed. Now you can login with your new password.";
                    $_SESSION['info'] = $info;
                    header('Location: login.php');
                }else{
                    $errors['db-error'] = "Failed to change your password!";
                }
            }
        }
?>