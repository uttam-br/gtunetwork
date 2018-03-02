<?php 
require("../../config.php");
require("../classes/User.php");

$user_id = $_SESSION['user_id'];
$user_object = new User($conn,$user_id);

echo $user_object->getChatters();


?>