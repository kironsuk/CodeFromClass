<?php
session_start();
require 'database.php';

//log out
$username = $_POST['username'];
if(strcmp($username,$_SESSION['username'])==0){

  session_destroy();
}else{
	exit;
}

//change creator of all comments made by user we are deleting to the "deleted" user
$stmt = $mysqli->prepare("update comments set creatorID = 'deleted' where creatorID = ?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->close();


//change author of all posts made by the user we are deleting to the "deleted" user. 
$stmt2 = $mysqli->prepare("update post set creatorID = 'deleted' where creatorID = ?");
if(!$stmt2){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}

$stmt2->bind_param('s',$username);
$stmt2->execute();
$stmt2->close();
header("Location: logout.php");

?>
