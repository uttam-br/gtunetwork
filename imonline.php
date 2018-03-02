<?php  
require_once('lib/includes/header.php');

if(isset($_POST)) {
	$datetime = date("Y-m-d H:i:s");
	$update_query=mysqli_query($conn,"UPDATE users SET last_active='$datetime' WHERE user_id='$user_id'");
}

?>