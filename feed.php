<?php
require_once('lib/includes/header.php');
require_once('lib/includes/unverified_redirect.php');

$name = ''; $type = ''; $size = ''; $error = '';

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
if(isset($_POST['post_button'])){
	if(!file_exists($_FILES['fileToUpload']['tmp_name']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
    	$target_file = "";
	} 
	else {
	 	$target_dir = "res/posts/img/";
		$target_file = $target_dir . uniqid() . basename($_FILES['fileToUpload']['name']);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$check = getimagesize($_FILES['fileToUpload']['tmp_name']);
		if($check === false){
			$_SESSION['error'] = 'File is not an image';
			$uploadOk = 0;
		 	exit();
		}
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
				$_SESSION['error'] =  'there was error';
				exit();
			}
		}
	} 
	$privacy = $_POST['privacy'];
	$post = new Post($conn, $user_id);
 	$post->submitPost($_POST['post_body'],'friends',$target_file,$privacy);
}	 
?>
<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>Feed | <?= $user->getFirstAndLastName(); ?></title>
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
			<a class='active' href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
			<a href='announcements.php'><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;Announcements</a>
			<a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
			<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
			<?php
			if($user->getType()=='student') {
				echo '<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
				<a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
			}
			?>
			<a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
		</div>
	</div>

	<div class='col-md-11 nav'>
		<nav id='nav_bar'>
			<a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
			<a  class='active' href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
			<a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
			<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
			<!-- <a href="mycollege.php">My College</a> -->
			<?php
			if($user->getType()=='student') {
				echo '<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
				<a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
			}
			?>
			<a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
		</nav>
	</div>
</div>


<div class='fluid-container middle_bar'>

  <?php require('lib/includes/user_info_left_side.php'); ?>	<!-- this takes col-md-3 -->
 
  <div class="col-md-6 feed_column column">
		
	<div class="post_area">

		<form id='feed_post_upload_form' method="post" action="feed.php" enctype="multipart/form-data">

			<textarea class='post_body_textarea' name="post_body" placeholder="Got something to post ?" required></textarea>
			<label class='addimage_button' for='fileToUpload'><i class="fa fa-picture-o" aria-hidden="true"></i>
			<input type='file' id='fileToUpload' name='fileToUpload'> </label>
			<span class='feed_privacy_select'>Audience&nbsp;&nbsp; 
				<select title='Privacy' name='privacy'>
					<option value='public'>Public</option>
					<option value='friends'>My friends only</option>
					<option value='college'>My college only</option>
				</select>
			</span>
			<input name="post_button" type="submit" value="Post">
			<div class='progress' style="display: none;" id='preg_bar'>
                	<div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                	</div>
              </div>
		</form>

		<div class='preview_box hidden' id='preview_img_div' >
			<img id='preview_img' src=''>
		</div>

		<script>
		$(document).ready(function(){
			$("#fileToUpload").change(function () {
			    readImageData(this);
			});
		});
		</script>
		
		<?php if(isset($_SESSION['success'])){
				echo "<p class='success_msg' style='margin:0; text-align:center;'>".$_SESSION['success']."</p>";
				unset($_SESSION['success']);	}
		?>

	</div>
	<?php 
	if(isset($_SESSION['error'])) { 
		echo "<div class='errors'>". $_SESSION['error']."</div>"; 
		unset($_SESSION['error']); 
	}
	?>
	<div class="posts_area">
	</div>
	
	<img id="loading" src="res/img/loading.gif">

  </div>

<script>
	var funCalled = 'false';
	var user_id = '<?php echo $user_id; ?>';
	$(document).ready(function(){
		var options = { 
	        url : 'feed.php',
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
	    $('#feed_post_upload_form').ajaxForm(options);
		
		$('#loading').show();

		$.ajax({
			url: "lib/ajax/feed_load_posts_friends.php",
			type: "POST",
			data: "page=1",
			cache:false,
			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function(){
			var height = $('.posts_area').height();
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();
			
			var bodyHeight = $(document).height() - $(window).height();
			var scrollPer = (scroll_top / bodyHeight);

			if( ( scrollPer > 0.8 ) && noMorePosts == 'false' && funCalled == 'false')
			{	
				funCalled = 'true';
				var ajaxReq = $.ajax({
					url: "lib/ajax/feed_load_posts_friends.php",
					type: "POST",
					data: "page=" + page,
					cache:false,
					success: function(response) {
						$('.posts_area').find('.nextPage').remove();
						$('.posts_area').find('.noMorePosts').remove();
						$('#loading').hide();
						$('.posts_area').append(response);
						funCalled = 'false';
					}
				});
			} // End if...
			return false;
		});
	});
</script>

	<div id='ann_col' class='col-md-3 column ann_col'>
	    <fieldset>
	    <legend class='ann_legend'><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;Announcements</legend>
	  		<?php $user->getAnnouncements(); ?>
	    </fieldset>
	</div>

</div> <!-- middle bar div -->
<script type="text/javascript">
	$(document).ready(function(){
		var colHeight = $(window).height() - 75;
		$('#ann_col').css('max-height',colHeight);
		
		$('#nav_button').click(function(){
			if($('#mobile_navbar').css("display") == 'none') {
				$('#mobile_navbar').slideDown('fast');
			} else {
				$('#mobile_navbar').slideUp('fast');
			}
		});
	});

	
</script>
</body>
</html>