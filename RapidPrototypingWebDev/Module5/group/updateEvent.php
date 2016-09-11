<?php
ini_set("session.cookie_httponly", 1);
require 'database.php';

session_start();

if ($_SESSION['token'] != $_POST['token'] OR $_SESSION['username'] != $_POST['username']){
	//die("Request forgery detected");
	exit();
}

$username = $mysqli->real_escape_string($_POST['username']);
$eventID = $mysqli->real_escape_string($_POST['eventID']);
$datetime = $mysqli->real_escape_string($_POST['datetime']);
if(isset($_POST['description'])){
  $description = $mysqli->real_escape_string($_POST['description']);
} else {
  $description = "";
}
if(isset($_POST['location'])){
  $location = $mysqli->real_escape_string($_POST['location']);
} else {
  $location = "";
}
if(isset($_POST['type'])){
	$type = $_POST['type'];
}else{
	echo "got here";
	$type = 's';
}



//echo($myDateTime);


$x = date_create_from_format('Y/n/d H:i:s',$datetime);
//date_modify($x,'+1 month');
if (!$x){
	echo "Wrong datetime Format";
	exit;
}
$datetime = date_format($x,"Y-m-d H:i:s");



$stmt = $mysqli->prepare("UPDATE events SET event_date=?, location=?, description=?, calendar_type=? where eventID = ?");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_param('sssss',$datetime,$location,$description, $type, $eventID);
$stmt->execute();
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_param('sssss',$datetime,$location,$description, $type, $eventID);
$stmt->execute();
$stmt->close();

if(isset($_POST['clone_user'])){
	$clone_user = $mysqli->real_escape_string($_POST['clone_user']);
	echo($clone_user);
	$stmt = $mysqli->prepare("INSERT INTO events(username, event_date, location, description,calendar_type) VALUES (?,?,?,?,?)");
	if(!$stmt){
	 printf("Query Prep Failed: %s\n", $mysqli->error);
	 exit;
	}
	$stmt->bind_param('sssss',$clone_user,$datetime,$location, $description, $type);
	$stmt->execute();
	$stmt->close();
}
?>
