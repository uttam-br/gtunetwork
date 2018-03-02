<div class="col-md-3 user_profile column">
		<legend><a style="text-decoration: none;" href="<?= $user_id ?>"><?php echo $user->getFirstAndLastName();?></a></legend>
		<div class="profile_pic_section">
			<img class="profile_pic" src="<?php echo $user->getProfilePic(); ?>" height="100px">
		</div>
		<?php if($user->getType()=='student')
				echo '<b>Enrollment No</b> - ' . $user->getEnrollmentNo(); 
			else if($user->getType()=='faculty')	
				echo '<b>Email</b> - '. $user->getEmail();
		?><br>
		<?php echo '<b>Friends</b> - ' . $user->numOfFriends(); ?><br>
		<?php echo '<b>Department</b><br>'. $user->getDept(); ?><br>
		<?php echo '<b>Institute</b><br>'. $user->getCollegeName(); ?><br>
</div>