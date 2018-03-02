<?php  
require_once("../../config.php");

$id = htmlentities($_POST['id']);
$id = mysqli_real_escape_string($conn,$id);

$user_id = $_SESSION['user_id'];

$check_user = mysqli_query($conn,"SELECT * FROM myclass WHERE id='$id' ");

if(mysqli_num_rows($check_user) == 1) {
	$row = mysqli_fetch_assoc($check_user);
	$user_added = $row['added_by'];

	if($user_id == $user_added) {
		$delete_query = mysqli_query($conn,"DELETE FROM myclass WHERE id='$id' ");
	}
}

?>