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


$title = $_POST['title'];
$body = $_POST['body'];
if (isset($_POST['link'])) {
	$link = $_POST['link'];
}
else $link = NULL;


//insert the data from the post
$stmt = $mysqli->prepare("insert into post (creatorID, title, body, link) values (?, ?, ?, ?)");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ssss', $username, $title, $body, $link);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("select max(postID) from post where creatorID = ?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($maxPost);
$stmt->fetch();
$stmt->close();
header('location: viewpage.php?postID='.$maxPost);
?>
