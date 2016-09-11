<?php
$mysqli = new mysqli('localhost','root','letmein','module5');

//Try to connect
if($mysqli->connect_errno) {
  printf("Connection Failed: %s\n", $mysqli->connect_error);
  exit;
}
?>