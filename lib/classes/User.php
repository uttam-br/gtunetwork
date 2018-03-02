<?php

class User {
	private $user;
	private $conn;
	public function __construct($conn,$user_id){
		$this->conn = $conn;
		$query = mysqli_query($this->conn, "SELECT * FROM users WHERE user_id='$user_id'");
		$this->user= mysqli_fetch_assoc($query);
	}

	public function getUserid(){
		return $this->user['user_id'];
	}

	public function getFirstName(){
		return $this->user['first_name'];
	}

	public function getLastName(){
		return $this->user['last_name'];
	}

	public function getFirstAndLastName(){
		return $this->user['first_name'].' '.$this->user['last_name'];
	}

	public function getMob(){
		return $this->user['mobile_no'];
	}

	public function getDOB(){
		$dob = $this->user['dob'];
		return $dob;
	}

	public function getType(){
		return $this->user['type'];
	}
	public function sem(){
		if($this->user['sem'] == '')
			return false;
		else
			return true;
	}

	public function getSem(){
		return $this->user['sem'];
	}
	public function getNumberOfLikes(){
		$user_id = $this->user['user_id'];
		$query = mysqli_query($this->conn,"SELECT num_likes FROM users WHERE user_id='$user_id'");
		$row = mysqli_fetch_array($query);
		return $row['num_likes'];
	}

