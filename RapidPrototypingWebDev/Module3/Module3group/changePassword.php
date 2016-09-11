<?php
require 'database.php';
session_start();

?>

<!DOCTYPE html>
<html>
<head>
	<title>Change Password</title>
	<link rel="stylesheet" type="text/css" href="style.css">

</head>

<body>
<form action="validatChange.php" method="post">
  <p class="sectionhead">Old Password:</p>
  <input type="password" name="oldPW" required> <br>

  <p class="sectionhead">New Password:</p>
  <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
  <input type= "password" name="newPW1" required  >  <br>

	<p class="sectionhead">Verify Password:</p>
	<input type= "password" name="newPW2" required > <br> <br>
  <button name="change" type="submit" value='ch'>Change Password</button>
</form>

<div id="menu">
<form action="manageAccount.php" method ="post">
	<input type="submit" name="send" value="Return to Profile">
</form>


<form action="home.php" method ="post">
	<input type="submit" name="send" value="Return to Homepage">
</form>
</div>

</body>
