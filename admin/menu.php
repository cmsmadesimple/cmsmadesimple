<div id="navcontainer">

<div id="welcome"><?php
if (isset($_SESSION["cms_admin_username"]))
{
	$gettext->setVar("name", $_SESSION["cms_admin_username"]);
}
else
{
	$gettext->setVar("name", "");
}
echo $gettext->gettext('Welcome ${name}');
$gettext->reset();
?>

<br/>
<br/>

<form action="index.php" method="post" name="cms_admin_lang_form">
<span class="smallselect"><?=$gettext->gettext("Language")?> : </span>
<select class="smallselect" name="change_cms_lang" onchange="cms_admin_lang_form.submit()" style="vertical-align: middle;">
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
</div>

<a href="listcontent.php"><?=$gettext->gettext("Content Management")?></a>
<a href="listtemplates.php"><?=$gettext->gettext("Template Management")?></a>
<a href="listcss.php"><?=$gettext->gettext("CSS Management")?></a>
<!--<a href="tools.php"><?=$gettext->gettext("Tools")?></a>-->
<a href="listusers.php"><?=$gettext->gettext("User Management")?></a>
<a href="listgroups.php"><?=$gettext->gettext("Group Management")?></a>
<a href="files.php"><?=$gettext->gettext("File Management")?></a>
<a href="plugins.php"><?=$gettext->gettext("Plugin Management")?></a>
<a href="editprefs.php"><?=$gettext->gettext("User Preferences")?></a>
<a href="adminlog.php"><?=$gettext->gettext("Admin Log")?></a>
<a href="../index.php" target="_new"><?=$gettext->gettext("Show Site")?></a>
<a href="logout.php"><?=$gettext->gettext("Logout")?></a>

<?

	$cmsmodules = $gCms->modules;

	$displaymodules = "";

	foreach ($cmsmodules as $key=>$value) {
		if (isset($cmsmodules[$key]['execute_admin_function']) 
			&& $cmsmodules[$key]['Installed'] == true
			&& $cmsmodules[$key]['Active'] == true
		)
		{
			$displaymodules .= "<a href=\"moduleinterface.php?module=$key\">$key</a>";
		}
	}

	if ($displaymodules != "") {
		echo "<div class=\"menutitle\">".$gettext->gettext("Modules")."</div>";
		echo $displaymodules;
	}

?>
<br />
</div> 
