<?php  
require('config.php');

if(isset($_POST['pw_reset_submit_button'])) {
	$scode_entered = $_POST['s_code'];
	$scode_entered = htmlentities($scode_entered);
	$scode_entered = mysqli_real_escape_string($conn,$scode_entered);

	$pw = $_POST['new_pw'];
	$pw = htmlentities($pw);
	$pw = mysqli_real_escape_string($conn,$pw);

	$confirm_pw = $_POST['new_confirm_pw'];
	$confirm_pw = htmlentities($confirm_pw);
	$confirm_pw = mysqli_real_escape_string($conn,$confirm_pw);

	if(isset($_SESSION['pw_reset_email'])) {
		$email = $_SESSION['pw_reset_email'];
	} else {
		$_SESSION['error'] = 'No email found for current session. Send security code.';
		header("Location: password_reset.php"); exit();
	}

	$scode_db_query = mysqli_query($conn,"SELECT * FROM scodes WHERE email='$email' ");
	$scode_db = mysqli_fetch_assoc($scode_db_query)['scode'];

	if($scode_db != $scode_entered) {
		$_SESSION['error'] = 'Security code is not correct';
		header("Location: password_reset.php");
		exit();
	} 

	if($pw != $confirm_pw) {
		$_SESSION['error'] = 'Passwords do not match';
		header("Location: password_reset.php");
		exit();	
	}

	if(strlen($pw) < 6){
		$_SESSION['error'] = 'Passwords too short';
		header("Location: password_reset.php");
		exit();		
	}

	$password = md5($pw);

	$update_query = mysqli_query($conn,"UPDATE users SET password='$password' WHERE email='$email' ");

	$delete_scode = mysqli_query($conn,"DELETE FROM scodes WHERE email='$email' ");

	$_SESSION['success'] = 'Password changed successfully. Go to <a href="index.php">Login</a>';
	header("Location: password_reset.php");
	exit();

}

?>
<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>Account Recovery</title>
	<link href="assets/css/index_style.css" rel="stylesheet">
	<script src="assets/js/index_javascript.js"></script>
</head>
<body>


<div class='fluid-container mobile_top_bar'>
	<div class='col-md-12 logo'>
		<a href='https://www.gtunetwork.com'>GTUnetwork</a>
	</div>
	<div class='col-md-1 mobile_top_logo text-center' >
		<a href='https://www.gtunetwork.com'>GTUnetwork</a>
	</div>
</div>

<div class='welcomepage_column column'>

	<?php if(isset($_SESSION['error'])) {
		echo "<div class='basic_info_div column errors_col'>".$_SESSION['error']."</div>"; unset($_SESSION['error']);
	} ?>
	<?php if(isset($_SESSION['success'])) {
		echo "<div class='basic_info_div column success_col'>".$_SESSION['success']."</div>"; unset($_SESSION['success']);
	} ?>

	<legend>Password Reset</legend>

	<p style='font-size:14px; color:#555;'>To initiate password reset process, submit your enrollment number or email.<br> Security code will be sent to your email id.</p>
	<form id='send_s_code_form' action='lib/handlers/pw_reset/ajax_security_code_sender.php' method='POST'>
		<input type="text" name="enroll" placeholder="Email ID / Enrollment Number" required="">
		<input type="submit" name="submit_button" value='Send Security Code'>
	</form>

	<div id='hidden_div'>

	</div>

	<p style='font-size:14px; color:#555;'>Complete below form for recovering password. Enter security code sent to your email and your new password.</p>
	<form action='password_reset.php' method="POST">
		<input type="text" name="s_code" placeholder="Security Code" required="">
		<input type="password" name="new_pw" placeholder="New Password" required="">
		<input type="password" name="new_confirm_pw" placeholder="Confirm Password" required="">
		<input type="submit" name="pw_reset_submit_button" value='Change Password'>
	</form>

</div>
<script>
$(document).ready(function(){
  	var options = { 
        url : 'lib/handlers/pw_reset/ajax_security_code_sender.php',
        type : 'POST',
        beforeSubmit : function(){
        },
        success : function(data){
        	$('#hidden_div').html(data);
        }
    }; 
    $('#send_s_code_form').ajaxForm(options);
});
</script>
</body>
</html>