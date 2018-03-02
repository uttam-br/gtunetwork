<?php
class Message {
	private $user_object;
	private $conn;
	public function __construct ($conn, $user_id) {
		$this->conn = $conn;
		$this->user_object = new User($conn,$user_id);
	}

	public function getMostRecentUser(){
		$user_id = $this->user_object->getUserid();
		$query = mysqli_query($this->conn," SELECT user_to,user_from FROM messages WHERE user_to='$user_id' OR user_from='$user_id' ORDER BY id DESC LIMIT 1");
		if( mysqli_num_rows($query) == 0 )
			return false;

		$row = mysqli_fetch_array($query);

		$user_to = $row['user_to'];
		$user_from = $row['user_from'];
		if($user_to != $user_id)
			return $user_to;
		else
			return $user_from;
	}

	public function sendMessage($user_to,$body,$date) {
		$check_msg = preg_replace('/\s+/', '', $body);
		if($check_msg != ""){
			$body = strip_tags($body);
			$body = mysqli_real_escape_string($this->conn,$body);
			$user_to = strip_tags($user_to);
			$user_to = mysqli_real_escape_string($this->conn,$user_to);
			$user_id = $this->user_object->getUserid();
			$query = mysqli_query($this->conn,"INSERT INTO messages VALUES ('','$user_to','$user_id','$body','$date','no','no','no' )");
			return true;
		}
	}

	public function getMessages($otherUser){
		$user_id = $this->user_object->getUserid();
		$data = "";

		$otherUser = htmlentities($otherUser);
		$otherUser = mysqli_real_escape_string($this->conn,$otherUser);

		$update_query = mysqli_query($this->conn,"UPDATE messages SET opened='yes' WHERE user_to='$user_id' AND user_from='$otherUser' ");

		$get_messages_query = mysqli_query($this->conn,"SELECT * FROM messages WHERE (user_to='$user_id' AND user_from='$otherUser') OR (user_from='$user_id' AND user_to='$otherUser') ");

		if(mysqli_num_rows($get_messages_query) > 0) {
		$day = "";
		while($row = mysqli_fetch_array($get_messages_query)){
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];
			$date_time = $row['date'];
			$dt = DateTime::createFromFormat("Y-m-d H:i:s", $date_time );
			$hours = $dt->format('h:iA'); // '20'
			$body = $row['body'];
			$opened = $row['opened'];
			$viewed = $row['viewed'];

			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['date']); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 

			if($interval->d < 1) {
				$time_message = 'Today';
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
					$data .= "<p class='notification_day_msg text-center' style='clear:both;'>$day_msg</p>";

			$div_top = ($user_to == $user_id) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";

			if($user_to == $user_id) {
				$time_msg = "<sub><span class='time_msg'>".$hours."</span></sub>";
			}
			else {
				if($opened == 'yes')
					$time_msg = "<sub><span class='time_msg'>".$hours."&nbsp;<img src='res/img/icons/seen.png' width='15' style='margin:0 2px; padding-bottom:2px;'></span></sub>";
				else if($viewed == 'yes')
					$time_msg = "<sub><span class='time_msg'>".$hours." <img src='res/img/icons/delivered.png' width='15' style='margin:2px;'></span></sub>";
				else 
					$time_msg = "<sub><span class='time_msg'>".$hours." <img src='res/img/icons/sent.png' width='15' style='margin:2px;'></span></sub>";
			}


			$data = $data . $div_top . $body ."&nbsp;<span style='margin:0; padding:0;'>".$time_msg."</span></div>";
		}
		} else {
			$data = '<p style="text-align:center; font-size:12px;" class="time_msg">no message show</p>';
		}
		return $data;

	}

	public function getLatestMessage($userLoggedIn,$user2) {

		$userLoggedIn = htmlentities($userLoggedIn);
		$userLoggedIn = mysqli_real_escape_string($this->conn,$userLoggedIn);
		$user2 = htmlentities($user2);
		$user2 = mysqli_real_escape_string($this->conn,$user2);

		$details_array = array();

		$query = mysqli_query($this->conn, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");

		$update_query = mysqli_query($this->conn,"UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn' AND user_from='$user2' ");
		

		$row = mysqli_fetch_array($query);
		$sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

		//Timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($row['date']); //Time of post
		$end_date = new DateTime($date_time_now); //Current time
		$interval = $start_date->diff($end_date); //Difference between dates 
		if($interval->y >= 1) {
			if($interval == 1)
				$time_message = $interval->y . " year ago"; //1 year ago
			else 
				$time_message = $interval->y . " years ago"; //1+ year ago
		}
		else if ($interval->m >= 1) {
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
		$time_message = 'Last Chat : '.$time_message;
		array_push($details_array, $sent_by);
		array_push($details_array, $row['body']);
		array_push($details_array, $time_message);

		return $details_array;
	}

	public function getConvos(){
		$user_id = $this->user_object->getUserid();
		$return_string = "";
		$convos = array();

		$set_viewed_query = mysqli_query($this->conn,"UPDATE messages SET viewed='yes' WHERE user_to='$user_id'");

		$query = mysqli_query($this->conn,"SELECT user_to,user_from FROM messages WHERE user_to='$user_id' OR user_from='$user_id' ORDER BY id DESC");

		if(mysqli_num_rows($query) > 0) {
		while($row=mysqli_fetch_array($query)){
			$user_to_push = ( $row['user_to'] != $user_id ) ? $row['user_to'] : $row['user_from'];
			if(!in_array($user_to_push,$convos)) {
				array_push($convos,$user_to_push);
			}
		}

		foreach($convos as $username) {
			$user_found_obj = new User($this->conn, $username);
			$latest_message_details = $this->getLatestMessage($user_id,$username);
			
			$is_unread_query = mysqli_query($this->conn,"SELECT opened FROM messages WHERE user_to='$user_id' AND user_from='$username' ORDER BY id DESC");

			$row = mysqli_fetch_array($is_unread_query);

			$style = ($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";

			$online_status = $user_found_obj->getLastActive();

			if($online_status == 'active') {
				$online_status = '<span style="background: rgb(66, 183, 42) none repeat scroll 0% 0%; border-radius: 50%; display: inline-block; height: 6px; margin-left: 4px; width: 6px;"></span>';
			}
			
			$dots = (strlen($latest_message_details[1]) >= 12 ) ? "..." : "";
			$split = str_split($latest_message_details[1],12);
			$split = $split[0] . $dots;
			$return_string .= "
			<div class='user_found_messages' style='".$style."'>
				<img onclick='showMessages(".$username.")' src='".$user_found_obj->getProfilePic()."'><a href='".$username."'>".$user_found_obj->getFirstAndLastName()."</a><br>
				<span class='time_msg'>".$latest_message_details[2]." ".$online_status."</span>
			</div>";
		}
		$return_string .= "<p style='text-align:center; margin:3px; color:#666; font-size:12px;'>click on profile picture to show conversation</p>";
		}
		else $return_string = "<p style='color:#555; text-align:center; margin:20px auto;'>You have no message.</p>";

		return $return_string;
	}

	
	public function getUnreadNumber(){
		$userLoggedIn = $this->user_object->getUserid();
		$query = mysqli_query($this->conn,"SELECT * FROM messages WHERE opened='no' AND user_to='$userLoggedIn' ");
		return mysqli_num_rows($query);
	}

}