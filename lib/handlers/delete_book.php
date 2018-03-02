<?php  
require('../../config.php');
$user_id = $_SESSION['user_id'];

$id = $_POST['id'];
$id = htmlentities($id);
$id = mysqli_real_escape_string($conn,$id);

$check_user = mysqli_query($conn,"SELECT * FROM books WHERE id='$id' ");

if(mysqli_num_rows($check_user) == 1 ) {
	$row = mysqli_fetch_assoc($check_user);
	$added_by = $row['added_by'];

	if($added_by == $user_id) {
		$delete_query = mysqli_query($conn,"DELETE FROM books WHERE id='$id' ");
	}
}


?>