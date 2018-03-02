<?php  
require('config.php');

$enroll = $_POST['enr'];
$grade = $_POST['cpi'];
$year_of_adm = substr($enroll,0,2);
$college_code = substr($enroll,2,3);
$course_code = substr($enroll,5,2);
$branch_code = substr($enroll,7,2);
$roll_no = substr($enroll,9,3);
$per = (($grade-0.5) * 10);

if($enroll != '' && $grade != '')
	$insert_query = mysqli_query($conn,"INSERT INTO results VALUE('','$enroll','$course_code','$branch_code','$college_code','$year_of_adm','','','','','','','','','$grade','$per') ");

?>
