<?php

if(isset($_POST['login_button']))  {
  
  $error = false ;  
  
  $enroll = htmlentities($_POST['enroll']);
  $enroll = str_replace(' ','',$enroll);
  $enroll = mysqli_real_escape_string($conn,$enroll);
  $password = htmlspecialchars($_POST['password']);
  $_SESSION['login_enroll'] = $enroll;

  if( filter_var( $enroll,FILTER_VALIDATE_EMAIL ) ) {
    $email = $enroll;
    $password = md5($password);
    $check_enroll = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
    $num_rows = mysqli_num_rows($check_enroll); // checking rows returned
    if($num_rows == 0) {
      $error = true;
      array_push($_SESSION['errors[]'],'login_email_not_registered_error'); // error if no rows
    }
    $check_query = mysqli_query($conn,"SELECT * FROM users WHERE email='$email' AND password='$password'");
    if(mysqli_num_rows($check_query) == 1){
      $row = mysqli_fetch_assoc($check_query);
      $user_id = $row['user_id'];

      $cstrong = true;
      $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
      $stoken = md5($token);
      $insert_query = mysqli_query($conn,"INSERT INTO tokens VALUES ('','$stoken','$user_id') ");

      setcookie("ID",$token,time() + 60 *60 * 24,'/',NULL,TRUE,TRUE);
      
      $_SESSION['user_id'] = $user_id;
      header("Location: home.php");
      exit();
    }
    else
      array_push($_SESSION['errors[]'],'email_invalid'); 
  }

  if(!filter_var($enroll,FILTER_VALIDATE_EMAIL)) {

    if(strlen($enroll)==12) {
      if(preg_match('/[0-9]/',$enroll)){
        $check_enroll = mysqli_query($conn, "SELECT user_id FROM users WHERE enroll='$enroll'");
        $num_rows = mysqli_num_rows($check_enroll); // checking rows returned
        if($num_rows == 0) {
          $error = true;
          array_push($_SESSION['errors[]'],'login_enroll_not_registered_error'); // error if no rows
        }
      }
      else{
        $error = true;
        array_push($_SESSION['errors[]'],'login_enroll_error');
      }
    }
    else{
      $error = true;
      array_push($_SESSION['errors[]'],'login_enroll_error'); 
    }

  }
  if(strlen($password)>=5) {
    if(preg_match('/[a-zA-Z0-9!@#$%^&*]/',$password)) {
      $password = md5($password);
    }         
    else{
      $error = true;
      array_push($_SESSION['errors[]'],'password_error');
    }
  }
  else{
    array_push($_SESSION['errors[]'],'password_error');
    $error = true; 
  }

  if($error == false) {
    $query = mysqli_query($conn,"SELECT * FROM users WHERE enroll='$enroll' and password='$password'");
    $num_rows = mysqli_num_rows($query);
    if($num_rows == 1) {
      $row = mysqli_fetch_array($query);
      $user_id = $row['user_id'];

      $token_query = mysqli_query($conn,"SELECT user_id FROM tokens WHERE user_id='$user_id' ");

      if(mysqli_num_rows ($token_query ) > 4) {
        $token_query = mysqli_query($conn,"DELETE FROM tokens WHERE user_id='$user_id' ");
      } 

      $cstrong = true;
      $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
      $stoken = md5($token);
      $insert_query = mysqli_query($conn,"INSERT INTO tokens VALUES ('','$stoken','$user_id') ");

      setcookie("ID",$token,time() + 60 *60 * 24,'/',NULL,TRUE,TRUE);

      $_SESSION['user_id']=$user_id;
      header("Location: home.php");
      exit();
    }
    else {
      $error = true;
      array_push($_SESSION['errors[]'],'password_error');
    }
  }
}
?>