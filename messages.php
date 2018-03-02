<?php  
require_once('lib/includes/header.php');

$message_obj = new Message($conn,$user_id);

if(isset($_GET['u'])){
	$user_to = $_GET['u'];
	$user_to_obj = new User($conn,$user_to);
}

?>

<div class='msg_column col-md-3'>

	<?php  
		$online_status = $user_to_obj->getLastActive();

		if($online_status == 'active') {
			$online_status = '<span style="background: rgb(66, 183, 42) none repeat scroll 0% 0%; border-radius: 50%; display: inline-block; height: 6px; margin-left: 4px; width: 6px;"></span>';
		}

		echo "<p style='margin:3px; padding:2px 5px; font-size:12px;'><a target='_parent' href='$user_to'>".$user_to_obj->getFirstAndLastName()."</a> <span class='time_msg'>$online_status</span></p>";
		echo "<div class='loaded_messages' id='scroll_messages'>";
		echo $message_obj->getMessages($user_to);
		echo "</div>";
	?>

	<div class="message_post">
		<?php 
			echo "<textarea name='message_body' id='message_textarea' placeholder='Write Your Message'></textarea>";
			echo "<button type='submit' name='post_message' id='message_submit'><i class='fa fa-paper-plane' aria-hidden='true'></i></button>";
		?>
	</div>

	<script>
	$(document).ready(function(){
		var firsttime = true;
		var old_data = "old_data";
		var new_data = "new_data";
		var user_id = '<?= $user_id ?>';
		var otherUser = '<?= $user_to ?>';
		
				
		functionRef = setInterval(function(){
			$.ajax({
				type:"POST",
				url : 'lib/ajax/messages_load_messages.php',
				data : "otherUser=" + otherUser,
				cache : false,
				success : function(data){
					old_data = new_data;
					new_data = data;
					$('.loaded_messages').html(data);
					if(!(old_data == new_data) && firsttime == false) {
						var div = document.getElementById('scroll_messages');
						div.scrollTop = div.scrollHeight;
					}
					firsttime = false;
				}
			});
		},2000);

		var div = document.getElementById('scroll_messages');
		div.scrollTop = div.scrollHeight;
		$('#message_submit').click(function(){
			var user_to = '<?= $user_to ?>';
			var msg = $('#message_textarea').val();
			msg = encodeURIComponent(msg);
			$.ajax({
				type: "POST",
				url: 'lib/ajax/messages_send_message.php',
				data: 'user_to=' + user_to + '&msg=' + msg,
				cache: false,
				success : function() {
					$.ajax({
						type:"POST",
						url : 'lib/ajax/messages_load_messages.php',
						data : "otherUser=" + user_to,
						cache : false,
						success : function(data){
							$('.loaded_messages').html(data);
							var div = document.getElementById('scroll_messages');
							div.scrollTop = div.scrollHeight;
							$('#message_textarea').val('');
						}
					});
				}
			});
		});

	   //  $("#message_textarea").keypress(function (e) {
	   //      if(e.which == 13) {
	   //         	var user_to = '<?= $user_to ?>';
				// var msg = $('#message_textarea').val();
				// msg = encodeURIComponent(msg);
				// $.ajax({
				// 	type: "POST",
				// 	url: 'lib/ajax/messages_send_message.php',
				// 	data: 'user_to=' + user_to + '&msg=' + msg,
				// 	cache: false,
				// 	success : function() {
				// 		$.ajax({
				// 			type:"POST",
				// 			url : 'lib/ajax/messages_load_messages.php',
				// 			data : "otherUser=" + user_to,
				// 			cache : false,
				// 			success : function(data){
				// 				$('.loaded_messages').html(data);
				// 				var div = document.getElementById('scroll_messages');
				// 				div.scrollTop = div.scrollHeight;
				// 				$('#message_textarea').val('');
				// 			}
				// 		});
				// 	}
				// });
	   //      }
	   //  });
	});
	</script>

</div>