<?php header('Access-Control-Allow-Origin: *'); ?>
<?php 

/*********************************************
index.php
This file checks to see if a user is logged in.
If not, the user is redirected to login.php by a 
javascript redirect.

If yes, the saved citations and new citation form are
displayed.
**********************************************/
ini_set('display_errors', 'On');
include 'db.php';
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<style  type="text/css">
	#top {width:100%; background:#f9f9f9; margin:1.2em 0 6px 0; border:1px solid #ddd;}
	#title {width:61%; color:#000;font-family: sans-serif; font-size: 30px;}
	#logout {text-align: right;}
	#content {width: 100%; margin:4px 0 0 0; background:none; border-spacing: 0px;}
	#savedcitations {width:55%; border:1px solid #cef2e0; background:#f5fffa; vertical-align:top; color:#000;}
	#savedcitationsheading {margin:3px; background:#cef2e0; font-size:120%; font-weight:bold; border:1px solid #a3bfb1; text-align:left; color:#000; padding:0.2em 0.4em;}	
	#citationcreator{border:1px solid #cedff2; background:#f5faff; vertical-align:top;}
	#newcitationheading{margin:3px; background:#cedff2; font-size:120%; font-weight:bold; border:1px solid #a3b0bf; text-align:left; color:#000; padding:0.2em 0.4em;}
	</style>
	<?php
	if (! isset($_SESSION['uid']))
	{
	session_destroy(); //not logged in, destroy session
	//redirect to login page
	echo "<script type='text/javascript'>";
	echo "window.location.replace('login.php');";
	echo "</script>";
}

