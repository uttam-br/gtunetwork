<?php  
require('lib/includes/header.php');
require('lib/includes/unverified_redirect.php');

if($user->sem())
  $sem = $user->getSem();
?>

<!DOCTYPE html>
<html>
<head>
  <?php require_once('assets.php'); ?>
	<title>Study Material | <?= $user->getFirstAndLastName(); ?></title>
</head>
<body>

<div class='fluid-container top_bar'>
  <div class='col-md-1 logo'>
    <a href='home.php'>GTUnetwork</a>
  </div>

  <div class='mobile_nav'>
    <button id='nav_button'><i class="fa fa-bars" aria-hidden="true"></i></button>
    <div id='mobile_navbar'>
      <a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
      <a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
      <a href='announcements.php'><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;Announcements</a>
      <a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
      <a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
      <?php
      if($user->getType()=='student') {
        echo '<a class="active" href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
        <a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
      }
      ?>
      <a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
    </div>
  </div>

  <div class='col-md-11 nav'>
    <nav id='nav_bar'>
      <a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
      <a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
      <a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
      <a href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
      <a class='active' href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
      <a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>
      <a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
    </nav>
  </div>
</div>



<div class='fluid-container middle_bar'>

  <?php require('lib/includes/user_info_left_side.php'); ?>	<!-- this takes col-md-3 -->
 
  <div class='col-md-8 column study_material_main_column'>
    <?php 
    if(!isset($sem)) { 
    ?>
    	<select onchange='loadStudyMaterial(this.value)' class='select_button' id='sem_select'>
    		<option value='0'>Select Semester</option>
    		<option value='1'>1st/2nd Semester</option>
    		<option value='3'>3rd Semester</option>
    		<option value='4'>4th Semester</option>
    		<option value='5'>5th Semester</option>
    		<option value='6'>6th Semester</option>
    		<option value='7'>7th Semester</option>
    		<option value='8'>8th Semester</option>
    	</select>
    <?php 
    }
    ?>
    <?php
        if(isset($sem)) {
    ?>
      <a onclick='changeSemButton();' class='change_sem_button'>Change Sem</a>
    <?php 
    }
    ?>
    <div id='change_sem_section'></div>
    
    <img id='loading' src='res/img/loading.gif' class='home_loading_gif' style="display: none;">

    <div id='study_material_section'></div>

  </div>

</div>

<script type="text/javascript">
  $(document).ready(function(){
    var sem = '<?= $sem ?>';
    loadStudyMaterial(sem);

    $('#nav_button').click(function(){
      if($('#mobile_navbar').css("display") == 'none') {
        $('#mobile_navbar').slideDown('fast');
      } else {
        $('#mobile_navbar').slideUp('fast');
      }
    });

  });
</script>

</body>
</html>