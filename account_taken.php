<?php  
require('config.php');

function compress_image($source_url, $destination_url, $quality) {
	$info = getimagesize($source_url);
	if ($info['mime'] == 'image/jpeg')
		$image = imagecreatefromjpeg($source_url);
	elseif ($info['mime'] == 'image/gif')
		$image = imagecreatefromgif($source_url);
	elseif ($info['mime'] == 'image/png')
		$image = imagecreatefrompng($source_url);
	imagejpeg($image, $destination_url, $quality);
 	return $destination_url;
}

if(isset($_POST['submit_button'])) {
	$error = false;

	$enroll = $_POST['enroll'];
	$enroll = htmlentities($enroll);
	$enroll = mysqli_real_escape_string($conn,$enroll);

	$check_enroll_entry = mysqli_query($conn,"SELECT * FROM acc_rec WHERE enroll='$enroll' ");

	if(mysqli_num_rows($check_enroll_entry) > 0) {
		$_SESSION['error'] = 'Your request is already in queue.';
		$error = true;
		die();
	}

	$email = $_POST['email'];

	if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
		$_SESSION['error'] = 'Email invalid';
		$error = true;
		die();
	}

	$email = htmlentities($email);
	$email = mysqli_real_escape_string($conn,$email);

	$target_dir = "res/acc_rec/";
	$target_file = $target_dir . uniqid();
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($_FILES['fileToUpload']['name'],PATHINFO_EXTENSION));
	$check = getimagesize($_FILES['fileToUpload']['tmp_name']);
	if($_FILES['fileToUpload']['size']>15728640){
		$_SESSION['error'] = 'sorry file is too large.';
		$uploadOk = 0;
		exit();
	}
	if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif'){
		$_SESSION['error'] = 'Sorry file type is not supported.';
		$uploadOk = 0;
		exit();
	}
	if($uploadOk == 0){
		$target_file = '';
		echo 'upload error';
		exit();
	} else {
		if ($_FILES['fileToUpload']['size'] <= 50000) {
			$upload = compress_image($_FILES["fileToUpload"]["tmp_name"], $target_file, 80);	
	    }
		else if ($_FILES['fileToUpload']['size'] <= 250000) {
			$upload = compress_image($_FILES["fileToUpload"]["tmp_name"], $target_file, 40);	
	    }
	    else if ($_FILES['fileToUpload']['size'] <= 524290) {
			$upload = compress_image($_FILES["fileToUpload"]["tmp_name"], $target_file, 30);	
	    }
		else if($_FILES['fileToUpload']['size'] <= 1048576)
			$upload = compress_image($_FILES["fileToUpload"]["tmp_name"], $target_file, 20);	
	    else if($_FILES['fileToUpload']['size'] <  15728640)
			$upload = compress_image($_FILES["fileToUpload"]["tmp_name"], $target_file, 10);	
		else{
			$uploadOk = 0;
			$_SESSION['error'] =  'there was error';
			exit();
		}
	}


	if(strlen($enroll) != 12 || !preg_match('/[0-9]/', $enroll)) {
		$_SESSION['error'] = 'Enrollment number invalid';
		$error = true;
		exit();
	}

	$image = $target_file;

	if($uploadOk == 1 && $error == false) {
		$insert_query = mysqli_query($conn,"INSERT INTO acc_rec VALUES('','$enroll','$email','$image') ");
	}

}
?>

<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>Account Recovery</title>
	<link href="assets/css/index_style.css" rel="stylesheet">
	<script src="assets/js/index_javascript.js"></script>

	<script>
		$(document).ready(function(){
		  	var options = { 
		        url : 'account_taken.php',
		        type : 'POST',
		        beforeSubmit : function(){
		          $('#preg_bar').show();
		          $('.progress-bar').width('0%')
		        },
		        uploadProgress : function(event, position, total, percentComplete) {
		          $('.progress-bar').width(percentComplete+'%')
		          $('.progress-bar').html('<div id="progress-status">'+percentComplete+' %</div>')
		        },
		        success : function(){
		          $('#preg_bar').hide();
		          document.location.reload(false);
		        }
		    }; 
		    $('#rec_form').ajaxForm(options);
		});
	</script>

</head>
<body>

<div class='fluid-container mobile_top_bar'>
	<div class='col-md-1 logo'>
		<a href='https://www.gtunetwork.com'>GTUnetwork</a>
	</div>
	<div class='col-md-1 mobile_top_logo text-center'>
		<a href='https://www.gtunetwork.com'>GTUnetwork</a>
	</div>

</div>


<div class='container acc_rec_middle_col column' >

	<?php if(isset($_SESSION['error'])) {
		echo "<div class='basic_info_div column errors_col'>".$_SESSION['error']."</div>"; unset($_SESSION['error']);
	} ?>

	<form id='rec_form' method="POST" action='account_taken.php'>
		<br>
		Enter Enrollment Number
		<input type='text' name='enroll' placeholder="Enrollment Number" required>
		<legend class='viy_leg'>Verify its you</legend>
		<p>Upload photo of your College ID card</p>
		<input type='file' name='fileToUpload' required="">
		<br>
		<p>Get notified when we done verification</p>
		<input type='email' name='email' placeholder="Enter your Email" required="">
		<div class='progress' style="display: none;" id='preg_bar'>
        	<div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
        	</div>
      	</div>
		<input type='submit' name='submit_button' value='Submit'>
	</form>

</div>

</body>
</html>