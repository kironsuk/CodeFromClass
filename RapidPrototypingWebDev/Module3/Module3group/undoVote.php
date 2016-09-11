<?php

require 'database.php';
session_start();
if (isset($_SESSION['username'])){
  $username = $_SESSION['username'];
}else{
	exit;
}

$postID = $_SESSION['postID'];

$getVoteVal = $mysqli->prepare("select sum(value) from score where postID=? AND userID=?");
if(!$getVoteVal){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$getVoteVal->bind_param('is', $postID, $username);

$getVoteVal->execute();

$getVoteVal->bind_result($val);

$getVoteVal->close();

$undoPostVote = $mysqli->prepare("update post set score = score - ? where postID=?");
if(!$undoPostVote){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$undoPostVote->bind_param('ii',$val,$postID);
$undoPostVote->execute();
$undoPostVote->close();



$stmt = $mysqli->prepare("delete from score where postID=? AND userID=? ");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ss', $postID, $username);

$stmt->execute();

$stmt->close();

header('location: viewpage.php?postID='.$postID);
