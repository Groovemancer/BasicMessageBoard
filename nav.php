<?php
	require 'db/connect.php';
	require 'functions/security.php';
	session_start();

	if (!empty($_POST) && !isset($_SESSION['userID'])) {
		if (isset($_POST['user'], $_POST['password'])) {
			$user = trim($_POST['user']);
			$password = trim($_POST['password']);

			if (!empty($user) && !empty($password)) {

				if (isset($_POST['login'])) {
					$records = array();
					if ($results = $db->query("SELECT UserName, UserID, UserPassword FROM Users")) {
						if ($results->num_rows) {
							while ($row = $results->fetch_object()) {
								$records[] = $row;
								if (strtolower($row->UserName) == strtolower($user) && $row->UserPassword == md5($password)) {
									$_SESSION['userID'] = $row->UserID;
									break;
								}
							}
							$results->free();
						}
					}
				} elseif (isset($_POST['register'])) {
					$date = date("Y-m-d H:i:s");
					$insert = $db->prepare("INSERT INTO Users (UserName, UserPassword, CreationDate) VALUES (?, ?, ?)");
					$insert->bind_param('sss', $user, md5($password), $date);
					
					if ($insert->execute()) {
						header("Location: " . $_SERVER['REQUEST_URI']);
					}
				}
				
			}
		}
	}

	if (!empty($_POST) && isset($_SESSION['userID'])) {
		if (isset($_POST['logout'])) {
			session_unset();
			header("Location: " . $_SERVER['REQUEST_URI']);
		}
	}

	function getUserName($db) {
		if (isset($_SESSION['userID'])) {
			$res = $db->prepare("SELECT UserName FROM Users WHERE UserID = ? LIMIT 1");		
			$res->bind_param('i', $_SESSION['userID']);
			if ($res->execute()) {
				$res->bind_result($userName);
				$res->fetch();
				echo '<table width="50%"><tr>';
				echo '<td>Logged in as: ' . $userName . '</td>';
				echo '<td><form action="" method="post">';
				echo '<div class="field"><button type="submit" value="logout" name="logout">Logout</button></div>';
				echo '</form></td></tr>';
			}

		} else {
			echo '<table width="50%"><tr>';
			echo '<form action="" method="post">';
			
			echo '<td>User Name</td><td><div class="field"><input type="text" name="user" id="user" autocomplete="off"></div></td>';
			echo '<td>Password</td><td><div class="field"><input type="password" name="password" id"password" autocomplete="off"></div></td>';
			echo '<td><div class="field"><button type="submit" value="login" name="login">Login</button></div></td>';
			echo '<td><div class="field"><button type="submit" value="register" name="register">Register</button></div>';
			echo '</form></td>';
			echo '</tr>';
		}
	}	
?>

<html>
	<body>
		<center>
			<?php getUserName($db); ?>
			
				<tr>
					<td><a href="boards.php" target="main">Board Index</a></td>
				</tr>
			</table>
		</center>
	</body>
</html>