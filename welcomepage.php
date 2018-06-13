<?php  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

	require_once('lib/includes/header.php');

	if($user->verifiedUser()) {
		header("Location: home.php");
		exit();
	}

	$email = $user->getEmail();
	$code = $user->getVerificationCode();

	$mail = new PHPMailer(true);                          

    $mail->SMTPDebug = 0;                                 
    $mail->isSMTP();                                      
    $mail->Host = 'smtp.gmail.com';  
    $mail->SMTPAuth = true;                               
    $mail->Username = 'uttam.rabari.edx@gmail.com';                 
    $mail->Password = 'uttam@7819';                           
    $mail->SMTPSecure = 'ssl';                            
    $mail->Port = 465;                                    

    $mail->setFrom('uttam.rabari.edx@gmail.com', 'Uttam Rabari');
    $mail->addAddress($email);     // Add a recipient
    $mail->addReplyTo('uttam.rabari.edx@gmail.com');

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Verification code - GTUnetwork';
    $mail->Body    = 'Welcome to GTUnetwork.<br>Your verification code is <b>'.$code.'</b>';

	if(isset($_POST['verify_button'])) {
		$email = $_POST['email'];
		$entered_code = $_POST['verification_code'];

		if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
			$_SESSION['error'] = 'Email Incorrect';
			header("Location: welcomepage.php");
			exit();
		}

		$select_query = mysqli_query($conn,"SELECT * FROM users WHERE email='$email' AND email_code='$entered_code' ");

		if(mysqli_num_rows($select_query) == 1) {
			$update_query = mysqli_query($conn,"UPDATE users SET email='$email' , email_verified='yes' WHERE user_id='$user_id' ");
			header("Location: welcomepage.php");
			exit();
		} 

		$_SESSION['error'] = 'Verification code incorrect';
		header("Location: welcomepage.php");
		exit();
	}

	if(isset($_POST['send_mail_btn'])) {

		$email = htmlentities($_POST['email']);

		if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
			$_SESSION['error'] = 'Email ID incorrect';
			header("Location: welcomepage.php"); exit();
		}

		$email = filter_var($email,FILTER_SANITIZE_EMAIL);

		$check_email = mysqli_query($conn,"SELECT * FROM users WHERE email='$email' ");

		if(mysqli_num_rows($check_email) != 0) {
			$_SESSION['error'] = 'Account with that email already exists';
			header("Location: welcomepage.php");
			exit();
		}

		$update_email = mysqli_query($conn,"UPDATE users SET email='$email' WHERE user_id='$user_id' ");
	
		$mail = new PHPMailer(true);                          

	    $mail->SMTPDebug = 0;                                 
	    $mail->isSMTP();                                      
	    $mail->Host = 'gtunetwork.com';  
	    $mail->SMTPAuth = true;                               
	    $mail->Username = 'info@gtunetwork.com';                 
	    $mail->Password = 'uttam@1997';                           
	    $mail->SMTPSecure = 'ssl';                            
	    $mail->Port = 465;                                    

	    $mail->setFrom('info@gtunetwork.com', 'GTUnetwork');
	    $mail->addAddress($email);     // Add a recipient
	    $mail->addReplyTo('info@gtunetwork.com');

	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Verification code - GTUnetwork';
	    $mail->Body    = 'Welcome to GTUnetwork.<br>Your verification code is <b>'.$code.'</b>';

		header("Location: welcomepage.php"); exit();
	}

?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once('assets.php'); ?>
	<title>Welcome to GTUnetwork</title>
	<style type="text/css">
		.btn {
			display: block;
			margin:5px auto;
		}
	</style>
	<link href="assets/css/index_style.css" rel="stylesheet">
	<script src="assets/js/index_javascript.js"></script>
</head>

<body>

<div class='fluid-container mobile_top_bar'>
	<div class='col-md-1 logo'>
		<a href='https://www.gtunetwork.com'>GTUnetwork</a>
	</div>
	<div class='col-md-1 mobile_top_logo'>
		<a href='https://www.gtunetwork.com'>GTUnetwork</a>
	</div>

	<div class='col-md-1 welcomepage_logout_button'><a href='lib/handlers/logout.php'><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a></div>
</div>

<div class='welcomepage_column column'>

	<legend>Email Verification</legend>
	
	<?php if(isset($_SESSION['error'])) {
		echo "<div class='basic_info_div column errors_col'>".$_SESSION['error']."</div>"; unset($_SESSION['error']);
	} ?>

	<button id='chng_mail_btn' class='btn btn-info'>Change email</button><br>

	<form action='welcomepage.php' method='POST'>
	 <?php  if ($mail->send()) { ?>
		<p class='alert alert-success'>Email has been successfully sent to <?= $email ?><br><span class='time_msg'>Please Check Your inbox, spam etc. Please wait atleast 5 min.</span></p> 
	<?php } else { ?>
		<p class='alert alert-danger'>Unable to send email. Please try again later. If problem persist please send us email at info@gtunetwork.com</p>
	<?php } ?>

	<p style="font-size:14px; color:#555;">Please Enter Verfication Code Below</p>
	
		<input type="email" class='hidden' id='email' name="email" value='<?= $email ?>'>
		<input class='btn hidden' type='submit' name='send_mail_btn' id='email_send_button' value='Send Mail'>
		<input type='text' placeholder="Enter Verfication Code" name='verification_code'>
		<input type='submit' value='Verify' name='verify_button'>
	</form>
	<a href='welcomepage.php' class='resend_link'>Resend Code</a>

</div>

</body>

<script type="text/javascript">
	
$(document).ready(function(){
	$('#chng_mail_btn').click(function(){
		$('#email').removeClass('hidden');
		$("#email_send_button").removeClass('hidden');
	});
});

</script>

</html>