	public function getNumberOfPosts(){
		$user_id = $this->user['user_id'];
		$query = mysqli_query($this->conn,"SELECT num_posts FROM users WHERE user_id='$user_id'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}

	public function getEnrollmentNo(){
		return $this->user['enroll'];
	}

	public function getCollegeName(){
		$id = $this->user['college_id'];
		$select_query = mysqli_query($this->conn,"SELECT * FROM institutes WHERE code='$id'");
		$rows = mysqli_fetch_assoc($select_query);
		return $rows['name'];
	}

	public function getCollegeCode(){
		return $this->user['college_id'];
	}

	public function getProfilePic(){
		return $this->user['profile_pic'];
	}

	public function getCourseId(){
		return $this->user['course_id'];
	}

	public function getDept(){
		$id = $this->user['branch_id'];
		$query = mysqli_query($this->conn, "SELECT * FROM department WHERE code='$id'");
		$row = mysqli_fetch_assoc($query);
		return $row['name'];
	}

	public function getDeptCode(){
		return $this->user['branch_id'];
	}

	public function getYearOfAdm(){
		return $this->user['year_of_adm'];
	}

	public function getEmail(){
		return $this->user['email'];
	}

	public function getJoiningDate(){
		$dt = new DateTime($this->user['signup_date']);
		$date = $dt->format('d-m-Y');
		return $date;
	}

	public function getPendingRequests() {
		$return_string = "";
		$user_id = $this->user['user_id'];
		$select_query = mysqli_query($this->conn,"SELECT * FROM friend_requests WHERE user_from='$user_id'");
		while($row=mysqli_fetch_assoc($select_query)) {

			$user_to = $row['user_to'];
			$user_to_object = new User($this->conn,$user_to);
			$user_to_name = $user_to_object->getFirstAndLastName();
			$return_string .= "<a href='$user_to'>" . $user_to_name . "</a>&nbsp;&nbsp;&nbsp;";
		
		}


		return $return_string ;
	}

	public function getLastActive() {

		$username = $this->user['user_id'];

		$online_status_query = mysqli_query($this->conn,"SELECT last_active FROM users WHERE user_id='$username' ");

		$row = mysqli_fetch_assoc($online_status_query);

		$from = $row['last_active'];
		$datetimenow = date("Y-m-d H:i:s");

		$from_time = new DateTime($from);
		$timenow = new DateTime($datetimenow);

		$interval = $timenow->diff($from_time);
		
		if($interval->i < 2 && $interval->h <= 0 && $interval->d <= 0 && $interval->m <=0 && $interval->y <= 0) {
			return 'active';
		} else {
			$datetimenow = date("Y-m-d H:i:s");
			$from_time = new DateTime($from);
			$timenow = new DateTime($datetimenow);
			$interval = $timenow->diff($from_time);
			if($interval->y >= 1) {
				if($interval == 1)
					$time_message = $interval->y . " yr"; //1 year ago
				else
					$time_message = $interval->y . " yrs"; //1+ year ago
			}
			else if ($interval-> m >= 1) {
				if($interval->m == 1) {
					$time_message = $interval->m . " month". $days;
				}
				else {
					$time_message = $interval->m . " months". $days;
				}

			}
			else if($interval->d >= 1) {
				if($interval->d == 1) {
					$time_message = " 1 day ago";
				}
				else {
					$time_message = $interval->d . " days ago";
				}
			}
			else if($interval->h >= 1) {
				if($interval->h == 1) {
					$time_message = $interval->h . " hr";
				}
				else {
					$time_message = $interval->h . " hrs";
				}
			}
			else if($interval->i >= 1) {
				$time_message = $interval->i . " min";
			}
			
			return 'Last Active ' . $time_message;
		}
	}

	public function getChatters() { // this returns all the friends to show

		$user_object = new User($this->conn,$this->user['user_id'] );

		$user_array = $this->user['friends'];
		$user_array_explode = explode(",",$user_array);
		$friend_array = array();
		$friends = "";

		foreach($user_array_explode as $i){
			array_push($friend_array,$i);
		}
		$i = 0;
		foreach( $friend_array as $friend){
			if($i++==0) continue;
			if($i == sizeof($friend_array)) continue;

			$friend_object = new User($this->conn,$friend);
			$online_status = $friend_object->getLastActive();
			if($online_status == 'active') {
				$friend_profilepic = $friend_object->getProfilePic();
				$friend_name = $friend_object->getFirstAndLastName();
				$friend_dept = $friend_object->getDept();
				$friend_num_of_friends = $friend_object->numOfFriends();

				$friends .= "
				<a href='$friend'>  
					<div class='online_profile'> 
						<img src=".$friend_profilepic.">$friend_name
					</div> 
				</a>";
			}
		}
		if($friends == "")
			$friends = "<p style='color:#555; text-align:center; margin:20px auto;'>Your friends are offline.</p>";
		return $friends;
	}

	public function getVisibility() {
		return $this->user['visibility'];
	}

	public function getVerificationCode() {
		return $this->user['email_code'];
	}

	public function getNumOfChatters() {

		$user_object = new User($this->conn,$this->user['user_id'] );
		$actives = 0;
		$user_array = $this->user['friends'];
		$user_array_explode = explode(",",$user_array);
		$friend_array = array();
		$friends = "";

		foreach($user_array_explode as $i){
			array_push($friend_array,$i);
		}
		$i = 0;
		foreach( $friend_array as $friend){
			if($i++==0) continue;
			if($i == sizeof($friend_array)) continue;

			$friend_object = new User($this->conn,$friend);
			$online_status = $friend_object->getLastActive();
			if($online_status == 'active') {
				$actives++;
			}
		}
		return $actives;
	}

	public function isCollegemate($user_id_to_check){ // 150 130 107 120
		$user_to_check_object = new User($this->conn,$user_id_to_check);
		$user_to_check_college = $user_to_check_object->getCollegeCode();
		$user_college = $this->user['college_id'];
		if($user_to_check_college == $user_college)
			return true;
		else
			return false;
	}

	public function isClassmate($user_id_to_check){
		$user_enroll = $this->user['enroll'];
		$user_college_code = $this->user['college_id'];
 		$user_branch_code = $this->user['branch_id'];
		$user_course_id = $this->user['course_id'];
		$user_year_of_adm = $this->user['year_of_adm'];

		$user_to_check_object = new User($this->conn,$user_id_to_check);
		$user_to_enroll = $user_to_check_object->getEnrollmentNo();
		$user_to_college_code = $user_to_check_object->getCollegeCode();
		$user_to_branch_code = $user_to_check_object->getDeptCode();
		$user_to_course_id = $user_to_check_object->getCourseId();
		$user_to_year_of_adm = $user_to_check_object->getYearOfAdm();

		if($user_college_code != $user_to_college_code){
			return false;
		}
		else if($user_branch_code != $user_to_branch_code){
			return false;
		}
		else if($user_course_id == '01'){

			if($user_to_course_id == '01'){
				if($user_year_of_adm == $user_to_year_of_adm)
					return true;
				else
					return false;
			}
			else if($user_to_course_id == '31'){
				if($user_year_of_adm == $user_to_year_of_adm + 1)
					return true;
				else
					return false;
			}
			else
				return false;
		}
		else if($user_course_id == '31'){

			if($user_to_course_id == '31'){
				if($user_year_of_adm == $user_to_year_of_adm)
					return true;
				else
					return false;
			}
			else if ($user_to_course_id == '01'){
				if($user_year_of_adm == $user_to_year_of_adm-1)
					return true;
				else
					return false;
			}
		}
	}

	public function isFriend($user_id_to_check){
		$usernameComma = "," . $user_id_to_check . ",";

		if((strstr($this->user['friends'], $usernameComma) || $user_id_to_check == $this->user['user_id'])) {
			return true;
		}
		else {
			return false;
		}
	}

	public function isStrictDeptmate($user_to_check){
		$user_to_check_object = new User($this->conn,$user_to_check);
		$user_to_check_dept = $user_to_check_object->getDeptCode();
		$user_dept = $this->user['branch_id'];
		$user_to_check_college = $user_to_check_object->getCollegeCode();
		$user_college = $this->user['college_id'];

		if($user_to_check_college == $user_college && $user_to_check_dept == $user_dept ){
			return true;
		}
		return false;
	}


	public function getNumOfRequests() {
		$user_id = $this->user['user_id'];
		$select_query = mysqli_query($this->conn,"SELECT * FROM friend_requests WHERE user_to='$user_id'");
		$number = mysqli_num_rows($select_query);
		return $number;
	}

	public function numOfFriends(){
		$user_id = $this->user['user_id'];
		$user_detail_query= mysqli_query($this->conn,"SELECT * FROM users WHERE user_id='$user_id' ");
		$user_array = mysqli_fetch_array($user_detail_query);
		$num_friends = ( substr_count($user_array['friends'],',') ) - 1;
		return $num_friends;
	}

	public function numOfFriendsFromProfile($user_to_check){
		$user_to_check_object = new User($this->conn,$user_to_check);
		return $user_to_check_object->numOfFriends();
	}

	public function didReceiveRequest($user_from){
		$user_to = $this->user['user_id'];
		$check_request_query = mysqli_query($this->conn,"SELECT * FROM friend_requests WHERE user_to='$user_to' and user_from='$user_from' ");
		$num_rows = mysqli_num_rows($check_request_query);
		if( $num_rows > 0){
			return true;
		}
		else
			return false;
	}

	public function didSendRequest($user_to){
		$user_from = $this->user['user_id'];
		$check_request_query = mysqli_query($this->conn,"SELECT * FROM friend_requests WHERE user_to='$user_to' and user_from='$user_from'");
		$num_rows = mysqli_num_rows($check_request_query);
		if( $num_rows > 0){
			return true;
		}
		else
			return false;
	}

	public function removeFriend($user_to_remove){
		$logged_in_user = $this->user['user_id'];

		$query = mysqli_query($this->conn,"SELECT friends FROM users WHERE user_id='$user_to_remove' ");
		$row = mysqli_fetch_array($query);
		$friends_user_to_remove = $row['friends'];

		$new_friend_array = str_replace($user_to_remove.",","",$this->user['friends']  );
		$remove_friend = mysqli_query($this->conn,"UPDATE users SET friends='$new_friend_array' WHERE user_id='$logged_in_user' ");

		$new_friend_array = str_replace( $this->user['user_id']."," ,"", $friends_user_to_remove );
		$remove_friend = mysqli_query($this->conn,"UPDATE users SET friends='$new_friend_array' WHERE user_id='$user_to_remove' ");
		header("Location: ".$user_to_remove);
		exit();
	}

	public function cancelRequest($user_to){
		$user_from = $this->user['user_id'];
		$check_request_query = mysqli_query($this->conn,"DELETE FROM friend_requests WHERE user_to='$user_to' and user_from='$user_from'");
		header("Location: ".$user_to);
	}

	public function sendRequest($user_to){
		$user_from = $this->user['user_id'];
		$query = mysqli_query($this->conn,"INSERT INTO friend_requests VALUES ('','$user_to','$user_from') ");
		header("Location: ".$user_to);
		exit();
	}

	public function getFriendArray(){
		$user_id = $this->user['user_id'];
		$query = mysqli_query($this->conn,"SELECT friends FROM users WHERE user_id='$user_id' ");
		$row = mysqli_fetch_array($query);
		return $row['friends'];
	}


	public function getFriendsArray($user_to_check) { // this returns all the friends to show

		$user_object = new User($this->conn,$this->user['user_id'] );

		if( $user_object->numOfFriendsFromProfile($user_to_check) == 0)
		return "<p style='border:1px solid #eee; padding:2px 10px; text-align:center;'>No Friends</p>";

		$user_to_check_object = new User($this->conn,$user_to_check);
		$user_array = $user_to_check_object->getFriendArray();
		$user_array_explode = explode(",",$user_array);
		$friend_array = array();
		$friends = "";

		foreach($user_array_explode as $i){
			array_push($friend_array,$i);
		}
		$i = 0;
		foreach( $friend_array as $friend){
			if($i++==0) continue;
			if($i == sizeof($friend_array)) continue;
			$friend_object = new User($this->conn,$friend);

			$friend_profilepic = $friend_object->getProfilePic();
			$friend_name = $friend_object->getFirstAndLastName();
			$friend_dept = $friend_object->getDept();
			$friend_num_of_friends = $friend_object->numOfFriends();

			$friends .= "
			<a href='$friend'>  
				<div class='friend_profile'> 
					<img src='".$friend_profilepic."'>"
					.$friend_name.
					"<div class='friend_profile_details'>
						Friends : ".$friend_num_of_friends. "<br>".$friend_dept."</div></div> 
			</a>";
		}
		return $friends;
	}

	public function getMutualFriends($user_to_check) {
		$mutual_friends = 0;
		$user_array = $this->user['friends'];
		$user_array_explode = explode(",",$user_array);

		$query = mysqli_query($this->conn,"SELECT friends FROM users WHERE user_id='$user_to_check' ");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friends'];
		$user_to_check_array_explode = explode(",",$user_to_check_array);

		foreach($user_array_explode as $i){
			foreach($user_to_check_array_explode as $j){
				if($i == $j && $i != "")
					$mutual_friends++;
			}
		}

		return $mutual_friends;
	}

	public function getMutualFriendsArray($user_to_check) {
		$user_object = new User( $this->conn,$this->user['user_id'] );
		if($user_object->getMutualFriends($user_to_check) == 0)
			return "<p style='border:1px solid #eee; padding:2px 10px; text-align:center'>No Mutual Friends</p>";
		$user_array = $this->user['friends'];
		$user_array_explode = explode(",",$user_array);
		$friend_array = array();
		$mutual_friends = "";
		$query = mysqli_query($this->conn,"SELECT friends FROM users WHERE user_id='$user_to_check' ");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friends'];
		$user_to_check_array_explode = explode(",",$user_to_check_array);

		foreach($user_array_explode as $i){
			foreach($user_to_check_array_explode as $j){
				if($i == $j && $i != "")
					array_push($friend_array,$i);
			}
		}
		foreach( $friend_array as $friend){
			$friend_object = new User($this->conn,$friend);
			$friend_name = $friend_object->getFirstAndLastName();
			$friend_profilepic = $friend_object->getProfilePic();
			$friend_dept = $friend_object->getDept();
			$friend_num_of_friends = $friend_object->numOfFriends();

			$mutual_friends .= "
			<a href='$friend'>  
				<div class='friend_profile'> 
					<img src='".$friend_profilepic."'>"
					.$friend_name.
					"<div class='friend_profile_details'>
						Friends : ".$friend_num_of_friends. "<br>".$friend_dept."</div></div> 
			</a>";
		}
		return $mutual_friends;
	}

	public function getRankClass(){
		$rank = 1;
		$enroll = $this->user['enroll'];
		$year_of_adm = $this->user['year_of_adm'];
		$college_id = $this->user['college_id'];
		$course_id = $this->user['course_id'];
		$branch_id = $this->user['branch_id'];

		$check_query = mysqli_query($this->conn,"SELECT * FROM results WHERE enroll='$enroll' ");
		if(mysqli_num_rows($check_query) == 0 ) {
			return 'NA';
		}

		if($course_id == '01'){
			$year_of_adm_diploma = $year_of_adm + 1 ;
			$query = mysqli_query($this->conn,"SELECT * FROM results WHERE (course_id='01' AND college_id='$college_id' AND year_of_adm='$year_of_adm' AND dept_id='$branch_id') OR ( course_id='31' AND college_id='$college_id' AND year_of_adm='$year_of_adm_diploma' AND dept_id='$branch_id' ) ORDER BY total_cpi DESC");

			while($row=mysqli_fetch_assoc($query)){
				if($enroll == $row['enroll']){
					break;
				}
				$rank++;
			}

		}
		else if($course_id == '31'){
			$year_of_adm_regular = $year_of_adm - 1;

			$query = mysqli_query($this->conn,"SELECT * FROM results WHERE (course_id='31' AND college_id='$college_id' AND year_of_adm='$year_of_adm' AND dept_id='$branch_id') OR (course_id='01' AND college_id='$college_id' AND year_of_adm='$year_of_adm_regular' AND dept_id='$branch_id' ) ORDER BY total_cpi DESC");

			while($row=mysqli_fetch_assoc($query)){
				if($enroll == $row['enroll']){
					break;
				}
				$rank++;
			}
		}

		return $rank;
	}

	public function getRankCollege(){
		$rank = 1;
		$enroll = $this->user['enroll'];
		$college_id = $this->user['college_id'];

		$check_query = mysqli_query($this->conn,"SELECT * FROM results WHERE enroll='$enroll' ");
		if(mysqli_num_rows($check_query) == 0 ) {
			return 'NA';
		}

		$query = mysqli_query($this->conn, "SELECT * FROM results WHERE college_id='$college_id' ORDER BY total_cpi DESC" );

		while($row=mysqli_fetch_assoc($query)){
				if($enroll == $row['enroll']){
					break;
				}
				$rank++;
		}

		return $rank;
	}

	public function getRankDept(){
		$rank = 1;
		$enroll = $this->user['enroll'];
		$branch_id = $this->user['branch_id'];
		$college_id = $this->user['college_id'];

		$check_query = mysqli_query($this->conn,"SELECT * FROM results WHERE enroll='$enroll' ");
		if(mysqli_num_rows($check_query) == 0 ) {
			return 'NA';
		}

		$query = mysqli_query($this->conn, "SELECT * FROM results WHERE dept_id='$branch_id' AND college_id='$college_id' ORDER BY total_cpi DESC" );

		while($row=mysqli_fetch_assoc($query)){
				if($enroll == $row['enroll']){
					break;
				}
				$rank++;
		}

		return $rank;
	}

	public function getClassmatesArray(){

		$user_id = $this->user['user_id'];
		$user = new User($this->conn,$user_id);
		$classmates_array =  array();
		$enroll = $this->user['enroll'];
		$year_of_adm = $this->user['year_of_adm'];
		$college_id = $this->user['college_id'];
		$course_id = $this->user['course_id'];
		$branch_id = $this->user['branch_id'];

		if($course_id == '01'){
			$year_of_adm_diploma = $year_of_adm + 1 ;
			$query = mysqli_query($this->conn,"SELECT * FROM users WHERE (course_id='01' AND college_id='$college_id' AND year_of_adm='$year_of_adm' AND branch_id='$branch_id') OR ( course_id='31' AND college_id='$college_id' AND year_of_adm='$year_of_adm_diploma' AND branch_id='$branch_id' ) ORDER BY year_of_adm,roll_no ");

			if(mysqli_num_rows($query) > 1) {
				$return_string = "<table style='width: 98%; margin: 2px 1%;'><tr><td class='classmate_roll'>No.</td><td class='classmate_name'>Name</td><td></td><td></td></tr>";
				while($row=mysqli_fetch_assoc($query)){
					$classmate_user_id = $row['user_id'];
					if($classmate_user_id == $user_id) continue;
					$classmate_roll = $row['roll_no'];
					$classmate_first_name = $row['first_name'];
					$classmate_last_name = $row['last_name'];
					$return_string .= "
						<tr class='classmate_row'>
								<td class='classmate_roll'>$classmate_roll</td>
								<td class='classmate_name'><a href='$classmate_user_id'>$classmate_first_name $classmate_last_name</a></td>
								<td class='classmate_message'>
									<a onclick='openMessages($classmate_user_id)'><i title='Message' class='fa fa-envelope-o' aria-hidden='true'></i></a>
								</td>
						</tr>";
				}
			}
			else{
				$return_string = '<p style="text-align:center; margin:10px;">No classmate registered.</p>';
				return $return_string;
			}
			$return_string .= '</table>';
			return $return_string;
		}
		else if($course_id == '31'){
			$year_of_adm_regular = $year_of_adm - 1 ;
			$query = mysqli_query($this->conn,"SELECT * FROM users WHERE (course_id='31' AND college_id='$college_id' AND year_of_adm='$year_of_adm' AND branch_id='$branch_id') OR ( course_id='01' AND college_id='$college_id' AND year_of_adm='$year_of_adm_regular' AND branch_id='$branch_id' ) ORDER BY year_of_adm,roll_no ");

			if(mysqli_num_rows($query) > 0) {
				$return_string = "<table><tr><td class='classmate_roll'>No.</td><td class='classmate_name'>Name</td><td></td><td></td></tr>";
				while($row=mysqli_fetch_assoc($query)){
					$return_string .= "";
					$classmate_user_id = $row['user_id'];
					if($classmate_user_id == $user_id) continue;
					$classmate_roll = $row['roll_no'];
					$classmate_first_name = $row['first_name'];
					$classmate_last_name = $row['last_name'];
					$return_string .= "
						<div class='classmate_profile'>
							<tr>
								<td class='classmate_roll'>$classmate_roll</td>
								<td class='classmate_name'><a href='$classmate_user_id'>$classmate_first_name $classmate_last_name</a></td>
								<td class='classmate_message'>
									<a onclick='openMessages($classmate_user_id)'><i title='Message' class='fa fa-envelope-o' aria-hidden='true'></i></a>
								</td>
							</tr>
						</div>";
				}
			}
			else{
				$return_string = '<p>No classmate registered. Invite them to register.</p>';
				return $return_string;
			}
			$return_string .= '</table>';
			return $return_string;
		}

	}

	public function loadMyDocs($type){
		$user_id = $this->user['user_id'];
		$day = "";
		$return_string = '';
		$user_object = new User($this->conn, $user_id);
		$dept = $user_object->getDeptCode();
		$course_id = $user_object->getCourseId();
		$college_id = $user_object->getCollegeCode();

		if($course_id == '01')
			$batch = $user_object->getYearOfAdm();
		else if($course_id == '31')
			$batch = ( $user_object->getYearOfAdm() - 1 );

		$data_query = mysqli_query($this->conn,"SELECT * FROM myclass WHERE type='$type' AND dept='$dept' AND batch='$batch' AND college_id='$college_id' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0){

			while($row = mysqli_fetch_assoc($data_query)){
				$id = $row['id'];
				$added_by = $row['added_by'];

				if($added_by == $user_id) {
					$delete_btn = "<button class='delete_classfile_button' onclick='deleteClassFile(".$id.")'>Delete</button>";
					$delete_warning_div = "<div class='hidden alert alert-danger delete_warning' id='delete_warning_".$id."'>Are you sure ? 
					<button class='confirm_delete_btn' onclick='confirmDeleteClassFile(".$id.")'>Yes</button>
					<button class='confirm_delete_btn' onclick='dontDeleteClassFile(".$id.")'>No</button>
					</div>";
				}
				else {
					$delete_warning_div = "";
					$delete_btn = "";
				}
				
				$subject = $row['subject'];
				$add_info = $row['add_info'];
				$filepath = $row['filepath'];
				$datetime = $row['datetime'];
				$dt = new DateTime($datetime);
				$datetime = $dt->format("d-m-Y");

				$added_by_object = new User($this->conn,$added_by);
				$name = $added_by_object->getFirstAndLastName();

 				$date_time_now = date("Y-m-d H:i:s");
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $dt->diff($end_date); //Difference between dates 

				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}

				if($day_msg != "")
						$return_string .= "<p style='text-align:center; margin:5px;' class='notification_day_msg'>$day_msg</p>";


				$return_string .= "
				$delete_warning_div
				<div id='classfile_$id' class='item'>
					<div class='head'><span style='font-size:9px;'>Subject : </span> $subject<span style='float:right;' class='file_link'><a href='$filepath' target='_blank'><i class='fa fa-download'></i> Download</a></span></div>";

				if($add_info != ''){
					$return_string .= "<div class='add_info'><span style='font-size:9px;'>Add.Info.</span> $add_info </div>";
				}

				$return_string .= "<div class='time_msg fac_file_time_msg'>Added by : <a href='$added_by'>$name</a> $delete_btn</div>
					</div>";
			}// while loop

			echo $return_string;

		} // number of rows greater than 0 if.
		else
			echo "<p style='text-align:center; border:1px solid #eee; padding:2px;'>Nothing to Display</p>";

	}

	public function getNoOfDocs($type){
		$no_of_docs = 0;
		$user_object = new User($this->conn, $this->user['user_id']);

		$dept = $user_object->getDeptCode();
		$course_id = $user_object->getCourseId();
		$college_id = $user_object->getCollegeCode();

		if($course_id == '01')
			$batch = $user_object->getYearOfAdm();
		else if($course_id == '31')
			$batch = ( $user_object->getYearOfAdm() - 1 );

		$data_query = mysqli_query($this->conn,"SELECT * FROM myclass WHERE type='$type' AND dept='$dept' AND batch='$batch' AND college_id='$college_id' ORDER BY id DESC");

		$no_of_docs = mysqli_num_rows($data_query);

		return $no_of_docs;
	}


	public function getCollegeDetails(){
		$college_code = $this->user['college_id'];

		$select_query = mysqli_query($this->conn,"SELECT * FROM institutes WHERE code='$college_code' ");

		$row = mysqli_fetch_assoc($select_query);

		$name = $row['name'];
		$logo = $row['logo_image'];
		$principal = $row['principal'];
		$contact = $row['contact'];
		$website = $row['website'];
		$branches = $row['branches'];
		$branches_array = explode(',', $branches);
		$city = $row['city'];
		$return_string = "
		<div class='college_info_div'>
			<span class='college_name'>$name</span>
			<hr>
			<img class='mycollege_logo' src='$logo'>
			<span><span class='mycollege_label'>Code</span> <span class='mycollege_value'>$college_code</span></span><br>
			<span><span class='mycollege_label'>Principal</span> <span class='mycollege_value'>$principal</span></span><br>
			<span><span class='mycollege_label'>Contact No.</span><span class='mycollege_value'>$contact</span></span><br>
			<span><span class='mycollege_label'>Website</span> <a target='_blank' href='http://$website'><span class='mycollege_value'>$website</span></a></span><br>
			<span><span class='mycollege_label'>City</span><span class='mycollege_value'>$city</span> </span><br>
			<span class='mycollege_label'>Branches</span>
			<div class='branches'>";
		foreach ($branches_array as $branch){
			$return_string .= "<span class='mycollege_branch_name'>". $branch . "</span><br>";
		}
		$return_string .= '</div></div>';
		return $return_string;
	}


	public function getStudyMaterial($sem) {
		$user_id = $this->user['user_id'];

		$sem = htmlentities($sem);
		$return_string = "";

		$update_sem_query = mysqli_query($this->conn,"UPDATE users SET sem='$sem' WHERE user_id='$user_id'");
		$dept = $this->user['branch_id'];

		if($sem == '1')
			$select_query = mysqli_query($this->conn,"SELECT * FROM subjects WHERE sem='1' AND dept='00'");
		else if($sem == '3' || $sem == '4' || $sem == '5' || $sem == '6'|| $sem == '7' || $sem == '8')
			$select_query = mysqli_query($this->conn, "SELECT * FROM subjects WHERE dept LIKE '%,$dept,%' AND sem='$sem' ");
		if(mysqli_num_rows ($select_query) > 0) {
			while($row = mysqli_fetch_assoc($select_query)) {
				$sub_code = $row['code'];
				$sub_name = $row['name'];
				$papers = $row['papers'];
				$papers_array = explode(',', $papers);
				$ebooks = $row['ebooks'];
				$ebooks_array = explode(',',$ebooks);

				$return_string .= "
				<div class='subject'>
					<div class='sub_name'>$sub_name <span class='sub_code'> [$sub_code]</span><span class='sub_syllabus'><i class='fa fa-file-pdf-o' aria-hidden='true'></i>&nbsp;<a target='_blank' href='files/$sem/$sub_code/$sub_code.pdf'>syllabus</a></span>
					</div>
					<div class='subject_details'>";
				if($papers != ''){
					$return_string .= "<div class='papers'>Question Papers<br>";
					foreach($papers_array as $paper){
						$season = substr($paper,0,1);
						if($season == 1)
							$season = 'Summer';
						else if($season == 2)
							$season = 'Winter';
						$season .= ' 20'.substr($paper,1,2);
						$return_string .= "<span class='papers_link'><a target='_blank' href='files/$sem/$sub_code/$paper.pdf'><i class='fa fa-file-pdf-o' aria-hidden='true'></i>&nbsp;$season</a> </span>";
					}
					$return_string .='</div>';
				}

				if($ebooks != ''){
					$return_string .= '<div class="ebooks">Material<br>';
					foreach($ebooks_array as $ebook){
						$return_string .= "<span class='ebooks_link'><a target='_blank' href='files/$sem/$sub_code/$ebook.pdf'><i class='fa fa-file-pdf-o' aria-hidden='false'></i>&nbsp;$ebook</a><br></span>";
					}
					$return_string .= '</div>';
				}

				$return_string .="</div></div>";

			} 

			echo $return_string;
		}else {
			echo  '<p style="text-align:center; border:1px solid #eee; padding:10px;">Nothing to show</p>';
		}

	}


	function getAnnouncements() {
		$college_id = $this->user['college_id'];
		$day = "";
		$dept = $this->user['branch_id'];
		$return_string = "";
		$select_query = mysqli_query($this->conn,"SELECT * FROM anns WHERE dept LIKE '%,$dept,%' AND college_id='$college_id' ORDER BY id DESC");
		if( mysqli_num_rows($select_query) > 0 ) {
			
			while($row = mysqli_fetch_assoc($select_query)){

				$added_by = $row['added_by'];
				$added_by_object = new User($this->conn,$added_by);
				$added_by_name = $added_by_object->getFirstAndLastName();
				$heading = $row['heading'];
				$ann = $row['ann'];
				$datetime = $row['date'];

				$date_time_now = date("Y-m-d H:i:s");
				$start_time = new DateTime($datetime);
				$end_time = new DateTime($date_time_now);
				$interval = $start_time->diff($end_time);

				$dt = new DateTime($datetime);
				$datetime = $dt->format("d-m-Y");


				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}


				if($row['add_info'] == '')
					$add_info = "";
				else
					$add_info = "<div class='add_info'>".$row['add_info']."</div>";

				if($row['file'] == '') {
					$file_path = "";
				}
				else
					$file_path = "<div class='file_link'><a target='_blank' href='".$row['file']."'>Uploaded file</a></div>";

				if($day_msg != "")
					$return_string .= "<p class='day_msg_p'>$day_msg</p>";

				$return_string .= "
				<div class='announcement'>
					<div class='heading'>$heading</div>
					<div class='ann_detail'>$ann</div>
					$add_info
					<div class='time_msg fac_file_time_msg'>Announced on $datetime by <a href='$added_by'>$added_by_name</a></div>
					$file_path
				</div>";

			} // while loop
		} // check if more than 0 rows returned
		else
			$return_string .= "<p style='text-align:center; margin:10px; border:1px solid #eee; padding:10px;'>No Announcement</p>";
		echo $return_string;
	} // function ends here

