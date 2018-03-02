<?php  
require('../../../config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../PHPMailer/src/Exception.php';
require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';

if(isset($_POST['submit_button'])){

	$enroll = $_POST['enroll'];
	$enroll = htmlentities($enroll);
	$enroll = mysqli_real_escape_string($conn,$enroll);

	if(filter_var($enroll,FILTER_VALIDATE_EMAIL)) {
		$email = $enroll;
	} else {
		$select_query = mysqli_query($conn,"SELECT email FROM users WHERE enroll='$enroll' ");
		$email = mysqli_fetch_assoc($select_query)['email'];
	}

	$check_email = mysqli_query($conn,"SELECT * FROM users WHERE email='$email' ");

	if(mysqli_num_rows($check_email) == 0){
		echo '<p style="text-align:center; color:#e60000">Account does not exists</p>';
		exit();
	}

	$check_query = mysqli_query($conn,"SELECT * FROM scodes WHERE email='$email' ");
	$datetime = mysqli_fetch_assoc($check_query)['datetime'];
	$datetime_obj = new DateTime($datetime);

	$datetimenow = date("Y-m-d H:i:s");
	$datetimenow_obj = new DateTime($datetimenow);

	$interval = $datetime_obj->diff($datetimenow_obj);
		
	$select_sc_query = mysqli_query($conn,"SELECT * FROM scodes WHERE email='$email' ");
	$security_code = mysqli_fetch_assoc($select_sc_query)['scode'];

	if($interval->i > 10 || mysqli_num_rows($check_query) == 0 ) {

		$security_code = substr(md5(time()),0,4);
		
		$datetime = date("Y-m-d	H:i:s");

		if(mysqli_num_rows($check_query) == 0) {
			$db_query = mysqli_query($conn,"INSERT INTO scodes VALUES ('','$security_code','$email','$datetime') ");
		} else {
			$db_query = mysqli_query($conn,"UPDATE scodes SET scode='$security_code', datetime='$datetime' WHERE email='$email' ");
		}
		
	}


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
    $mail->Subject = 'Account Security Code';
    $mail->Body    = 'Ignore this email if you havent initiated password recovery process. Your security code for password reset is : <b>'.$security_code.'</b>';

	if ( $mail->send()) { 
	 echo "<p class='alert alert-success'>Email has been successfully sent.<br><span class='time_msg'>Please Check Your inbox, spam etc. Please wait atleast 5 min.</span></p>"; 
	} else { 
	 echo "<p class='alert alert-danger'>Unable to send email. Please try again later. If problem persist please send email at info@gtunetwork.com</p>";
	} 

	 $_SESSION['pw_reset_email'] = $email;
	 exit();
}

?>