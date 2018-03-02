<?php  
require('../../../config.php');
require('../../classes/User.php');

if($_POST['fac_class_submit']) {

	$user = new User($conn,$_SESSION['user_id']);
	$department = $_POST['dept'];
	$batch = $_POST['batch'];
	$college_id = $user->getCollegeCode();
	$batch_1 = $batch+1;

	$department = htmlentities($department);
	$department = mysqli_real_escape_string($conn,$department);
	$batch = htmlentities($batch);
	$batch = mysqli_real_escape_string($conn,$batch);

	$data_query = mysqli_query($conn,"SELECT * FROM users WHERE branch_id='$department' AND ( (year_of_adm='$batch' AND course_id='01') OR (year_of_adm='$batch_1' AND course_id='31')) AND college_id='$college_id' ORDER BY enroll ");


	if($batch != '2014' && $batch != '2015' && $batch != '2016' && $batch != '2017') {
		echo "<p style='padding:10px; color:#d32f2f; border:1px solid #eee;'>Invalid Batch</p>";
		exit();
	} else if (!($_POST['dept'] == '07' || $_POST['dept'] == '16' || $_POST['dept'] == '11' || $_POST['dept'] == '03' || $_POST['dept'] == '17' || $_POST['dept'] == '21' ) ) {
		echo "<p style='padding:10px; color:#d32f2f; border:1px solid #eee;'>Invalid Department</p>";
		exit();
	}

	if(mysqli_num_rows($data_query) == 0) {
		echo "<p style='padding:10px; color:#555; border:1px solid #eee;'>Nothing to show</p>";
		exit();
	}
	echo "<div id='classmates'><table style='text-align:center; width:100%; border:1px solid #ddd;'>";

	while($row = mysqli_fetch_assoc($data_query)) {

		$user_found_obj = new User($conn,$row['user_id']);
		$user_found_user_id = $user_found_obj->getUserid();
		$user_enroll = $user_found_obj->getEnrollmentNo();
		$user_name = $user_found_obj->getFirstAndLastName();
		echo "<tr class='classmate_row'>
				<td class='classmate_roll'>$user_enroll</td>
				<td class='classmate_name'><a href='$user_found_user_id'>$user_name</a></td>
		</tr>";
	}

	echo "</table></div>";

}

?>