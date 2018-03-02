<?php 

if(isset($_POST['announcement_submit'])) {
	$error = false;
	if(!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		$target_file = "";
	}
	else {
		$target_dir = "res/class_faculty_files/";
		$target_file = $target_dir . uniqid() ;
		$uploadOk = 1;
		$fileType = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
		$check = filesize($_FILES['file']['tmp_name']);
		if($check === false){
			$_SESSION['error'] = 'file invalid';
			$uploadOk = 0;
			return;
		}
		if($_FILES['file']['size'] >  52428800 ){
			$_SESSION['error']='file_too_large';
			$uploadOk = 0;
			return;
		}
		if($fileType != 'jpg' && $fileType != 'png' && $fileType != 'jpeg' && $fileType != 'doc' && $fileType != 'docx'  && $fileType != 'pdf'){
			$_SESSION['error'] = 'file_invalid';
			$uploadOk = 0;
			return;
		}
		if($uploadOk == 0){
			$error = true;
		} 
		else {
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
		    }
			else{
				$_SESSION['error'] = 'file_upload_error';
				$error = true;
				return;
			}
		}
	}
	$file_path = $target_file;
	$heading = $_POST['heading'];
	$heading = htmlentities($heading);
	$heading = mysqli_real_escape_string($conn,$heading);
	$_SESSION['heading'] = $heading;
	$ann = $_POST['announcement'];
	$ann = htmlentities($ann);
	$ann = mysqli_real_escape_string($conn,$ann);
	$_SESSION['ann'] = $ann;
	$add_info = isset($_POST['add_info']) ? $_POST['add_info'] : "" ;
	$add_info = htmlentities($add_info);
	$add_info = mysqli_real_escape_string($conn,$add_info);
	$_SESSION['add_info'] = $add_info;
	$date = date('Y-m-d H:i:s');
	$college_id = $user->getCollegeCode();
	$added_by = $user->getUserid();
	if(isset($_POST['dept']) ) {
		$dept = implode(',',$_POST['dept']);
		$dept = ','.$dept.',';	
	}
	$check_heading = preg_replace('/\s+/','',$heading);
	$check_ann = preg_replace('/\s+/','',$ann);
	if(!isset($_POST['dept']) ) {
		$_SESSION['error'] = "Please select department";
		$error = true;
	} else if($check_heading == "") {
		$_SESSION['error'] = 'Please Enter Heading';
		$error = true;
	} else if($check_ann == "") {
		$_SESSION['error'] = 'Please Enter Announcement';
		$error = true;
	}
	$_SESSION['assign_success'] = "<script>
		 $(document).ready(function(){
			$('#fac_ann_btn').addClass('myclass_active');
        	$('#fac_ann_form').removeClass('hidden');
		});
		</script>";
	if($error == false) {
		$insert_query = mysqli_query($conn,"INSERT INTO anns VALUES ('','$added_by','$heading','$ann','$dept','$date','$add_info','$file_path','$college_id' ) ");
		$_SESSION['heading'] = $_SESSION['add_info'] = $_SESSION['ann'] = "";
		$_SESSION['success'] = true;
		$dept_array = $_POST['dept'];
		foreach($dept_array as $branch) {
			$select_students = mysqli_query($conn,"SELECT * FROM users WHERE branch_id='$branch' AND college_id='$college_id' AND type='student'");
			$notified_users = array();
			$notification = new Notification($conn, $user_id);
			while($row = mysqli_fetch_assoc($select_students)) {
				$notification->insertNotification( '', $row['user_id'], 'fac_ann_send');
			}
		}
		exit();
	}
}

