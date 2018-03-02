<?php  
require("../../config.php");
require("../classes/User.php");
require("../classes/Message.php");

$user_id = $_SESSION['user_id'];

$message_obj = new Message($conn, $user_id);	

echo $message_obj->getConvos();

?>