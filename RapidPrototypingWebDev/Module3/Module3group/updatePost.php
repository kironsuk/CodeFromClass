<?php
require 'database.php';
session_start();

//token check 
if ($_SESSION['token'] != $_POST['token']){
	die("Request forgery detected");
}

//Check that we are logged in
if (isset($_SESSION['username'])){
	$username = $_SESSION['username'];
}
else{
	header("Location: home.php");
	exit;
}


$postID = $_POST['postID'];
$title = $_POST['title'];
$link = $_POST['link'];
$body = $_POST['body'];


//update post sql statment
$stmt = $mysqli->prepare("update post set title=?, link=?, body = ? where postID=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ssss', $title, $link, $body, $postID);
$stmt->execute();
$stmt->close();


header('location: viewpage.php?postID='.$postID);



?>