<?php  
require("../../config.php");
require("../classes/User.php");

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn,"SELECT * FROM friend_requests WHERE user_to='$user_id' ");

if(mysqli_num_rows($query) == 0)
	echo "<p style='color:#555; text-align:center; margin:20px auto;'>You have no friend requests.</p>";
else {
	while( $row = mysqli_fetch_array($query)) {
		echo "<div class='home_req_msg'>";
		$user_from = $row['user_from'];
		$user_from_obj = new User($conn,$user_from);

		echo "<span class='home_request_message'><a href='profile.php?user_id=".$user_from_obj->getUserid()."'>" . $user_from_obj->getFirstAndLastName() . "</a> sent you a friend request.</span>";
		echo "</div>";
	}
	echo "<div style='margin:15px; text-align:right;'>Go to <a href='requests.php'>Requests</a> Page</div>";
}

?>