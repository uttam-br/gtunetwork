<?php
class Notification {
	private $user_object;
	private $conn;
	public function __construct ($conn, $user_id) {
		$this->conn = $conn;
		$this->user_object = new User($conn,$user_id);
	}

	public function getUnreadNumber() {
		$user_id = $this->user_object->getUserid();
		$query = mysqli_query($this->conn, "SELECT * FROM notifications WHERE opened='no' AND user_to='$user_id'");
		return mysqli_num_rows($query);
	}

	public function getNotifications() {
		$user_id = $this->user_object->getUserid();
		$return_string = "";
		$day = "";
		$set_viewed_query = mysqli_query($this->conn, "UPDATE notifications SET viewed='yes' WHERE user_to='$user_id'");

		$query = mysqli_query($this->conn, "SELECT * FROM notifications WHERE user_to='$user_id' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "<p style='color:#555; text-align:center; margin:20px auto;'>You have no notification.</p>";
			return;
		}

		while($row = mysqli_fetch_array($query)) {

			$user_from = $row['user_from'];

			$user_data_query = mysqli_query($this->conn, "SELECT * FROM users WHERE user_id='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);

			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['datetime']); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 

			$dt = new DateTime($row['datetime']);
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
					$return_string .= "<p class='notification_day_msg'>$day_msg</p>";

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
				if($interval->s < 30) {
					$time_message = "Just now";
				}
				else {
					$time_message = $interval->s . " seconds ago";
				}
			}

			$opened = $row['opened'];
			$style = ($opened == 'no') ? "background-color: #DDEDFF;  margin:5px 0;" : "border:1px solid #eee; margin:5px 5px;";
			$style .= "padding:5px 10px; font-size:12px;";

			$return_string .=   "<div style='".$style."'>
									<a href='".$row['link']."'>".$row['message']."</a>&nbsp;&nbsp;<span class='time_msg'>[".$time_message."]</span>
								 </div>
								 ";
		}

		return $return_string;
	}

	public function insertNotification($post_id, $user_to, $type) {

		$post_id = htmlentities($post_id);
		$post_id = mysqli_real_escape_string($this->conn,$post_id);

		$user_to = htmlentities($user_to);
		$user_to = mysqli_real_escape_string($this->conn,$user_to);

		$type = htmlentities($type);
		$type = mysqli_real_escape_string($this->conn,$type);

		$user_id = $this->user_object->getUserid();
		$userLoggedInName = $this->user_object->getFirstAndLastName();

		$date_time = date("Y-m-d H:i:s");

		switch($type) {
			case 'comment':
				$message = $userLoggedInName . " <i class=\'fa fa-comments-o\' aria-hidden=\'true\'></i> commented on your post";
				break;
			case 'like':
				$message = $userLoggedInName . " <i class=\'fa fa-thumbs-o-up\' aria-hidden=\'true\'></i> liked your post";
				break;
			case 'profile_post':
				$message = $userLoggedInName . " posted on your profile";
				break;
			case 'comment_non_owner':
				$message = $userLoggedInName . " also <i class=\'fa fa-comments-o\' aria-hidden=\'true\'></i> commented on post";
				break;
			case 'profile_comment':
				$message = $userLoggedInName . " commented on your profile post";
				break;
			case 'request_accept':
				$message = $userLoggedInName . ' accepted your <i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i> request';
				break;
			case 'fac_ann_send' : 
				$message = $userLoggedInName . ' made an <i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i> announcement';
				break;
			case 'fac_ass_send' : 
				$message = $userLoggedInName . ' send <i class=\"fa fa-file-text-o\" aria-hidden=\"true"></i> '.$post_id;
				break;
			case 'fac_notice_send' : 
				$message = $userLoggedInName . ' send <i class=\"fa fa-bell\" aria-hidden=\"true\"></i> notice to your class';
				break;
			case 'student_send_file' : 
				$message = $userLoggedInName . ' send <i class=\"fa fa-file-text-o\" aria-hidden=\"true"></i> file';
				break;
		}
		if($type == 'request_accept')
			$link = $user_id;
		else if($type == 'fac_ann_send')
			$link = "announcements.php";
		else if($type == 'fac_ass_send' || $type == 'fac_notice_send' || $type == 'student_send_file')
			$link = 'myclass.php';
		else
			$link = "post.php?id=" . $post_id;

		$insert_query = mysqli_query($this->conn, "INSERT INTO notifications VALUES('', '$user_to', '$user_id', '$message', '$link', '$date_time', 'no', 'no')");
	}

}
?>
