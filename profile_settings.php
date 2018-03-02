<?php  
require_once('lib/includes/header.php');

$user_id = $_SESSION['user_id'];
$user_object = new User($conn,$user_id);

$name = $user_object->getFirstAndLastName();
$firstname = $user_object->getFirstName();
$lastname = $user_object->getLastName();
$email = $user_object->getEmail();
$profile_pic = $user_object->getProfilePic();
$visiblity = $user_object->getVisibility();
$dept_code = $user_object->getDeptCode();
$college_code = $user_object->getCollegeCode();

if(isset($_POST['upload'])) {
	function getHeight($image) {
        $sizes = getimagesize($image);
        $height = $sizes[1];
        return $height;
    }
    function getWidth($image) {
        $sizes = getimagesize($image);
        $width = $sizes[0];
        return $width;
    }
    function resize512($image,$ext) {
        chmod($image, 0777);
        $oldHeight=getHeight($image);
        $oldWidth=getWidth($image);
        switch ($ext)
        {
            case 1;
                $source = imagecreatefromjpeg($image);
            break;

            case 2;
                $source = imagecreatefromgif($image);
            break;

            case 3;
                $source = imagecreatefrompng($image);
            break;
        }
        $newImage = imagecreatetruecolor(512,512);
        $bgcolor = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $bgcolor);  

        if($oldHeight<$oldWidth) {
            $newImageHeight = 512;
            $newImageWidth = ceil((512*$oldWidth)/$oldHeight);
            imagecopyresampled($newImage,$source,-ceil(($newImageWidth-512)/2),0,0,0,$newImageWidth,$newImageHeight,$oldWidth,$oldHeight);
        } else {
            $newImageHeight = ceil((512*$oldHeight)/$oldWidth);
            $newImageWidth = 512; 
            imagecopyresampled($newImage,$source,0,-ceil(($newImageHeight-512)/2),0,0,$newImageWidth,$newImageHeight,$oldWidth,$oldHeight);
        }
        
        switch ($ext)
        {
            case 1;
        		imagejpeg($newImage,$image,90);
            break;

            case 2;
        		imagegif($newImage,$image,90);
            break;

            case 3;
        		imagepng($newImage,$image,90);
            break;
        }
        return $newImage;
    }	
	$target_path = "res/profile/profile_pics/";                 
	$userfile_name = htmlentities($_FILES["pfpic"]["name"]);      
	$userfile_tmp = htmlentities($_FILES["pfpic"]["tmp_name"]);  
	$userfile_size = $_FILES["pfpic"]["size"];     
	$filename = basename($_FILES["pfpic"]["name"]); 
	$file_ext = htmlentities(strtolower(pathinfo($filename,PATHINFO_EXTENSION))); 
	$large_image_location = $target_path.$filename; 
	$ext='';
	if($file_ext == 'jpg' || $file_ext == 'jpeg')
	    $ext=1;
	else if($file_ext == 'gif')
		$ext=2;
	else if($file_ext == 'png')
		$ext=3;
	else
	    $ext=0;

    $target = $target_path . uniqid() . '.' . $file_ext;
    if($userfile_size > 5242880) {
    	$_SESSION['error'] = 'maximum of 5mb size is allowed';
    	exit();
    }
    if($ext != 0) {
        if(move_uploaded_file($userfile_tmp,$target)) {
            $newImg=resize512($target,$ext);
            $update_query = mysqli_query($conn,"UPDATE users SET profile_pic='$target' WHERE user_id='$user_id' ");
            $_SESSION['success'] = 'Profile picture updated successfully';
            exit();
        } else { 
            $_SESSION['error'] = 'the file could not be uploaded, please try again';
            exit();
		}
    }
    else {
        $_SESSION['error'] =  'this file extension is not accepted, please use "jpg", "jpeg", "gif" or "png" file formats';
    	exit();
    }
}

if(isset($_POST['name_submit'])) {
	$fname = htmlentities($_POST['fname']);
	$lname = htmlentities($_POST['lname']);
	$check_fname = preg_replace('/\s+/', "", $fname);
	$check_lname = preg_replace('/\s+/', "", $lname);
	if(strlen($check_fname) < 2 || strlen($check_lname) < 2) {
		$_SESSION['error'] = 'either first name or last name is invalid';
		header("Location: profile_settings.php");
		exit();
	}
	if(strlen($check_fname) > 32 || strlen($check_lname) > 32) {
		$_SESSION['error'] = 'Maximum allowed characters for name exceeds';
		header("Location: profile_settings.php"); exit();
	}
	$update_query = mysqli_query($conn,"UPDATE users SET first_name='$fname', last_name='$lname' WHERE user_id='$user_id' ");
	$_SESSION['success'] = "Name changed successfully";
	header("Location: profile_settings.php"); exit();
}

