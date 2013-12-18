<?php
ini_set('display_errors', 'On');
/*Database info */
$dbhost = 'localhost';
$dbname = 'mydatabase';
$dbuser = 'dbuser';
$dbpass = 'password';


$mysql_handle = mysql_connect($dbhost, $dbuser, $dbpass)
or die("Error connecting to database server");

mysql_select_db($dbname, $mysql_handle)
or die("Error selecting database: $dbname");


//echo 'Successfully connected to database!';
/**********************************************
UNCOMMENT AND RUN ONCE
**********************************************/
/*
$query = 'create table citationusers(id integer not null auto_increment, username varchar(64), password varchar(64), count integer, citation1 varchar(256), 
	citation2 varchar(256), citation3 varchar(256), primary key(id))';

mysql_query($query)
or die('unable to create table with query: ' . $query);
mysql_close($mysql_handle);
echo 'Successfully created table!';
*/
?>
