<?php
  require_once('config.php');
  include('lib/handlers/form_handlers/signup_handler.php');
  include('lib/handlers/form_handlers/login_handler.php');
    
  if(isset($_COOKIE['ID'])) {
    $stoken = md5($_COOKIE['ID']);
    $select_query = mysqli_query($conn,"SELECT user_id FROM tokens WHERE token='$stoken' ");
    $row = mysqli_fetch_assoc($select_query);
    $user_id = $row['user_id'];
    $_SESSION['user_id'] = $user_id ;
  }

  if(isset($_SESSION['user_id']))
      header("Location: home.php");
?>

<!DOCTYPE html>
<html>
<head>
 <?php require('assets.php'); ?>
 <meta name="description" content="GTUNetwork aims at making communication between faculties and students more effective. Here you will find all your class assignments, practicals as well as notes from faculties as well as shared by other classmates. There is News Feed which keeps you up to date with information about whats going on in the college. In study material section we provide you with all the necessary material you'll need in your semester studies. And there is also 'Bookstore' section where you can sell your old books and buy used books from others.">
 <meta name="keywords" content="gtu,gtunetwork,gtunetwork.com,college,social networking,gtu practicals,gtu assignments, books, ebooks, pdf books, gtu books">
 <title>GTUNetwork</title>
 <link href="assets/css/index_style.css" rel="stylesheet">
 <script src="assets/js/index_javascript.js"></script>
 <script type="text/javascript">
   function doValidate() {
    var fname = $('#signup_fname').val();
    var lname = $('#signup_lname').val();
    var enroll = $('#signup_enroll').val();
    var email = $('#signup_email').val();
    var password = $('#signup_password').val();
    var confirm_password = $('#signup_confirm_password').val();
    if(fname.length == 0 || lname.length == 0 || enroll.length == 0 || email.length == 0 || password.length == 0 || confirm_password == 0 ) {
      $('#error_div').html('All fields are required');
      return false;
    }
    if(fname.length < 2) {
      $('#error_div').html('First name is too short');
      return false;
    }
    if(lname.length < 2) {
      $('#error_div').html('Last name is too short');
      return false;
    }
    if(enroll.length != 12) {
      $('#error_div').html('Enrollment Number Invalid');
      return false;
    }
    if(password !== confirm_password) {
      $('#error_div').html("Passwords do not match");
      return false;
    }
    if(password.length <= 5) {
      $('#error_div').html('Password too short (min. 6 chars)');
      return false;
    }
    return true;
  }

  function facDoValidate() {
    var fname = $('#signup_fac_fname').val();
    var lname = $('#signup_fac_lname').val();
    var email = $('#signup_fac_email').val();
    var password = $('#signup_fac_password').val();
    var confirm_password = $('#signup_fac_confirm_password').val();

    if(fname.length == 0 || lname.length == 0 || email.length == 0 || password.length == 0 || confirm_password == 0 ) {
      $('#fac_error_div').html('All fields are required');
      return false;
    }
    if(fname.length < 2) {
      $('#fac_error_div').html('First name is too short');
      return false;
    }
    if(lname.length < 2) {
      $('#fac_error_div').html('Last name is too short');
      return false;
    }
    if(password !== confirm_password) {
      $('#fac_error_div').html("Passwords do not match");
      return false;
    }
    if(password.length <= 5) {
      $('#fac_error_div').html('Password too short (min. 6 chars)');
      return false;
    }
    return true;
  }
 </script>
<?php 
 if(isset($_SESSION['signup_form_script'])) {
    echo $_SESSION['signup_form_script'];
    unset($_SESSION['signup_form_script']);
 }
?>
</head>
<body>

<div class="top_bar">

   <div class='col-md-6 col-sm-12 login_form_column'>
      <form id="login_form" method="post" action="index.php">
        <span class='login_inputs'>
          <input type="text" name="enroll" placeholder="Enrollment Number" value="<?php if(isset($_SESSION['login_enroll'])) { echo $_SESSION['login_enroll']; $en = $_SESSION['login_enroll']; unset($_SESSION['login_enroll']); } ?>" required>
          <input type="password" name="password" autocomplete='off' placeholder="Password" required>
        </span>
          <input type="submit" name="login_button" value="Login"><br>
          
          <div class="errors">
            <?php 
              if(in_array('login_enroll_error',$_SESSION['errors[]'])) echo 'Enrollment Number Invalid'; 
              else if(in_array('login_enroll_not_registered_error',$_SESSION['errors[]'])) echo 'Enrollment Number not registered';
              else if(in_array('login_email_not_registered_error',$_SESSION['errors[]'])) echo 'Email not registered';
              else if(in_array('login_error',$_SESSION['errors[]'])) echo "Password Incorrect <a style='color:#5095cf;' href='password_reset.php?en=$en'>Forgot Password ?</a>";
              else if(in_array('password_error',$_SESSION['errors[]'])) echo "Password Incorrect <a style='color:#5095cf;' href='password_reset.php?en=$en'>Forgot Password ?</a>";
            ?>
          </div>
      </form>
    </div>
</div>

<div class="container middle_bar">
 <div class='col-md-6'>

    <div class='intro'>
      <p class='mobile_logo'>GTUnetwork</p>
      <p class='description' style="border:1px solid #eee; padding:10px;">GTUNetwork aims at making communication between faculties and students more effective. Here you will find all your class assignments, practicals as well as notes from faculties as well as shared by other classmates. There is News Feed which keeps you up to date with information about whats going on in the college. In study material section we provide you with all the necessary material you'll need in your semester studies. And there is also 'Bookstore' section where you can sell your old books and buy used books from others.</p>
    </div>

 </div>

 <div class='col-md-6'>
