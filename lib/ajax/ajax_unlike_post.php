<?php  
require("../../config.php");
require("../classes/User.php");
require("../classes/Notification.php");

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];
$user = new User($conn,$user_id);

$post_details_query = mysqli_query($conn,"SELECT * FROM posts WHERE id='$post_id'");
$post_details_row = mysqli_fetch_assoc($post_details_query);

$total_likes = $post_details_row['likes'];
$post_added_by = $post_details_row['added_by'];

$check_query = mysqli_query($conn,"SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id'");

if ( mysqli_num_rows($check_query) > 0 ) {
	$total_likes--;
	$query = mysqli_query($conn,"UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
	$delete_user = mysqli_query($conn,"DELETE FROM likes WHERE user_id='$user_id' AND post_id='$post_id' ");
}

?>