	public function getNoOfDocsFromFac($type) {
		$number = 0;
		$dept = $this->user['branch_id'];

		if($this->user['course_id'] == '01')
			$batch = $this->user['year_of_adm'];
		else if($this->user['course_id'] == '31')
			$batch = ( $this->user['year_of_adm'] - 1 );

		$college_id = $this->user['college_id'];

		$query = mysqli_query($this->conn,"SELECT * FROM fac_assigns WHERE dept='$dept' AND batch='$batch' AND type='$type' AND college_id='$college_id' ");

		$number = mysqli_num_rows($query);
		return $number;
	}

	public function getFilesFromFac($type){
		$day = "";
		$return_string = "";
		$college_id = $this->user['college_id'];
		$dept = $this->user['branch_id'];
		if($this->user['course_id'] == '01')
			$batch = $this->user['year_of_adm'];
		else if($this->user['course_id'] == '31')
			$batch = ( $this->user['year_of_adm'] - 1 );

		$select_query = mysqli_query($this->conn,"SELECT * FROM fac_assigns WHERE dept = '$dept' AND batch = '$batch' AND type='$type' AND college_id='$college_id' ORDER BY id DESC");

		if(mysqli_num_rows($select_query) > 0) {

			while($row=mysqli_fetch_assoc($select_query)) {

				$type_of_file = $row['type'];

				$added_by = $row['added_by'];
				$added_by_object = new User($this->conn,$added_by);
				$added_by_name = $added_by_object->getFirstAndLastName();
				$subject = $row['subject'];
				$add_info = $row['add_info'];
				$file_path = $row['file'];
				$datetime = $row['date'];
				$dt = new DateTime($datetime);
				$datetime = $dt->format("d-m-Y h:i A");

				$date_time_now = date("Y-m-d H:i:s");
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $dt->diff($end_date); //Difference between dates 

				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}

				if($day_msg != "")
						$return_string .= "<p style='text-align:center; margin:5px;' class='notification_day_msg'>$day_msg</p>";

				$return_string .= "
				<div class='fac_file_item'>
					<div class='fac_file_subject'><span style='font-size:9px;'>Subject : </span> $subject</div>
					<div class='fac_file_addinfo'>$add_info</div>
					<div class='fac_file_link'><span style='font-size:9px'>Attachment : </span> <a target='_blank' href='$file_path'><i class='fa fa-download' aria-hidden='true'></i> Download</a></div>
					<div class='time_msg fac_file_time_msg'>$datetime Added by : <a href='$added_by'>$added_by_name</a></div>
				</div>";

			}//while loop ends here.

		}	// checking if numbers of row greater than 0
		else {
			$return_string = '<p style="border:1px solid #eee; text-align:center;">Nothing to Display</p>';
		} // else of if numbers of rows greater than 0

