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


$commentID = $_POST['commentID'];
$stmt = $mysqli->prepare("delete from comments where commentID = ?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}

$stmt->bind_param('i',$commentID);

$stmt->execute();

$stmt->close();

header('location: viewpage.php?postID='.$_POST['postID']);


?>
