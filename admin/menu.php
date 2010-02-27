<div id="navcontainer">

<?php
die('this file is not used');
$userid = get_userid();
global $gCms;
$db =& $gCms->GetDb();
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

?>

<div id="welcome">
<?php if (isset($_SESSION['cms_admin_username'])) echo lang('welcomemsg', array($_SESSION['cms_admin_username']))?>
</div>

<div>

<a href="listcontent.php"><?php echo lang('contentmanagement')?></a>

<?php

if (check_permission($userid, 'Add Templates') || check_permission($userid, 'Remove Templates') || check_permission($userid, 'Modify Templates'))
{

?>
<a href="listtemplates.php"><?php echo lang('templatemanagement')?></a>
<?php

}

if (check_permission($userid, 'Add Stylesheets') || check_permission($userid, 'Remove Stylesheets') || check_permission($userid, 'Modify Stylesheets'))
{

?>
<a href="listcss.php"><?php echo lang('cssmanagement')?></a>
<?php

}

?>
<a href="listhtmlblobs.php"><?php echo lang('blobmanagement')?></a>
<a href="listusers.php"><?php echo lang('usermanagement')?></a>
<?php

if (check_permission($userid, 'Add Groups') || check_permission($userid, 'Remove Groups') || check_permission($userid, 'Modify Groups') || check_permission($userid, 'Modify Groups Assignments') || check_permission($userid, 'Modify Permissions'))
{

?>
<a href="listgroups.php"><?php echo lang('groupmanagement')?></a>
<?php

}
/*
if (check_permission($userid, 'Modify Files'))
{

?
<a href="files.php"><?php echo lang('filemanagement')?</a>
?php

}
*/
?>
<a href="plugins.php"><?php echo lang('pluginmanagement')?></a>
<?php

if (check_permission($userid, 'Modify Site Preferences'))
{

?>
<a href="siteprefs.php"><?php echo lang('siteprefs')?></a>
<?php

}

?>
<a href="systeminfo.php"><?php echo lang('systeminfo')?></a>
<a href="adminlog.php"><?php echo lang('adminlog')?></a>
<a href="adminlog.php"><?php echo lang('adminlog')?></a>
<a href="<?php

if (isset($config['url_rewriting']) && $config['url_rewriting'] == 'mod_rewrite')
{
	$query = "SELECT content_alias, content_id FROM " . cms_db_prefix() . "content WHERE default_content = '1'";
	$result = $db->query($query);
	if ($result && $result->RecordCount() > 0)
	{
		$row = $result->FetchRow();
		if ($row['content_alias'] != '')
		{
			echo "../" . $row['content_alias'] . $config["page_extension"];
		}
		else
		{
			echo "../" . $row['content_id'] . $config["page_extension"];
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

	foreach ($cmsmodules as $key=>$value)
	{
		if (isset($cmsmodules[$key]['object']) 
			&& $cmsmodules[$key]['installed'] == true
			&& $cmsmodules[$key]['active'] == true
			&& $cmsmodules[$key]['object']->HasAdmin()
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
