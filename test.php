<?php  
require('config.php');
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

<script>
function getContent(enroll){
		$.get('http://gtu-info.com/Student-2013/'+ enroll +'/a',function(data){
			$('.result').html(data);
			var grade = $('#cphPageContainer_lblCPI').html();
			$.post('insert.php',{ enr: enroll, cpi: grade },function(){
			});
		});
		$('.result').html(' ');
}
function start(){
	var year_of_adm = '14';
	var college_id = "012";
	var course_id = "01";
	var dept_id = "01";
	var roll_no = "000";
	let timerId = setInterval( function(){
		var enroll = year_of_adm + college_id + course_id + dept_id + roll_no;
		roll_no++;
		if(roll_no == 150)
		    clearInterval(timerId);
		getContent(enroll);
	} , 1000);
}
</script>

<button onclick='start()'>Click to Start</button>
<p class='result'>Results appears here...</p>