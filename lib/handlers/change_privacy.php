<?php 	 
require_once("../../config.php");

if(isset($_POST))  {
	$option = htmlentities($_POST['option']);
	$option = mysqli_real_escape_string($conn,$option);
	$user_id = $_SESSION['user_id'] ;
	if($option == 0 || $option == 1 || $option == 2 ){
		$update_query = mysqli_query($conn,"UPDATE users SET visibility='$option' WHERE user_id='$user_id' ");
	}
}
?>