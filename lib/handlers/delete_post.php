<?php  
require_once("../../config.php");

$post_id = htmlentities($_POST['post_id']);
$post_id = mysqli_real_escape_string($conn,$post_id);

$user_id = $_SESSION['user_id'];

$check_post_owner = mysqli_query($conn,"SELECT * FROM posts WHERE id='$post_id' ");

if(mysqli_num_rows($check_post_owner) == 1) {
	$row = mysqli_fetch_assoc($check_post_owner);
	$added_by = $row['added_by'];

	if($added_by == $user_id) {
		$update_delete_query = mysqli_query($conn,"UPDATE posts SET deleted='yes' WHERE id='$post_id' ");
	}
}
?>