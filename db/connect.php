<?php
	$db = new mysqli('localhost', 'admin', 'password', 'forums');
	if ($db->connect_errno) {
		die('Sorry, we are having some problems.');
	}
?>