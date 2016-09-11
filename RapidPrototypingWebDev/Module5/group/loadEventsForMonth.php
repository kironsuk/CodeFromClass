<?php
ini_set("session.cookie_httponly", 1);
require 'database.php';


session_start();

if ($_SESSION['token'] != $_POST['token'] OR $_SESSION['username'] != $_POST['username']){
	exit;
}

if( ! isset($_POST['year']) OR ! isset( $_POST['month']) OR !isset($_POST['username']) ){
	exit;
}

$year = $_POST['year'];
$month = $_POST['month'];
$username = $_POST['username'];
$cal_type = $_POST['type'];

//Load posts
$stmt = $mysqli->prepare("SELECT eventID, YEAR(event_date), MONTH(event_date),DAY(event_date), HOUR(event_date), MINUTE(event_date), location, description from events where MONTH(event_date)=? AND YEAR(event_date)=? AND username = ? AND calendar_type LIKE ?");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_param('ssss',$month,$year,$username, $cal_type);
$stmt->execute();
$stmt->bind_result($id,$year,$month,$day,$hour,$minute,$loc,$des);
$rows = array();
$i = 0;
while($stmt->fetch()){
  $row = array("eventID"=>$id, "year"=>$year, "month"=>($month-1),"day"=>$day, "hour"=>str_pad($hour, 2, "0", STR_PAD_LEFT), "minute"=> str_pad($minute, 2, "0", STR_PAD_LEFT),"description"=>htmlentities($des),"location"=>htmlentities($loc));
  array_push($rows,$row);
}
$stmt->close();
echo(json_encode($rows));

?>
