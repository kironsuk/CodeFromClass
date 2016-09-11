<?php
session_start();

require 'database.php';

//logging in code
if (isset($_POST['log'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	$stmt = $mysqli->prepare("SELECT COUNT(*), username, password FROM user WHERE username=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
// Bind the parameter
	$stmt->bind_param('s', $username);
	$stmt->execute();

// Bind the results
	$stmt->bind_result($cnt, $user_id, $pwd_hash);
	$stmt->fetch();

// Compare the submitted password to the actual password hash
	if( $cnt == 1 && crypt($password, $pwd_hash)==$pwd_hash){
	// Login succeeded!
		$_SESSION['username'] = $user_id;
		$_SESSION['token'] = substr(md5(rand()),0,10);
		header('location: home.php');
	}else{
		//throw error here.
	   echo "login failed";
	   exit;
	}





//create user code
}elseif (isset($_POST['create'])) {

	$username = $_POST['username'];
	$password = $_POST['password'];
	if (isset($_POST['email'])){
		//didn't end up using this field but functionality is there
		$email = $_POST['email'];
	}
	else{
		$email = null;
	}


	//make sure the user doesn't exists already
	$stmt = $mysqli->prepare("select COUNT(*)  from user where username=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->bind_result($count);
	$stmt->close();
	if ($count != 0){
		echo "user exists";
		//throw error here.
		exit;
	}

	else{
		//put into table
		$stmt = $mysqli->prepare("insert into user (username, password) values (?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ss', $username, crypt($password));
		$stmt->execute();
		$stmt->close();
		//successfully added user
		$_SESSION['username'] = $username;
	}




}
else{
	header('location: createorloginuser.php');
	exit;

}
header('location: home.php');
?>
