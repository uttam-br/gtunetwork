<?php  
require("../../config.php");
require("../classes/User.php");

if(isset($_POST)) {

	$user_id = $_SESSION['user_id'];
	$sem = $_POST['sem'];

	$user_id_object = new User($conn, $user_id);

	$user_id_object->getStudyMaterial($sem);

}
?>