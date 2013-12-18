<?php
//loginhandler.php
//this file handles login and 
//new account creation
//and redirects to index.php upon
//successful account creation or login.
{
/* create new user if new account requested
	or log in user
*/
include 'db.php';

	if(array_key_exists("username", $_REQUEST)
		&& array_key_exists("password", $_REQUEST))

	{	
	//Check for log in errors	
	//validate username and password in case javascript not available
		if( strlen($_REQUEST['username']) < 5 || strlen($_REQUEST['password']) < 5) 
		{
			die ( '<a href="login.php">Username and password must be at least 5 characters</a>');
		}

	//create new account
		if(array_key_exists("newUser", $_REQUEST))
		{
			$username = htmlspecialchars($_REQUEST["username"]);
			$password = htmlspecialchars($_REQUEST["password"]);
			$example = htmlspecialchars("Example (n.d.). In Wikipedia. Retrieved 12/25/13, from http://en.wikipedia.org/wiki/Example");
			$query = "select username from citationusers where username = '" . $username . "'";
			$rs = mysql_query($query);
			if( @mysql_result($rs, 0, 'username') != $username )
			{
				$query = "insert into citationusers (username, password , count, citation1) values ('$username', '$password', 1, '" . $example ."')";
				$rs = mysql_query($query);
				if (!$rs) {die ('<a href="login.php">new user creation failed</a>');}
			}
			else
			{
				die ( '<a href="login.php">Account creation failed, user name exists</a>');

			}
		}

	//Log in
		$username = htmlspecialchars($_REQUEST["username"]);
		$password = htmlspecialchars($_REQUEST["password"]);
		$query = "select password from citationusers where username = '" . $username . "'";
		$rs = mysql_query($query);

		if ( !$rs) //login fails on username
		{
			die( '<a href="login.php">Login failed</a>');
		}

	//login succeeds
		else if( @htmlspecialchars(mysql_result($rs, 0, "password")) == $password) 
		{
			session_start();
			$_SESSION['uid'] = 1;
			$_SESSION['username'] = $username;
		echo "<script>";
		echo "window.location.replace('index.php');";
		echo "</script>";	
		echo '<a href="index.php">Click here if not redirected failed</a>';
	}

	//incorrect password
	else
	{
		echo '<a href="login.php">Login failed</a>';
	}

}

else
{
		include 'index.php';
}
}
?>