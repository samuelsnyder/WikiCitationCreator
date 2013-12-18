<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">
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
</body>
</html>