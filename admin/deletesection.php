<?php

require_once("../include.php");

check_login($config);

$section_id = -1;
if (isset($_GET["section_id"])) {

	$section_id = $_GET["section_id"];

	$db = new DB($config);

	$query = "DELETE FROM ".$config->db_prefix."sections where section_id = $section_id";
	$result = $db->query($query);
	$db->close();
}

redirect("listsections.php");
