<?php

echo "<p>Creating userplugins table...";

$dbdict = NewDataDictionary($db);
$flds = "
	userplugin_id I,
	userplugin_name C(255),
	code X,
	create_date T,
	modified_date T
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."userplugins", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($config["db_prefix"]."userplugins_seq");

echo "[done]</p>";

echo "<p>Creating css table...";

$dbdict = NewDataDictionary($db);
$flds = "
	css_id I,
	css_name C(255),
	css_text X,
	create_date T,
	modified_date T
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."css", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($config["db_prefix"]."css_seq");

echo "[done]</p>";

echo "<p>Creating css_assoc table...";

$dbdict = NewDataDictionary($db);
$flds = "
	assoc_to_id I,
	assoc_css_id I,
	assoc_type C(80),
	create_date T,
	modified_date T
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."css_assoc", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating modify CSS permission...";

cms_mapi_create_permission($gCms, 'Modify CSS', 'Modify CSS');
cms_mapi_create_permission($gCms, 'Add CSS', 'Add CSS');
cms_mapi_create_permission($gCms, 'Remove CSS', 'Remove CSS');
cms_mapi_create_permission($gCms, 'Add CSS association', 'Add CSS association');
cms_mapi_create_permission($gCms, 'Edit CSS association', 'Edit CSS association');
cms_mapi_create_permission($gCms, 'Remove CSS association', 'Remove CSS association');

echo "[done]</p>";

echo "<p>Adding head_tags to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."pages", "head_tags X");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating modify code blocks permission...";

cms_mapi_create_permission($gCms, 'Modify Code Blocks', 'Modify Code Blocks');

echo "[done]</p>";

echo "<p>Clearing cache and template directories... ";

function clear_dir_5($dir){

	$path = dirname(dirname(__FILE__))."/smarty/cms/".$dir."/";

	$handle=opendir($path);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != ".." && is_file($path.$file)) {
			#echo ($path.$file);
			unlink($path.$file);
		}
	}
}

clear_dir_5("templates_c");
clear_dir_5("cache");

echo "[done]</p>";

echo "<p>Updating templates sytem... ";

# we now gonna move the template's css as independent css
$query = "SELECT * FROM ".cms_db_prefix()."templates";
$result = $db->Execute($query);
if ($result)
{
	while ($row = $result->FetchRow())
	{
		$tid	= $row["template_id"];
		$ttext	= $row["stylesheet"];
		$tname	= $row["template_name"]."-CSS";

		# we create the css
		$new_css_id = $db->GenID(cms_db_prefix()."css_seq");
		$cssquery = "INSERT INTO ".cms_db_prefix()."css (css_id, css_name, css_text, create_date, modified_date) VALUES ('$new_css_id', ".$db->qstr($tname).", ".$db->qstr($ttext).", ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";
		$cssresult = $db->Execute($cssquery);
		if (FALSE == $cssresult)
		{
			echo "<p>Error inserting new CSS</p>";
		}

		# we create the association
		$cssassocquery = "INSERT INTO ".cms_db_prefix()."css_assoc (assoc_to_id,assoc_css_id,assoc_type,create_date,modified_date)
			VALUES ('$tid','$new_css_id','template',".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$cssassocresult = $db->Execute($cssassocquery);
		if (FALSE == $cssassocresult)
		{
			echo "<p>Error inserting new CSS association</p>";
		}
	}

	# we nom update templates modified date
	$tplquery = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$db->DBTimeStamp(time());
	$tplresult = $db->Execute($tplquery);
	if (FALSE == $tplresult)
	{
		echo "<p>Error updating templates</p>";
	}
}
else
{
	echo "<p>Error getting templates</p>";
}
echo "[done]</p>";

#echo "<p>Deleting stylesheet column... ";
#$dbdict = NewDataDictionary($db);
#$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."templates", "stylesheet");
#$dbdict->ExecuteSQLArray($sqlarray);
#echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 6";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
