<div id="navcontainer">

<?php

$userid = get_userid();

?>

<div id="welcome">
<?php if (isset($_SESSION['cms_admin_username'])) echo lang('welcomemsg', array($_SESSION['cms_admin_username']))?>
</div>

<div>
<ul class="navlist">
<li><a href="listcontent.php"><?php echo lang('contentmanagement')?></a></li>

<?php

if (check_permission($userid, 'Add Template') || check_permission($userid, 'Remove Template') || check_permission($userid, 'Modify Template'))
{

?>
<li><a href="listtemplates.php"><?php echo lang('templatemanagement')?></a></li>
<?php

}

if (get_site_preference('useadvancedcss') == "1")
{
	if (check_permission($userid, 'Add CSS') || check_permission($userid, 'Remove CSS') || check_permission($userid, 'Modify CSS'))
	{

?>
<li><a href="listcss.php"><?php echo lang('cssmanagement')?></a></li>
<?php

	}
}

?>
<li><a href="listhtmlblobs.php"><?php echo lang('blobmanagement')?></a></li>
<li><a href="listusers.php"><?php echo lang('usermanagement')?></a></li>
<?php

if (check_permission($userid, 'Add Group') || check_permission($userid, 'Remove Group') || check_permission($userid, 'Modify Group') || check_permission($userid, 'Modify Group Assignments') || check_permission($userid, 'Modify Permissions'))
{

?>
<li><a href="listgroups.php"><?php echo lang('groupmanagement')?></a></li>
<?php

}

if (check_permission($userid, 'Modify Files'))
{

?>
<li><a href="files.php"><?php echo lang('filemanagement')?></a></li>
<?php

}

?>
<li><a href="plugins.php"><?php echo lang('pluginmanagement')?></a></li>
<?php

if (check_permission($userid, 'Modify Site Preferences'))
{

?>
<li><a href="siteprefs.php"><?php echo lang('siteprefs')?></a></li>
<?php

}

?>
<li><a href="adminlog.php"><?php echo lang('adminlog')?></a></li>
<li><a href="<?php

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

?>" target="_blank"><?php echo lang('showsite')?></a></li>
<li><a href="editprefs.php"><?php echo lang('userprefs')?></a></li>
<li><a href="logout.php"><?php echo lang('logout')?></a></li>
</ul> 
<?php

	$cmsmodules = $gCms->modules;

	$displaymodules = "<ul class=\"navlist\">";

	foreach ($cmsmodules as $key=>$value)
	{
		if (isset($cmsmodules[$key]['object']) 
			&& $cmsmodules[$key]['installed'] == true
			&& $cmsmodules[$key]['active'] == true
		)
		{
			if ($cmsmodules[$key]['object']->HasAdmin())
			{
				$displaymodules .= "<li><a href=\"moduleinterface.php?module=$key\">$key</a></li>";
			}
		}
	}

	if ($displaymodules != "<ul class=\"navlist\">") {
		echo "<div class=\"menutitle\">".lang('modules')."</div>";
		echo $displaymodules;
	}

?>
</ul><br />
</div>

<br />

</div> 
