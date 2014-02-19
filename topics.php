<?php
	require 'db/connect.php';
	session_start();

	$id = -1;
	if (!empty($_GET)) {
		if (isset($_GET['id'])) {
			$id = trim($_GET['id']);
		}
	}

	function getBoardName($db, $boardID) {
		$res = $db->prepare("SELECT BoardName FROM Boards WHERE BoardID = ?");		
		$res->bind_param('i', $boardID);
		if ($res->execute()) {
			$res->bind_result($result);
			$res->fetch();
			echo $result;
		}
	}

	function getTopics($db, $boardID) {
		$res = $db->prepare("SELECT TopicID, TopicName, UserName, PostDateTime,
									(SELECT PostDateTime FROM Posts WHERE TopicID = Topics.TopicID ORDER BY PostDateTime DESC LIMIT 1) AS Recent,
									(SELECT COUNT(*) FROM Posts WHERE TopicID = Topics.TopicID) AS PostCount
									FROM Topics INNER JOIN Users ON
									Topics.UserID = Users.UserID WHERE BoardID = ?
									ORDER BY Recent DESC");
		$res->bind_param('i', $boardID);
		if ($res->execute()) {
			$res->bind_result($topicId, $topicName, $userName, $postDateTime, $recent, $postCount);

			echo '<table width="80%" border="1">';
			while ($res->fetch()) {
				echo '<tr>';
				echo '<td><a href="posts.php?id=' . $topicId . '" target="main">' . $topicName . '</td>';
				echo '<td>Created By: ' . $userName . '</td>';
				echo '<td>Post Count: ' . $postCount . '</td>';
				echo '<td>Created Date: ' . $postDateTime . '</td>';
				echo '<td>Recent Post Date: ' . $recent . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
?>

<html>
	<body>
		<center>
			<h2><?php getBoardName($db, $id) ?></h2>
			<?php echo '<a href="newtopic.php?id='.$id.'" target="main">Create Topic</a><br>'; ?>
			<?php getTopics($db, $id); ?>
		</center>
	</body>
</html>