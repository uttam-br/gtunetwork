<?php 
	$error = false;
	$fname = $lname = $enroll = $email = $password = $confirm_password = "";
	$_SESSION['errors[]'] = array();
?>

<?php  
if(isset($_POST['signup_button'])) {
	$error = false;

	$enroll = htmlentities($_POST['enroll']);
	$enroll = str_replace(' ','',$enroll);
	$enroll = mysqli_real_escape_string($conn,$enroll);
	$_SESSION['enroll'] = $enroll;

	$fname = htmlentities($_POST['fname']);
	$fname = str_replace(' ', '',  $fname);
	$fname = ucfirst(strtolower($fname));
	$fname = mysqli_real_escape_string($conn,$fname);
	$_SESSION['fname']=$fname;

	$lname = htmlentities($_POST['lname']);
	$lname = str_replace(' ', '',  $lname);
	$lname = ucfirst(strtolower($lname));
	$lname = mysqli_real_escape_string($conn,$lname);
	$_SESSION['lname']=$lname;

	$email = htmlentities($_POST['email']);
	$email = strtolower($email);
	$email = mysqli_real_escape_string($conn,$email);
	$_SESSION['email'] = $email;

	$password = htmlentities($_POST['password']);
	$confirm_password = htmlentities($_POST['confirm_password']);

	if(strlen($fname) < 3 || strlen($fname) > 32) {
		array_push($_SESSION['errors[]'],"fname_error"); $error = true;
	}

	if(strlen($lname) < 3 || strlen($lname) > 32) {
		array_push($_SESSION['errors[]'],"lname_error"); $error = true;
	}

	if(strlen($enroll) == 12){
		if(preg_match('/[0-9]/',$enroll)){
				$enroll_check_query = mysqli_query($conn, "SELECT enroll FROM users WHERE enroll='$enroll' ");
				$num_row = mysqli_num_rows($enroll_check_query);
				if($num_row > 0){
					array_push($_SESSION['errors[]'],"enroll_registered");
					$error = true;
				}
		}
		else{
			array_push($_SESSION['errors[]'],"enroll_invalid");
			$error = true;	
		}
	}
	else{
		array_push($_SESSION['errors[]'],"enroll_invalid");
		$error = true;
	}

	if(filter_var($email,FILTER_VALIDATE_EMAIL)){
		$email = filter_var($email,FILTER_SANITIZE_EMAIL);
		$email_check_query = mysqli_query($conn,"SELECT email FROM users WHERE email='$email'");
		$num_rows = mysqli_num_rows($email_check_query);
		if($num_rows > 0){
			array_push($_SESSION['errors[]'],"email_registered");
			$error = true;
		}
	} else{
		array_push($_SESSION['errors[]'],"email_error"); $error =true;
	}

	if($password == $confirm_password){
		if(strlen($password)>5){
			if(preg_match('/[a-zA-Z0-9!@#$%*]/',$password)){
				$password = md5($password);
			}
			else{
				array_push($_SESSION['errors[]'],"password_invalid");
				$error = true;
			}
		}
		else{
		array_push($_SESSION['errors[]'],"password_too_short");
		$error = true;
		}
	}
	else{
		$error = true;
		array_push($_SESSION['errors[]'],"passwords_not_match");
	}

	if($error == false){
		$mob = "";	/// mobile number is also needed to be taken from the user as time passes.
		$dob = ""; // this is dob that is needed to be taken from the user.
		$code=substr(md5(time()),0,4);
		$profile_pic ="res/profile/profile_pics/default.png";
		$year_of_adm = '20'.substr($enroll,0,2);
		$college_code = substr($enroll,2,3);
		$course_code = substr($enroll,5,2);
		$branch_code = substr($enroll,7,2);
		$roll_no = substr($enroll,9,3);
		$signup_date = date("Y-m-d");
		$last_active = date("Y-m-d H:i:s");

		$check_college_code = mysqli_query($conn,"SELECT * FROM institutes WHERE code='$college_code' ");

		if(mysqli_num_rows($check_college_code) == 0 ) {
			$_SESSION['error'] = 'Registration is for Degree Engineering only.';
			header("Location: index.php"); exit();
		}

		$check_department_code = mysqli_query($conn,"SELECT * FROM department WHERE code='$branch_code' ");

		if(mysqli_num_rows($check_department_code) == 0) {
			$_SESSION['error'] = 'Enrollment Number Invalid';
			header("Location: index.php"); exit();
		}
		$query = mysqli_query($conn,"INSERT INTO users VALUES('','$fname','$lname','$enroll','$email','$password','$year_of_adm','$college_code','$course_code','$branch_code','$roll_no','$profile_pic', '$signup_date','$last_active',',','$code','no','0','0','$dob','$mob','student','','0') ");
		
		$user_id = mysqli_insert_id($conn);

		$cstrong = true;
	    $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
	    $stoken = md5($token);
	    $insert_query = mysqli_query($conn,"INSERT INTO tokens VALUES ('','$stoken','$user_id') ");

	    setcookie("ID",$token,time() + 60 *60 * 24,'/',NULL,TRUE,TRUE);

		$_SESSION['user_id'] = $user_id;
		header("Location: home.php");
		exit();
	}
}

