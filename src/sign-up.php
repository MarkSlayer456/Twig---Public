<?php
	ob_start();
	session_start();	
?>
<!doctype html>
<html>
	<head>
		<title> Twig Create Account </title>
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
		</style>
	</head>
	<body>
	<h1 class='title'> Welcome to Twig! </h1>
	<h3 class='title'> Create an account </h3>
	<form method='POST'>
			<table>
				
				<tr>
				<td> First Name: <td> <input type='text' name='fname' placeholder='john' required>
				<tr>
				<td> Last Name: <td> <input type='text' name='lname' placeholder='john' required>
				<tr>
				<td> Date of Birth: <td> <input type='date' name='dob' placeholder='john' required>
				<tr>
				<td> Username: <td> <input type='text' name='Username' placeholder='Username345' required>
				<tr>
				<td> Password: <td> <input type='password' name='Password' required> </input>
				<tr>
				<td> Retype Password: <td> <input type='password' name='Retype' required> </input>
				<tr>
				<tr>
				<td> <input type='submit' name='submit' value='submit'>
				<tr>
			</table>
	</form>
	<form method='POST'>
		<table>
			<tr>
			<td> <input type='submit' name='login' value='Back to login'>
		</table>
	</form>
			
<?php
	include "config.php";
	function create_new_user($username, $password) {
		global $myconn;
		$table_data = $myconn->query("select * from `user`") or
					die ("Error querying database: (user) " . $myconn->error);
		while($fetch = $table_data->fetch_assoc()) {
			if(strcasecmp($fetch["username"], $username) == 0) {
				echo "<h3 class='title'> Username already taken! </h3>";
				return 0;
			}
		}
		$stmt = $myconn->prepare("insert into `user` (`username`, `password`, `usrinfo`)
			values(?, ?, ?)") or
			die("prepare" . $myconn->error);
		$hash = crypt($password,'$6$rounds=500000$thisissaltforthepassword$');
		$user_info = [
			"first_name" => $_POST['fname'], 
			"last_name" => $_POST['lname'],
			"date_of_birth" => $_POST['dob'],
			"friends" => [],
			"friend_requests" => [],
		];
		$user_info_json = json_encode($user_info);
		
		$stmt->bind_param("sss", $username, $hash, $user_info_json);
		$stmt->execute();
		echo "<h3 class='title'> Account created </h3>";
		// TODO add a redirect to homepage
	}

	if(($_SERVER['REQUEST_METHOD'] == 'POST')) {
		if(isset($_POST["login"])) {
			header("LOCATION: <ip address>/login.php");
			return 1;
		}
		if(strcasecmp((htmlspecialchars($_POST['Password'])), htmlspecialchars(($_POST['Retype']))) == 0) {
			create_new_user(htmlspecialchars($_POST['Username']), htmlspecialchars($_POST['Password']));
		} else {
			echo "<h3 class='title'> Passwords did not match, please try again! </h3>";
		}
	}
	ob_end_flush();
?>
	
	</body>
</html>
