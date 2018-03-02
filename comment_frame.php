<?php 
	require('lib/includes/header.php');	
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php require('assets.php'); ?>
	<style>
		body { background-color:#fff; font-family: 'Nunito', serif;  }
	</style>
</head>
<body>

<?php  
	if(isset($_GET['post_id'])){
		$post_id = $_GET['post_id'];
	}
	$post_query = mysqli_query($conn,"SELECT * FROM posts WHERE id='$post_id' ");
	$row = mysqli_fetch_assoc($post_query);
	$post_added_by = $row['added_by']; 

	if(isset($_POST['postComment'.$post_id])) {
		$post_body = $_POST['post_body'];
		$post_body = mysqli_escape_string($conn, $post_body);
		$date_time_now = date("Y-m-d H:i:s");
		$check = preg_replace('/\s+/','',$post_body);
		if($check != "") {

			$insert_post = mysqli_query($conn,"INSERT INTO comments VALUES ('','$post_body','$user_id','$post_added_by','$date_time_now','no','$post_id' ) ");

			if($post_added_by != $user_id) {
				$notification = new Notification($conn, $user_id);
				$notification->insertNotification($post_id,$post_added_by,'comment');
			}

			$get_commenters = mysqli_query($conn,"SELECT * FROM comments WHERE post_id='$post_id'");
			$notified_users = array();

			while($row = mysqli_fetch_array($get_commenters)) {
				$commenter = $row['user_from'];
				if( $commenter != $post_added_by && $commenter != $user_id && !in_array($commenter,$notified_users)) {
					array_push($notified_users, $commenter);
					$notification = new Notification($conn, $user_id);
					$notification->insertNotification($post_id, $commenter, 'comment_non_owner');
				}
			}
		} // if of check
	} // isset post

	?>
	
	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
		<textarea name="post_body" placeholder="Write Comment Here...." required></textarea>
		<button type='submit' name='postComment<?php echo $post_id; ?>'><i class='fa fa-paper-plane' aria-hidden='true'></i></button>
	</form>

<!-- Load comments -->

<?php
	$get_comments = mysqli_query($conn,"SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id DESC");
	$count = mysqli_num_rows($get_comments);
	if($count != 0) {
		while($comment = mysqli_fetch_array($get_comments)){
			$comment_body = $comment['body'];
			$posted_to = $comment['user_to'];
			$posted_by = $comment['user_from'];
			$date_added = $comment['date_added'];
			$deleted = $comment['deleted'];

			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
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

			$user_obj = new User($conn, $posted_by);
			
			?>

	  		<div class="comment_section">
	  			<a href="<?= $posted_by ?>"><img src="<?= $user_obj->getProfilePic()?>" 
	  				style="float: left; margin-right: 10px; width: 40px;" ></a>
	  			<a href="<?= $posted_by ?>" target="_parent" style="font-size:12px;"><?= $user_obj->getFirstAndLastName() ?></a>
	  			<span class="time_msg"> <?= $time_message ?> </span>
	  			<div style="font-size:12px;"><?= $comment_body ?></div>
	 		</div>

			<?php
	 	
	 	}
	 }
	 else{
	  	echo "<p style='color:#999; margin-top:5px; font-size:12px; text-align:center'>No Comments</p>";
	 }
?>

</body>
</html>