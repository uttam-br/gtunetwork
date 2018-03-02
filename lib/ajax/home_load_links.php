<?php 
require("../../config.php");
require("../classes/User.php");

$user_id = $_SESSION['user_id'];
$user = new User($conn,$user_id);

$num_requests = $user->getNumOfRequests();

echo $num_requests;
?>