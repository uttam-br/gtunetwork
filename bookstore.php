<?php  
require_once('lib/includes/header.php');
require_once('lib/includes/unverified_redirect.php');

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


if(isset($_POST['sell_submit'])){
	$_SESSION['errors[]'] = array();
	$error = false;
	$year = $_POST['year'];
	$year = htmlentities($year);
	$year = mysqli_real_escape_string($conn,$year);
	if(!($year == '1' || $year == '2' || $year == '3' || $year == '4')){
		$error = true;
		array_push($_SESSION['errors[]'],'year_error');
	}
	$_SESSION['year'] = $year;

	$title = $_POST['title'];
	$title = htmlentities($title);
	$title = mysqli_real_escape_string($conn,$title);
	$_SESSION['title']=$title;
	if( (strlen($title) < 5) || (strlen($title) > 100) ){
		$error = true;
		array_push($_SESSION['errors[]'],'title_error');
	}

	$description = $_POST['description'];
	$description = htmlentities($description);
	$description = mysqli_real_escape_string($conn,$description);
	$_SESSION['description']= $description;

	if(strlen($description) < 3){
		$error = true;
		array_push($_SESSION['errors[]'],'description_error');
	}

	$contact = $_POST['contact'];
	$contact = htmlentities($contact);
	$contact = mysqli_real_escape_string($conn,$contact);
	$_SESSION['contact']=$contact;

	if(strlen($contact) != 10 || !( preg_match('/[0-9]/', $contact) ) ){
		$error = true;
		array_push($_SESSION['errors[]'],'contact_error');
	}

	$price = $_POST['price'];
	$price = htmlentities($price);
	$price = mysqli_real_escape_string($conn,$price);
	$_SESSION['price']=$price;

	if(!preg_match('/[0-9]/', $price)){
		$error = true;
		array_push($_SESSION['errors[]'],'price_error');
	}

	$target_dir = 'res/bookstore/img/';
	$target_file_image1 = $target_dir . uniqid() . basename($_FILES['image1']['name']);
	$target_file_image2 = $target_dir .uniqid(). basename($_FILES['image2']['name']);
	$uploadOk1 = 1;
	$uploadOk2 = 1;
	$image1_filetype = strtolower(pathinfo($target_file_image1, PATHINFO_EXTENSION));
	$image2_filetype = strtolower(pathinfo($target_file_image2, PATHINFO_EXTENSION));

	$check_image1 = getimagesize($_FILES['image1']['tmp_name']);
	$check_image2 = getimagesize($_FILES['image2']['tmp_name']);

	if($check_image1 === false || $check_image2 === false){
		array_push($_SESSION['errors[]'],'image_error');
		$uploadOk1 = 0;
		$uploadOk2 = 0; 
	}

	if($image1_filetype != 'jpg' && $image1_filetype != 'png' && $image1_filetype != 'jpeg' &&   $image2_filetype != 'jpg' && $image2_filetype != 'png' && $image2_filetype != 'jpeg')
	{	
		array_push($_SESSION['errors[]'],"image_error");
		$uploadOk1 = 0 ; $uploadOk2 = 0;
	}	

	if($uploadOk2 == 0 || $uploadOk1 == 0){
		array_push($_SESSION['errors[]'],"image_error");
	} else {
		if ($_FILES['image1']['size'] <= 50000) {
			$upload = compress_image($_FILES["image1"]["tmp_name"], $target_file_image1, 80);	
	    }
		else if ($_FILES['image1']['size'] <= 250000) {
			$upload = compress_image($_FILES["image1"]["tmp_name"], $target_file_image1, 40);	
	    }
	    else if ($_FILES['image1']['size'] <= 524290) {
			$upload = compress_image($_FILES["image1"]["tmp_name"], $target_file_image1, 30);	
	    }
		else if($_FILES['image1']['size'] <= 1048576)
			$upload = compress_image($_FILES["image1"]["tmp_name"], $target_file_image1, 20);	
	    else if($_FILES['image1']['size'] <  15728640)
			$upload = compress_image($_FILES["image1"]["tmp_name"], $target_file_image1, 10);	
		else{
			array_push($_SESSION['errors[]'],"image_error");
		}
		if ($_FILES['image2']['size'] <= 50000) {
			$upload = compress_image($_FILES["imag1"]["tmp_name"], $target_file_image2, 80);	
	    }
		else if ($_FILES['image2']['size'] <= 250000) {
			$upload = compress_image($_FILES["image2"]["tmp_name"], $target_file_image2, 40);	
	    }
	    else if ($_FILES['image2']['size'] <= 524290) {
			$upload = compress_image($_FILES["image2"]["tmp_name"], $target_file_image2, 30);	
	    }
		else if($_FILES['image2']['size'] <= 1048576)
			$upload = compress_image($_FILES["image2"]["tmp_name"], $target_file_image2, 20);	
	    else if($_FILES['image2']['size'] <  15728640)
			$upload = compress_image($_FILES["image2"]["tmp_name"], $target_file_image2, 10);	
		else{
			array_push($_SESSION['errors[]'],"image_error");
		}
	}

	if($uploadOk1 == 1 && $uploadOk2 == 1 && $error == false){
		$added_by = $user->getUserid();
		$branch_code = $user->getDeptCode();
		$college_code =$user->getCollegeCode();
		$datetime = date("Y-m-d H:i:s");
		$insert_query = mysqli_query($conn,"INSERT INTO books VALUES ('','$title','$description' ,'$contact','$added_by','$datetime','$target_file_image1','$target_file_image2','$price','$branch_code','$college_code','$year' ) ");

		$_SESSION['year'] = $_SESSION['title'] = $_SESSION['description'] = $_SESSION['contact'] = $_SESSION['price'] = "";

		$_SESSION['post_success'] = 'Book Posted Successfully';
		exit();
	}

	exit();
}// if post submit is pressed
?>