/*If user is logged in, show content */
if (isset($_SESSION['uid']))
{

	$username = htmlspecialchars($_SESSION["username"]);
	echo "<div id=top>";
	echo "<form id=logout action=logout.php>" . $username . "<button name=username value=''>Log out</button></form>";
	echo "<div id=title>Wikipedia Citation Creator</div>";
	echo "</div>";
	//add citations to database
	if (isset($_REQUEST['count'])) 
	{
		$count = $_REQUEST['count'];

		//is there new citation text? If yes, update count
		if (@$_REQUEST['citation' . ($count)] != '')
		{
			$query = "update citationusers set count=" . $count . " where username='" . $username . "'";
			mysql_query($query);
		}

		for ($i = 1; $i <= $count; $i++)
		{	
			if (isset($_REQUEST['citation' . $i]) )
			{
				$text = htmlspecialchars($_REQUEST['citation' . $i]);
				$query = "update citationusers set citation" .$i . "='" . $text . "' where username='" . $username . "'";
				$rs = mysql_query($query);

				if (!$rs) //updated failed because column citation$i does not exist, create colun
				{
					$query = "alter table citationusers add citation" .$i . " varchar(256)";
					mysql_query($query);
					//add data
					$query = "update citationusers set citation" .$i . "='" . $text . "' where username='" . $username . "'";
					mysql_query($query);
				}
			}
		}
	}
	?>
	<title>citation List</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script src="jquery.js"></script>
	<script src="jquery.validate.js"></script>
	<script>
	//validate
	$(document).ready(function(){
	$( "#citationform" ).validate({
		rules: {
			date: {
				required: true,
				date: true
			}
		}
	});
});
	$(document).ready(function(){	
		$("#save").hide();
		$("#createcitation").click(function(){
			$("textarea#newcitation").val("Loading");		
			var data, title;
				title = $("#newpage").val(); 	//title of wiki page
				data="title=" + title;		//string for POST
				$.ajax({
					type:"POST",
					data:data,
					url:"savexml.php",
					success:getData,
					error:errorFunction,
				});

			});
	});

	function getData(data, info){
		$.ajax({
			url:"pageinfo.xml",
			success:printData,
			failure:errorFunction
		});
	}

	function printData(data,info){
		var title, url, txt;
		var pageData = $(data).find("page");
		if ($(pageData).attr('title'))
		{

			if ( typeof( $(pageData).attr('missing') ) != 'undefined') 
			{
				$("textarea#newcitation").val("Wiki page does not exist");
			}
			else if ( typeof($(pageData).attr('invalid')) != 'undefined')
			{
				$("textarea#newcitation").val("Invalid page title");
			}
			else 
			{
				title = $(pageData).attr('title');
				url = $(pageData).attr('fullurl');
				$("#newpage").val(title);
				if($("#apa").is(':checked') )
				{

					//Example in APA style
					//Plagiarism. (n.d.). In Wikipedia. Retrieved August 10, 2004, from http://en.wikipedia.org/wiki/Plagiarism
					txt = title + " (n.d.). In Wikipedia. Retrieved " + $("#date").val() +  ", from " + url;
					$("textarea#newcitation").val(txt);
					$("#save").show();
				}

				else if($("#mla").is(':checked') )
				{

					//Example in MLA style
					//"Plagiarism." Wikipedia, The Free Encyclopedia. Wikimedia Foundation, Inc. 22 July 2004. Web. 10 Aug.
					txt = "\"" + title + ".\" Wikipedia, The Free Encyclopedia. Wikimedia Foundation, Inc. " + $("#date").val() +  ". Web.";
					$("textarea#newcitation").val(txt);
					$("#save").show();
				}


			}
		}
		else
		{
			errorFunction(data, "Unable to retrieve information"); //problem with XML file
		}
	}

	function errorFunction(data,info)
	{
		$("textarea#newcitation").text("error occurred:" + info);
	}
	</script>
</head>
<body>
	<?php

//Add citations to page
	$query = "select count from citationusers where username = '" . $username . "'";
	$rs = mysql_query($query);
	$count = mysql_result($rs, 0, "count");
	$query = "select citation1";

	for ($i = 2; $i <= $count ; $i++)
	{
		$query = $query . ", citation".$i;	//select citation1, citation2, ... 
	}


	//extra citations from database
	$query = $query . " from citationusers where username = '" . $username . "'";
	$rs = mysql_query($query);
	echo "<table id=content><tr><td id=savedcitations>";
	echo "<h2 id=savedcitationsheading >Saved Citations:</h2>";
	echo "<form action=index.php>";
	echo "<input type=hidden name=count value=" . $count . ">";
	echo "<ul>";
	for ($i = 1; $i <= $count; $i++)
	{
		$citation = "citation" . $i ;
		if (mysql_result($rs, 0, $citation) != '') //ignore blank citations
		echo "<li> " .mysql_result($rs, 0, $citation). "<button type=submit value='' name=citation".$i.">Remove</button></li>\n";
	}
	?>
</form>
</ul>
</td><td id=citationcreator>
<h2 id=newcitationheading >New citation:</h2>
<form id=citationform>
	Article Title: <input id=newpage type=text value='Page Title'> Date: <input id=date name=date type=text value='Date Accessed'><br>
	Style: <input id=apa name=style checked=checked type=radio>APA <input id=mla name=style type=radio>MLA
	<input id=createcitation type=button action='' value="Create citation">
	<input type=submit class=hidden id=save action=index.php value=Save Citation>
	<input type=hidden name=count value=<?php echo ($count+1);?>><br>
	<textarea cols=50 rows=3 id=newcitation name=citation<?php echo ($count+1);?>>Citation will appear here</textarea>
</form>
</td></tr></table>

<?php
}