if(isset($_POST['fac_ass_sub_btn'])) {
	$error = false;
	if (!($_POST['type'] == 'assignment' || $_POST['type'] == 'practical' || $_POST['type'] == 'other'))  
	{
		$_SESSION['error'] = 'Please Select Type';
		$error = true;
	} else if (!($_POST['batch'] == '2014' || $_POST['batch'] == '2015' ||$_POST['batch'] == '2016' ||$_POST['batch'] == '2017' )) {
		$_SESSION['error'] = 'Please Select Batch';
		$error = true;
 	}

 	$dept = $_POST['dept'];

 	$check_dept_query = mysqli_query($conn,"SELECT * FROM department WHERE code='$dept' ");

 	if(mysqli_num_rows($check_dept_query) == 0) {
 		$_SESSION['error'] = 'Please Select Department';
 		$error = true;
 	}
 	
	$subject = htmlentities($_POST['subject']);
	$subject = mysqli_real_escape_string($conn, $subject);
	$_SESSION['subject'] = $subject; 
	$add_info = htmlentities($_POST['add_info']) ;
	$add_info = mysqli_real_escape_string($conn, $add_info);
	$_SESSION['ass_add_info'] = $add_info;
	$type = htmlentities($_POST['type']) ;
	$type = mysqli_real_escape_string($conn,$type);
	$batch = htmlentities($_POST['batch']) ;
	$batch = mysqli_real_escape_string($conn,$batch);
	$dept = htmlentities($_POST['dept']);
	$dept = mysqli_real_escape_string($conn,$dept);
	$target_dir = "res/class_faculty_files/";
	$target_file = $target_dir . uniqid();
	$uploadOk = 1;
	$fileType = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
	$check = filesize($_FILES['file']['tmp_name']);
	if($check === false){
		$_SESSION['error'] = 'file invalid';
		$uploadOk = 0;
		$error = true;
	}
	if($_FILES['file']['size'] >  52428800 ){
		$_SESSION['error'] = 'file too large';
		$uploadOk = 0;
		$error = true;
	}
	if($fileType != 'jpg' && $fileType != 'png' && $fileType != 'jpeg' && $fileType != 'doc' && $fileType != 'docx'  && $fileType != 'pdf'){
		$_SESSION['error'] = 'file type invalid';
		$uploadOk = 0;
		$error = true;
	}
	if($uploadOk == 0){
		$_SESSION['error'] = 'file upload error';
	} 
	else {
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
	    }
		else{
			$_SESSION['error'] = 'file upload error';
		}
	}
	$file_path = $target_file ; 
	$date = date("Y-m-d H:i:s");
	$added_by = $user->getUserid();
	$college_id = $user->getCollegeCode();
	$check_subject = preg_replace("/\s+/","",$subject);
	if($check_subject == "") {
		$_SESSION['error'] = 'Please Enter Subject';
		$error = true;
	}
	$_SESSION['assign_success'] = "<script>
		 $(document).ready(function(){
			$('#fac_ass_btn').addClass('myclass_active');
        	$('#fac_ass_form').removeClass('hidden');
		});
		</script>";
	if($error == false) {
		$insert_query = mysqli_query($conn,"INSERT INTO fac_assigns VALUES('','$added_by','$subject','$add_info','$type','$batch','$dept','$file_path','$date','$college_id') ");
		$_SESSION['subject'] = $_SESSION['ass_add_info'] = "";
		$_SESSION['success'] = true;
		if($type == 'other')
			$type = 'something';
		// notifying regular students
		$select_students = mysqli_query($conn,"SELECT * FROM users WHERE branch_id='$dept' AND year_of_adm='$batch' AND college_id='$college_id' AND type='student'");
		$notified_users = array();
		$notification = new Notification($conn, $user_id);
		while($row = mysqli_fetch_assoc($select_students)) {
			$notification->insertNotification( $type , $row['user_id'], 'fac_ass_send');
		}
		//notifying d2d students
		$yoa = $batch + 1 ;
		$select_students = mysqli_query($conn,"SELECT * FROM users WHERE branch_id='$dept' AND year_of_adm='$yoa' AND college_id='$college_id' AND type='student'");
		$notified_users = array();
		$notification = new Notification($conn, $user_id);
		while($row = mysqli_fetch_assoc($select_students)) {
			$notification->insertNotification( $type , $row['user_id'], 'fac_ass_send');
		}
	}
	exit();
}

