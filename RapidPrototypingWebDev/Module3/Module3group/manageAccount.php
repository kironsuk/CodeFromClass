<?php

session_start();

require 'database.php';

//token check
$ref_url = $_SERVER['HTTP_REFERER'];
$refData = parse_url($ref_url);
if (!strpos($refData['path'],'home.php')) {
	if ($_SESSION['token'] != $_SESSION['POST']['token']){
		die("Request forgery detected");
	}
}
else if ($_SESSION['token'] != $_POST['manageAccount']){
	die("Request forgery detected");
}
?>



<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>User Profile</title>


</head>

<body>
	<?php
	$ref_url = $_SERVER['HTTP_REFERER'];
	$refData = parse_url($ref_url);
	if (!strpos($refData['path'],'changePassword.php')) {
		$_SESSION['error']="";
	}

	printf("<h2>Profile: %s </h2>\n", htmlspecialchars($_SESSION['username']));

	if(isset($_SESSION['error'])){
		printf('%s',$_SESSION['error']);
	}
	if(isset($_SESSION['username'])){
		$username = $_SESSION['username'];
	}
	else {
		header('location: manageAccount.php');
	}

	printf("<h2>Posts: </h2>");

	$stmt = $mysqli->prepare("select postID, title, score, link from post WHERE creatorID = ?  order by score desc");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s',$username);
	$stmt->execute();
	$stmt->bind_result($postID, $title, $score, $link);


	while($stmt->fetch()){
		if (empty($link)){
			printf("<div class=\"comments\"><p>%s - %s </p>",
				htmlspecialchars($score),
				htmlspecialchars($title));
		}
		else {
			printf("<div class=\"comments\"> <p>%s - <a href=%s>%s</a> </p>",
				htmlspecialchars($score),
				htmlspecialchars($link),
				htmlspecialchars($title)
				);
		}

		?>
		<br>
		<form action="deletePost.php" method="post" style="display:inline;">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
			<button name="postID" type="submit" value=<?php echo $postID; ?>>Delete</button>
		</form>
		<form action="editPost.php" method="post" style="display:inline;">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
			<button name="postID" type="submit" value=<?php echo $postID; ?>>Edit</button>
		</form>
		<form action="viewpage.php" method="get" style="display:inline;">
			<button name="postID" type="submit" value=<?php echo $postID; ?>>Full Post with Comments</button>
		</form>
	</div>
	<?php
}
$stmt->close();
printf("<h2>Comments: </h2>");

//deprecated code with some additional funcationality
/*
//$stmt = $mysqli->prepare("select c.postID, c.body, c.commentID, p.title, p.creatorID from comments as c inner join post as p on c.postID = p.postID where c.creatorID =? order by commenttime desc");
$stmt = $mysqli->prepare("select c.postID, c.body, c.commentID, p.title, p.link from comments as c inner join post as p on c.postID = p.postID where creatorID =? order by commenttime desc");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($postID,$body,$commentID,$title,$link);

while($stmt->fetch()){
	if (empty($link)){
		printf("\t<div class=\"comments\"><p>\"%s\" (%s by %s) </p>",
			htmlspecialchars($body),
			htmlspecialchars($title),
			htmlspecialchars($username)
			);
	}
	else {
		printf("\t<div class=\"comments\"><p>\"%s\"  ( posted on <a href=%s>%s</a> by %s)</p>",
			htmlspecialchars($body),
			htmlspecialchars($link),
			htmlspecialchars($title),
			htmlspecialchars($username)
			);
	}
	*/
//$stmt = $mysqli->prepare("select c.postID, c.body, c.commentID, p.title, p.creatorID from comments as c inner join post as p on c.postID = p.postID where c.creatorID =? order by commenttime desc");


$stmt = $mysqli->prepare("select postID, body, commentID from comments where creatorID =? order by commenttime desc");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($postID,$body,$commentID);

while($stmt->fetch()){
		printf("\t<div class=\"comments\"><p>%s </p>",
			htmlspecialchars($body)
			);
	?>
	<br>
	<form action="deleteComment.php" method="post" style="display:inline;" >
		<input type="hidden" name = "postID" value = <?php echo $postID;?> >
		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
		<button name="commentID" type="submit" value=<?php echo $commentID; ?>>Delete</button>
	</form>
	<form action="editComment.php" method="post" style = "display:inline;">
		<input type="hidden" name = "postID" value = <?php echo $postID;?> >
		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
		<button name="commentID" type="submit" value=<?php echo $commentID; ?>>Edit</button>
	</form>

	<form action="viewpage.php" method="get" style="display:inline;">
		<button name="postID" type="submit" value=<?php echo $postID; ?>>Full Post with Comments</button>
	</form>
	<br>
</div>
<?php
}
$stmt->close();

?>



<div id="menu">
<form action="home.php" method ="post">
	<input type="submit" name="send" value="Return to Homepage">
</form>
  <form action="createPost.php" method="post">
  	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
   <input type="submit" name = "send" value="Post Something!">
 </form>
 <form action="changePassword.php" method="post">
 	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
	<button name="changepw" type="submit" value=<?php echo $_SESSION['username']; ?>>Change Password</button>
</form>
 <form action="deleteUser.php" method="post">
 	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
	<button name="username" type="submit" value=<?php echo $_SESSION['username']; ?>>Delete Account</button>
</form>

</div>

</body>
</html>
