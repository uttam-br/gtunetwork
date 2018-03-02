<?php  
require("../../config.php");
require("../classes/User.php");
require("../classes/Post.php");

$limit = 10; // no of posts to be loaded.. This needs to be fixed for infinite loading...FIX IT

$posts = new Post($conn, $_SESSION['user_id']);
$posts->loadPosts($_REQUEST,$limit,'friends');


?>