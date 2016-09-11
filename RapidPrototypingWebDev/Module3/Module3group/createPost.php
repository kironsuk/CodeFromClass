<?php

session_start();

require 'database.php';

if ($_SESSION['token'] != $_POST['token']){
	die("Request forgery detected");
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Submit Post</title>
	<link rel="stylesheet" type="text/css" href="style.css">

</head>

<body>

	<h1>Make a Post!</h1>

	<!--Let user log in or create new username.-->
	<form action="post.php" method="post">
		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
		<p class="sectionhead"> Title: </p> <input type="text" name="title" required> <br>
		<p class="sectionhead"> Link: </p> <input type="text" name="link"> <br>
		<p class="sectionhead"> Body: </p> <textarea name="body" id="body" cols="45" rows="5"></textarea><br>
		<input type="submit" name = "send" value="Post">
	</form>

	<div id="menu">
		<form action="home.php" method ="post">
			<input type="submit" name="send" value="Return to Homepage">
		</form>
	</div>
	<?php
 //Echo any error that redirects to the homepage. (Logout is passed as an error)
	if (isset($ERROR)){echo "<p>".$ERROR."</p>";}

	?>
</body>
</html>
