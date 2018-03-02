<?php  
require_once('lib/includes/header.php');
require_once('lib/includes/unverified_redirect.php');
$update_notification = mysqli_query($conn,"UPDATE notifications SET opened='yes' WHERE user_to='$user_id' AND link='announcements.php' ");
?>
<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>Announcements | <?= $user->getFirstAndLastName() ?></title>
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
			<a class='active' href='announcements.php'><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;Announcements</a>
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
		</div>
	</div>
</div>

<div id='ann_col' class='column mobile_ann_col'>
    <fieldset>
    <legend class='ann_legend'>Announcements</legend>
  		<?php $user->getAnnouncements(); ?>
    </fieldset>
</div>

</body>
<script>
$(document).ready(function(){
	$('#nav_button').click(function(){
		if($('#mobile_navbar').css("display") == 'none') {
			$('#mobile_navbar').slideDown('fast');
		} else {
			$('#mobile_navbar').slideUp('fast');
		}
	});
});
</script>
</html>