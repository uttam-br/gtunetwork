<?php  
require("../../config.php");
require("../classes/User.php");
require("../classes/Message.php");

$user_id = $_SESSION['user_id'];
$user_to = $_POST['user_to'];
$msg = $_POST['msg'];
$date = date("Y-m-d H:i:s");

$message_obj = new Message($conn, $user_id);	
$message_obj->sendMessage($user_to,$msg,$date);
?>