if(isset($_POST['password_submit'])) {
	$old_pw = htmlentities($_POST['old_pw']);
	$new_pw = htmlentities($_POST['new_pw']);
	$new_confirm_pw = htmlentities($_POST['new_confirm_pw']);
	$old_pw = md5($old_pw);
	$db_old_pw_query = mysqli_query($conn,"SELECT password FROM users WHERE user_id='$user_id' ");
	$row = mysqli_fetch_assoc($db_old_pw_query);
	$db_old_pw = $row['password'];

	if($old_pw != $db_old_pw) {
		$_SESSION['error'] = 'Old password is incorrect';
		header("Location: profile_settings.php");
		exit();
	}
	if($new_pw != $new_confirm_pw) {
		$_SESSION['error'] = 'Passwords do not match';
		header("Location: profile_settings.php");
		exit();	
	}
	if(strlen($new_pw) < 6) {
		$_SESSION['error'] = 'Password should be minimum of 6 characters';
		header("Location: profile_settings.php");
		exit();
	}
	$new_pw_hashed = md5($new_pw);
	$update_query = mysqli_query($conn,"UPDATE users SET password='$new_pw_hashed' WHERE user_id='$user_id' ");
	$_SESSION['success'] = 'Password changed successfully';
	header("Location: profile_settings.php");
	exit();
}

if(isset($_POST['profile_settings_submit'])) {

	$dept_code = $_POST['dept_code'];
	$college_code = $_POST['college_code'];

	$check_dept_code = mysqli_query($conn,"SELECT * FROM department WHERE code='$dept_code' ");

	if(mysqli_num_rows($check_dept_code) == 0) {
		$_SESSION['error'] = 'Invalid Department Code';
		header("Location: profile_settings.php"); exit();
	}

	$check_college_code = mysqli_query($conn,"SELECT * FROM institutes WHERE code='$college_code' ");

	if(mysqli_num_rows($check_college_code) == 0 ){
		$_SESSION['error'] = 'Invalid College Code';
		header("Location: profile_settings.php"); exit();
	}

	$update_query = mysqli_query($conn,"UPDATE users SET college_id='$college_code', branch_id='$dept_code' WHERE user_id='$user_id' ");

	header("Location: home.php");
	exit();
}

?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once('assets.php'); ?>
	<title>Settings | <?= $user->getFirstAndLastName(); ?></title>
  	<style type="text/css">
		.back_to_home_button{
			margin-left: 5px;
 			background-color: #fff;
			font-size: 16px;
			padding:6px;
			text-align: center;
			display: block;
			margin-right:auto; margin-left: auto;
			text-decoration: none;
		}
		.back_to_home_button:hover{
			text-decoration: none;
		}
	</style>
</head>

<body>

<div class='fluid-container top_bar'>
	<a class='back_to_home_button' href="home.php">&nbsp;<i class="fa fa-home fa-2x" aria-hidden="true"></i>&nbsp;</a>
</div>

