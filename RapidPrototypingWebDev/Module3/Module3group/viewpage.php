<?php
session_start()
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>View Post</title>

</head>


<body>

	<?php

	require 'database.php';

	$postID = $_GET['postID'];
	$_SESSION['postID'] = $postID;

	if (isset($_SESSION['username'])){
		$username = $_SESSION['username'];
		$vote_stmt = $mysqli->prepare("select value, count(*) from score where postID=".$postID." AND userID = '".$username."'");
		if(!$vote_stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$vote_stmt->execute();
		$vote_stmt->bind_result($score, $votes);
		$vote_stmt->fetch();

		echo "<div class=\"viewpost\">";
		if ($votes == 1 && $score == 1){
			?>
			<span id="arrow">
				<a href = "undoVote.php" class="undo-arrow-up"></a>
				<a href = "downvote.php" class="arrow-down"></a>
			</span>
			<?php
		}

		else if ($votes == 1){
			?>
			<span id="arrow">
				<a href = "upvote.php" class="arrow-up"></a>
				<a href = "undoVote.php" class="undo-arrow-down"></a>
			</span>
			<?php
		}
		else {
			?>
			<span id="arrow">
				<a href = "upvote.php" class="arrow-up"></a>
				<a href = "downvote.php" class="arrow-down"></a>
			</span>
			<?php
		}

		$vote_stmt->close();
	}
	?>

	<?php

	$stmt = $mysqli->prepare("select creatorID, title, score, body, link from post where postID=". $postID);

	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->bind_result($creator, $title, $score, $body, $link);
	$stmt->fetch();

	if (empty($link)){
		printf(" %s - %s by %s</div> <br> <div class=postbody> %s <br></div> \n",
			htmlspecialchars($score),
			htmlspecialchars($title),
			htmlspecialchars($creator),
			htmlspecialchars($body)
			);
	}
	else {
		printf(" %s - <a href=%s>%s</a> by %s</div> <br> <div class=postbody> %s <br></div> \n",
			htmlspecialchars($score),
			htmlspecialchars($link),
			htmlspecialchars($title),
			htmlspecialchars($creator),
			htmlspecialchars($body)
			);
	}

	$stmt->close();


	$stmt2 = $mysqli->prepare("select commentID, creatorID, body from comments where postID=".$postID." order by commenttime desc");
	$stmt2->execute();
	$stmt2->bind_result($commentID,$creator, $body);

	if (isset($_SESSION['username'])){
		?>
		<form action="comment.php" method="post">
			<p class="sectionhead"> Comment here: </p> <textarea name="body" id="body" cols="45" rows="5"></textarea><br>
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
			<input type="hidden" name="postID" value=<?php echo $postID; ?>><br>
			<input type="submit" name = "send" value="Comment">
		</form>
		<?php
	}

	echo "<h3> Comment section</h3>";

	while($stmt2->fetch()){
		printf("<div class= \"comments\"> <p>\"%s\" - %s </p>\n",
			htmlspecialchars($body),
			htmlspecialchars($creator)
			);

		if (isset($_SESSION['username'])&& strcmp($_SESSION['username'],$creator)==0){
			?>
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

			
			<?php
		}
		echo "</div>";
	}
	?>
	<div id="menu">

		<form action="home.php" method ="post">
			<input type="submit" name="send" value="Return to Homepage">
		</form>
		<?php
		if (isset($_SESSION['username'])){
			?>
			<form action="deletePost.php" method="post" >
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
				<button name="postID" type="submit" value=<?php echo $postID; ?>>Delete Post</button>
			</form>
			<form action="editPost.php" method="post" >

				<button name="postID" type="submit" value=<?php echo $postID; ?>>Edit Post</button>
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
			</form>
			<form action="logout.php" >
				<input type="submit" value="Logout">
			</form>
			<?php
		}else{
			?>
			<form action="createorloginuser.php" >
				<input type="submit"  value="Login">
			</form>
			<?php
		}

		?>


	</div>

</body>
</html>
