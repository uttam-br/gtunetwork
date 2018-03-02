<?php  

if(isset($_POST['submit_button'])) {
	
	$error = false;
	$_SESSION['errors[]'] = array();


	if(!isset($_POST['subject']) || !isset($_POST['type']) || !isset($_FILES)  ) {
		$_SESSION['error'] = 'One or more required fields are not filled';
		header("Location: myclass.php");
		exit();
	}

	$subject = htmlentities($_POST['subject']);
	$subject = mysqli_real_escape_string($conn,$subject);
	$add_info = htmlentities($_POST['add_info']);
	$add_info = mysqli_real_escape_string($conn,$add_info);
	$type = $_POST['type'];
	$type = htmlentities($type);
	$type = mysqli_real_escape_string($conn,$type);
	$datetime = date('Y-m-d H:i:s');
	$added_by = $user_id ;

	$check_subject = preg_replace('/\s+/', "", $subject);

	if($check_subject == "") {
		$_SESSION['error'] = 'Subject is empty';
		exit();
	}
	
	$target_dir = "res/class_student_files/";
	$target_file = $target_dir . uniqid();
	$uploadOk = 1;
	$fileType = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
	$check = filesize($_FILES['file']['tmp_name']);
	
	if($_FILES['file']['size'] >  22428800 ){
		$_SESSION['error'] = 'File too large, max upload size 20MB';
		exit();
	}

	if($fileType != 'jpg' && $fileType != 'png' && $fileType != 'jpeg' && $fileType != 'doc' && $fileType != 'docx'  && $fileType != 'pdf'){
		$_SESSION['error'] = 'File type invalid';
		exit();
	}

	if($uploadOk == 0){
		$_SESSION['error'] = 'Invalid File';
		exit();
	} 
	else {
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
	    }
		else{
			$_SESSION['error'] = 'Error in uploading file';
		exit();
		}
	}

	$filepath = htmlentities($target_file);

	$dept = $user->getDeptCode();
	$college_id = $user->getCollegeCode();
	$course_id = $user->getCourseId();
	$year_of_adm = $user->getYearOfAdm();

	if($course_id == '01') {
		$batch = $user->getYearOfAdm();
	}

	else if($course_id == '31'){
		$batch = ($user->getYearOfAdm() - 1);
	}


	if($error == false && $uploadOk == 1) {
		$insert_query = mysqli_query($conn,"INSERT INTO myclass VALUES ('','$subject','$add_info','$type','$batch','$dept','$college_id','$filepath','$datetime','$added_by','0','0') ");

		$notification = new Notification($conn, $user_id);

		if($course_id == '01') {
			$select_students = mysqli_query($conn,"SELECT * FROM users WHERE course_id='01' AND branch_id='$dept' AND year_of_adm='$year_of_adm' AND college_id='$college_id' AND type='student'");
			$notified_users = array();
			while($row = mysqli_fetch_assoc($select_students)) {
				if($row['user_id'] == $user_id) {
					continue; 
				}
				$notification->insertNotification( $type , $row['user_id'], 'student_send_file');
			}
			//notifying d2d students
			$yoa = $year_of_adm + 1 ;
			$select_students = mysqli_query($conn,"SELECT * FROM users WHERE course_id='31' AND branch_id='$dept' AND year_of_adm='$yoa' AND college_id='$college_id' AND type='student'");
			$notified_users = array();
			while($row = mysqli_fetch_assoc($select_students)) {
				if($row['user_id'] == $user_id) continue;
				$notification->insertNotification( $type , $row['user_id'], 'student_send_file');
			}
		} else if($course_id == '31') {
			$select_students = mysqli_query($conn,"SELECT * FROM users WHERE course_id='31' AND branch_id='$dept' AND year_of_adm='$year_of_adm' AND college_id='$college_id' AND type='student'");
			$notified_users = array();
			while($row = mysqli_fetch_assoc($select_students)) {
				if($row['user_id'] == $user_id) {
					continue; 
				}
				$notification->insertNotification( $type , $row['user_id'], 'student_send_file');
			}
			//notifying regular students
			$yoa = $year_of_adm - 1 ;
			$select_students = mysqli_query($conn,"SELECT * FROM users WHERE course_id='01' branch_id='$dept' AND year_of_adm='$yoa' AND college_id='$college_id' AND type='student'");
			$notified_users = array();
			while($row = mysqli_fetch_assoc($select_students)) {
				if($row['user_id'] == $user_id) continue;
				$notification->insertNotification( $type , $row['user_id'], 'student_send_file');
			}
		}

		$_SESSION['success'] = true ; 
		exit();
	}
}

?>