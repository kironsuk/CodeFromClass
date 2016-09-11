<?php

require 'database.php';
session_start();
if (isset($_SESSION['username'])){
  $username = $_SESSION['username'];
}else{
	exit;
}

$postID = $_SESSION['postID'];
$value = 1;

//Get any existing vote from session user on current post
$getVote=$mysqli->prepare("select sum(value) from score where postID=? AND userID=?");
if(!$getVote){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$getVote->bind_param('is',$postID,$username);
$getVote->execute();
$getVote->bind_result($vote);
$getVote->fetch();
$getVote->close();

//If user has voted, remove their vote from the total score column in post table
if(isset($vote)){
  $removeVote=$mysqli->prepare("update post set score = score-? where postID=?");
  if(!$removeVote){
  	printf("Query Prep Failed: %s\n", $mysqli->error);
  	exit;
  }
  $removeVote->bind_param('ii',$vote,$postID);
  $removeVote->execute();
  $removeVote->close();
}

//Remove user vote from score table
$stmt = $mysqli->prepare("delete from score where postID=? AND userID=? ");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ss', $postID, $username);
$stmt->execute();
$stmt->close();


//Add new user vote to score table
$stmt = $mysqli->prepare("insert into score (postID, userID, value) values (?, ?, ?)");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ssi', $postID, $username, $value);
$stmt->execute();
$stmt->close();

//Update post score
$stmt2 = $mysqli->prepare("update post set score = score + 1 where postID = ".$postID);
$stmt2->execute();
$stmt2->close();
header('location: viewpage.php?postID='.$postID);
