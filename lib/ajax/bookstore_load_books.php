<?php  
require("../../config.php");
require("../classes/User.php");
require("../classes/Book.php");

$year = $_REQUEST['year'];
$year = $year;

$books = new Book($conn, $_SESSION['user_id']);
$books->getBooks($_REQUEST['year']);

?>