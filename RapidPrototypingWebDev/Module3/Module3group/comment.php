<?php
session_start();

require 'database.php';

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

$postID = $_POST['postID'];
$body = $_POST['body'];


//insert comment
$stmt = $mysqli->prepare("insert into comments (creatorID, postID, body) values (?, ?, ?)");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}

$stmt->bind_param('sis', $username, $postID, $body);

$stmt->execute();

$stmt->close();
header("Location: viewpage.php?postID=".$postID);

?>
