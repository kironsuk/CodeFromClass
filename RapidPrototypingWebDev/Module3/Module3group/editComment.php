<?php
session_start();

//token check 
if ($_SESSION['token'] != $_POST['token']){
	die("Request forgery detected");
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Login or Register</title>
	<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>
	<?php 
	require 'database.php';

//Check that we are logged in
	if (isset($_SESSION['username'])){
		$username = $_SESSION['username'];
	}
	else{
		exit;
	}

	$commentID = $_POST['commentID'];

//select the comment
	$stmt = $mysqli->prepare("select body, creatorID from comments where commentID=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s', $commentID);
	$stmt->execute();
// Bind the results
	$stmt->bind_result($body,$username);
	$stmt->fetch();

//check that we are the right user for this comment
	if (strcmp($_SESSION['username'],$username)==0) {
		?>
		<form action="updateComment.php" method="post">
			<p class="sectionhead"> Update Comment: </p> <textarea name="body" id="body" cols="45" rows="5" ><?php echo htmlspecialchars($body);?></textarea><br>
			<input type="hidden" name="commentID" value=<?php echo $commentID; ?>>
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
			<input type="hidden" name="postID" value=<?php echo $_POST['postID']; ?>><br>
			<input type="submit" value="Update">
		</form>


		<?php
	}else{
		echo "wrong user";
	}?>

	<form action="viewpage.php" method="get">
		<button name="postID" type="submit" value=<?php echo $_POST['postID']; ?>>Go Back</button>
	</form>

</body>