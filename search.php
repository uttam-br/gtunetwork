<?php  
require_once('lib/includes/header.php');

if(isset($_GET)) {

	$query = htmlentities($_GET['search_input']) ;

	$check_query = preg_replace('/\s+/', '', $query);

	if($check_query != "") {
	
		$displayed = 0;

		$college_id = $user->getCollegeCode();

		$return_string = "";
		$names = explode(" ", $query);

		if(count($names) == 2) {
			$usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND college_id='$college_id' ");
			$extUsersReturned = mysqli_query($conn,"SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND college_id<>'$college_id' ");
		}
		else if(preg_match('/[0-9]/', $query)) {
			$usersReturned = mysqli_query ($conn, "SELECT * FROM users WHERE enroll LIKE '%$query%' AND college_id='$college_id' ");
			$extUsersReturned = mysqli_query ($conn, "SELECT * FROM users WHERE enroll LIKE '%$query%' AND college_id<>'$college_id' ");
		}

		else if(count($names) == 1){
			$usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND college_id='$college_id' ");
			$extUsersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND college_id<>'$college_id' ");
		}

		while($row = mysqli_fetch_array($usersReturned)) {

				$user = new User($conn, $row['user_id']);

				if($row['user_id'] == $user_id)
					continue;

				if($user->isFriend($user_id)) {
					//nothing just checking.. hahaha.
				}
				else if($row['visibility'] == '1') {
					if(!$user->isStrictDeptmate($user_id))
						continue;
				} else if($row['visibility'] == '2') {
					if(!$user->isCollegemate($user_id))
						continue;
				}

				if($row['user_id'] != $user_id) {
					$mutual_friends = $user->getMutualFriends($user_id) . " Mutual Friends";
				}
				else {
					$mutual_friends = "";
				}

				$college = $user->getCollegeName();
				$dept = $user->getDept();

				$displayed++;

				$return_string .= "
				<div class='searchresultDisplay'>
					<a href='".$row['user_id']."' style='color:#000;'>
						<div class='liveSearchProfilePic'>
							<img style='height:40px; margin:2px;' src='". $row['profile_pic'] . "'>
						</div>
						<div class='liveSearchText'>
							<span class='search_profile_name'>".$row['first_name'] . " " . $row['last_name']. "</span>
							<div class='time_msg'>$mutual_friends<br>$dept<br>$college</div>
						</div>
					</a>
				</div>";

			}

			while($row = mysqli_fetch_array($extUsersReturned)) {

				$user = new User($conn, $row['user_id']);

				if($row['user_id'] == $user_id)
					continue;

				if($user->isFriend($user_id)) {
					//nothing just checking.. hahaha.
				}
				else if($row['visibility'] == '1') {
					if(!$user->isStrictDeptmate($user_id))
						continue;
				} else if($row['visibility'] == '2') {
					if(!$user->isCollegemate($user_id))
						continue;
				}

				if($row['user_id'] != $user_id) {
					$mutual_friends = $user->getMutualFriends($user_id) . " Mutual Friends";
				}
				else {
					$mutual_friends = "";
				}

				$college = $user->getCollegeName();
				$dept = $user->getDept();

				$displayed++;

				$return_string .= "
				<div class='searchresultDisplay'>
					<a href='".$row['user_id']."' style='color:#000;'>
						<div class='liveSearchProfilePic'>
							<img style='height:40px; margin:2px;' src='". $row['profile_pic'] . "'>
						</div>
						<div class='liveSearchText'>
							<span class='search_profile_name'>".$row['first_name'] . " " . $row['last_name']. "</span>
							<div class='time_msg'>$mutual_friends<br>$dept<br>$college</div>
						</div>
					</a>
				</div>";

			}

			if($displayed == 0 ) {
				$return_string = "
				<div class='searchresultDisplay' style='border:none;'> 
					<p style='text-align:center; border:none; margin:10px; color:#888;'>No result found</p>
				</div>";
			} else {
				$return_string .= "
				<div class='searchresultDisplay' style='border:none;'> 
					<p style='text-align:center; border:none; margin:00px; color:#888;'>No more result found</p>
				</div>
				"; 
			}
	} else {
		header("Location: home.php");
		exit();
	}
}
else {
	header("Location: home.php");
	exit();
}

?>
<!DOCTYPE html>
<html>
<head>	
	<?php require_once('assets.php'); ?>
	<title>Search Results <?= $query ?></title>
	<style type="text/css">
		.back_to_home_button{
			margin-left: 5px;
 			background-color: #fff;
			font-size: 16px;
			padding:6px;
			text-align: center;
			display: block;
			margin-right:auto; margin-left: auto;
			text-decoration: none;
		}
		.back_to_home_button:hover{
			text-decoration: none;
		}
	</style>
</head>
<body>

<div class='fluid-container top_bar'>
	<a class='back_to_home_button' href="home.php">&nbsp;<i class="fa fa-home fa-2x" aria-hidden="true"></i>&nbsp;</a>
</div>

<div class='middle_bar'>
	<div class='search_results column'>
		<?php if(isset($_SESSION['error'])) {
			echo $_SESSION['error']; unset($_SESSION['error']);
		} ?>
		<fieldset>
			<legend class="ann_legend">Search Results</legend>
			<?php echo $return_string; ?>
		</fieldset>
	</div>
</div>

</body>
</html>