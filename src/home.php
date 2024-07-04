<?php
	ob_start();
	session_start();
	
	mkdir("./twig/users/" . escapeshellcmd($_SESSION["username"]), 0766, true);
	system("touch ./twig/users/" . escapeshellcmd($_SESSION["username"]) . "/posts.json");
	if(isset($_POST['logout'])) {
		session_unset();
		session_destroy();
		header("LOCATION: <ip address>/login.php");
	} else if(isset($_POST['friends'])) {
		header("LOCATION: <ip address>/friends.php");
	}
?>
<!DOCTYPE html>
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
		.content-div {
			border: 2px dashed white;
			display: block;
			margin: 10px 12% 0px 12%;
			width: 75%;
		}
		.post-button {
			display: block;
			margin: 0px 0% 0px 80%;
			width: 5%;
		}
		.content-image {
			display: block;
			margin: 2% 16% 2% 16%;
			width: 66%;
			height: 50vh;
			font-size: 20px;
			font-family: "Times New Roman";
		}
		.content-text {
			font-size: 20px;
			font-family: "Times New Roman";
		}
		.content-text-title {
			font-size: 24px;
			font-family: "Times New Roman";
			font-weight: bold;
			border-bottom: 2px solid white;
			border-right: 2px solid white;
		}
		.post-textarea {
			background-color: #02eb9d;
			border: 2px dashed black;
			display: block;
			margin: 0px 12% 0px 12%;
			width: 75%;
		}
		.update-status {
			display: block;
			margin: 0px 12% 0px 12%;
			width: 75%;
			font-size: 20px;
			font-family: "Times New Roman";
		}
		.upload-picture {
			display: block;
			margin: 0px 12% 0px 12%;
			width: 75%;
			font-size: 20px;
			font-family: "Time New Roman";
		}
		</style>
	</head>
	<body>
		<script src="twig.js"></script>
		<div class = "main-title"> Twig<span class = "mini-title">.com </span> </div>
		<div class = "ghost">
			<form method="post">
				<input class="section" type="submit" name="home" value="Home"> </input>
				<input class="section" type="submit" name="friends" value="Friends"> </input>
				<input class="section" type="submit" name="logout" value="Logout"> </input>
			</form> 
		</div>
<!--		<form action="upload.php" method="post" enctype="multipart/form-data">
			Select image to upload:
			<input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" value="Upload Image" name="submit">
		</form>-->
		
		<form method="post" enctype="multipart/form-data">
			<span class="update-status" >  Update your status: </span> <br>
			<textarea class="post-textarea" type="textarea" name="newstatus" value="newstatus"> </textarea>
			<input class="upload-picture"  type="file" name="upfile" id="upfile"> <br>
			<input class="post-button" type="submit" name="post" value="post"> </input>
		</form> 
		<?php
		
		
			$friends_posts_to_see = 3;
			$image_name = "";
			if(isset($_POST['post'])) { // user posted new post
				try {
					$target_dir = "uploads/";
					$target_file = $target_dir . htmlspecialchars(basename( $_FILES["upfile"]["name"]));
					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
					// Undefined | Multiple Files | $_FILES Corruption Attack
					// If this request falls under any of them, treat it invalid.
					if(!isset($_FILES['upfile']['error']) || is_array($_FILES['upfile']['error'])) throw new RuntimeException('Invalid parameters.');

					// Check $_FILES['upfile']['error'] value.
					switch ($_FILES['upfile']['error']) {
						case UPLOAD_ERR_OK:
							break;
						case UPLOAD_ERR_NO_FILE:
							throw new RuntimeException('No file sent.');
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							throw new RuntimeException('Exceeded filesize limit.');
						default:
							throw new RuntimeException('Unknown errors.');
					}

					// You should also check filesize here.
					if ($_FILES['upfile']['size'] > 100000000) {
						throw new RuntimeException('Exceeded filesize limit.');
					}

					// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
					// Check MIME Type by yourself.
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					
					/*if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
						throw new RuntimeException('Invalid file format.');
					}*/
					if (false === $ext = array_search($finfo->file($_FILES['upfile']['tmp_name']), array('jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'),true)) {
						throw new RuntimeException('Invalid file format.');
					}

					// You should name it uniquely.
					// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
					// On this example, obtain safe unique name from its binary data.
					$image_name = sprintf('./uploads/%s.%s', sha1_file($_FILES['upfile']['tmp_name']), $ext);
					if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $image_name)) {
						throw new RuntimeException('Failed to move uploaded file.');
					}

					//echo 'File is uploaded successfully.';

				} catch (RuntimeException $e) {
					//TODO display error to user
				}
			
				$data = file_get_contents("./twig/users/" . escapeshellcmd($_SESSION["username"]) . "/posts.json");
				$posts = json_decode($data, true);
				foreach($posts as $p) {
					$posts_arr[] = $p;
				}
				//$image = isset($_POST['post']) ? htmlspecialchars($_POST['imageUpload']) : "";
				$user = escapeshellcmd($_SESSION["username"]);
				//file_put_contents("./twig/users/" . $user) . "/posts/images/" . $image);
				$message = isset($_POST['post']) ? htmlspecialchars($_POST['newstatus']) : "";
				if(strcmp($message, "") != 0 || strcmp($image, "") != 0) { // message is not empty post
					$post['user'] = $user; // name of the user
					$post['message'] = $message; // message the user sent
					$post['time'] = time(); // time post was made
					$post['image'] = $image_name; // name of the image
					$posts_arr[] = $post;
					file_put_contents("./twig/users/" . escapeshellcmd($_SESSION["username"]) . "/posts.json", json_encode($posts_arr));
					header("LOCATION: <ip address>/home.php"); // reload page to reflect changes
				} else {
					echo "<h1> ERROR NO MESSAGE! </h1>";
					//TODO probably should have an error message or something?
				}
			}
			
			$posts_arr2 = [];
			$usrinfo = $_SESSION["usrinfo"];
			for($i = 0; $i < count($usrinfo->{'friends'}); $i++) {
				$data = file_get_contents("./twig/users/" . escapeshellcmd($usrinfo->{'friends'}[$i]) . "/posts.json");
				if($data == null) continue;
				$posts = json_decode($data, true);
				$count = 0;
				for($j = count($posts)-1; $j >= 0; $j--) {
					$posts_arr2[] = $posts[$j];
					$count++;
					if($count == $friends_posts_to_see) break;
				}
			}
			
			$data = file_get_contents("./twig/users/" . escapeshellcmd($_SESSION["username"]) . "/posts.json");
			if($data != null) {
				$posts = json_decode($data, true);
				foreach($posts as $p) {
					$posts_arr2[] = $p;
				}
			}
			$post_time = array_column($posts_arr2, 'time');
			array_multisort($post_time, SORT_DESC, $posts_arr2);
			for($i = 0; $i < count($posts_arr2); $i++) {
				//TODO work on formatting
				if(strlen($posts_arr2[$i]['image']) > 0) {
					echo "<div class = 'content-div'> 
							<span class = 'content-text-title'>" . $posts_arr2[$i]['user'] . "</span> 
							<br> 
							<img src=" . $posts_arr2[$i]['image'] . " class = 'content-image'>
							<span class = 'content-text'>" . $posts_arr2[$i]['message'] . " </span> 
						</div>";
				} else {
					echo "<div class = 'content-div'> 
							<span class = 'content-text-title'>" . $posts_arr2[$i]['user'] . "</span> 
							<br> 
							<span class = 'content-text'>" . $posts_arr2[$i]['message'] . " </span> 
						</div>";
				}
			}
		ob_end_flush();
		?>
	</body>
</html>
