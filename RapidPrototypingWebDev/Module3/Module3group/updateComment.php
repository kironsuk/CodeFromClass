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
	exit;
}


$commentID = $_POST['commentID'];
$postID = $_POST['postID'];
$body = $_POST['body'];


//update sql statement
$stmt = $mysqli->prepare("update comments set body = ? where commentID=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ss', $body, $commentID);
$stmt->execute();
$stmt->close();

header('location: viewpage.php?postID='.$postID);



?>