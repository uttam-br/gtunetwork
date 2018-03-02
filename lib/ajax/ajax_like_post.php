<?php  
require("../../config.php");
require("../classes/User.php");
require("../classes/Notification.php");

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];
$user = new User($conn,$user_id);

$post_details_query = mysqli_query($conn,"SELECT * FROM posts WHERE id='$post_id' ");

if(mysqli_num_rows($post_details_query) == 1) {
	$row = mysqli_fetch_assoc($post_details_query);
	$total_likes = $row['likes'];
	$post_added_by = $row['added_by'];

	$check_query = mysqli_query($conn,"SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id' ");

	if ( mysqli_num_rows($check_query) == 0 ) {
		$total_likes++;
		$query = mysqli_query($conn,"UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
		$insert_user = mysqli_query($conn,"INSERT INTO likes VALUES ('','$user_id','$post_id') ");

		if($post_added_by != $user_id){
			$notification = new Notification($conn, $user_id);
			$notification->insertNotification($post_id,$post_added_by,'like');
		}
	}
}

?>