<?php

require_once("../include.php");

$group_id = -1;
if (isset($_GET["group_id"])) {

	$group_id = $_GET["group_id"];

	$db = new DB($config);

	$query = "DELETE FROM ".$config->db_prefix."groups where group_id = $group_id";
	$result = $db->query($query);
	$db->close();
}

redirect("listgroups.php");
