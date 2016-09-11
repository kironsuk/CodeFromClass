<?php


$mysqli = new mysqli('localhost','root','letmein','module3');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}

if (isset($_SESSION['username'])){
  $username = $_SESSION['username'];
}
else {
  $username = 'jdilorenzo';
}
$title = $_POST['title'];
$body = $_POST['body'];
if (isset($_POST['link'])) {
  $link = $_POST['link'];
}
else $link = NULL;

echo $title;
?>
