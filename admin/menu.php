<div id="navcontainer">

<div id="welcome">
<?php echo lang('welcomemsg', array($_SESSION["cms_admin_username"]))?>
</div>

<div>

<a href="listcontent.php"><?php echo lang('contentmanagement')?></a>
<a href="listtemplates.php"><?php echo lang('templatemanagement')?></a>
<?php if (get_site_preference('useadvancedcss') == "1") { ?>
<a href="listcss.php"><?php echo lang('cssmanagement')?></a>
<?php } ?>
<a href="listhtmlblobs.php"><?php echo lang('blobmanagement')?></a>
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

</div>

<br />

</div> 
