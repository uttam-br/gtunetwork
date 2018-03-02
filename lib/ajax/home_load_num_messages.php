<?php 
require("../../config.php");
require("../classes/User.php");
require("../classes/Message.php");

$user_id = $_SESSION['user_id'];

$messages = new Message($conn,$user_id);
$num_messages = $messages->getUnreadNumber();

if($num_messages > 0) echo '['.$num_messages .']';

?>