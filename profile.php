<?php  
require('lib/includes/header.php');
require('lib/includes/unverified_redirect.php');

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

if(isset($_GET['user_id'])){
	$profile_user_id = htmlentities($_GET['user_id']);
	$check_user_id = mysqli_query($conn,"SELECT user_id FROM users WHERE user_id='$profile_user_id' ");
	if(mysqli_num_rows($check_user_id) == 0) {
		header("Location: ".$user_id);
		exit();
	}
	
	$profile_user_object = new User($conn,$profile_user_id);
	
	if($profile_user_object->getVisibility() == 1){
		if(!$profile_user_object->isStrictDeptmate($user_id)) {
			header("Location: home.php"); exit();
		}
	} 
	else if( $profile_user_object->getVisibility() == 2 ){
		if(!$profile_user_object->isCollegemate($user_id)) {
			header("Location: home.php"); exit();
		}
	}
	$update_opened_query = mysqli_query($conn,"UPDATE notifications SET opened='yes' WHERE user_to='$user_id' AND link='$profile_user_id'");	
}
else{
	header("Location: ".$user_id);
	exit();
}

$message_obj = new Message($conn,$user_id);

if(isset($_POST['remove_friend'])){
	$user = new User($conn,$user_id);
	$user->removeFriend($profile_user_id);
}
if(isset($_POST['add_friend'])){
	$user = new User($conn,$user_id);
	$user->sendRequest($profile_user_id);
}
if(isset($_POST['respond_request'])){
	header("Location: requests.php");
}
if(isset($_POST['cancel_request'])){
	$user = new User($conn,$user_id);
	$user->cancelRequest($profile_user_id);
}
if(isset($_POST['post_message'])){
		$body = mysqli_real_escape_string($conn, $_POST['message_body']);
		$body = htmlentities($body);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessageFromProfile($profile_user_id,$body,$date);
}
?>

