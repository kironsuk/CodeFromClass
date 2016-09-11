<!DOCTYPE html>
<html>
<head>
	<title>Login or Register</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<form action="validate.php" method="post">
  <p class="sectionhead">Username:</p>
  <input type="text" name="username" required> <br>

  <p class="sectionhead">Password:</p>
  <input type= "password" name="password" required  >
  <br>
  <br>
  <button name="log" type="submit" value='l'>Login</button>
  <button name="create" type="submit" value='c'>Create User</button>
</form>


</body>