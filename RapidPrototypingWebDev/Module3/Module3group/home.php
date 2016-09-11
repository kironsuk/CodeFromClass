<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
  <title>Welcome to Red Dot</title>

</head>



<?php
require 'database.php';




 //Greet user
echo "<h1>Welcome to Red Dot!</h1>";

if (isset($_SESSION['username'])){
 printf("<h3>Hello, %s </h3> ", htmlspecialchars($_SESSION['username']));
}

//Load posts
$stmt = $mysqli->prepare("select postID, creatorID, title, score, link from post order by score desc, posttime desc");
if(!$stmt){
 printf("Query Prep Failed: %s\n", $mysqli->error);
 exit;
}
$stmt->execute();
$stmt->bind_result($postID,$creator, $title, $score, $link);

while($stmt->fetch()){

  if (empty($link)){
  	printf("<div class=\"post\"><p>%s - %s by %s</p>\n",
  		htmlspecialchars($score),
  		htmlspecialchars($title),
      htmlspecialchars($creator));
  }
  else {
    printf("<div class=\"post\"><p>%s - <a  href=%s>%s</a> by %s</p>\n",
      htmlspecialchars($score),
      htmlspecialchars($link),
      htmlspecialchars($title),
      htmlspecialchars($creator)
      );
  }

  //If logged in, allow user to edit and delete their posts
  if (isset($_SESSION['username'])&& strcmp($_SESSION['username'],$creator)==0){
    ?>
    <form action="deletePost.php" method="post" style='display:inline;'>
      <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
      <button name="postID" type="submit" value=<?php echo $postID; ?>>Delete</button>
    </form>
    <form action="editPost.php" method="post" style='display:inline;'>

      <button name="postID" type="submit" value=<?php echo $postID; ?>>Edit</button>
      <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
    </form>
    <?php
  }

  // button to view the Full Post with Comments
  ?>

  <form action="viewpage.php" method="get">
   <button name="postID" type="submit" value=<?php echo $postID; ?>>Full Post with Comments</button>
 </form>
</div>

 <br>
 <?php
}

$stmt->close();
?>
<?php

if (isset($_SESSION['username'])){
  ?>
  <div id="menu">
  <form action="createPost.php" method="post">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
   <input type="submit" name = "send" value="Post Something!">
 </form>

 <form action="manageAccount.php" method="post">
  <button name="manageAccount" type="submit" value="<?php echo $_SESSION['token'];?>" >User Profile</button>
</form>
<form action="logout.php" >
 <input type="submit" value="Logout">
</form>
</div>
<?php
}else{
  ?>
  <div id="menu">
  <form action="createorloginuser.php" >
   <input type="submit"  value="Login">
 </form>
</div>
 <?php
}

?>

</body>
</html>
