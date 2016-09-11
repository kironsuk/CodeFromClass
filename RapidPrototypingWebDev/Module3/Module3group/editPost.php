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

	$postID = $_POST['postID'];
	
//pick up details from the post
	$stmt = $mysqli->prepare("select creatorID, title, link, body from post where postID=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s', $postID);
	$stmt->execute();
// Bind the results
	$stmt->bind_result($username, $title, $link, $body);
	$stmt->fetch();

//check that we are the right user for this comment
	if (strcmp($_SESSION['username'],$username)==0) {
		?>
		<form action="updatePost.php" method="post">
			<p class="sectionhead"> Title: </p> <input type="text" name="title" value=<?php echo htmlspecialchars($title); ?> required> <br>
			<p class="sectionhead"> Link: </p> <input type="text" name="link" value=<?php echo htmlspecialchars($link); ?> > <br>
			<p class="sectionhead"> Body: </p> <textarea name="body" id="body" cols="45" rows="5"><?php echo htmlspecialchars($body); ?></textarea><br>
			<input type = "hidden" value = <?php echo $postID; ?> name ="postID"> 
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
			<input type="submit" name = "send" value="Edit Post">
		</form>
		<?php
	}else{
		echo "wrong user";
	}?>

	<form action="home.php" >
		<button >Go Back</button>
	</form>


</body>