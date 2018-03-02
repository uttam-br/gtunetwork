<?php 
require("../../config.php");
require("../classes/User.php");

$user_id = $_SESSION['user_id'];
$user = new User($conn,$user_id);

$num_requests = $user->getNumOfRequests();

if($num_requests>0) echo '['.$num_requests.']';
?>