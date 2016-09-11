<?php
session_start();

require 'database.php';

//Check that we are logged in
if (isset($_SESSION['username'])){
  $username = $_SESSION['username'];
}
else{
	exit;
}


//delete comments first
$postID = $_POST['postID'];
$stmt = $mysqli->prepare("delete from comments where postID = ?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('i',$postID);
$stmt->execute();
$stmt->close();

//now the post itself
$stmt2 = $mysqli->prepare("delete from post where postID = ?");
if(!$stmt2){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt2->bind_param('i',$postID);
$stmt2->execute();
$stmt2->close();
header("Location: home.php");

?>
