<div id="navcontainer">

<div id="welcome"><?php echo lang('welcomemsg', array($_SESSION["cms_admin_username"]))?>
<br>
<br>

<form action="index.php" method="post" name="cms_admin_lang_form">
<span class="smallselect"><?php echo lang('language')?> : </span>
<select class="smallselect" name="change_cms_lang" onchange="cms_admin_lang_form.submit()" style="vertical-align: middle;">
<?php
	asort($nls["language"]);
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
		echo ">$val";
		if (isset($nls["englishlang"][$key]))
		{
			echo " (".$nls["englishlang"][$key].")";
		}
		echo "</option>\n";
	}
?>
</select>
</form>
</div>

<a href="listcontent.php"><?php echo lang('contentmanagement')?></a>
<a href="listtemplates.php"><?php echo lang('templatemanagement')?></a>
<?php if (get_site_preference('useadvancedcss') == "1") { ?>
<a href="listcss.php"><?php echo lang('cssmanagement')?></a>
<?php } ?>
<!--<a href="tools.php"><?php echo lang('tools')?></a>-->
<a href="listusers.php"><?php echo lang('usermanagement')?></a>
<a href="listgroups.php"><?php echo lang('groupmanagement')?></a>
<a href="files.php"><?php echo lang('filemanagement')?></a>
<a href="plugins.php"><?php echo lang('pluginmanagement')?></a>
<a href="siteprefs.php"><?php echo lang('siteprefs')?></a>
<a href="adminlog.php"><?php echo lang('adminlog')?></a>
<a href="<?php

if (isset($config['assume_mod_rewrite']) && $config['assume_mod_rewrite'] == true)
{
	$query = "SELECT page_alias, page_id FROM " . cms_db_prefix() . "pages WHERE default_page = '1'";
	$result = $db->query($query);
	if ($result && $result->RowCount() > 0)
	{
		$row = $result->FetchRow();
		if ($row['page_alias'] != '')
		{
			echo "../" . $row['page_alias'] . ".shtml";
		}
		else
		{
			echo "../" . $row['page_id'] . ".shtml";
		}
	}
	else
	{
		echo "../index.php";
	}
}
else
{
	echo "../index.php";
}

?>" target="_blank"><?php echo lang('showsite')?></a>
<a href="editprefs.php"><?php echo lang('userprefs')?></a>
<a href="logout.php"><?php echo lang('logout')?></a>

<?php

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
		echo "<div class=\"menutitle\">".lang('modules')."</div>";
		echo $displaymodules;
	}

?>
<br>
</div> 
