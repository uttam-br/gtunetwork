<?php
require('lib/includes/header.php');
require('lib/includes/unverified_redirect.php');

$messages = new Message($conn,$user_id);
$num_messages = $messages->getUnreadNumber();
$notifications = new Notification($conn,$user_id);
$num_notifications = $notifications->getUnreadNumber();
$num_requests = $user->getNumOfRequests();
$total = $num_requests + $num_notifications + $num_messages;

?>
<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>Home <?php if($total > 0) echo "(".$total.")"; ?> | <?= $user->getFirstAndLastName();?></title>
</head>
<body>

<div class='fluid-container top_bar'>
	<div class='col-xs-1 logo'>
		<a class='nav-brand' href='home.php'>GTUnetwork</a>
	</div>
	<div class='mobile_nav'>
		<button id='nav_button'><i class="fa fa-bars" aria-hidden="true"></i></button>
		<div id='mobile_navbar'>
			<a class='active' href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
			<a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
			<a href='announcements.php'><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;Announcements</a>
			<a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
			<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
			<?php
			if($user->getType()=='student') {
				echo '<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
				<a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
			}
			?>
			<a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
		</div>
	</div>
	<div class='col-md-11 nav'>
		<nav id='nav_bar'>
			<a class='active' href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
			<a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
			<a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
			<a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
			<?php
			if($user->getType()=='student') {
				echo '<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
				<a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
			}
			?>
			<a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
		</nav>
	</div>
