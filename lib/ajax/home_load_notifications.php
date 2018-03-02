<?php 
require("../../config.php");
require("../classes/User.php");
require("../classes/Notification.php");

$user_id = $_SESSION['user_id'];

$notifications = new Notification($conn, $user_id);
echo $notifications->getNotifications();
?>