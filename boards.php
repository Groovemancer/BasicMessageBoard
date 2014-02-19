<?php
	require 'db/connect.php';
	
	function getBoards($db) {
		$records = array();
		if ($results = $db->query("SELECT * FROM Boards")) {
			if ($results->num_rows) {
				while ($row = $results->fetch_object()) {
					$records[] = $row;
				}
				$results->free();
			}
		}
		echo '<table width="80%" border="1">';
		foreach ($records as $r) {
			echo '<tr>';
			echo '<td><a href="topics.php?id=' . $r->BoardID . '" target="main">' . $r->BoardName . '</a></td>';
			echo '</tr>';
		}
		echo '</table>';
	}
?>

<html>
	<body>
		<center>
			<h2>Boards</h2>
			<?php getBoards($db); ?>
		</center>
	</body>
</html>