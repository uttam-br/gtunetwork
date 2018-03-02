<?php
require('lib/includes/header.php');
require('lib/includes/unverified_redirect.php');

if(isset($_GET['id'])){
	$post_id = $_GET['id'];
	$post_id=htmlentities($post_id);
	$update_opened_query = mysqli_query($conn,"UPDATE notifications SET opened='yes' WHERE user_to='$user_id' AND link='post.php?id=$post_id'");
} else {
	header("Location: home.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once('assets.php'); ?>
	<title>Post | <?php echo $user->getFirstAndLastName() ?></title>
	<script>
		function toggle<?php echo $post_id; ?>() {
			var element = document.getElementById("toggleComment<?php echo $post_id; ?>");
			if(element.style.display == 'block')
				$('#toggleComment<?php echo $post_id; ?>').toggle();
			else
				$('#toggleComment<?php echo $post_id; ?>').toggle();
		}

	</script>
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

<div class='main_container'>
<?php

$select_query = mysqli_query($conn,"SELECT * FROM posts WHERE id='$post_id'");

$comments_check = mysqli_query($conn,"SELECT * FROM comments WHERE post_id='$post_id'");
$comment_check_num = mysqli_num_rows($comments_check);

$row = mysqli_fetch_assoc($select_query);
$added_by = $row['added_by'];
$post_body = $row['body'];
$added_by_object = new User($conn,$added_by);
if(!$added_by_object->isFriend($user_id)){
	header("Location: home.php");
	exit();
}
$profile_pic = $added_by_object->getProfilePic();
$name = $added_by_object->getFirstAndLastName();
$imagePath = $row['image'];
$date_posted = $row['date_posted'];
$date_time_now = date("Y-m-d H:i:s");
$start_time = new DateTime($date_posted);
$end_time = new DateTime($date_time_now);
$interval = $start_time->diff($end_time);

if($interval->y >= 1) {
	if($interval == 1)
		$time_message = $interval->y . " year ago"; //1 year ago
	else
		$time_message = $interval->y . " years ago"; //1+ year ago
}
else if ($interval-> m >= 1) {
	if($interval->d == 0) {
		$days = " ago";
	}
	else if($interval->d == 1) {
		$days = $interval->d . " day ago";
	}
	else {
		$days = $interval->d . " days ago";
	}

	if($interval->m == 1) {
		$time_message = $interval->m . " month". $days;
	}
	else {
		$time_message = $interval->m . " months". $days;
	}
}
else if($interval->d >= 1) {
	if($interval->d == 1) {
		$time_message = "Yesterday";
	}
	else {
		$time_message = $interval->d . " days ago";
	}
}
else if($interval->h >= 1) {
	if($interval->h == 1) {
		$time_message = $interval->h . " hour ago";
	}
	else {
		$time_message = $interval->h . " hours ago";
	}
}
else if($interval->i >= 1) {
	if($interval->i == 1) {
		$time_message = $interval->i . " minute ago";
	}
	else {
		$time_message = $interval->i . " minutes ago";
	}
}
else {
	if($interval->s < 5) {
		$time_message = "Just now";
	}
	else {
		$time_message = $interval->s . " seconds ago";
	}
}

if($imagePath != ""){
	$imagePath = "<div class='load_post_image'><img src='$imagePath' width='100%'></div>";
}
else
	$imagePath = ""	;

$select_query = mysqli_query($conn,"SELECT * FROM likes WHERE post_id='$post_id'");
$num_of_likes = mysqli_num_rows($select_query);

$liked_query = mysqli_query($conn,"SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id' ");

if(mysqli_num_rows($liked_query) > 0) {
	$class = 'unlike_button';
	$like_button = "<button class='".$class."' id='like_button$post_id' onclick='unlikePost(".$post_id.")'><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> Like</button>";
}
else {
	$class = 'like_button';
	$like_button = "<button class='".$class."' id='like_button$post_id' onclick='likePost(".$post_id.")'><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> Like</button>";
}

$string = '';

$string .= "<div class='posts_status_post'>
				<div class='head'>
					<div class='post_profile_pic'>
						<img src='$profile_pic'>
					</div>
					<div class='posted_by' style='color:#ACACAC'>
						<a href='$added_by'>$name</a><br>
						<span class='time_msg'>$time_message</span>
					</div>
				</div>

				<div id='post_body'>
					$post_body
					<br>
					$imagePath
				</div>

				<div class='newsfeedPostOptions'>
				  <table>
				  <tr>
				   <td>$like_button
				   <span class='likes_link' onclick='loadLikes(".$post_id.")'>
				   	<a><span id='num_of_likes_of_".$post_id."'>".$num_of_likes."</span> Likes
				   	</a>
				   </span>
				   </td>
				   <td><span class='comment_link' onclick='javascript:toggle$post_id();'>
				   <i class='fa fa-comments-o' aria-hidden='true'></i> Comments</span></td>
				  </tr>
				  </table>
				</div>
				
				<div id='likes_div_$post_id' style='display:none;'></div>

				<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
					<iframe src='comment_frame.php?post_id=$post_id' id='comment_iframe'></iframe>
				</div>

			</div>";

echo $string;

?>
</div>
</body>
</html>
