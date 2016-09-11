<?php
require 'database.php';




$date = '3-4-2016';
$username = 'test';

//Load posts
$stmt = $mysqli->prepare("select event_date, location, description from events where event_date=? AND username = ? order by eventID desc");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->bind_params($date,$test);
$stmt->execute();
$stmt->bind_result($date,$loc,$des);

while($stmt->fetch()){


}

?>