if(isset($_POST['fac_notify_submit'])) {
	$error = false;
	$subject = htmlentities($_POST['subject']);
	$subject = mysqli_real_escape_string($conn,$subject);
	$_SESSION['notify_subject'] = $subject;
	$message = htmlentities($_POST['message']);
	$message = mysqli_real_escape_string($conn,$message);
	$_SESSION['notify_message'] = $message;
	$batch = htmlentities($_POST['batch']);
	$dept = htmlentities($_POST['dept']);
	$add_info = htmlentities($_POST['add_info']);
	$add_info = mysqli_real_escape_string($conn,$add_info);
	$_SESSION['notify_addinfo'] = $add_info;
	if (!($_POST['batch'] == '2014' || $_POST['batch'] == '2015' ||$_POST['batch'] == '2016' ||$_POST['batch'] == '2017' )) {
		$_SESSION['error'] = 'Please Select Batch';
		$error = true;
 	} else if (!( $_POST['dept'] == '07' || $_POST['dept'] == '16' || $_POST['dept'] == '17' || $_POST['dept'] == '11' || $_POST['dept'] == '03' || $_POST['dept'] == '21')) {
 		$_SESSION['error'] = 'Please Select Department';
 		$error = true;
 	}
 	if(!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		$target_file = "";
	} else {
	 	$target_dir = "res/class_faculty_files/";
		$target_file = $target_dir . uniqid();
		$uploadOk = 1;
		$fileType = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
		$check = filesize($_FILES['file']['tmp_name']);
		if($check === false){
			$_SESSION['error'] = 'file invalid';
			$uploadOk = 0;
			$error = true;
		}
		if($_FILES['file']['size'] >  52428800 ){
			$_SESSION['error'] = 'file too large';
			$uploadOk = 0;
			$error = true;
		}
		if($fileType != 'jpg' && $fileType != 'png' && $fileType != 'jpeg' && $fileType != 'doc' && $fileType != 'docx'  && $fileType != 'pdf'){
			$_SESSION['error'] = 'file type invalid';
			$uploadOk = 0;
			$error = true;
		}
		if($uploadOk == 0){
			$_SESSION['error'] = 'file upload error';
		} 
		else {
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
		    }
			else{
				$_SESSION['error'] = 'file upload error';
			}
		}
	}
	$file_path = $target_file ; 
	$check_subject = preg_replace("/\s+/","",$subject);
	$check_message = preg_replace("/\s+/","",$message);
	if($check_subject == ""){
		$_SESSION['error'] = 'Please enter Subject';
		$error = true;
	} else if ($check_message == "") {
		$_SESSION['error'] = 'Please enter Message';
		$error = true;
	}
	$datetime = date("Y-m-d H:i:s");
	$added_by = $user->getUserid();
	$college_id = $user->getCollegeCode();
	$_SESSION['assign_success'] = "<script>
		 $(document).ready(function(){
			$('#fac_notify_btn').addClass('myclass_active');
        	$('#fac_notify_form').removeClass('hidden');
		});
		</script>";
 	if($error == false) {
 		$insert_query = mysqli_query($conn,"INSERT INTO notices VALUES ('','$subject','$message','$batch','$dept','$file_path','$add_info','$datetime','$added_by','$college_id')  ");
 		$_SESSION['notify_subject'] = $_SESSION['notify_message'] = $_SESSION['notify_addinfo'] = "";
 		$_SESSION['success'] = true;
 		$select_students = mysqli_query($conn,"SELECT * FROM users WHERE branch_id='$dept' AND year_of_adm='$batch' AND college_id='$college_id' AND type='student'");
		$notified_users = array();
		$notification = new Notification($conn, $user_id);
		while($row = mysqli_fetch_assoc($select_students)) {
			$notification->insertNotification( '' , $row['user_id'], 'fac_notice_send');
		}
		//notifying d2d students
		$yoa = $batch + 1 ;
		$select_students = mysqli_query($conn,"SELECT * FROM users WHERE branch_id='$dept' AND year_of_adm='$yoa' AND college_id='$college_id' AND type='student'");
		$notified_users = array();
		$notification = new Notification($conn, $user_id);
		while($row = mysqli_fetch_assoc($select_students)) {
			$notification->insertNotification( '' , $row['user_id'], 'fac_notice_send');
		}
 		exit();
 	}
 	exit();
}

?>