<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>Bookstore | <?= $user->getFirstAndLastName() ?></title>
</head>
<body>

<div class='fluid-container top_bar'>
	<div class='col-md-1 logo'>
		<a href='home.php'>GTUnetwork</a>
	</div>
	
	<div class='mobile_nav'>
		<button id='nav_button'><i class="fa fa-bars" aria-hidden="true"></i></button>
		<div id='mobile_navbar'>
			<a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
			<a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
 		    <a href='announcements.php'><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;Announcements</a>
			<a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
			<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
			<?php
			if($user->getType()=='student') {
				echo '<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
				<a class="active" href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
			}
			?>
			<a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
		</div>
	</div>

	<div class='col-md-11 nav'>
		<nav id='nav_bar'>
			<a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
			<a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
			<a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
			<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
			<!-- <a href="mycollege.php">My College</a> -->
			<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
			<a class='active' href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>
			<a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
		</nav>
	</div>
</div>


<div class='fluid-container middle_bar'>
	
  <?php require('lib/includes/user_info_left_side.php'); ?>	<!-- this takes col-md-3 -->

	<div class='col-md-7 column feed_column'>
		
		<h3 class='bookstore_heading'>Bookstore</h3>
	
		<div>
			<p style='margin:10px 0 10px 0; font-size:12px; color:#555;'>You can sell your used books as well as buy used books from others. If you prefer to read ebook then please check out <a href='study.php'>Study Material</a> section.</p>
		</div>

		<p class='success_msg'><?php if(isset($_SESSION['post_success'])) echo $_SESSION['post_success']; unset($_SESSION['post_success']); ?></p>
		
		<div class='main_label'>
			<a class='active' id='sell_button'>Sell Book</a>
			<a id='buy_button'>Buy Book</a>
		</div>

		<div class='sell_form'>

			<form action='bookstore.php' id='sell_form' method="POST" enctype="multipart/form-data">
				<p><select name='year' form="sell_form">
						<option value='0'>Select Year</option>
						<option value='1'>First</option>
						<option value='2'>Second</option>
						<option value='3'>Third</option>
						<option value='4'>Fourth</option>
					</select>
				</p>

				<input type='text' name='title' placeholder='Book Title' value="<?php 
				if(isset($_SESSION['title'])) echo $_SESSION['title']; unset($_SESSION['title']); ?>" required><br>

				<input type='text' class='textarea' name='description' value="<?php 
				if(isset($_SESSION['description'])) echo $_SESSION['description'];  unset($_SESSION['description']);?>" placeholder="Book Description" required><br>

				Book Image Front : <input type='file' name='image1' required>
				Book Image Back : <input type='file' name='image2' required><br>
				
				<input type='tel' name='contact' value="<?php 
				if(isset($_SESSION['contact'])) echo $_SESSION['contact'];  unset($_SESSION['contact']);?>" placeholder='Contact Number' required><br>

				<input type='text' placeholder='Price' value="<?php 
				if(isset($_SESSION['price'])) echo $_SESSION['price']; unset($_SESSION['price']);?>" name='price' required>
				
				<?php  
				if(isset($_SESSION['errors[]'])){
					echo '<div class="errors">';
					if(in_array('year_error',$_SESSION['errors[]'])) echo 'Select Year<br>';
					if(in_array('title_error',$_SESSION['errors[]'])) echo 'Check Book Title<br>';
					if(in_array('description_error',$_SESSION['errors[]'])) echo 'Check Description<br>';
					if(in_array('contact_error',$_SESSION['errors[]'])) echo 'Check Mobile Number<br>';
					if(in_array('price_error',$_SESSION['errors[]'])) echo 'Check Price<br>';
					if(in_array('image_error',$_SESSION['errors[]'])) echo 'Check Images [ Make sure individual files are less than 5mb ]<br>';
					unset($_SESSION['errors[]']);
					echo '</div>';
				}
				?>

				<input type='submit' name='sell_submit' value='Sell'>
				<div class='progress' style="display: none;" id='preg_bar'>
                	<div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                	</div>
              	</div>
				<p class='text-center time_msg'>Uploading files may take some time depending on file size. So be patient.</p>
			</form>

			

		</div>

		<div id='buy_form' class='hidden'>

			<p style='text-align:center'>
				<select name='year' onchange='showBooks(this.value)'>
						<option value='0'>Select Year</option>
						<option value='1'>First</option>
						<option value='2'>Second</option>
						<option value='3'>Third</option>
						<option value='4'>Fourth</option>
					</select>
			</p>
			
			<img id='loading' src='res/img/loading.gif' class='home_loading_gif' style="display: none;">
		
			<div id='bookShowcase'></div>

		</div>
		
	</div>
</div>

<script>
	$(document).ready(function(){

		var options = { 
	        url : 'bookstore.php',
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
	    $('#sell_form').ajaxForm(options);

		$('#nav_button').click(function(){
			if($('#mobile_navbar').css("display") == 'none') {
				$('#mobile_navbar').slideDown('fast');
			} else {
				$('#mobile_navbar').slideUp('fast');
			}
		});

		$('#sell_button').click(function(){
			$('#buy_button').removeClass('active');
			$('#buy_form').addClass('hidden');
			$('#sell_button').addClass('active');
			$('#sell_form').removeClass('hidden');
		});

		$('#buy_button').click(function(){
			$('#sell_button').removeClass('active');
			$('#sell_form').addClass('hidden');
			$('#buy_button').addClass('active');
			$('#buy_form').removeClass('hidden');
		});

	});
		
</script>
</body>
</html>