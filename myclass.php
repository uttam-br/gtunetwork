<?php
  require_once('lib/includes/header.php');
  require_once('lib/handlers/form_handlers/myclass_form_handler.php');
  require_once('lib/handlers/form_handlers/faculty_class_handler.php');
  require('lib/includes/unverified_redirect.php');

  $update_query = mysqli_query($conn,"UPDATE notifications SET opened='yes' WHERE user_to='$user_id' AND link='myclass.php' ");
?>
<!DOCTYPE html>
<html>
<head>
  <?php require('assets.php'); ?>
	<title>Class | <?= $user->getFirstAndLastName(); ?></title>
  <script type="text/javascript">
      function openMessages(with_user){
        $.ajax({
          url:'messages.php?u='+with_user,
          success : function(data) {
            $('#messanger').html(data);
            $('#messanger').slideDown('medium');
          }
        });
      }

      $(document).ready(function(){
        $('#student_upload_button').click(function(){
          $('#loading').removeClass('hidden');
        })
      });
  </script>
  <?php  
      if(isset($_SESSION['assign_success'])) { echo $_SESSION['assign_success']; unset($_SESSION['assign_success']); }
  ?>  
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
      <a class='active' href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
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
      <a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a>
      <a href='feed.php'><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;News Feed</a>
      <a href="<?= $user_id ?>"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;Profile</a>
      <a class='active' href="myclass.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;Class</a>
      <?php
      if($user->getType()=='student') {
        echo '<a href="study.php"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;&nbsp;Study Material</a>
      <a href="bookstore.php"><i class="fa fa-leanpub" aria-hidden="true"></i>&nbsp;&nbsp;Bookstore</a>';
      } ?>
      <a class='logout_button' href="lib/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a>
    </nav>
  </div>
</div>


 <!--  if user is student  -->


