<?php  
require('../../config.php');

$type = $_POST['type'];
$type= htmlentities($type);
$type = mysqli_real_escape_string($conn,$type); 

$user_id = $_SESSION['user_id'];

if($type == 'ann') {

	$id = $_POST['id'];
	$id = htmlentities($id);
	$id = mysqli_real_escape_string($conn,$id);

	$check_user = mysqli_query($conn,"SELECT * FROM anns WHERE id='$id' ");

	if(mysqli_num_rows($check_user) == 1) {
		$row = mysqli_fetch_assoc($check_user);
		$added_by = $row['added_by'];

		if($added_by == $user_id ) {

			$delete_query = mysqli_query($conn,"DELETE FROM anns WHERE id='$id' ");

		}

	}
}

if($type == 'ass') {
	$id = $_POST['id'];
	$id = htmlentities($id);
	$id = mysqli_real_escape_string($conn,$id);
	$check_user = mysqli_query($conn,"SELECT * FROM fac_assigns WHERE id='$id' ");
	if(mysqli_num_rows($check_user) == 1 ) {
		$row = mysqli_fetch_assoc($check_user);
		$added_by = $row['added_by'];
		if($added_by == $user_id) {
			$delete_query = mysqli_query($conn,"DELETE FROM fac_assigns WHERE id='$id' ");
		}
	}
}


if($type == 'notice') {
	$id = $_POST['id'];
	$id = htmlentities($id);
	$id = mysqli_real_escape_string($conn,$id);
	$check_user = mysqli_query($conn,"SELECT * FROM notices WHERE id='$id' ");
	if(mysqli_num_rows($check_user) == 1 ) {
		$row = mysqli_fetch_assoc($check_user);
		$added_by = $row['added_by'];
		if($added_by == $user_id) {
			$delete_query = mysqli_query($conn,"DELETE FROM notices WHERE id='$id' ");
		}
	}
}



?>