if(isset($_POST['fac_signup_button'])) {
	$error = false;

	$_SESSION['signup_form_script'] = "<script>
	$(document).ready(function(){ 
		$('#fac_form_btn').addClass('active');
    	$('#student_form_btn').removeClass('active');
    	$('#signup_student_form').addClass('hidden');
    	$('#signup_faculty_form').removeClass('hidden'); 
	});
	</script>";
	
	$fname = htmlentities($_POST['fname']);
	$fname = str_replace(' ', '',  $fname);
	$fname = ucfirst(strtolower($fname));
	$fname = mysqli_real_escape_string($conn,$fname);
	$_SESSION['fac_fname']=$fname;

	$lname = htmlentities($_POST['lname']);
	$lname = str_replace(' ', '',  $lname);
	$lname = ucfirst(strtolower($lname));
	$lname = mysqli_real_escape_string($conn,$lname);
	$_SESSION['fac_lname']=$lname;

	$email = htmlentities($_POST['email']);
	$email = strtolower($email);
	$email = mysqli_real_escape_string($conn,$email);
	$_SESSION['fac_email'] = $email;

	$password = htmlentities($_POST['password']);
	$confirm_password = htmlentities($_POST['confirm_password']);

	if(strlen($fname) < 3 || strlen($fname) > 32) {
		array_push($_SESSION['errors[]'],"fname_error"); $error = true;
	}

	if(strlen($lname) < 3 || strlen($lname) > 32) {
		array_push($_SESSION['errors[]'],"lname_error"); $error = true;
	}

	if(filter_var($email,FILTER_VALIDATE_EMAIL)){
		$email = filter_var($email,FILTER_SANITIZE_EMAIL);
		$email_check_query = mysqli_query($conn,"SELECT email FROM users WHERE email='$email'");
		$num_rows = mysqli_num_rows($email_check_query);
		if($num_rows > 0){
			array_push($_SESSION['errors[]'],"email_registered");
			$error = true;
		}
	} else{
		array_push($_SESSION['errors[]'],"email_error"); $error =true;
	}

	if($password == $confirm_password){
		if(strlen($password)>5){
			if(preg_match('/[a-zA-Z0-9!@#$%*]/',$password)){
				$password = md5($password);
			}
			else{
				array_push($_SESSION['errors[]'],"password_invalid");
				$error = true;
			}
		}
		else{
		array_push($_SESSION['errors[]'],"password_too_short");
		$error = true;
		}
	}
	else{
		$error = true;
		array_push($_SESSION['errors[]'],"passwords_not_match");
	}

	if($error == false){
		$mob = "";	/// mobile number is also needed to be taken from the user as time passes.
		$dob = ""; // this is dob that is needed to be taken from the user.
		$code=substr(md5(time()),0,4);
		$profile_pic ="res/profile/profile_pics/default.png";
		$year_of_adm = "";
		$college_code = "";
		$course_code = "01";
		$branch_code = "";
		$roll_no = "";
		$signup_date = date("Y-m-d");
		$last_active = date("Y-m-d H:i:s");

		$query = mysqli_query($conn,"INSERT INTO users VALUES('','$fname','$lname','$enroll','$email','$password','$year_of_adm','$college_code','$course_code','$branch_code','$roll_no','$profile_pic', '$signup_date','$last_active',',','$code','no','0','0','$dob','$mob','faculty','','0') ");
		
		$user_id = mysqli_insert_id($conn);

		$cstrong = true;
	    $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
	    $stoken = md5($token);
	    $insert_query = mysqli_query($conn,"INSERT INTO tokens VALUES ('','$stoken','$user_id') ");

	    setcookie("ID",$token,time() + 60 *60 * 24,'/',NULL,TRUE,TRUE);

		$_SESSION['user_id']=$user_id;
		$_SESSION['edit_profile_warning'] = 'Please enter your department and college code to continue';
		header("Location: home.php");
		exit();
	}
}

?>
