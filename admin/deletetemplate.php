<?php

require_once("../include.php");

check_login($config);

$template_id = -1;
if (isset($_GET["template_id"])) {

	$template_id = $_GET["template_id"];

	$db = new DB($config);

	$query = "DELETE FROM ".$config->db_prefix."templates where template_id = $template_id";
	$result = $db->query($query);
	$db->close();
}

redirect("listtemplates.php");
