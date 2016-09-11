<?php
session_start();
require 'database.php';

//logging in code
if (isset($_POST['login'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	$stmt = $mysqli->prepare("SELECT COUNT(*), username, password FROM users WHERE username=?");
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
	$stmt->close();

// Compare the submitted password to the actual password hash
	if( $cnt == 1 && crypt($password, $pwd_hash)==$pwd_hash){
	// Login succeeded!
		$token= substr(md5(rand()),0,10);
		$_SESSION['token'] = $token;
		$_SESSION['username']=$username;
		$returnObj = array('username'=>$username, 'token'=>$token, 'success'=>'yes');
		echo (json_encode($returnObj));
	}else{
		//throw error here.
	   //echo "login failed";
	   exit;
	}





//create user code
}elseif (isset($_POST['create'])) {

	$username = $_POST['username'];
	$password = $_POST['password'];


	//make sure the user doesn't exists already
	$stmt = $mysqli->prepare("select COUNT(*)  from users where username=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->bind_result($count);
	$stmt->close();
	if ($count != 0){
		//echo "user exists";
		//throw error here.
		exit;
	}

	else{
		//put into table
		$stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ss', $username, crypt($password));
		$stmt->execute();
		$stmt->close();
		//successfully added user
		$token= substr(md5(rand()),0,10);
		$_SESSION['token'] = $token;
		$_SESSION['username']=$username;
		$returnObj = array('username'=>$username, 'token'=>$token, 'success'=>'yes');
		echo (json_encode($returnObj));
	}




}
else{
	
	exit;

}
?>
