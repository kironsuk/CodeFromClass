<?php
$mysqli = new mysqli('localhost','root','letmein','module3');

//Try to connect
if($mysqli->connect_errno) {
  printf("Connection Failed: %s\n", $mysqli->connect_error);
  exit;
}

//Try to select from test talbe
$stmt = $mysqli->prepare("select username, email from user order by username desc");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$stmt->bind_result($username, $email);

echo "<ul>\n";
while($stmt->fetch()){
	printf("\t<li>%s %s</li>\n",
		htmlspecialchars($username),
		htmlspecialchars($email)
	);
}
echo "</ul>\n";

$stmt->close();
 ?>
