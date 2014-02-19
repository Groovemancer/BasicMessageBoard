<?php
	require 'db/connect.php';
	require 'functions/security.php';
	session_start();

	$id = -1;
	if (!empty($_GET)) {
		if (isset($_GET['id'])) {
			$id = trim($_GET['id']);
		}
	}
	if (!empty($_POST)) {
		if (isset($_POST['body'])) {
			$body = escape($_POST['body']);

			if (!empty($body)) {
				$date = date("Y-m-d H:i:s");
				
				$insert = $db->prepare("INSERT INTO Posts (TopicID, Body, UserID, PostDateTime) VALUES (?, ?, ?, ?)");
				$insert->bind_param('isis', $id, $body, $_SESSION['userID'], $date);				
				
				if ($insert->execute()) {
					header("Location: " . $_SERVER['REQUEST_URI']);
					exit;
				}
			}
		}
	}

	function getTopicName($db, $topicID) {
		$res = $db->prepare("SELECT TopicName FROM Topics WHERE TopicID = ?");		
		$res->bind_param('i', $topicID);
		if ($res->execute()) {
			$res->bind_result($result);
			$res->fetch();
			echo $result;
		}
	}

	function getPosts($db, $topicID) {
		$res = $db->prepare("SELECT Body, UserName, PostDateTime FROM Posts INNER JOIN Users ON
									Posts.UserID = Users.UserID WHERE TopicID = ? ORDER BY PostID ASC");
		$res->bind_param('i', $topicID);
		if ($res->execute()) {
			$res->bind_result($r_body, $r_user, $r_dateTime);
			while ($res->fetch()) {
				echo '<table width="80%" border="1">';
				echo '<tr>';
				echo '<td>' . $r_user . '</td>';
				echo '<td>Post Date: ' . $r_dateTime . '</td>';
				echo '</tr>';
				echo '<tr><td colspan="2">' . $r_body . '</td></tr>';
				echo '</table><br>';
			}
		}
	}

	function post() {
		if (isset($_SESSION['userID'])) {			
			echo '<form action="" method="post">
					<div class="field">
						<textarea rows="5" cols="80%" name="body" id="body"></textarea>
					</div>
					<input type="submit" value="Reply">
				</form>';
		} else {
			echo "Log in to Post.";
		}
	}
?>

<html>
	<body>
		<center>
			<h2><?php getTopicName($db, $id) ?></h2>
			<?php
				getPosts($db, $id);
				post();
			?>
		</center>
	</body>
</html>