<DIV ID="navcontainer">

<DIV ID="welcome">
<?php echo lang('welcomemsg', array($_SESSION["cms_admin_username"]))?>
</DIV>

<A HREF="listcontent.php"><?php echo lang('contentmanagement')?></A>
<A HREF="listtemplates.php"><?php echo lang('templatemanagement')?></A>
<?php if (get_site_preference('useadvancedcss') == "1") { ?>
<A HREF="listcss.php"><?php echo lang('cssmanagement')?></A>
<?php } ?>
<A HREF="listhtmlblobs.php">HTML Blob Management</A>
<!--<a href="tools.php"><?php echo lang('tools')?></a>-->
<A HREF="listusers.php"><?php echo lang('usermanagement')?></A>
<A HREF="listgroups.php"><?php echo lang('groupmanagement')?></A>
<A HREF="files.php"><?php echo lang('filemanagement')?></A>
<A HREF="plugins.php"><?php echo lang('pluginmanagement')?></A>
<A HREF="siteprefs.php"><?php echo lang('siteprefs')?></A>
<A HREF="adminlog.php"><?php echo lang('adminlog')?></A>
<A HREF="<?php

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

?>" TARGET="_blank"><?php echo lang('showsite')?></A>
<A HREF="editprefs.php"><?php echo lang('userprefs')?></A>
<A HREF="logout.php"><?php echo lang('logout')?></A>

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
<BR>
</DIV> 
