<?php 
require("../../config.php");
require("../classes/User.php");
require("../classes/Notification.php");

$user_id = $_SESSION['user_id'];

$notifications = new Notification($conn,$user_id);
$num_notifications = $notifications->getUnreadNumber();

if($num_notifications > 0) echo '['.$num_notifications .']';
					
?>