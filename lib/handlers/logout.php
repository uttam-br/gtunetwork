<?php 
session_start();
session_destroy();
setcookie("ID",$token,time()-3600,'/',NULL,NULL,TRUE);
header("Location: ../../index.php");
exit();
?>	