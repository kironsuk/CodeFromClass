<?php
ini_set("session.cookie_httponly", 1);
require 'database.php';

session_start();
if ($_SESSION['token'] != $_POST['token'] OR $_SESSION['username'] != $_POST['username']){
	die("Request forgery detected");
}

$username = "test";
$data = $_POST['data'];

$date = $data[1]+"-"+($data[2]+1)+"-"+$data[3]+" "+$data[4]+":"+$data[5]+":00";

$stmt = $mysqli->prepare("INSERT INTO events VALUES(event_date=?, location=?, description=?, username=?");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_param('ssss',$date,$data[6],$data[0],$username);
$stmt->execute();
$stmt->close();


?>