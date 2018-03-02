<?php
class Post {
	private $user_object;
	private $conn;
	public function __construct ($conn, $user_id) {
		$this->conn = $conn;
		$this->user_object = new User($conn,$user_id);
	}

	public function submitPost($textBody,$to,$imageName,$privacy){
		$to = htmlentities($to);
		$to = mysqli_real_escape_string($this->conn,$to);
		$imageName = htmlentities($imageName);
		$imageName = mysqli_real_escape_string($this->conn,$imageName);
		$textBody = htmlentities($textBody);
		$textBody = mysqli_real_escape_string($this->conn,$textBody);
		$privacy = htmlentities($privacy);
		$check = preg_replace('/\s+/','',$textBody);
		if($check != ""){
			$date_posted = Date("Y-m-d H:i:s");
			$added_by = $this->user_object->getUserid();
			$added_by_enroll = $this->user_object->getEnrollmentNo();
			$inst_code = $this->user_object->getCollegeCode();

			$query = mysqli_query($this->conn,"INSERT INTO posts VALUES( '', '$textBody' , '$added_by' ,'$added_by_enroll','$to','$date_posted','$inst_code','no','0','$imageName','$privacy' )");
			$returned_id = mysqli_insert_id($this->conn);


			$num_posts = $this->user_object->getNumberOfPosts();
			$num_posts++;
			$update_query = mysqli_query($this->conn,"UPDATE users SET num_posts='$num_posts' WHERE user_id='$added_by'");
			$_SESSION['success'] = 'Posted Successfully !!!';

		} 
	}