<?php if($user->getType() == 'student') { ?>

<div id='middle_bar' class='fluid-container middle_bar' >
  <div class='col-md-4 fac_send_files column' id='faculty_column'>
    <fieldset>
      <legend class='ann_legend'>From Faculty</legend>

      <div id='fac_div'>

      <button class='myclass_main_button' id='fac_notice_load_button'>Notices<span style='float:right;'>
      <?php echo $user->getNoOfNotices(); ?></span></button>
      <div id='fac_notice_load' class='hidden'>
        <?php $user->getNotices(); ?>
      </div>

      <button class='myclass_main_button' id='fac_practical_load_button'>Practicals<span style='float:right;'>
      <?php echo $user->getNoOfDocsFromFac('practical'); ?></span></button>
      <div id='fac_practical_load' class='hidden'>
        <?php $user->getFilesFromFac('practical'); ?>
      </div>

      <button class='myclass_main_button' id='fac_assignment_load_button'>Assignments<span style='float:right;'>
      <?php echo $user->getNoOfDocsFromFac('assignment'); ?></span></button>
      <div id='fac_assignment_load' class='hidden'>
        <?php $user->getFilesFromFac('assignment'); ?>
      </div>

      <button class='myclass_main_button' id='fac_other_load_button'>Other Files<span style='float:right;'>
      <?php echo $user->getNoOfDocsFromFac('other'); ?></span></button>
      <div id='fac_other_load' class='hidden'>
        <?php $user->getFilesFromFac('other'); ?>
      </div>
      </div>

    </fieldset>
  </div>

  <div class="col-md-4 myclass_main_column column">
   
    <legend class='ann_legend'>From Class Members</legend>
    <div id='class_files_div'>  
    <button class='myclass_main_button' id='practical_load_button'>Practicals<span style='float:right;'>
    <?php echo $user->getNoOfDocs('practical'); ?></span></button>
    <div id='practical_load' class='hidden'>
    <?php  
      $user->loadMyDocs('practical');
    ?>
    </div>

    <button class='myclass_main_button' id='assignment_load_button'>Assignments<span style='float:right;'><?php echo $user->getNoOfDocs('assignment'); ?></span></button>

    <div id='assignment_load' class='hidden'>
      <?php $user->loadMyDocs('assignment'); ?>
    </div>

    <button class='myclass_main_button' id='notes_load_button'>
      Notes<span style='float:right;'><?php echo $user->getNoOfDocs('notes'); ?></span>
    </button>

    <div id='notes_load' class='hidden'>
      <?php  
        $user->loadMyDocs('notes');
      ?>
    </div>
    </div>
    
    <div class='upload_section'> 
      <fieldset>
        <legend class='ann_legend'>Upload File</legend>

        <?php if(isset($_SESSION['success'])) { echo "<p style='text-align:center; margin:10px;' class='success_msg'>Operation Successful !!!</p>"; unset($_SESSION['success']); }?>
        <?php if(isset($_SESSION['error'])) { echo "<p style='text-align:center; margin:10px;' class='errors'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); }?>

        <div id='add_form'>
          <form class='myclass_form' id='student_upload_form' style="border:none;" action='myclass_student_upload.php' method='POST' enctype="multipart/form-data">
            <input name='subject' id='form_subject' type='text' placeholder="Subject" title='Enter Subject' required>
            <input name='add_info' id='form_add_info' type="text" placeholder="Additional Info (Optional)">
            File : <input name='file' id='uploadFile' type='file' required>
            <input name='type' type='radio' value='practical' required="required">Practical <input type='radio' name='type' value='assignment'> Assignment <input type='radio' name='type' value='notes'> Notes
            <input name='submit_button' onclick='return doValidate();' type='submit' value='ADD'>
            
            <div id='error_div'></div>
            <div class='progress' style="display: none;" id='preg_bar'>
                <div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                </div>
              </div>
              <div id='targetLayer'></div>
            <p style='text-align: center;' class='time_msg'>Uploading file may take time depending on size of file. So be patient</p>
          </form>
            <div id='loader-icon' class='success_msg text-center' style='display: none;'></div>
        </div>

      </fieldset>
    </div>

  <script type="text/javascript">

    $(document).ready(function(){
      var options = { 
        url : 'myclass.php',
        type : 'POST',
        beforeSubmit : function(){
          $('#preg_bar').show();
          $('.progress-bar').width('0%')
        },
        uploadProgress : function(event, position, total, percentComplete) {
          $('.progress-bar').width(percentComplete+'%')
          $('.progress-bar').html('<div id="progress-status">'+percentComplete+' %</div>')
        },
        success : function(){
          $('#preg_bar').hide();
          $('#loader-icon').show();
          document.location.reload(false);
        }
      }; 
      $('#student_upload_form').ajaxForm(options);
    });
</script>

  </div> <!-- main column div -->

  <div id='classmates_column' class='col-md-3 column myclass_classmates_column'>
    <div class='myclass_right_head'>
      <legend class='ann_legend'>Class Members</legend>
    	<p style='margin:0 5px 5px 0; color:#888; font-size:10px; text-align: right;'>only registered users are shown</p>
    </div>
  	<div id='classmates'></div>
  </div>
</div>

<div id='messanger' style='display: none;' class='col-md-3 class_messanger'></div>

<script>
    $(document).ready(function(){
      $(document).mouseup(function(e) {
        var middlebar = $('#class_files_div');
        var fac_div = $('#fac_div');

        if(!fac_div.is(e.target) && fac_div.has(e.target).length ===0 ) {
          $('#fac_practical_load_button').removeClass('myclass_active');
          $('#fac_practical_load').addClass('hidden');
          $('#fac_assignment_load_button').removeClass('myclass_active');
          $('#fac_assignment_load').addClass('hidden');
          $('#fac_other_load_button').removeClass('myclass_active');
          $('#fac_other_load').addClass('hidden');
          $('#fac_notice_load_button').removeClass('myclass_active');
          $('#fac_notice_load').addClass('hidden');
        }

        if( !middlebar.is(e.target) && middlebar.has(e.target).length === 0){
          $('#practical_load_button').removeClass('myclass_active');
          $('#practical_load').addClass('hidden');
          $('#notes_load_button').removeClass('myclass_active');
          $('#notes_load').addClass('hidden');
          $('#assignment_load_button').removeClass('myclass_active');
          $('#assignment_load').addClass('hidden');
        }

        var container = $("#messanger");
        if (!container.is(e.target)  && container.has(e.target).length === 0  ) {
            container.slideUp('medium');
            clearInterval(functionRef);
        }

      });

      var docHeight = $(window).height();
      var colHeight = docHeight - 120;
      var facColHeight = docHeight - 60;
      $('#faculty_column').css('max-height',facColHeight);     

      $.ajax({
        url: 'lib/ajax/myclass_load_classmates.php',
        type: "POST",
        cache: false,
        success: function(data){
          $('#classmates').html(data);
          $('#classmates').css('max-height',colHeight);
        }
      });
      

      $('#practical_load_button').click(function(){
        $('#assignment_load_button').removeClass('myclass_active');
        $('#assignment_load').addClass('hidden');
        $('#notes_load_button').removeClass('myclass_active');
        $('#notes_load').addClass('hidden');
        $('#practical_load_button').addClass('myclass_active');
        $('#practical_load').removeClass('hidden');
      });

      $('#assignment_load_button').click(function(){
        $('#practical_load_button').removeClass('myclass_active');
        $('#practical_load').addClass('hidden');
        $('#notes_load_button').removeClass('myclass_active');
        $('#notes_load').addClass('hidden');
        $('#assignment_load_button').addClass('myclass_active');
        $('#assignment_load').removeClass('hidden');
      });
      
      $('#notes_load_button').click(function(){
        $('#practical_load_button').removeClass('myclass_active');
        $('#practical_load').addClass('hidden');
        $('#assignment_load_button').removeClass('myclass_active');
        $('#assignment_load').addClass('hidden');
        $('#notes_load_button').addClass('myclass_active');
        $('#notes_load').removeClass('hidden');
      });

      // faculty desk js

      $('#fac_notice_load_button').click(function(){
        $('#fac_practical_load_button').removeClass('myclass_active');
        $('#fac_practical_load').addClass('hidden');
        $('#fac_assignment_load_button').removeClass('myclass_active');
        $('#fac_assignment_load').addClass('hidden');
        $('#fac_other_load_button').removeClass('myclass_active');
        $('#fac_other_load').addClass('hidden');
        $('#fac_notice_load_button').addClass('myclass_active');
        $('#fac_notice_load').removeClass('hidden');
      });


      $('#fac_practical_load_button').click(function(){
        $('#fac_notice_load_button').removeClass('myclass_active');
        $('#fac_notice_load').addClass('hidden');
        $('#fac_assignment_load_button').removeClass('myclass_active');
        $('#fac_assignment_load').addClass('hidden');
        $('#fac_other_load_button').removeClass('myclass_active');
        $('#fac_other_load').addClass('hidden');
        $('#fac_practical_load_button').addClass('myclass_active');
        $('#fac_practical_load').removeClass('hidden');
      });

       $('#fac_assignment_load_button').click(function(){
        $('#fac_notice_load_button').removeClass('myclass_active');
        $('#fac_notice_load').addClass('hidden');
        $('#fac_practical_load_button').removeClass('myclass_active');
        $('#fac_practical_load').addClass('hidden');
        $('#fac_other_load_button').removeClass('myclass_active');
        $('#fac_other_load').addClass('hidden');
        $('#fac_assignment_load_button').addClass('myclass_active');
        $('#fac_assignment_load').removeClass('hidden');
      });

      $('#fac_other_load_button').click(function(){
        $('#fac_notice_load_button').removeClass('myclass_active');
        $('#fac_notice_load').addClass('hidden');
        $('#fac_practical_load_button').removeClass('myclass_active');
        $('#fac_practical_load').addClass('hidden');
        $('#fac_assignment_load_button').removeClass('myclass_active');
        $('#fac_assignment_load').addClass('hidden');
        $('#fac_other_load_button').addClass('myclass_active');
        $('#fac_other_load').removeClass('hidden');
      });
    });
</script>


<!-- if user is faculty -->


<?php 
} 
else if( $user->getType() == 'faculty' ) {
?>

<div class='fluid-container middle_bar' id='middle_bar'>

<?php require('lib/includes/user_info_left_side.php'); ?> <!-- this takes col-md-3 -->

<div class='col-md-8 faculty_class_column column'>

  <div class='faculty_upload_section'> 
  
    <legend style='margin:10px 0px;'>I want to </legend>

    <?php if(isset($_SESSION['success'])) { echo "<p style='text-align:center; margin:10px;' class='success_msg'>Operation Successful !!!</p>"; unset($_SESSION['success']); }?>
    <?php if(isset($_SESSION['error'])) { echo "<p style='text-align:center; margin:10px;' class='errors'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); }?>
    
    <button class='myclass_main_button' id='fac_ann_btn'>Make Announcement</button>
    <div id='fac_ann_form' class='hidden'>
      <form id='faculty_ann_form' class='myclass_form' action='myclass.php' method='POST' enctype="multipart/form-data">
        <fieldset class='dept_box'>
          <legend>Select Departments</legend>
        <span class='myclass_faculty_checkbox'><input type="checkbox" name="dept[]" value='07'>Computer Engineering<span>
        <span class='myclass_faculty_checkbox'><input type="checkbox" name="dept[]" value='16'>Information Technology</span>
        <span class='myclass_faculty_checkbox'><input type="checkbox" name="dept[]" value='11'>EC Engineering</span>
        <span class='myclass_faculty_checkbox'><input type="checkbox" name="dept[]" value='17'>IC Engineering</span>
        <span class='myclass_faculty_checkbox'><input type="checkbox" name="dept[]" value='03'>BM Engineering</span>
        <span class='myclass_faculty_checkbox'><input type="checkbox" name="dept[]" value='21'>Metallurgical Engineering</span>
        </fieldset>
        <input name='heading' type='text' placeholder="Heading" title='Enter Heading' value='<?php if(isset($_SESSION['heading'])) echo $_SESSION['heading']; unset($_SESSION['heading']); ?>' required>
        <textarea name='announcement' placeholder='Type Announcement Here' value='<?php if(isset($_SESSION['ann'])) echo $_SESSION['ann']; unset($_SESSION['ann']); ?>' required></textarea>
        File (optional) : <input name='file' type='file'>
        <input name='add_info' type="text" placeholder="Additional Info (Optional)"  value='<?php if(isset($_SESSION['add_info'])) echo $_SESSION['add_info']; unset($_SESSION['add_info']); ?>' >
        <input name='announcement_submit' type='submit' value='Make Announcement'>
        <div class='progress' style="display: none;" id='preg_bar'>
          <div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                  </div>
        </div>
        <p class='text-center time_msg'>Uploading file may take time depending on size of file. So be patient</p>
      </form>
    </div>


     








    <button class='myclass_main_button' id='fac_ass_btn'>Send Assignment/Practical</button>
    <div id='fac_ass_form' class='hidden'>
      <form id='faculty_ass_form' class='myclass_form' action='myclass.php' method='POST' enctype="multipart/form-data">
        <input name='subject' type='text' placeholder="Subject" value='<?php if(isset($_SESSION['subject'])) { echo $_SESSION['subject']; unset($_SESSION['subject']); } ?>'  required>
        <input name='add_info' type="text" placeholder="Additional Info (Optional)" value='<?php if(isset($_SESSION['ass_add_info'])) { echo $_SESSION['ass_add_info']; unset($_SESSION['ass_add_info']); } ?>' >
        <select name='type' for='faculty_ass_form' required>
          <option value='0'>Select Type</option>
          <option value='assignment'>Assignment</option>
          <option value='practical'>Practical</option>
          <option value='other'>Other</option>
        </select>
        <select name='batch' for='faculty_ass_form' required>
          <option value='0'>Select Batch</option>
          <option value='2014'>2014-Batch</option>
          <option value='2015'>2015-Batch</option>
          <option value='2016'>2016-Batch</option>
          <option value='2017'>2017-Batch</option>
        </select>
        <select name='dept' for='faculty_ass_form' required>
          <option value='0'>Select Department</option>
          <option value='07'>Computer Engineering</option>
          <option value='16'>Information Technology</option>
          <option value='11'>EC Engineering</option>
          <option value='17'>IC Engineering</option>
          <option value='03'>BM Engineering</option>
          <option value='21'>Metallurgical Engineering</option>
        </select>
        <label>Upload File <input name='file' type='file' required></label>
        <input name='fac_ass_sub_btn' type='submit' value='Send File'>
        <div class='progress' style="display: none;" id='preg_bar'>
          <div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                  </div>
        </div>
        <p class='text-center time_msg'>Uploading file may take time depending on size of file. So be patient</p>
      </form>
    </div>

    <button class='myclass_main_button' id='fac_notify_btn'>Notify Students</button>
    <div id='fac_notify_form' class='hidden'>
      <form id='faculty_notify_form' class='myclass_form' action='myclass.php' method='POST' enctype="multipart/form-data">
        <input type="text" name="subject" placeholder="Enter Subject" value='<?php if(isset($_SESSION['notify_subject'])) { echo $_SESSION['notify_subject']; unset($_SESSION['notify_subject']); } ?>' required>
        <input type="text" name="message" placeholder="Enter Message to Send" value='<?php if(isset($_SESSION['notify_message'])) { echo $_SESSION['notify_message']; unset($_SESSION['notify_message']); } ?>' required>
        <select name='batch' for='faculty_notify_form' required>
          <option value='0'>Select Batch</option>
          <option value='2014'>2014-Batch</option>
          <option value='2015'>2015-Batch</option>
          <option value='2016'>2016-Batch</option>
          <option value='2017'>2017-Batch</option>
        </select>
        <select name='dept' for='faculty_notify_form' required>
          <option value='0'>Select Department</option>
          <option value='07'>Computer Engineering</option>
          <option value='16'>Information Technology</option>
          <option value='11'>EC Engineering</option>
          <option value='17'>IC Engineering</option>
          <option value='03'>BM Engineering</option>
          <option value='21'>Metallurgical Engineering</option>
        </select>
        File (optional) : <input type='file' name='file'>
        <input type="text" name="add_info" placeholder="Additional Information (Optional)" value='<?php if(isset($_SESSION['notify_addinfo'])) { echo $_SESSION['notify_addinfo']; unset($_SESSION['notify_addinfo']); } ?>'>
        <input type="submit" name="fac_notify_submit" value='Notify Students'>
        <div class='progress' style="display: none;" id='preg_bar'>
          <div class='progress-bar' role='progressbar' area-valuenow='60' area-valuemin='0' area-valuemax='100'>
                  </div>
        </div>
        <p class='text-center time_msg'>Uploading file may take time depending on size of file. So be patient</p>
      </form>

    </div>

    <button class='myclass_main_button' id='fac_list_class'>List a class</button>
    <form action='lib/handlers/form_handlers/fac_list_class.php' class='myclass_form hidden' id='list_class_form'>
     <select name='dept' required>
        <option value='0'>Select Department</option>
        <option value='07'>Computer Engineering</option>
        <option value='16'>Information Technology</option>
        <option value='11'>EC Engineering</option>
        <option value='17'>IC Engineering</option>
        <option value='03'>BM Engineering</option>
        <option value='21'>Metallurgical Engineering</option>
      </select>
      <select name='batch' required>
        <option value='0'>Select Batch</option>
        <option value='2014'>2014-Batch</option>
        <option value='2015'>2015-Batch</option>
        <option value='2016'>2016-Batch</option>
        <option value='2017'>2017-Batch</option>
      </select>
      <input type="submit" name="fac_class_submit" value="Search">
    </form>

    <div id='fac_listed_class'></div>

  <script type="text/javascript">
    $(document).ready(function(){
      var options = { 
          url : 'lib/handlers/form_handlers/fac_list_class.php',
          type : 'POST',
          beforeSubmit : function(){
          },
          success : function(data){
            $('#fac_listed_class').html(data);
          }
      }; 
      $('#list_class_form').ajaxForm(options);
    });
  </script>
        

     <script>
      $(document).ready(function(){
        var options = { 
          url : 'myclass.php',
          type : 'POST',
          beforeSubmit : function(){
            $('.progress').show();
            $('.progress-bar').width('0%')
           },
           uploadProgress : function(event, position, total, percentComplete) {
            $('.progress-bar').width(percentComplete+'%')
            $('.progress-bar').html('<div id="progress-status">'+percentComplete+' %</div>')
           },
           success : function(){
            $('.progress').hide();
            document.location.reload(false);
           }
        }; 
        $('#faculty_ann_form').ajaxForm(options);
        $('#faculty_ass_form').ajaxForm(options);
        $('#faculty_notify_form').ajaxForm(options);
      });
      </script>

  </div> <!-- upload section ending div -->

  <legend style='margin:10px 0px;'>Your activities</legend>

  <button class='myclass_main_button' id='fac_anns_made_btn'>Your Announcements</button>
  <div id='fac_anns_made_div' class='hidden'>
    <?php $user->facLoadMyAnnouncements(); ?>
  </div>

  <button class='myclass_main_button' id='fac_ass_posted_btn'>Your Assignments/Practicals</button>
  <div id='fac_ass_posted_div' class='hidden'>
    <?php $user->facLoadMyAssignments(); ?>
  </div>

  <button class='myclass_main_button' id='fac_notices_posted_btn'>Your Notices</button>
  <div id='fac_notices_posted_div' class='hidden'>
    <?php $user->facLoadMyNotices(); ?>
  </div>



</div> <!--  middle column section -->

</div> <!-- middle_bar ending div -->

<script>
    $(document).ready(function(){
      $(document).mouseup(function(e) {
          var middlebar = $('#middle_bar');

          if( !middlebar.is(e.target) && middlebar.has(e.target).length === 0){
            $('#fac_ass_btn').removeClass('myclass_active');
            $('#fac_ass_form').addClass('hidden');
            $('#fac_notify_btn').removeClass('myclass_active');
            $('#fac_notify_form').addClass('hidden');
            $('#fac_ann_btn').removeClass('myclass_active');
            $('#fac_ann_form').addClass('hidden');
            $('#fac_list_class').removeClass('myclass_active');
            $('#list_class_form').addClass('hidden');
            $('#fac_anns_made_btn').removeClass('myclass_active');
            $('#fac_anns_made_div').addClass('hidden');
            $('#fac_ass_posted_btn').removeClass('myclass_active');
            $('#fac_ass_posted_div').addClass('hidden');
            $('#fac_notices_posted_btn').removeClass('myclass_active');
            $('#fac_notices_posted_div').addClass('hidden');
             
          }
      });

      $('#fac_ann_btn').click(function(){
        $('#fac_ass_btn').removeClass('myclass_active');
        $('#fac_ass_form').addClass('hidden');
        $('#fac_notify_btn').removeClass('myclass_active');
        $('#fac_notify_form').addClass('hidden');
        $('#fac_ann_btn').addClass('myclass_active');
        $('#fac_ann_form').removeClass('hidden');
        $('#fac_list_class').removeClass('myclass_active');
        $('#list_class_form').addClass('hidden');
      });

      $('#fac_ass_btn').click(function(){
        $('#fac_ann_btn').removeClass('myclass_active');
        $('#fac_ann_form').addClass('hidden');
        $('#fac_notify_btn').removeClass('myclass_active');
        $('#fac_notify_form').addClass('hidden');
        $('#fac_ass_btn').addClass('myclass_active');
        $('#fac_ass_form').removeClass('hidden');
        $('#fac_list_class').removeClass('myclass_active');
        $('#list_class_form').addClass('hidden');
      });

      $('#fac_notify_btn').click(function(){
        $('#fac_ann_btn').removeClass('myclass_active');
        $('#fac_ann_form').addClass('hidden');
        $('#fac_ass_btn').removeClass('myclass_active');
        $('#fac_ass_form').addClass('hidden');
        $('#fac_notify_btn').addClass('myclass_active');
        $('#fac_notify_form').removeClass('hidden');
        $('#fac_list_class').removeClass('myclass_active');
        $('#list_class_form').addClass('hidden');
      });

      $('#fac_list_class').click(function(){
        $('#fac_ann_btn').removeClass('myclass_active');
        $('#fac_ann_form').addClass('hidden');
        $('#fac_ass_btn').removeClass('myclass_active');
        $('#fac_ass_form').addClass('hidden');
        $('#fac_notify_btn').removeClass('myclass_active');
        $('#fac_notify_form').addClass('hidden');
        $('#fac_list_class').addClass('myclass_active');
        $('#list_class_form').removeClass('hidden');
      })

      $('#fac_anns_made_btn').click(function(){
        $('#fac_anns_made_btn').addClass('myclass_active');
        $('#fac_anns_made_div').removeClass('hidden');
        $('#fac_ass_posted_btn').removeClass('myclass_active');
        $('#fac_ass_posted_div').addClass('hidden');
        $('#fac_notices_posted_btn').removeClass('myclass_active');
        $('#fac_notices_posted_div').addClass('hidden');
      });
      $('#fac_ass_posted_btn').click(function(){
        $('#fac_anns_made_btn').removeClass('myclass_active');
        $('#fac_anns_made_div').addClass('hidden');
        $('#fac_ass_posted_btn').addClass('myclass_active');
        $('#fac_ass_posted_div').removeClass('hidden');
        $('#fac_notices_posted_btn').removeClass('myclass_active');
        $('#fac_notices_posted_div').addClass('hidden');
      });
      $('#fac_notices_posted_btn').click(function(){
        $('#fac_anns_made_btn').removeClass('myclass_active');
        $('#fac_anns_made_div').addClass('hidden');
        $('#fac_ass_posted_btn').removeClass('myclass_active');
        $('#fac_ass_posted_div').addClass('hidden');
        $('#fac_notices_posted_btn').addClass('myclass_active');
        $('#fac_notices_posted_div').removeClass('hidden');
      });

    });
</script>

<?php } ?> <!-- faculty user php code ends here -->

<script>
  $(document).ready(function(){
   

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