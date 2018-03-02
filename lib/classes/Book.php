<?php

class Book {
	private $user_object;
	private $conn;
	public function __construct ($conn, $user_id) {
		$this->conn = $conn;
		$this->user_object = new User($conn,$user_id);
	}

	public function getBooks($year){
		$user_id = $this->user_object->getUserid();
		$user_id_college = $this->user_object->getCollegeCode();
		$user_id_dept = $this->user_object->getDeptCode();

		$year = htmlentities($year);
		$year = mysqli_real_escape_string($this->conn,$year);

		$str = "";

		$data_query = mysqli_query($this->conn, "SELECT * FROM books WHERE (year='$year' AND branch_code ='$user_id_dept') AND college_code='$user_id_college' ORDER BY id DESC");

		if(mysqli_num_rows($data_query)>0){

			while($row = mysqli_fetch_assoc($data_query)) {
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

				$str .= "<div class='book_post'>
								<div class='book_head'>
									<div class='book_details'> 
									  <div class='book_title'>Title : $title</div>
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
			echo "<p style='text-align:center; padding:3px; border:1px solid #eee; font-size:13px;'>No Books to load</p>";
		}

	}
}
?>