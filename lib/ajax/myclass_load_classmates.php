<?php  
require("../../config.php");
require("../classes/User.php");

$user_id = $_SESSION['user_id'];
$user_id_object = new User($conn,$user_id);
echo $user_id_object->getClassmatesArray();

?>