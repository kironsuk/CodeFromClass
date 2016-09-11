<?php
ini_set("session.cookie_httponly", 1);
require 'database.php';

session_start();

if ($_SESSION['token'] != $_POST['token'] OR $_SESSION['username'] != $_POST['username']){
	//die("Request forgery detected");
	exit();
}

$username = $mysqli->real_escape_string($_POST['username']);
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
	$type = 's';
}



//$datetime = $data[1]+"-"+($data[2]+1)+"-"+$data[3]+" "+$data[4]+":"+$data[5]+":00";
$x = date_create_from_format('Y/n/d H:i:s',$datetime);
//date_modify($x,'+1 month');
if (!$x){
	echo "Wrong datetime Format";
	exit;
}
$datetime = date_format($x,"Y-m-d H:i:s");



$stmt = $mysqli->prepare("INSERT INTO events(username, event_date, location, description,calendar_type) VALUES (?,?,?,?,?)");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_param('sssss',$username,$datetime,$location, $description, $type);
$stmt->execute();

?>
