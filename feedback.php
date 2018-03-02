<?php  
require('lib/includes/header.php');
$uttam_user_id = 1;
$uttam_user = new User($conn,$uttam_user_id);

?>
<!DOCTYPE html>
<html>
<head>
	<?php require('assets.php'); ?>
	<title>About</title>
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

<div class='container about_middle_bar'>
	<div class="col-md-3 about_user_profile column">
		<legend><a style="text-decoration: none;" href="<?= $uttam_user_id ?>"><?php echo $uttam_user->getFirstAndLastName();?></a></legend>
		<div class="profile_pic_section">
			<img class="profile_pic" src="<?php echo $uttam_user->getProfilePic(); ?>" height="100px">
		</div>
		<?php if($user->getType()=='student')
				echo '<b>Enrollment No</b> - ' . $uttam_user->getEnrollmentNo(); 
			else if($user->getType()=='faculty')	
				echo '<b>Email</b> - '. $uttam_user->getEmail();
		?><br>
		<?php echo '<b>Friends</b> - ' . $uttam_user->numOfFriends(); ?><br>
		<?php echo '<b>Department</b><br>'. $uttam_user->getDept(); ?><br>
		<?php echo '<b>Institute</b><br>'. $uttam_user->getCollegeName(); ?><br>
	</div>

<div class='col-md-8 column'>
	uttam 
</div>


</div>

</body>
</html>