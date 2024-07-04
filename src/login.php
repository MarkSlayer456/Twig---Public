<?php
	ob_start();
	session_start();
?>
<!doctype html>
<html>
	<head>
		<title> Twig Login </title>
		<style>
		body  {
			background-color: #00cc88;
		}
		table {
			text-align: center;
			margin-left: auto;
			margin-right: auto;
		}
		.title {
			color: white;
			text-shadow: 1px 1px #000000;
			text-align: center;
		}
		.input-title {
			width: 100px;
			text-align: right;
		}
		.inputs {
			width: 200px;
		}
		</style>
	</head>
	<body>
	<h1 class='title'> Welcome back to Twig! </h1>
	<form method='POST'>
			<table>
				<tr>
				<td class='input-title'> Username: <td> <input class='inputs' type='text' name='Username' placeholder='Username345' required>
				<tr>
				<td class='input-title'> Password: <td> <input class='inputs' type='password' name='Password' required> </input>
				<tr>
				<td class='input-title'> Captcha: <td> <img class='inputs' src='/twig/captcha.php'>
				<tr>
				<td class='input-title'> <td> <input class='inputs' type="text" name="captcha"/>
				<tr>
				<td class='input-title'> <td> <input class='inputs' type='submit' name='login' value='login'>
				<tr>
			</table>
	</form>
	<form method='POST'>
			<table>
				<tr>
				<td> <input type='submit' name='sign-up' value='sign-up'>
			</table>
	</form>
			
<?php
	include "config.php";
	function check_captcha() {
		if(isset($_POST) & !empty($_POST)){
			if($_POST['captcha'] == $_SESSION['code']){
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	function login($username, $password) {
		global $myconn;
		$table_data = $myconn->query("select * from `user`") or
					die ("Error querying database: (user) " . $myconn->error);
		$hash = crypt($password,'$6$rounds=500000$thisissaltforthepassword$');
		while($fetch = $table_data->fetch_assoc()) {
			if(strcasecmp($fetch["username"], $username) == 0) {
				if(strcasecmp($fetch["password"], $hash) == 0) {
					$_SESSION["usrinfo"] = json_decode($fetch["usrinfo"]);
					$_SESSION["username"] = $username;
					header("LOCATION: <ip address>/home.php");
					return 1;
				}
			}
		}
		
		echo "<h3 class='title'> Invalid username or password, please try again! </h3>"; 
	}

	if(($_SERVER['REQUEST_METHOD'] == 'POST')) {
		if(isset($_POST["sign-up"])) {
			header("LOCATION: <ip address>/sign-up.php");
			return 1;
		}
		if(check_captcha()) {
			login(htmlspecialchars($_POST['Username']), htmlspecialchars($_POST['Password']));
		}
	}
	ob_end_flush();
?>
	
	</body>
</html>
