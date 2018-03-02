<?php    
require_once('config.php');
require_once('lib/classes/User.php');
require_once('lib/classes/Post.php');
require_once('lib/classes/Message.php');
require_once('lib/classes/Notification.php');
require_once('lib/classes/Book.php');


if(isset($_SESSION['user_id']) ){
	$user_id = $_SESSION['user_id'];
	$user = new User($conn,$_SESSION['user_id']);
}
else {
	header("Location: index.php");
	exit();
}

?>