<div class="middle_bar">

	<?php if(isset($_SESSION['error'])) {
		echo "<div class='basic_info_div column errors_col'>".$_SESSION['error']."</div>"; unset($_SESSION['error']);
	} ?>
	<?php if(isset($_SESSION['success'])) {
		echo "<div class='basic_info_div column success_col'>".$_SESSION['success']."</div>"; unset($_SESSION['success']);
	} ?>
	<?php  
		if(isset($_SESSION['edit_profile_warning'])) {
			echo "<div class='edit_profile_warning'>";
			echo $_SESSION['edit_profile_warning'];
			echo "</div>";
			unset($_SESSION['edit_profile_warning']);
		}
	?>

	<div class='basic_info_div column'>
		<form id='profile_pic_upload_form' class='profile_picture_update_div' action='<?php echo $_SERVER["PHP_SELF"]; ?>' method='POST' enctype='multipart/form-data'>
			<p>Update Profile Picture</p>
			<span class='time_msg'>Note: Profile Picture may get cropped after upload</span>
			<img id='profile_pic' src='<?= $profile_pic ?>'>
			<div>
				<label class='profile_update_input'>Select Image<input id='pro_pic' type='file' name='pfpic'></label>
				<input type='submit' name='upload' value='Upload profile picture'>
				<div class='progress' style="display: none;" id='preg_bar'>
                	<div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                	</div>
              	</div>
			</div>
			
		</form>

		<script>
		$(document).ready(function(){
			$("#pro_pic").change(function () {
			    readProfileImageData(this);
			});
	      	var options = { 
		        url : 'profile_settings.php',
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
		    $('#profile_pic_upload_form').ajaxForm(options);
	    });
		</script>


		<legend class='profile_setting_legend'>Basic Information</legend>

		<form class='basic_info_form' action= '<?php echo $_SERVER["PHP_SELF"]; ?>' method='POST'>
			<p>First Name <input type='text' id='fname_input' name='fname' value='<?= $firstname ?>'></p>	
			<p>Last Name <input type='text' id='lname_input' name='lname' value='<?= $lastname ?>' ></p>
			<p>Email Address : <b><?= $email ?></b></p>
			<div class='errors' id='basic_info_error'></div>
			<input type='submit' onclick='return validateBasicInfo();' name='name_submit' value='Save'>
		</form>
<?php if($user->getType()=='faculty') { ?>
		<form class='basic_info_form' action= '<?php echo $_SERVER["PHP_SELF"]; ?>' method='POST'>
		<?php  
		if(isset($_SESSION['edit_profile_warning'])) {
			echo "<div class='edit_profile_warning'>";
			echo $_SESSION['edit_profile_warning'];
			echo "</div>";
			unset($_SESSION['edit_profile_warning']);
		}
		?>
			<p>Department GTU Code <input type='text' id='dept_input' name='dept_code' value='<?= $dept_code ?>'></p>	
			<p>College GTU Code <input type='text' id='college_input' name='college_code' value='<?= $college_code ?>' ></p>
			<div class='errors' id='profile_info_error'></div>
			<input type='submit' onclick='return validateDetails();' name='profile_settings_submit' value='Save'>
		</form>
<?php } ?>
	</div>

	<div class='change_password_div column'>
		<legend class='profile_setting_legend'>Login &amp; Security</legend>
		<button id='change_pw_btn'>Change Password</button>
		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
			<div id='pw_div' style='display: none;'>
				<input type='text' id='old_pw' placeholder='Old Password' name='old_pw'>
				<input type='password' id='new_pw' placeholder='New Password' name='new_pw'>
				<input type='password' id='new_confirm_pw' placeholder='Confirm New Password' name="new_confirm_pw">
				<div class='errors' id='password_errors'></div>
				<input type='submit' name='password_submit' onclick='return validatePasswords();' value='Save'>
			</div>
		</form>
	</div>

	<div class='privacy_option_div column'>
		<legend class='profile_setting_legend'>Privacy Setting</legend>
		<p style='margin:10px; color:#666;'>Visiblity of Profile <span class='time_msg'>[ Your friends will always be able to see your profile. Select who else can see your profile.]</span></p>
	<?php if($visiblity == 0) { ?>
		<select class='privacy_select' id='select_visibility'>
			<option value='0' selected>Default</option>
			<option value='1'>Only my Department</option>
			<option value='2'>Only my College</option>
		</select>
	<?php } else if($visiblity == 1) { ?>
		<select class='privacy_select' id='select_visibility'>
			<option value='0'>Default</option>
			<option value='1' selected>Only my Department</option>
			<option value='2'>Only my College</option>
		</select>
	<?php } else if($visiblity == 2) { ?>
		<select class='privacy_select' id='select_visibility'>
			<option value='0'>Default</option>
			<option value='1'>Only my Department</option>
			<option value='2' selected>Only my College</option>
		</select>
		<?php } ?>


		<img id='loading' style='display: none;' src='<?= ROOT_LOCATION ?>/res/img/loading.gif' class='home_loading_gif'>
		<div id='select_option_result_div'></div>
	</div>

</div>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#change_pw_btn').click(function(){
				$('#pw_div').toggle();
			});

			$('#select_visibility').change(function(){
				var value = $('#select_visibility option:selected').val();
				$('#loading').show();
				$.ajax({
					url: 'lib/handlers/change_privacy.php',
					data : "option=" + value,
					type : "POST",
					cache : false,
					success : function(data) {
						$("#loading").hide();
						$('#select_option_result_div').html('Visiblity Changed');
					},
					error : function(){
						$('#loading').hide();
						$('#select_option_result_div').html("Unable to change. please try again");
					} 
				});
			});
		});

		function validateBasicInfo(){
			var fname = $('#fname_input').val();
			var lname = $('#lname_input').val();

			if(fname.length === 0 || fname === "" || lname.length === 0 || lname === "") {
				$('#basic_info_error').html('Input all required fields');
				return false;
			} else if( fname.length < 3 || lname.length < 3 ) {
				$('#basic_info_error').html('Minimum of 3 characters required');
				return false;
			}

			$.ajax({
				
			});
			return true;
		}

		function validatePasswords(){
			var old_pw = $('#old_pw').val();
			var new_pw = $('#new_pw').val();
			var new_confirm_pw = $('#new_confirm_pw').val();

			if( old_pw.length === 0 || new_pw.length === 0 || new_confirm_pw.length === 0 || old_pw === "" || new_pw === "" || new_confirm_pw === ""){
				$('#password_errors').html("All fields are required"); return false;
			}

			if( new_pw !== new_confirm_pw ) {
				$('#password_errors').html("New password and confirm password should match"); return false;
			}

			if( new_pw.length < 6 ) {
				$('#password_errors').html('Password should be minimum of 6 characters');
				return false;
			}		
			return true;
		}

		function validateDetails(){
			var dept_code = $('#dept_input').val();
			var college_code = $('#college_input').val();
			if(dept_code.length != 2) {
				$('#profile_info_error').html('Invaid Department Code');
				return false;
			} 
			if (college_code.length != 3) {
				$('#profile_info_error').html('Invaid College Code');
				return false;
			}
			return true;
		}

	</script>

</body>
</html>