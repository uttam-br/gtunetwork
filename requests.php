<?php  
include('lib/includes/header.php');
require('lib/includes/unverified_redirect.php');

?>
<!DOCTYPE html>
<html>
<head>	
	<?php require_once('assets.php'); ?>
	<title>Friend Requests | <?= $user->getFirstAndLastName(); ?> </title>
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


<div class="middle_bar" id="main_column">
	<div class="col-md-6 column request_column"> 
	<legend class='friend_requests_legend'>Friend Requests</legend>
	<?php  
		if(isset($_SESSION['req_accept'])){
			echo '<p style="color:#09b83e; margin:20px;">'.$_SESSION['req_accept'].'<p>';
			unset($_SESSION['req_accept']);
		}
		$query = mysqli_query($conn,"SELECT * FROM friend_requests WHERE user_to='$user_id' ");
		if(mysqli_num_rows($query) == 0)
			echo "<p style='color:#555; margin:20px;'>You have no friend requests.</p>";
		else 
		{
			while( $row = mysqli_fetch_array($query)) {
				echo "<div class='req_msg'>";
				$user_from = $row['user_from'];
				$user_from_obj = new User($conn,$user_from);

				echo "<span class='request_message'><a href='profile.php?user_id=".$user_from_obj->getUserid()."'>" . $user_from_obj->getFirstAndLastName() . "</a> sent you a friend request.</span>";

				$user_from_friend_array = $user_from_obj->getFriendArray();

				if(isset($_POST['accept_request'.$user_from])){
					$add_friend_query = mysqli_query($conn,"UPDATE users SET friends=CONCAT(friends,'$user_from,') WHERE user_id='$user_id' ");
					$add_friend_query = mysqli_query($conn,"UPDATE users SET friends=CONCAT(friends,'$user_id,') WHERE user_id='$user_from' ");
					$delete_query = mysqli_query($conn,"DELETE FROM friend_requests WHERE user_to='$user_id' and user_from='$user_from' ");
					$_SESSION['req_accept'] = 'You and '.$user_from_obj->getFirstAndLastName().' are friends !!!';

					$notification = new Notification($conn,$user_id);
					$notification->insertNotification('',$user_from,'request_accept');


					header("Location: requests.php");
					exit();
					// may be need to add exit here for security reasons.
				}
			
				if(isset($_POST['ignore_request'.$user_from])){
					$delete_query = mysqli_query($conn,"DELETE FROM friend_requests WHERE user_to='$user_id' and user_from='$user_from' ");
					echo 'Request Declined!!!';
					header("Location: requests.php");	
				}
				?>
		
				<form id='requests_action_form' action="requests.php" method="POST">
					<div class='requests_action_buttons'>
					<input type="submit" class="request_accept_button" name="accept_request<?= $user_from ?>" id="accept_button" value="Accept">
					<input type="submit" class="request_ignore_button" name="ignore_request<?= $user_from ?>" id="ignore_button" value="Ignore">
					</div>
				</form>
			
				<?php
				echo "</div>";
			}

		}

	?>
	</div>
</div>
</body>
</html>