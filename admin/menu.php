<div class="nav">
<h4><?php
  
if (isset($_SESSION["cms_admin_username"])) {
	$gettext->setVar("name", $_SESSION["cms_admin_username"]);
} else {
	$gettext->setVar("name", "");
}
echo $gettext->gettext('Welcome ${name}');
$gettext->reset();
  
?></h4>

<form action="index.php" method="post" name="cms_admin_lang_form">
<span class="smallselect"><?=$gettext->gettext("Language")?></span><br />
<select class="smallselect" name="change_cms_lang" onchange="cms_admin_lang_form.submit()">
<?
	foreach ($nls["language"] as $key=>$val) {
		echo "<option value=\"$key\"";
		if (isset($_POST["change_cms_lang"])) {
			if ($_POST["change_cms_lang"] == $key) {
				echo " selected";
			}
		} else if (isset($_COOKIE["cms_language"])) {
			if ($_COOKIE["cms_language"] == $key) {
				echo " selected";
			}
		}
		echo ">$val</option>\n";
	}
?>
</select>
</form>

<a href="listcontent.php"><?=$gettext->gettext("Content Management")?></a>
<a href="listtemplates.php"><?=$gettext->gettext("Template Management")?></a>
<a href="listusers.php"><?=$gettext->gettext("User Management")?></a>
<a href="listgroups.php"><?=$gettext->gettext("Group Management")?></a>
<a href="modules.php"><?=$gettext->gettext("Module Management")?></a>
<a href="adminlog.php"><?=$gettext->gettext("Admin Log")?></a>
<a href="editprefs.php"><?=$gettext->gettext("User Preferences")?></a>
<a href="editconfig.php"><?=$gettext->gettext("Edit Configuration")?></a>
<!--<a href="tools.php"><?=$gettext->gettext("Tools")?></a>-->
<a href="../index.php" target="_new"><?=$gettext->gettext("Show Site")?></a>
<a href="logout.php"><?=$gettext->gettext("Logout")?></a>
<?
	$installedmodules = array();

	$query = "SELECT * FROM ".cms_db_prefix()."modules";
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {

		$displaymodules = "";

		while ($row = $result->FetchRow()) {
			$installedmodules[$row['module_name']] = 1;
		}

		foreach ($gCms->modules as $key=>$value) {
			if (isset($gCms->modules[$key]['execute_admin_function']) && isset($installedmodules[$key])) {
				$displaymodules .= "<a href=\"moduleinterface.php?module=$key\">$key</a>";
			}
		}

		if ($displaymodules != "") {
			echo "<h4>".$gettext->gettext("Modules")."</h4>";
			echo $displaymodules;
		}

	}

?>

</div> 