else {
/* create new user if new account requested
	or log in user
*/

	if(array_key_exists("username", $_REQUEST)
		&& array_key_exists("password", $_REQUEST))

	{	
	//Check for log in errors	
	//validate username and password in case javascript not available
		if( strlen($_REQUEST['username']) < 5 || strlen($_REQUEST['password']) < 5) 
		{
			die ( '<a href="index.php">Username and password must be at least 5 characters</a>');
		}

	//create new account
		if(array_key_exists("newUser", $_REQUEST))
		{
			$username = htmlspecialchars($_REQUEST["username"]);
			$password = htmlspecialchars($_REQUEST["password"]);
			$query = "select username from citationusers where username = '" . $username . "'";
			$rs = mysql_query($query);
			if( @mysql_result($rs, 0, 'username') != $username )
			{
				$query = "insert into citationusers (username, password , count, citation1) values ('$username', '$password', 1, 'Example (n.d.). In Wikipedia. Retrieved Date Accessed, from http://en.wikipedia.org/wiki/Example')";
				$rs = mysql_query($query);
				if (!$rs) {die ('new user creation failed<br>');}
			}
			else
			{
				die ( '<a href="index.php">Account creation failed, user name exists</a>');

			}
		}

	//Log in
		$username = htmlspecialchars($_REQUEST["username"]);
		$password = htmlspecialchars($_REQUEST["password"]);
		$query = "select password from citationusers where username = '" . $username . "'";
		$rs = mysql_query($query);

	if ( !$rs) //login fails on username
	{
		die( '<a href="index.php">Login failed</a>');
	}

	//login succeeds
	else if( @htmlspecialchars(mysql_result($rs, 0, "password")) == $password) 
	{
		$_SESSION['uid'] = 1;
		$_SESSION['username'] = $username;
		echo '<a href="index.php">View citations</a>';
	}

	//incorrect password
	else
	{
		echo '<a href="index.php">Login failed</a>';
	}
}
else
	//build login anyway for no javascript
{
	?>
	<title>citation List - Log in</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="jquery.js" type="text/javascript">
	</script>
	<script src="jquery.validate.js" type="text/javascript">
	</script>
	<script type="text/javascript">


	$(document).ready(function(){
		$("#newusername").blur(function(){
			var data="username=" + $("#newusername").val();
			$.ajax({
				type: "POST",
				url:"checkuser.php",
				data: data,
				success:function(result){
					$("#checkuser").html(result);
				}});
		});
	});

//Form Validation
$(document).ready(function(){
	$("#loginForm").validate({

		rules: {
			username: {
				required: true,
				minlength: 5,
			},
			password: {
				required: true,
				minlength: 5,
			}
		},

		messages: {
			username:  {
				required: "Username required.",
				minlength: jQuery.format("You need to use at least {0} characters for your username.")
			},

			password: {
				required: "Password required.",
				minlength: jQuery.format("You need to use at least {0} characters for your password.")
			}
		}
	});
	$("#newUser").validate({

		rules: {
			username: {
				required: true,
				minlength: 5,
			},
			password: {
				required: true,
				minlength: 5,
			}
		},

		messages: {
			username:  {
				required: "Username required.",
				minlength: jQuery.format("You need to use at least {0} characters for your username.")
			},

			password: {
				required: "Password required.",
				minlength: jQuery.format("You need to use at least {0} characters for your password.")
			}
		}
	});

});
</script>
<style type="text/css">
#top {width:100%; background:#f9f9f9; margin:1.2em 0 6px 0; border:1px solid #ddd;}
#title {width:61%; color:#000;font-family: sans-serif; font-size: 30px;}
#content {width: 100%; margin:4px 0 0 0; background:none; border-spacing: 0px;}
#login {border:1px solid #cef2e0; background:#f5fffa; vertical-align:top; color:#000;}
#loginheading {margin:3px; background:#cef2e0; font-size:120%; font-weight:bold; border:1px solid #a3bfb1; text-align:left; color:#000; padding:0.2em 0.4em;}   
#createaccount {border:1px solid #cedff2; background:#f5faff; vertical-align:top;}
#createaccountheading{margin:3px; background:#cedff2; font-size:120%; font-weight:bold; border:1px solid #a3b0bf; text-align:left; color:#000; padding:0.2em 0.4em;}
</style>
</head>
<body>
	<div id="top">
		<div id="title">Wikipedia Citation Creator</div>
		<table id="content">
			<tr>
				<td id="login">
					<h4 id="loginheading">Log In</h4>
					<form id="loginForm" action="loginhandler.php" name="loginForm">
						<div>Username (at least 5 characters): <input type="text" id="username" name="username"></div>
						<div>Password (at least 5 characters): <input type="password" id="password" name="password"></div>
						<div><input type="submit" value="OK"></div>
					</form>
				</td>
				<td id="createaccount">
					<h4 id="createaccountheading">Create Account</h4>
					<form id="newUser" action="loginhandler.php" name="newUser">
						<div>Username (at least 5 characters): <input type="text" id="newusername" name="username">
							<div id="checkuser"></div>
						</div>
						<div>Password (at least 5 characters): <input type="password" id="newpassword" name="password"></div>
						<div><button id="newUserPass" name="newUser" value="true">Create Account</button></div>
					</form>
				</td>
			</tr>
		</table>
	</div>
			<?php

		}?>	
	</body>
	</html>
	<?php 
}
?>
