<?php
session_start();

require 'database.php';



//token check
if ($_SESSION['token'] != $_POST['token']){
	die("Request forgery detected");
}

$_SESSION['POST'] = $_POST;

//logging in code
if (!isset($_SESSION['username'])){
	header('location: home.php');
	exit;
}


$oldPassword = $_POST['oldPW'];
$newPassword = $_POST['newPW1'];
$newPasswordVerify = $_POST['newPW2'];
$username = $_SESSION['username'];

$stmt = $mysqli->prepare("SELECT password FROM user WHERE username=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
// Bind the parameter
$stmt->bind_param('s', $username);
$stmt->execute();

// Bind the results
$stmt->bind_result($pwd_hash);
$stmt->fetch();
$stmt->close();
// Compare the submitted password to the actual password hash
if(strcmp($newPassword,$newPasswordVerify) != 0){
	$_POST['error'] = "New passwords must match.";
	header('location: manageAccount.php');
}
if(crypt($oldPassword, $pwd_hash)==$pwd_hash){
// Login succeeded!
	$cryptPass = crypt($newPassword);
	$updatePass = $mysqli->prepare("UPDATE user SET password = ? WHERE username=?");
	$updatePass->bind_param('ss',$cryptPass,$username);
	$updatePass->execute();
	$updatePass->close();
	$_POST['error'] = "Password Changed";
	$_POST['manageAccount'] = $_SESSION['token'];
	header('location: manageAccount.php');
}else{
	//throw error here.
   $_POST['error'] = "Old credentials did not match.";
	 header('location: manageAccount.php');
   exit;
 }

header('location: manageAccount.php');
?>
