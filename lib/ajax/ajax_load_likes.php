<?php 
require("../../config.php");
require("../classes/User.php");

if(isset($_POST)){
	$user_id = $_SESSION['user_id'];
	$post_id = $_POST['post_id'];
	$select_query = mysqli_query($conn,"SELECT * FROM likes WHERE post_id='$post_id'");
		
	echo "<div class='likes_item'>";

	if(mysqli_num_rows($select_query) > 0) {	

		while($row = mysqli_fetch_assoc($select_query)){
			$id = $row['user_id'];
			if($id == $user_id)
				echo "<a href='$user_id'>You</a>&nbsp;&nbsp;&nbsp;";
			else {	
				$user_obj = new User($conn,$id);
				$username = $user_obj->getFirstAndLastName();
				echo "<a href='$id'>".$username."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}

	} else {
		echo "<p style='text-align:center; color:#999;'>No Likes</p>";
	}
	
	echo "</div>";

}



?>