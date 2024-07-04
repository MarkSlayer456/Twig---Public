<?php
	ob_start();
	include "config.php";
	session_start();
	if(isset($_POST['logout'])) {
		session_unset();
		session_destroy();
		header("LOCATION: <ip address>/login.php");
	} else if(isset($_POST['home'])) {
		header("LOCATION: <ip address>/home.php");
	}
	
	function fix_string_for_post($str) {
		$new_str = str_replace(['.', ' ', '['], '_', $str);
		return $new_str;
	}	
	$MAX_FRIENDS = 10000;
?>
<!doctype html>
<html>
	<head>
		<style>
		body  {
			background-color: #00cc88;
		}
		.main-title {
			font-size: 48px;
			color: white;
			text-align: left;
			font-weight: bold;
		}
		.section {
			display: inline-block;
			width: 55px;
			height: 55px;
			border-radius: 1000px;
			background-color: #77cc00;
			font-size: 12px;
			text-align: center;
			border: 2px solid white;
			vertical-align: middle;
			font-family: "Times New Roman";
			padding: 0px;
			margin: 0px;
		}
		.ghost {
			display: block;
			vertical-align: middle;
			text-align: left;
			width: 100%;
			height: 100%;
		}
		.mini-title {
			font-size: 12px;
		}
		.content-left {
			margin: 10px 1% 0px 10%;
			width: 35%;
			display: inline-block;
		}
		.content-right {
			margin: 10px 10% 0px 1%;
			width: 35%;
			display: inline-block;
			position: absolute;
		}
		.friend-title {
			border-bottom: 2px solid white;
			width: 100%;
		}
		.text {
			font-size: 16px;
			font-family: "Times New Roman";
		}
		.search-text {
			margin: 5px 0% 0px 0%;
			width: 75%;
			background-color: #02eb9d;
			border: 2px dashed black;
			height: 5%;
			display: inline-block;
		}
		
		.search-button {
			margin: 0px 0% 0px 0%;
			width: 15%;
			background-color: #77cc00;
			display: inline-block;
		}
		.double-button {
			margin: 0% 0% 0% 2%;
			width: 22%;
			background-color: #77cc00;
			display: inline-block;
		}
		.remove-friend-button {
			margin: 0% 0% 0% 0%;
			width: 25%;
			height: 100px;
			background-color: #8df200;
			border: 1px solid white;
			display: inline-block;
		}
		.friend-button {
			margin: 0% 0% 0% 0%;
			width: 73%;
			height: 100px;
			background-color: #8df200;
			border: 1px solid white;
			display: inline-block;
		}
		.form {
			width: 100%;
			display: inline;
		}
		.user-info {
			width: 50%;
			display: inline-block;
		}
		</style>
	</head>
	<body>
		<div class = "main-title"> Twig<span class = "mini-title">.com </span> </div>
		<div class = "ghost">
			<form method="post">
				<input class="section" type="submit" name="home" value="Home"> </input>
				<input class="section" type="submit" name="friends" value="Friends"> </input>
				<input class="section" type="submit" name="logout" value="Logout"> </input>
			</form> 
		</div>
		<div class="content-left">
			<div class = "text friend-title"> Friends List </div>
			<form class="form" method="post">
				<input class="search-text" type='text' name='search-text' placeholder='Serach for a friend'>
				<input class="search-button" type="submit" name="search" value="search"> </input>
				<?php
						$user = $_SESSION["usrinfo"];
						$user->{"friend_requests"} = array_values($user->{"friend_requests"}); // saftey reason
						$friend_requests_len = count($user->{"friend_requests"});
						for($i = 0; $i < $friend_requests_len; $i++) {
							if($user->{"friend_requests"}[$i] != null) {
								echo "<div class='user-info text'> You have a friend request from " . $user->{"friend_requests"}[$i] . "</div>";
								$accept = "'accept-" . $user->{"friend_requests"}[$i] . "'";
								$decline = "'decline-" . $user->{"friend_requests"}[$i] . "'";
								echo "<input class='double-button' type='submit' name=" . $accept . "value='accept'> </input>";
								echo "<input class='double-button' type='submit' name=" . $decline . "value='decline'> </input>";
							}
						}
						for($i = 0; $i < count($user->{"friends"}); $i++) {
							if($user->{"friends"}[$i] != null) {
								
								echo "<input class='friend-button' type='submit' name=" . $user->{"friends"}[$i] . " value=" . $user->{"friends"}[$i] . "> </input>";
								echo "<input class='remove-friend-button' type='submit' value='Remove Friend' name=remove-" . $user->{"friends"}[$i] . "> </input>";
							}
						}
				?>
			</form>
		</div>
		<div class="content-right">
			<div class = "text friend-title"> Find New Friends </div>
			<form class="form" method="post">
				<input class="search-text" type='text' name='search-find-text' placeholder='Find a new friend'>
				<input class="search-button" type="submit" name="search-find" value="search"> </input>
				<?php
					if(($_SERVER['REQUEST_METHOD'] == 'POST')) {
						if(isset($_POST['search-find'])) {
							// this is really slow can just query select the user you want instead of looping through the whole list of users 
							$table_data = $myconn->query("select * from `user`") or
										die ("Error querying database: (user) " . $myconn->error);
							while($fetch = $table_data->fetch_assoc()) {
								if(strcasecmp($fetch["username"], $_POST['search-find-text']) == 0) {
									$reciever = json_decode($fetch["usrinfo"]);
									$friend_count = count($reciever->{"friends"});
									for($i = 0; $i < $friend_count; $i++) { // make sure users aren't already friends
										if(strcasecmp($reciever->{"friends"}[$i], $_SESSION["username"]) == 0) {
											echo "<div> You are already friends with " . $fetch["username"] . "</div>";
											return 1;
										}
									}
									for($i = 0; $i < count($reciever->{"friend_requests"}); $i++) { // make sure user hasn't already sent request
										if(strcasecmp($reciever->{"friend_requests"}[$i], $_SESSION["username"]) == 0) {
											echo "<div> You have already sent a request to " . $fetch["username"] . "</div>";
											return 1;
										}
									}
									if(strcasecmp($fetch["username"], $_SESSION["username"]) == 0) {
										echo "<div> You can't add yourself as a friend!</div>";
										return 1;
									}
									
									if($friend_count > $MAX_FRIENDS) {
										echo "<div> You have the maximum amount of friends! </div>";
										return 1;
									}
									$reciever->{"friend_requests"}[] = $_SESSION["username"];
									
									$stmt = $myconn->prepare("update `user` set `usrinfo`=? where username=?") or
										die("prepare" . $myconn->error);
									$reciever_info_json = json_encode($reciever);
									
									$stmt->bind_param("ss", $reciever_info_json, $fetch["username"]);
									$stmt->execute();
									echo "<div class='text'> Request sent to " . $fetch["username"] . "</div>";
								}
							}
						}
					}
				?>
			</form>
		</div>
		<?php
			if(($_SERVER['REQUEST_METHOD'] == 'POST')) {
				$userinfo = $_SESSION["usrinfo"];
				for($i = 0; $i < count($userinfo->{"friend_requests"}); $i++) {
					$accept_end = fix_string_for_post($userinfo->{"friend_requests"}[$i]);
					$accept = "accept-" . $accept_end;
					if(isset($_POST[$accept])) { // accepted a friend request
						if(count($userinfo->{"friends"}) > $MAX_FRIENDS) {
							echo "<div> You have the maximum amount of friends! </div>";
							return 1;
						}
						$stmt = $myconn->prepare("select `username`, `usrinfo` from `user` where username=?") or
										die ("Error querying database: (user) " . $myconn->error);
						$stmt->bind_param("s", $userinfo->{"friend_requests"}[$i]);
						$stmt->execute();
						$stmt->bind_result($friend_username, $friend_info);
						$stmt->fetch();
						
						$userinfo->{"friends"}[] = $friend_username;
						$friend_info_decoded = json_decode($friend_info);
						$friend_info_decoded->{"friends"}[] = $_SESSION["username"];
						unset($userinfo->{"friend_requests"}[$i]);
						$userinfo->{"friend_requests"} = array_values($userinfo->{"friend_requests"});
						$stmt->close();
						
						$stmt2 = $myconn->prepare("update `user` set `usrinfo`=? where username=?") or
										die("prepare" . $myconn->error);
						
						$stmt2->bind_param("ss", json_encode($userinfo), $_SESSION["username"]);
						$stmt2->execute();
						
						$stmt2->close();
						
						$stmt3 = $myconn->prepare("update `user` set `usrinfo`=? where username=?") or
										die("prepare" . $myconn->error);
						
						$stmt3->bind_param("ss", json_encode($friend_info_decoded), $friend_username);
						$stmt3->execute();
						$stmt3->close();
						$_SESSION["usrinfo"] = $userinfo;
						header("LOCATION: <ip address>/friends.php");
					}
				}
				$friend_count = count($userinfo->{"friends"});
				for($i = 0; $i < $friend_count; $i++) {
					$remove_friend_end = fix_string_for_post($userinfo->{"friends"}[$i]);
					$remove_friend = 'remove-' . $remove_friend_end;
					if(isset($_POST[$remove_friend])) {
						$stmt = $myconn->prepare("select `username`, `usrinfo` from `user` where username=?") or
										die ("Error querying database: (user) " . $myconn->error);
						$stmt->bind_param("s", $userinfo->{"friends"}[$i]);
						$stmt->execute();
						$stmt->bind_result($friend_username, $friend_info);
						$stmt->fetch();
						
						$friend_info_decoded = json_decode($friend_info);
						$friend_list_num = -1;
						$friends_friend_count = count($friend_info_decoded->{"friends"});
						for($j = 0; $j < $friends_friend_count; $j++) {
							if(strcasecmp($friend_info_decoded->{"friends"}[$j], $_SESSION["username"]) == 0) {
								$friend_list_num = $j;
							}
						}
						if($friend_list_num < 0) { // should be impossible to get here
							echo "<div> ERROR: Friend not found! </div>";
							return -1;
						}
						unset($friend_info_decoded->{"friends"}[$friend_list_num]); // delete you from your friend's friends list
						$friend_info_decoded->{"friends"} = array_values($friend_info_decoded->{"friends"});
						
						unset($userinfo->{"friends"}[$i]); // delete friend from your friend's list
						$userinfo->{"friends"} = array_values($userinfo->{"friends"});
						$stmt->close();
						
						$stmt2 = $myconn->prepare("update `user` set `usrinfo`=? where username=?") or
										die("prepare" . $myconn->error);
						
						$stmt2->bind_param("ss", json_encode($userinfo), $_SESSION["username"]);
						$stmt2->execute();
						
						$stmt2->close();
						
						$stmt3 = $myconn->prepare("update `user` set `usrinfo`=? where username=?") or
										die("prepare" . $myconn->error);
						
						$stmt3->bind_param("ss", json_encode($friend_info_decoded), $friend_username);
						$stmt3->execute();
						$stmt3->close();
						$_SESSION["usrinfo"] = $userinfo;
						header("LOCATION: <ip address>/friends.php");
					}	
				}
			}
		ob_end_flush();
		?>
	</body>
</html>
