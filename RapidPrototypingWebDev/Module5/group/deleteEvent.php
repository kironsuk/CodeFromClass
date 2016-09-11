<?php
ini_set("session.cookie_httponly", 1);
require 'database.php';

session_start();
if ($_SESSION['token'] != $_POST['token'] OR $_SESSION['username'] != $_POST['username']){
	die("Request forgery detected");
}

$username = "test";
$eventID = $_POST['eventID'];


$stmt = $mysqli->prepare("DELETE  from events where eventID = ?");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_param('s',$eventID);
$stmt->execute();
$stmt->close();



?>