	public function loadPosts($data,$limit,$from){

		$limit = htmlentities($limit);
		$limit = mysqli_real_escape_string($this->conn,$limit);
		$from = htmlentities($from);
		$from = mysqli_real_escape_string($this->conn,$from);

		$post_loaded = array();

		$user_id = $this->user_object->getUserid();
		
		$page = $data['page'];
		
		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;

		$str = "";

		if($from == 'friends'){

			$data_query = mysqli_query($this->conn,"SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

			if(mysqli_num_rows($data_query) >= 0 ) {

				$num_iterations = 0;
				$count = 1 ;

				while($row = mysqli_fetch_assoc($data_query)) {

					$post_id = $row['id'];
					
					if(in_array($post_id,$post_loaded)){
						continue;
					} else {
						array_push($post_loaded,$post_id);
					}
					
					$added_by = $row['added_by'];
					$privacy = $row['privacy'];

					if($privacy == 'public') {
						$load = 1;
					}
					if($privacy == 'friends') {
						if($this->user_object->isFriend($added_by))
							$load = 1;
						else 
							$load = 0;
					}
					if($privacy == 'college') {
						if($this->user_object->isCollegemate($added_by)) 
							$load = 1;
						else
							$load = 0;
					}

					if( $load ){

						if($row['to_wall'] == 'profile_pic')
							$detail = 'updated profile picture';
						else
							$detail = "";

						$post_body = $row['body'];

						if($post_body == 'profile_pic_update')
							$post_body = "";

						$date_posted = $row['date_posted'];
						$imagePath = $row['image'];
						$likes = $row['likes'];

						$check_user_likes = mysqli_query($this->conn,"SELECT * FROM likes WHERE user_id='$user_id' AND post_id='$post_id' ");
						if(mysqli_num_rows($check_user_likes) > 0)
							$user_liked = 'yes';
						else
							$user_liked = 'no';


						if($num_iterations++ < $start)
							continue;
						if($count > $limit)
							break;
						else
							$count++;

						$user_details_query = mysqli_query($this->conn,"SELECT * FROM users WHERE user_id='$added_by'");
						$user_row = mysqli_fetch_assoc($user_details_query);
						$first_name = $user_row['first_name'];
						$last_name = $user_row['last_name'];
						$profile_pic = $user_row['profile_pic'];
			?>
		<script>
			function toggle<?php echo $post_id; ?>() {
				var element = document.getElementById("toggleComment<?php echo $post_id; ?>");
				if(element.style.display == 'block')
					$('#toggleComment<?php echo $post_id; ?>').toggle();
				else
					$('#toggleComment<?php echo $post_id; ?>').toggle();
			}

		</script>
		<?php
						// comments number check.
						$comments_check = mysqli_query($this->conn,"SELECT * FROM comments WHERE post_id='$post_id'");
						$comment_check_num = mysqli_num_rows($comments_check);

						/// time frame

						$date_time_now = date("Y-m-d H:i:s");
						$start_time = new DateTime($date_posted);
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
						if($imagePath != ""){
							$imagePath = "<div class='load_post_image'><img src='$imagePath' width='100%'></div>";
						}
						else
							$imagePath = "";
						
						$select_query = mysqli_query($this->conn,"SELECT * FROM likes WHERE post_id='$post_id'");
						$num_of_likes = mysqli_num_rows($select_query);

						$liked_query = mysqli_query($this->conn,"SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id' ");

						if(mysqli_num_rows($liked_query) > 0) {
							$class = 'unlike_button';
							$like_button = "<button class='".$class."' id='like_button$post_id' onclick='unlikePost(".$post_id.")'><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> Like</button>";
						}
						else {
							$class = 'like_button';
							$like_button = "<button class='".$class."' id='like_button$post_id' onclick='likePost(".$post_id.")'><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> Like</button>";
						}


						$str .= "
						<div class='status_post column'>
							<div class='head'>
								<div class='post_profile_pic'>
									<img src='$profile_pic'>
								</div>
								<div class='posted_by' style='color:#555'>
									<a href='$added_by'>$first_name $last_name</a>&nbsp".$detail."<br>
									<span class='time_msg'>$time_message [$privacy]</span>
								</div>
							</div>

							<div id='post_body'>
								$post_body
								$imagePath
							</div>

							<div class='newsfeedPostOptions'>
							  <table>
							  <tr>
							   <td>$like_button
							   <span class='likes_link' onclick='loadLikes(".$post_id.")'>
							   	<a><span id='num_of_likes_of_".$post_id."'>".$num_of_likes."</span> Likes
							   	</a>
							   </span>
							   </td>
							   <td><span class='comment_link' onclick='javascript:toggle$post_id();'>
							   <i class='fa fa-comments-o' aria-hidden='true'></i> Comments</span></td>
							  </tr>
							  </table>
							</div>
							
							<div id='likes_div_$post_id' style='display:none;'></div>

							<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$post_id' id='comment_iframe'></iframe>
							</div>
						</div>
						";

					}// friends if

				} // while loop of $row.
				
				$str .= "
					<div class='status_post'>
						<div class='head'>
							<div class='post_profile_pic'>
								<img src='res/img/branding/gn.png'>
							</div>
							<div class='posted_by' style='color:#555'>
								<a href='home.php'>GTU Network</a><br>
								<span class='time_msg'>Since start</span>
							</div>
						</div>

						<div id='post_body'>
							Share something here. You can share it to your friends, everyone at your college or make it public.
							<img src='res/img/branding/gn_old.png' style='width:50%; display:block; margin:auto;'>
						</div>
						
					</div>
					";

				if($count > $limit)
						$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) ."'>
								 <input type='hidden' class='noMorePosts' value='false'>";
					else
						$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center'>No more posts to load !!!</p>";
			} // if of posts are greater than 0

			echo $str;

		} // if $from variable is friends.

		
		if($from == 'profile') {

			$profile_user_id = htmlentities($data['user_id']);
			$profile_user_id = mysqli_real_escape_string($this->conn,$profile_user_id);

			$profile_object = new User($this->conn,$profile_user_id);

			$data_query = mysqli_query($this->conn,"SELECT * FROM posts WHERE deleted='no' and added_by='$profile_user_id'  ORDER BY id DESC");

			$num_iterations = 0;
			$count =1 ;

			if(mysqli_num_rows($data_query) > 0) {

			 	while($row = mysqli_fetch_assoc($data_query)) {
				
					$added_by = $row['added_by'];
					
					$privacy = $row['privacy'];
					
					if($privacy == 'public') {
						$load = 1;
					}
					if($privacy == 'friends') {
						if($this->user_object->isFriend($added_by))
							$load = 1;
						else 
							$load = 0;
					}
					if($privacy == 'college') {
						if($this->user_object->isCollegemate($added_by)) 
							$load = 1;
						else
							$load = 0;
					}

					if( $load )	{

						$post_id = $row['id'];
						$post_body = $row['body'];

						if($added_by == $user_id) {
							$delete_btn = "<button class='delete_button' onclick='deletePost(".$post_id.")'>Delete</button>";
							$delete_warning_div = "<div class='hidden alert alert-danger delete_warning' id='delete_warning_".$post_id."'>Are you sure ? 
							<button class='confirm_delete_btn' onclick='confirmdelete(".$post_id.")'>Yes</button>
							<button class='confirm_delete_btn' onclick='dontdelete(".$post_id.")'>No</button>
							</div>";
						}
						else {
							$delete_warning_div = "";
							$delete_btn = "";
						}
						
						if($post_body == 'profile_pic_update')
							$post_body = "";
						
						$date_posted = $row['date_posted'];
						$imagePath = $row['image'];

						if($num_iterations++ < $start)
							continue;
						if($count > $limit)
							break;
						else
							$count++;

						$user_details_query = mysqli_query($this->conn,"SELECT * FROM users WHERE user_id='$added_by'");
						$user_row = mysqli_fetch_assoc($user_details_query);
						$first_name = $user_row['first_name'];
						$last_name = $user_row['last_name'];
						$profile_pic = $user_row['profile_pic'];
			?>
		<script>
			function toggle<?php echo $post_id; ?>() {
				var element = document.getElementById("toggleComment<?php echo $post_id; ?>");
				if(element.style.display == 'block')
					element.style.display = 'none';
				else
					element.style.display = 'block';
			}

		</script>
		<?php
						// comments number check.
						$comments_check = mysqli_query($this->conn,"SELECT * FROM comments WHERE post_id='$post_id'");
						$comment_check_num = mysqli_num_rows($comments_check);

						/// time frame

						$date_time_now = date("Y-m-d H:i:s");
						$start_time = new DateTime($date_posted);
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
						if($imagePath != ""){
							$imagePath = "<div class='load_post_image'><img src='$imagePath' width='100%'></div>";
						}
						else
							$imagePath = "";

						$select_query = mysqli_query($this->conn,"SELECT * FROM likes WHERE post_id='$post_id'");
						$num_of_likes = mysqli_num_rows($select_query);

						$liked_query = mysqli_query($this->conn,"SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id' ");

						if(mysqli_num_rows($liked_query) > 0) {
							$class = 'unlike_button';
							$like_button = "<button class='".$class."' id='like_button$post_id' onclick='unlikePost(".$post_id.")'><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> Like</button>";
						}
						else {
							$class = 'like_button';
							$like_button = "<button class='".$class."' id='like_button$post_id' onclick='likePost(".$post_id.")'><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> Like</button>";
						}

						$str .= "
						$delete_warning_div
						<div id='post_$post_id' class='status_post'>
							<div class='head'>
								<div class='post_profile_pic'>
									<img src='$profile_pic'>
								</div>
								<div class='posted_by' style='color:#ACACAC'>
									<a href='$added_by'>$first_name $last_name</a><br>
									<span class='time_msg'>$time_message [$privacy]</span>
									$delete_btn
								</div>
							</div>

							<div id='post_body'>
								$post_body$imagePath
							</div>

							<div class='newsfeedPostOptions'>
							  <table>
							  <tr>
							   <td>$like_button
							   <span class='likes_link' onclick='loadLikes(".$post_id.")'>
							   	<a><span id='num_of_likes_of_".$post_id."'>".$num_of_likes."</span> Likes
							   	</a>
							   </span>
							   </td>
							   <td><span class='comment_link' onclick='javascript:toggle$post_id();'>
							   <i class='fa fa-comments-o' aria-hidden='true'></i> Comments</span></td>
							  </tr>
							  </table>
							</div>
							
							<div id='likes_div_$post_id' style='display:none;'></div>

							<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$post_id' id='comment_iframe'></iframe>
							</div>
						</div>
						";

					}
					
				} // while loop of $row.
				
				if($count > $limit)
					$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) ."'>
							 <input type='hidden' class='noMorePosts' value='false'>";
				else
					$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center'>No more posts to load !!!</p>";

			} // if of posts are greater than 0

			if(!$this->user_object->isFriend($profile_user_id)) {		
				$str .= '<p style="border:1px solid #eee; padding:2px 10px; text-align:center">You can only see limited number of posts as you are not friend with '.$profile_object->getFirstAndLastName().". Send ".$profile_object->getFirstName()." request by clicking add friend button to see all the posts ".$profile_object->getFirstAndLastName()." share.</p>";
			} else if(mysqli_num_rows($data_query) == 0) {
				$str .= "<p style='text-align:center; padding:2px 5px; border:1px solid #eee;'>Nothing to show</p>";				
			}
			
			echo $str;
		
		}

	} // function close here.

}

?>