<!DOCTYPE html>
<html>
<head>
	<?php require_once('assets.php'); ?>
	<title>Profile | <?= $profile_user_object->getFirstAndLastName(); ?></title>
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
			<a class='active' href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
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
				<a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
				<a class='active' href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
				<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
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

	<div class='container middle_bar'>
		<div class='row'>
		<div class='col-md-3 column profile_col fonts_fix' id='main_column'>
			<?php  
				echo "<div class='profile_name_heading'>".$profile_user_object->getFirstAndLastName();
				if($user_id == $profile_user_id) 
					echo "<span class='edit_button'><a href='profile_settings.php'><i class='fa fa-pencil' aria-hidden='true'></i></a></span>";
				echo "</div>";
				echo "<div class='user_profile_about'>";
				echo "<span class='user_profile_pic'><img src='".$profile_user_object->getProfilePic()."'>";
				echo "</span>";
				
				echo "<div class='profile_details'>";
				echo '<p>';
				
				if($profile_user_object->getType() == 'student')
					echo $profile_user_object->getEnrollmentNo()."<br>";
				if($profile_user_object->getType() == 'faculty')
					echo 'Email : '.$profile_user_object->getEmail()."<br>";
				echo "Department <br>".$profile_user_object->getDept()."<br>";
				echo "Institute <br>".$profile_user_object->getCollegeName()."<br>";
				echo "Member since " . $profile_user_object->getJoiningDate()."<br>";
				echo '</p>';
				if($profile_user_object->getType() == 'student') {
				echo "<p style='color:green; text-align:center; font-size:20px;'>Rank</p>";
				echo "In Class : <b>" . $profile_user_object->getRankClass()."</b><br>";
				echo "In ".$profile_user_object->getDept()." : <b>" . $profile_user_object->getRankDept()."</b><br>";
				echo "In ".$profile_user_object->getCollegeName()." : <b>" . $profile_user_object->getRankCollege()."</b><br>";
				}
				echo "<br>";
				
				if($user_id != $profile_user_id){

				 	echo "<a id='message_button'>Message <i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
					
					echo "<form action='$profile_user_id' method='POST'>";

					if($user->isFriend($profile_user_id)){
						echo "<input type='submit' name='remove_friend' class='remove_button' value='Remove Friend'<br>";
					}
					else if($user->didReceiveRequest($profile_user_id)){
							echo "<input type='submit' name='respond_request' class='respond_button' value='Respond'<br>";
						}
					else if($user->didSendRequest($profile_user_id)){
						echo "<input type='submit' name='cancel_request' class='requestsent_button' value='Cancel Request'<br>";
					}
					else 
						echo "<input type='submit' name='add_friend' class='addfriends_button' value='Add Friend'<br>";
					
					echo "</form>";

				}
				echo "</div></div>";
				?>
			
		</div>

		<div class='col-md-8 profile_col profile_page_main_column column'>
			<nav class='sub_navigation'>
				<a id='posts_button' class='active'>Posts</a>
				<a id='friends_button'>Friends</a>
			<?php if($profile_user_object->getType() == 'student') { ?>
					<a id='books_button'>Books Ads</a>
			<?php } ?>
			</nav>
			<br>

			<div id='posts_tab'>
				
				<div id='profile_content_div'></div>
			<script>

				var funCalled = 'false';

				var profile_user_id = '<?php echo $profile_user_id; ?>';

				$(document).ready(function(){
					// ajax request for first time posts.
					$.ajax({
						url: "lib/ajax/profile_load_posts_profile.php",
						type: "POST",
						data: "page=1&user_id=" + profile_user_id,
						cache:false,
						success: function(data) {
							$('#profile_content_div').html(data);
						}
					});

					$(window).scroll(function(){
						var height = $('#profile_content_div').height();
						var scroll_top = $(this).scrollTop();
						var page = $('#profile_content_div').find('.nextPage').val();
						var noMorePosts = $('#profile_content_div').find('.noMorePosts').val();
						
						var bodyHeight = $(document).height() - $(window).height();
						var scrollPer = (scroll_top / bodyHeight);

						if( ( scrollPer > 0.8 ) && noMorePosts == 'false' && funCalled=='false' )
						{	
							funCalled = 'true';
							var ajaxReq = $.ajax({
								url: "lib/ajax/profile_load_posts_profile.php",
								type: "POST",
								data: "page=" + page + "&user_id=" + profile_user_id,
								cache:false,
								success: function(response) {
									$('#profile_content_div').find('.nextPage').remove();
									$('#profile_content_div').find('.noMorePosts').remove();
									$('#loading').hide();
									$('#profile_content_div').append(response);
									funCalled = 'false';
								}
							});
						} // End if...
						return false;

					});
				});
			</script>
			</div>

			<div id='friends_tab' class='hidden'>
				<div class='friends_tab'>

				<?php 
				if($user_id != $profile_user_id)
				 	echo "<div class='profile_friends_label'>Mutual Friends 
							<span class='num_mutual_friends'>".$user->getMutualFriends($profile_user_id)."</span>
						  </div>
						  <div id='mutual_friends'>".$user->getMutualFriendsArray($profile_user_id)."</div>";
					
					echo "<div class='profile_friends_label'>Friends 
							<span class='num_mutual_friends'>".$user->numOfFriendsFromProfile($profile_user_id)."</span>
						  </div>
						  <div id='mutual_friends'>".$user->getFriendsArray($profile_user_id)."</div>";
				?>

				</div>
			</div>

			<div id='books_tab' class='hidden'>
				<?php 
					$profile_user_object->loadMyBooks($user_id);
				?>
			</div>

		</div>	
		</div>
	</div>
		
	<div id='messanger' class='col-md-3' style="display:none; "></div>

	<script type="text/javascript">

		$(document).ready(function(){

			$('#nav_button').click(function(){
				if($('#mobile_navbar').css("display") == 'none') {
					$('#mobile_navbar').slideDown('fast');
				} else {
					$('#mobile_navbar').slideUp('fast');
				}
			});

			$(document).mouseup(function(e) {
			    var container = $("#messanger");
			    // if the target of the click isn't the container nor a descendant of the container
			    if (!container.is(e.target)  && container.has(e.target).length === 0  ) 
			    {
			        container.slideUp('medium');
            		clearInterval(functionRef);
			    }
			});
			
			$('#posts_button').click(function(){
				$('#friends_button').removeClass('active');
				$('#friends_tab').addClass('hidden');
				$('#posts_button').addClass('active');
				$('#posts_tab').removeClass('hidden');
				$('#books_button').removeClass('active');
				$('#books_tab').addClass('hidden');
			});

			$('#friends_button').click(function(){
				$('#friends_button').addClass('active');
				$('#friends_tab').removeClass('hidden');
				$('#posts_button').removeClass('active');
				$('#posts_tab').addClass('hidden');
				$('#books_button').removeClass('active');
				$('#books_tab').addClass('hidden');
			});

			$('#books_button').click(function(){
				$('#friends_button').removeClass('active');
				$('#friends_tab').addClass('hidden');
				$('#posts_button').removeClass('active');
				$('#posts_tab').addClass('hidden');
				$('#books_button').addClass('active');
				$('#books_tab').removeClass('hidden');
			});

			$('#message_button').click(function(){
				$.ajax({
					url:'messages.php?u='+'<?= $profile_user_id ?>',
					success : function(data) {
						$('#messanger').html(data);
						$('#messanger').slideDown('medium');
					}
				});
			});

		});

	</script>

</body>
</html>