<?php

ob_start(); 
session_start();
$conn = mysqli_connect("localhost","root","","gtunetwork");
if(mysqli_connect_errno())
	die(mysqli_connect_errno());
$timezone = date_default_timezone_set("Asia/Kolkata");

define("ROOT_LOCATION",'/gtunetwork');

?>