<br>
  <div class='signup_alt_btn'> 
    <button class='active alt_btn' id='student_form_btn'>Student</button> <button class='alt_btn' id='fac_form_btn'>Faculty</button>
  </div>

  <form id="signup_student_form" class='signup_form' method="post" action="index.php">
    
    <div id='error_div' class='errors'>
      <?php 
        if(isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); }
        else {
          if(in_array('enroll_invalid',$_SESSION['errors[]'])) echo 'Check Enrollment Number<br>'; 
          else if(in_array('enroll_registered',$_SESSION['errors[]'])) echo 'Enrollment Number Registered<br>';
          if(in_array('fname_error',$_SESSION['errors[]'])) echo 'Check First Name<br>';
          if(in_array('lname_error',$_SESSION['errors[]'])) echo 'Check Last Name<br>';
          if(in_array('email_error',$_SESSION['errors[]'])) echo 'Check Email ID<br>';
          else if(in_array('email_registered',$_SESSION['errors[]'])) echo 'Email Registered<br>';
          if(in_array('passwords_not_match',$_SESSION['errors[]'])) echo 'Passwords do not Match<br>';
          else if(in_array('password_too_short',$_SESSION['errors[]'])) echo 'Password too short<br>';
          else if(in_array('password_invalid',$_SESSION['errors[]'])) echo 'Check Password<br>'; 
        }
      ?>
    </div>

    <input id='signup_enroll' type="text" name="enroll" placeholder="Enrollment Number" value="<?php if(isset($_SESSION['enroll'])) echo $_SESSION['enroll']; unset($_SESSION['enroll']) ;?>" autocomplete='off' required><br>
    
    <input id='signup_fname' type="text" name="fname" placeholder="First Name" value="<?php if(isset($_SESSION['fname'])) echo $_SESSION['fname']; unset($_SESSION['fname']);?>" autocomplete='off' required><br>
 
    <input id='signup_lname' type="text" name="lname" placeholder="Last Name"  value="<?php if(isset($_SESSION['lname'])) echo $_SESSION['lname']; unset($_SESSION['lname']); ?>" autocomplete='off' required><br>

    <input id='signup_email' type="email" name="email" placeholder="Email ID" value="<?php if(isset($_SESSION['email'])) echo $_SESSION['email']; unset($_SESSION['email']) ;?>" autocomplete='off' required="required"><br>
   
    <input id='signup_password' type="password" name="password" placeholder="Password" autocomplete='off' required="required"><br>

    <input id='signup_confirm_password' type="password" name="confirm_password" autocomplete='off' placeholder="Confirm Password" required="required"><br>
    
    <input id='signup_button' type="submit" onclick='return doValidate();' name="signup_button" value="Sign Up">

    <p class='mobile_signup_p'>Account already taken ? <a href="account_taken.php">Click here</a></p>



  </form>

  <form id="signup_faculty_form" class='signup_form hidden' method="post" action="index.php">
    
    <div id='fac_error_div' class='errors'>
      <?php 
        if(in_array('fname_error',$_SESSION['errors[]'])) echo 'Check First Name<br>';
        if(in_array('lname_error',$_SESSION['errors[]'])) echo 'Check Last Name<br>';
        if(in_array('email_error',$_SESSION['errors[]'])) echo 'Check Email ID<br>';
        else if(in_array('email_registered',$_SESSION['errors[]'])) echo 'Email Registered<br>';
        if(in_array('passwords_not_match',$_SESSION['errors[]'])) echo 'Passwords do not Match<br>';
        else if(in_array('password_too_short',$_SESSION['errors[]'])) echo 'Password too short<br>';
        else if(in_array('password_invalid',$_SESSION['errors[]'])) echo 'Check Password<br>'; 
      ?>
    </div>


    <input id='signup_fac_fname' type="text" name="fname" placeholder="First Name" value="<?php if(isset($_SESSION['fac_fname'])) echo $_SESSION['fac_fname']; unset($_SESSION['fac_fname']);?>" autocomplete='off' required><br>
 
    <input id='signup_fac_lname' type="text" name="lname" placeholder="Last Name"  value="<?php if(isset($_SESSION['fac_lname'])) echo $_SESSION['fac_lname']; unset($_SESSION['fac_lname']); ?>" autocomplete='off' required><br>

    <input id='signup_fac_email' type="email" name="email" placeholder="Email ID [ Use Institute Email ]" value="<?php if(isset($_SESSION['fac_email'])) echo $_SESSION['fac_email']; unset($_SESSION['fac_email']) ;?>" autocomplete='off' required="required"><br>
   
    <input id='signup_fac_password' type="password" name="password" placeholder="Password" autocomplete='off' required="required"><br>

    <input id='signup_fac_confirm_password' type="password" name="confirm_password" autocomplete='off' placeholder="Confirm Password" required="required"><br>

    <input id='signup_button' type="submit" onclick='return facDoValidate();' name="fac_signup_button" value="Sign Up">

  </form>
 
 </div>
 
</div>

<script type="text/javascript">
$(document).ready(function(){
  $('#student_form_btn').click(function(){
    $('#student_form_btn').addClass('active');
    $('#fac_form_btn').removeClass('active');
    $('#signup_faculty_form').addClass('hidden');
    $('#signup_student_form').removeClass('hidden');
  });
  $('#fac_form_btn').click(function(){
    $('#fac_form_btn').addClass('active');
    $('#student_form_btn').removeClass('active');
    $('#signup_student_form').addClass('hidden');
    $('#signup_faculty_form').removeClass('hidden');
  });
});
</script>

</body>
</html>