</div>

	
<div class='fluid-container middle_bar'>

	<?php require('lib/includes/user_info_left_side.php'); ?>	<!-- this takes col-md-3 -->
	
	<div class='sub_nav col-md-3 column'>
		
		<div class='links'>
			
			<a title='Friend Requests' class='linkActive' id='requests_button'><i class="fa fa-user-plus" aria-hidden="true"></i> <span id='num_of_requests'></span></a> <span style='color:#8183e9'>|</span>
			<a title='Messages' id='messages_button'><i class="fa fa-envelope-o" aria-hidden="true"></i> <span id='num_of_messages'></span></a> <span style='color:#8183e9'>|</span>
			<a title='Notifications' id='notifications_button'><i class="fa fa-bell-o" aria-hidden="true"></i> <span id='num_of_notifications'></span></a> <span style='color:#8183e9'>|</span>
			<a title='Online Friends' id='chat_button'><span style="background: rgb(66, 183, 42) none repeat scroll 0% 0%; border-radius: 50%; display: inline-block; height: 6px; margin-left: 4px; width: 6px;"></span> <span style='font-size:9px' id='num_of_chatters'></span></a>

		</div>

		<img id='loading' src='res/img/loading.gif' class='home_loading_gif'>
		<div id='load_content_here'></div>

		<script>
		$(document).ready(function(){
			var num_load_requests = setInterval(function() {
				$.ajax({
					url : 'lib/ajax/home_load_num_requests.php',
					type : 'post',
					cache : false,
					success : function(data) {
						$('#num_of_requests').html(data);
					}
				});
			},1000);
			
			var num_load_messages = setInterval(function() {
				$.ajax({
					url : 'lib/ajax/home_load_num_messages.php',
					type : 'post',
					cache : false,
					success : function(data) {
						$('#num_of_messages').html(data);
					}
				});
			},1000);
			
			var num_load_notifications = setInterval(function() {
				$.ajax({
					url : 'lib/ajax/home_load_num_notifications.php',
					type : 'post',
					cache : false,
					success : function(data) {
						$('#num_of_notifications').html(data);
					}
				});
			},1000);
			
			var num_load_requests = setInterval(function() {
				$.ajax({
					url : 'lib/ajax/home_load_num_chatters.php',
					type : 'post',
					cache : false,
					success : function(data) {
						$('#num_of_chatters').html(data);
					}
				});
			},1000);
			$('#load_content_here').html('');
			$('#loading').show();
			$.ajax({
				url: 'lib/ajax/home_load_requests.php',
				type: "POST",
				cache: false,
				success : function(data){
					$('#loading').hide();
					$('#load_content_here').html(data);
				}
			});

			$('#requests_button').click(function(){
				$('#chat_button').removeClass('linkActive');
				$('#messages_button').removeClass('linkActive');
				$('#notifications_button').removeClass('linkActive');
				$('#requests_button').addClass('linkActive');
				$('#load_content_here').html('');
				$('#loading').show();
				$.ajax({
					url: 'lib/ajax/home_load_requests.php',
					type: "POST",
					cache: false,
					success : function(data){
						$('#loading').hide();
						$('#load_content_here').html(data);
					}
				});
			});

			$('#messages_button').click(function(){
				$('#chat_button').removeClass('linkActive');
				$('#messages_button').addClass('linkActive');
				$('#notifications_button').removeClass('linkActive');
				$('#requests_button').removeClass('linkActive');
				$('#load_content_here').html(' ');
				$('#loading').show();
				$.ajax({
					url: 'lib/ajax/home_load_convos.php',
					type: "POST",
					cache: false,
					success : function(data){
						$('#loading').hide();
						$('#load_content_here').html(data);
					}
				});
			});

			$('#notifications_button').click(function(){
				$('#chat_button').removeClass('linkActive');
				$('#messages_button').removeClass('linkActive');
				$('#notifications_button').addClass('linkActive');
				$('#requests_button').removeClass('linkActive');
				$('#load_content_here').html('');
				$('#loading').show();
				$.ajax({
					url: 'lib/ajax/home_load_notifications.php',
					type: "POST",
					cache: false,
					success : function(data){
						$('#loading').hide();
						$('#load_content_here').html(data);
					}
				});
			});

			$('#chat_button').click(function(){
				$('#messages_button').removeClass('linkActive');
				$('#notifications_button').removeClass('linkActive');
				$('#requests_button').removeClass('linkActive');
				$('#chat_button').addClass('linkActive');
				$('#load_content_here').html('');
				$('#loading').show();
				$.ajax({
					url: 'lib/ajax/home_load_chatters.php',
					type: 'POST',
					cache: false,
					success : function(data) {
						$('#loading').hide();
						$('#load_content_here').html(data);
					}
				});
			});
 		});
		</script>
	</div>
 
	<div class='search_section'>
		<form action='search.php' method="GET"> 
			<input type='text' id='search_input' name='search_input' placeholder="Search People..." required>
			<button type='submit' id='search_button'><i class="fa fa-search" aria-hidden="true"></i></button>
		</form>
	</div>
	 		
	<div class="col-md-6 home_main_column column">
		<div class='home_main_column_links'>
			<a href='feed.php'><div class='home_main_column_link'><h1><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</h1><p class='text-center link_description'>This is where you can see posts from your friends and everyone at your college.</p></div></a>
			<a href='profile.php'><div class='home_main_column_link'><h1><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</h1><p class='text-center link_description'>This is where you can see detail about your profile.</p></div></a>
			<a href='myclass.php'><div class='home_main_column_link'><h1><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</h1><p class='text-center link_description'><?php if($user->getType()=='student') echo "This is where pre-formed group of you and your classmate will be visible. You can share and access assignments, practicals and notes here."; else echo "This is where you can make announcment, send assignments/practicals as well as notify class of students."; ?></p></div></a>
			<?php
			if($user->getType()=='student') {
			echo "<a href='study.php'><div class='home_main_column_link'><h1><i class='fa fa-book' aria-hidden='true'></i>&nbsp;&nbsp;Study Material</h1><p class='text-center link_description'>This is where you will find the syllabus, previous year question papers, books and etc. sorted semester wise.</p></div></a>
			<a href='bookstore.php'><div class='home_main_column_link'><h1><i class='fa fa-leanpub' aria-hidden='true'></i>&nbsp;&nbsp;Bookstore</h1><p class='text-center link_description'>This is where you can sell or buy used books.</p></div></a>";
			} ?>
			<a href='feedback.php'><div class='home_main_column_link'><h1><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;Feedback</h1><p class='text-center link_description'>This is where you can provide your views about the website. If you face any problem, please take a moment and report it here.</p></div></a>

		</div>

	</div>

</div> <!-- middle bar div -->

<div id='messanger' class='col-md-3' style="display:none;"></div>


<script type="text/javascript">
	$(document).ready(function(){
		$(document).mouseup(function(e) {
		    var container = $("#messanger");
		    var loadedcontent = $('#load_content_here');
		    if (!container.is(e.target)  && container.has(e.target).length === 0) 
		    {
		        container.slideUp('medium');
		        clearInterval(functionRef);
		    }
		});

		$('#nav_button').click(function(){
			if($('#mobile_navbar').css("display") == 'none') {
				$('#mobile_navbar').slideDown('fast');
			} else {
				$('#mobile_navbar').slideUp('fast');
			}
		});

	});

	function showMessages(with_user){
		$.ajax({
			url:'messages.php?u='+with_user,
			success : function(data) {
				$('#messanger').html(data);
				$('#messanger').slideDown('medium');
			}
		});
	}

</script>

</body>
</html>