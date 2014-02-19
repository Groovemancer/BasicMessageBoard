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
		if (isset($_POST['title'], $_POST['body'])) {
			$title = escape($_POST['title']);
			$body = escape($_POST['body']);

			if (!empty($title) && !empty($body)) {
				$date = date("Y-m-d H:i:s");

				$topics = $db->prepare("INSERT INTO Topics (BoardID, TopicName, UserID, PostDateTime) VALUES (?, ?, ?, ?)");
				$topics->bind_param('isis', $id, $title, $_SESSION['userID'], $date);
				if ($topics->execute()) {
				}

				$tid = $db->insert_id;

				$insert = $db->prepare("INSERT INTO Posts (TopicID, Body, UserID, PostDateTime) VALUES (?, ?, ?, ?)");
				$insert->bind_param('isis', $tid, $body, $_SESSION['userID'], $date);
				
				if ($insert->execute()) {
					$loc = "posts.php?id=${tid}";
					header("Location:" . $loc);
				}
			}
		}
	}
?>

<html>
	<body>
		<center>
			<form action="" method="post">
				<table>
					<tr>
						<td>
							Title
							<div class="field">
								<input type="title" name="title" id="title" autocomplete="off">
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="field">
								<textarea rows="5" cols="80%" name="body" id="body"></textarea>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" value="Post">
						</td>
					</tr>
				</table>
			</form>
		</center>
	</body>
</html>