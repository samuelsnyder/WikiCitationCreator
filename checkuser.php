<?php
//checkuser.php 
//asynchronously checks if user exists
include("db.php");
session_start();

$username = htmlspecialchars($_POST["username"]); 
$query = "select username from citationusers where username = '" . $username . "'";
$rs = @mysql_query($query);
$result = @mysql_result($rs, '0');

if( $result == $username && $username)
{
	echo  "Username already in use";
}

?>