<?php

require_once("../include.php");

$user_id = -1;
if (isset($_GET["user_id"])) {

	$user_id = $_GET["user_id"];

	$db = new DB($config);

	$query = "DELETE FROM ".$config->db_prefix."users where user_id = $user_id";
	$result = $db->query($query);
	$db->close();
}

redirect("listusers.php");