		echo $return_string ;
	} // function ends here.

 	public function getNoOfNotices() {
 		$dept = $this->user['branch_id'];

 		if($this->user['course_id'] == '01')
 			$batch = $this->user['year_of_adm'];
 		else if($this->user['course_id'] == '31')
 			$batch = ($this->user['year_of_adm'] - 1);

 		$college_id = $this->user['college_id'];

 		$select_query = mysqli_query($this->conn,"SELECT id FROM notices WHERE dept='$dept' AND batch='$batch' AND college_id='$college_id' ");

 		return mysqli_num_rows($select_query);
 	}

 	public function getNotices(){
 		$day = "";
 		$return_string = "";
 		$dept = $this->user['branch_id'];

 		if($this->user['course_id'] == '01')
 			$batch = $this->user['year_of_adm'];
 		else if($this->user['course_id'] == '31')
 			$batch = ($this->user['year_of_adm'] - 1);

 		$college_id = $this->user['college_id'];

 		$select_query = mysqli_query($this->conn,"SELECT * FROM notices WHERE dept='$dept' AND batch='$batch' AND college_id='$college_id' ORDER BY id DESC ");

 		if(mysqli_num_rows($select_query) > 0) {

 			while($row = mysqli_fetch_assoc($select_query)) {

 				$subject = $row['subject'];
 				$message = $row['message'];
 				$file_path = $row['file'] == "" ? "" : "<div class='fac_file_link'><span style='font-size:9px'>Attachment : </span> <a target='_blank' href='".$row['file']."'><i class='fa fa-download' aria-hidden='true'></i> Download</a></div>";
 				$add_info = $row['add_info'];
 				$datetime = $row['datetime'];
 				$dt = new DateTime($datetime);
 				$datetime = $dt->format("d-m-Y h:i A");
 				$added_by = $row['added_by'];
 				$added_by_object = new User($this->conn, $added_by);
 				$added_by_name = $added_by_object->getFirstAndLastName();

				$date_time_now = date("Y-m-d H:i:s");
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $dt->diff($end_date); //Difference between dates 

				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}

				if($day_msg != "")
						$return_string .= "<p style='text-align:center; margin:5px;' class='notification_day_msg'>$day_msg</p>";


 				$return_string .= "
 				<div class='fac_file_item'>
 					<div class='fac_file_subject'><span style='font-size:9px;'>Subject : </span> $subject</div>
 					<div class='fac_file_message'><span style='font-size:9px;'>Notice : </span> $message</div>
 					<div class='fac_file_addinfo'></div>
 					$file_path
 					<div class='time_msg fac_file_time_msg'>Added on : $datetime  Added By : <a href='$added_by'>$added_by_name</a></div>
 				</div>
 				";

 			} // while loop


 		} // if for number of rows returned.

 		else {
 			$return_string = '<p style="text-align:center; padding:1px 10px; border:1px solid #eee;">No Notices</p>';
 		}

 		echo $return_string;

 	}

 	public function verifiedUser(){
 		if($this->user['email_verified'] == 'yes')
 			return true;
 		else
 			return false;
 	}

 	public function profileComplete() {
		if($this->user['college_id'] == '' || $this->user['branch_id'] == "") {
			return false;
		}
 		return true;
 	}

 	public function loadMyBooks($calling_user) {
		$user_id = $this->user['user_id'];

		$str = "";

		$data_query = mysqli_query($this->conn, "SELECT * FROM books WHERE added_by='$user_id' ");

		if(mysqli_num_rows($data_query)>0){

			while($row = mysqli_fetch_assoc($data_query)) {
				$id = $row['id'];
				$added_by = $row['added_by'];
				$title = $row['title'];
				$description = $row['description'];
				$contact = $row['contact'];
				$datetime = $row['datetime'];
				$image1 = $row['image1'];
				$image2 = $row['image2'];
				$price = $row['price'];
				$branch_code = $row['branch_code'];
				$college_code = $row['college_code'];

				$added_by_user_object = new User($this->conn, $added_by);
				$added_by_user_detail_query = mysqli_query($this->conn,"SELECT * FROM users WHERE user_id='$user_id' ");
				$added_by_user_row = mysqli_fetch_assoc($added_by_user_detail_query);
				$first_name = $added_by_user_row['first_name'];
				$last_name = $added_by_user_row['last_name'];
				$profile_pic = $added_by_user_row['profile_pic'];

				if($added_by == $calling_user) {
					$delete_btn = "<button style='float:right;' class='delete_classfile_button' onclick='deleteBook(".$id.")'>Delete</button>";
					$delete_warning_div = "<div class='hidden alert alert-danger delete_warning' id='book_delete_warning_".$id."'>Are you sure ? 
					<button class='confirm_delete_btn' onclick='confirmDeleteBook(".$id.")'>Yes</button>
					<button class='confirm_delete_btn' onclick='dontDeleteBook(".$id.")'>No</button>
					</div>";
				}
				else {
					$delete_warning_div = "";
					$delete_btn = "";
				}

				//time frame
				$date_time_now = date("Y-m-d H:i:s");
				$start_time = new DateTime($datetime);
				$end_time = new DateTime($date_time_now);
				$interval = $start_time->diff($end_time);

				if($interval->y >= 1) {
					if($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else 
						$time_message = $interval->y . " years ago"; //1+ year ago
				}
				else if ($interval-> m >= 1) {
					if($interval->d == 0) {
						$days = " ago";
					}
					else if($interval->d == 1) {
						$days = $interval->d . " day ago";
					}
					else {
						$days = $interval->d . " days ago";
					}

					if($interval->m == 1) {
						$time_message = $interval->m . " month". $days;
					}
					else {
						$time_message = $interval->m . " months". $days;
					}

				}
				else if($interval->d >= 1) {
					if($interval->d == 1) {
						$time_message = "Yesterday";
					}
					else {
						$time_message = $interval->d . " days ago";
					}
				}
				else if($interval->h >= 1) {
					if($interval->h == 1) {
						$time_message = $interval->h . " hour ago";
					}
					else {
						$time_message = $interval->h . " hours ago";
					}
				}
				else if($interval->i >= 1) {
					if($interval->i == 1) {
						$time_message = $interval->i . " minute ago";
					}
					else {
						$time_message = $interval->i . " minutes ago";
					}
				}
				else {
					if($interval->s < 5) {
						$time_message = "Just now";
					}
					else {
						$time_message = $interval->s . " seconds ago";
					}
				}

				$str .= "
				$delete_warning_div
				<div id='book_$id' class='book_post'>
								<div class='book_head'>
									<div class='book_details'> 
									  <div class='book_title'>Title : $title</div>
									  $delete_btn
									  <span class='book_description'>$description</span>
									</div> 
									<div class='book_seller_details'>
										Added by : <a href='$added_by'>$first_name $last_name</a>, Mobile Number : $contact
									</div> 
									<span class='book_time_msg'>Added $time_message</span>
								</div>
								<div class='book_post_image'>
									<img src='$image1'>
									<img src='$image2'>
								</div>
								<p class='book_price'>&#x20B9; $price</p> 
							</div>
							<br>
							";
			} // while loop.

			echo $str;
		} // number of rows return is greater than 0. if
		else{
			echo "<p style='text-align:center; padding:2px; border:1px solid #eee; font-size:14px;'>Nothing to Show</p>";
		}
 	}

 	public function facLoadMyAnnouncements() {
 		$user_id = $this->user['user_id'];
 		$college_id = $this->user['college_id'];
		$day = "";
		$dept = $this->user['branch_id'];
		$return_string = "";
		$select_query = mysqli_query($this->conn,"SELECT * FROM anns WHERE added_by='$user_id' ORDER BY id DESC");
		if( mysqli_num_rows($select_query) > 0 ) {
			
			while($row = mysqli_fetch_assoc($select_query)){

				$id = $row['id'];
				$added_by = $row['added_by'];

				if($added_by == $user_id) {
					$delete_btn = "<button class='delete_classfile_button' onclick='deleteAnn(".$id.")'>Delete</button>";
					$delete_warning_div = "<div class='hidden alert alert-danger delete_warning' id='delete_warning_".$id."'>Are you sure ? 
					<button class='confirm_delete_btn' onclick='confirmDeleteAnn(".$id.")'>Yes</button>
					<button class='confirm_delete_btn' onclick='dontDeleteAnn(".$id.")'>No</button>
					</div>";
				}
				else {
					$delete_warning_div = "";
					$delete_btn = "";
				}

				$added_by_object = new User($this->conn,$added_by);
				$heading = $row['heading'];
				$ann = $row['ann'];
				$datetime = $row['date'];

				$date_time_now = date("Y-m-d H:i:s");
				$start_time = new DateTime($datetime);
				$end_time = new DateTime($date_time_now);
				$interval = $start_time->diff($end_time);

				$dt = new DateTime($datetime);
				$datetime = $dt->format("d-m-Y");


				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}


				if($row['add_info'] == '')
					$add_info = "";
				else
					$add_info = "<div class='add_info'>".$row['add_info']."</div>";

				if($row['file'] == '') {
					$file_path = "";
				}
				else
					$file_path = "<div class='file_link'><a target='_blank' href='".$row['file']."'>Uploaded file</a></div>";

				if($day_msg != "")
					$return_string .= "<p class='day_msg_p'>$day_msg</p>";

				$depts = $row['dept'];
				$dept_array = explode(',', $depts);

				$i = 0;
				$departments= "";
				foreach($dept_array as $branch) {
					if($i++ == 0) continue;
					$select_query = mysqli_query($this->conn,"SELECT name FROM department WHERE code='$branch' ");
					$dept_row = mysqli_fetch_assoc($select_query);
					$departments .= $dept_row['name']. "&nbsp;&nbsp;";
				}


				$return_string .= "
				$delete_warning_div
				<div id='ann_$id' class='announcement'>
					<div class='heading'>$heading $delete_btn</div>
					<div class='ann_detail'>$ann</div>
					$add_info
					<div class='time_msg fac_file_time_msg'>Announced on $datetime</div>
					$file_path
					<span style='margin-left:5px; font-size:12px;'>For : $departments</span>
				</div>";

			} // while loop
		} // check if more than 0 rows returned
		else
			$return_string .= "<p style='text-align:center; margin:10px; border:1px solid #eee; padding:10px;'>No Announcement</p>";
		echo $return_string;

 	}

 	public function facLoadMyAssignments() {
 		$user_id = $this->user['user_id'];
 		$day = "";
		$return_string = "";

		$select_query = mysqli_query($this->conn,"SELECT * FROM fac_assigns WHERE added_by='$user_id' ORDER BY id DESC");

		if(mysqli_num_rows($select_query) > 0) {

			while($row=mysqli_fetch_assoc($select_query)) {

				$type_of_file = $row['type'];
				$whatisthis = ucfirst($type_of_file);
				$id = $row['id'];
				$added_by = $row['added_by'];
				$batch = $row['batch'];
				$dept = $row['dept'];

				$dept_name_query = mysqli_query($this->conn,"SELECT * FROM department WHERE code='$dept' ");
				$dept_name = mysqli_fetch_assoc($dept_name_query)['name'];

				if($added_by == $user_id) {
					$delete_btn = "<button class='delete_classfile_button' onclick='deleteFacFile(".$id.")'>Delete</button>";
					$delete_warning_div = "<div class='hidden alert alert-danger delete_warning' id='delete_warning_".$id."'>Are you sure ? 
					<button class='confirm_delete_btn' onclick='confirmDeleteFacFile(".$id.")'>Yes</button>
					<button class='confirm_delete_btn' onclick='dontDeleteFacFile(".$id.")'>No</button>
					</div>";
				}
				else {
					$delete_warning_div = "";
					$delete_btn = "";
				}

				$subject = $row['subject'];
				$add_info = $row['add_info'];
				$file_path = $row['file'];
				$datetime = $row['date'];
				$dt = new DateTime($datetime);
				$datetime = $dt->format("d-m-Y h:i A");

				$date_time_now = date("Y-m-d H:i:s");
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $dt->diff($end_date); //Difference between dates 

				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}

				if($day_msg != "")
						$return_string .= "<p style='text-align:center; margin:5px;' class='notification_day_msg'>$day_msg</p>";

				$return_string .= "
				$delete_warning_div
				<div id='fac_file_$id' class='fac_file_item'>
					<div class='fac_file_subject'><span style='font-size:9px;'>Subject : </span> $subject $delete_btn</div>
					<div class='fac_file_addinfo'>$add_info</div>
					<div class='fac_file_link'><a target='_blank' href='$file_path'><i class='fa fa-download' aria-hidden='true'></i> Download</a></div>
					<div class='time_msg fac_file_time_msg'>$datetime $type_of_file For $dept_name $batch-Batch</div>
				</div>";

			}//while loop ends here.

		}	// checking if numbers of row greater than 0
		else {
			$return_string = '<p style="border:1px solid #eee; text-align:center;">Nothing to Display</p>';
		} // else of if numbers of rows greater than 0

		echo $return_string ;
 	}

 	public function facLoadMyNotices() {
 		$user_id = $this->user['user_id'];
 		$day = "";
 		$return_string = "";

 		$select_query = mysqli_query($this->conn,"SELECT * FROM notices WHERE added_by='$user_id' ORDER BY id DESC ");

 		if(mysqli_num_rows($select_query) > 0) {

 			while($row = mysqli_fetch_assoc($select_query)) {

 				$id = $row['id'];

 				$subject = $row['subject'];
 				$message = $row['message'];
 				$file_path = $row['file'] == "" ? "" : "<div class='fac_file_link'><a target='_blank' href='".$row['file']."'><i class='fa fa-download' aria-hidden='true'></i> Download</a></div>";
 				$add_info = $row['add_info'];
 				$datetime = $row['datetime'];
 				$dt = new DateTime($datetime);
 				$datetime = $dt->format("d-m-Y h:i A");

 				$batch = $row['batch'];
 				$dept = $row['dept'];
 				$dept_name_query = mysqli_query($this->conn,"SELECT name FROM department WHERE code='$dept' ");
 				$dept_name = mysqli_fetch_assoc($dept_name_query)['name'];

 				$added_by = $row['added_by'];

				if($added_by == $user_id) {
					$delete_btn = "<button class='delete_classfile_button' onclick='deleteNotice(".$id.")'>Delete</button>";
					$delete_warning_div = "<div class='hidden alert alert-danger delete_warning' id='delete_warning_".$id."'>Are you sure ? 
					<button class='confirm_delete_btn' onclick='confirmDeleteNotice(".$id.")'>Yes</button>
					<button class='confirm_delete_btn' onclick='dontDeleteNotice(".$id.")'>No</button>
					</div>";
				}
				else {
					$delete_warning_div = "";
					$delete_btn = "";
				}

				$date_time_now = date("Y-m-d H:i:s");
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $dt->diff($end_date); //Difference between dates 

				if($interval->d < 1) {
					$time_message = 'In Last 24 Hours';
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					} 
				}
				else {
					$time_message = $dt->format('d-m-Y');
					if($day == $time_message)
						$day_msg = "";
					else if($day != $time_message ) {
						$day = $time_message;
						$day_msg = $day;
					}
				}

				if($day_msg != "")
						$return_string .= "<p style='text-align:center; margin:5px;' class='notification_day_msg'>$day_msg</p>";


 				$return_string .= "
 				$delete_warning_div
 				<div id='notice_$id' class='fac_file_item'>
 					<div class='fac_file_subject'><span style='font-size:9px;'>Subject : </span> $subject $delete_btn</div>
 					<div class='fac_file_message'><span style='font-size:9px;'>Notice : </span> $message</div>
 					<div class='fac_file_addinfo'></div>
 					$file_path
 					<div class='time_msg fac_file_time_msg'>Added on : $datetime For: $dept_name $batch-Batch</div>
 				</div>
 				";

 			} // while loop


 		} // if for number of rows returned.

 		else {
 			$return_string = '<p style="text-align:center; padding:1px 10px; border:1px solid #eee;">No Notices</p>';
 		}

 		echo $return_string;



 	}


}

?>
