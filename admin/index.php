<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='main';
$CMS_ADMIN_TITLE='adminhome';

require_once("../include.php");

check_login();

include_once("header.php");
?>

<div class="DashboardCallout">

<?php

if (file_exists(dirname(dirname(__FILE__)) . '/install'))
{
	echo '<p>'.lang('installdirwarning').'</p>';
}

?>

</div> <!-- end DashboardCallout -->

<!--

<div class="DashboardCallout">

<p class="DashboardCalloutTitle"><?php echo lang('newsitems') ?></p>

</div> --> <!-- end DashboardCallout -->

<div class="MainMenu">

<div class="MainMenuItem">
<a href="topcontent.php"><?php echo lang('content') ?></a>
<span class="description"><?php echo lang('contentdescription')." ".lang('subitems') ?>:
<a href="listcontent.php"><?php echo lang('pages') ?></a><?php if ($htmlPerms)
	      { ?>, <?php }
if ($htmlPerms) { ?><a href="listhtmlblobs.php"><?php echo lang('htmlblobs') ?></a><?php } 
if (isset($sectionCount['content']) && $sectionCount['content'] > 0)
    {
    foreach($modulesBySection['content'] as $sectionModule)
        {
        echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>";
        }
    }
?>.</span>
</div>

<?php if ($filePerms) { ?>
<div class="MainMenuItem">
<a href="topfiles.php"><?php echo lang('files') ?></a>
<span class="description"><?php echo lang('filemanagerdescription')." ".lang('subitems')
    ?>: <a href="files.php"><?php echo lang('filemanager') ?></a>, <a href="imagefiles.php"><?php echo lang('imagemanager') ?></a><?php
if (isset($sectionCount['files']) && $sectionCount['files'] > 0)
    {
    foreach($modulesBySection['files'] as $sectionModule)
        {
        echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>";
        }
    }
?>.</span>
</div>
<?php } ?>

<?php  if ( $layoutPerms) { ?>
<div class="MainMenuItem">
<a href="toplayout.php"><?php echo lang('layout') ?></a>
<span class="description"><?php echo lang('layoutdescription')." ".lang('subitems') ?>:
<?php if ($templatePerms)
	{ ?>
    <a href="listtemplates.php"><?php echo lang('templates') ?></a><?php if ($cssPerms) { ?>, <?php }
    } if ($cssPerms || $cssAssocPerms)
    { ?><a href="listcss.php"><?php echo lang('stylesheets') ?></a><?php }
if (isset($sectionCount['layout']) && $sectionCount['layout'] > 0)
    {
    foreach($modulesBySection['layout'] as $sectionModule)
        {
        echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>";
        }
    }
?>.</span>
</div>
<?php } ?>

<?php if ($groupPerms || $userPerms || $groupMemberPerms || $groupPermPerms) { ?>
<div class="MainMenuItem">
<a href="topusers.php"><?php echo lang('usersgroups') ?></a>
<span class="description"><?php echo lang('usersgroupsdescription')." ".lang('subitems') ?>:
<?php if ($userPerms) { ?><a href="listusers.php"><?php echo lang('users') ?></a><?php if
($groupPerms || $groupMemberPerms || $groupPermPerms) {?>, <? }
} if ($groupPerms) { ?><a href="listgroups.php"><?php echo lang('groups') ?></a><?php if
($groupPermPerms || $groupMemberPerms) { ?>, <?php }
} if ($groupPermPerms) { ?><a href="changegroupperm.php"><?php echo lang('grouppermissions') ?></a><?php if
($groupMemberPerms) {?>,  <?php }
} if ($groupMemberPerms) { ?><a href="changegroupassign.php"><?php echo lang('groupassignments') ?></a><?php } 
if (isset($sectionCount['usersgroups']) && $sectionCount['usersgroups'] > 0)
    {
    foreach($modulesBySection['usersgroups'] as $sectionModule)
        {
        echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>";
        }
    }
?>.</span>
</div>
<?php } ?>

<div class="MainMenuItem">
<a href="topextensions.php"><?php echo lang('extensions') ?></a>
<span class="description"><?php echo lang('extensionsdescription')." ".lang('subitems') ?>:
<?php if ($modulePerms) {?><a href="listmodules.php"><?php echo lang('modules') ?></a>, <?php }
?><a href="listtags.php"><?php echo lang('tags') ?></a><?php
if ($codeBlockPerms) {?>, <a href="listusertags.php"><?php echo lang('usertags') ?></a><?php } ?>.
<?php
	# Any modules with an admin interface?
	$displaymodules = '';
    foreach($modulesBySection as $thisSectionModules)
        {
        foreach ($thisSectionModules as $sectionModule)
            {
            $displaymodules .= " <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>,";
            }
        }
	if ($displaymodules != '')
	{
		$displaymodules = substr($displaymodules, 0, strlen($displaymodules) - 2);
		echo ' '.lang('modules').':' . rtrim($displaymodules,",");
	}
?>
</span>
</div>

<div class="MainMenuItem">
<a href="editprefs.php"><?php echo lang('preferences') ?></a>
<span class="description"><?php echo lang('preferencedescription');
if (isset($sectionCount['preferences']) && $sectionCount['preferences'] > 0)
    {
    echo " ".lang('subitems').": ";
    foreach($modulesBySection['preferences'] as $sectionModule)
        {
        echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>";
        }
    }
?></span>
</div>
 
<div class="MainMenuItem">
<a href="topadmin.php"><?php echo lang('admin') ?></a>
<span class="description"><?php echo lang('admindescription')." ".lang('subitems') ?>: <?php if ($sitePrefPerms) {
?><a href="siteprefs.php"><?php echo lang('sitepreferences') ?></a>, <?php } ?><a href="adminlog.php"><?php echo lang('adminlog') ?></a><?php 
if (isset($sectionCount['admin']) && $sectionCount['admin'] > 0)
    {
    foreach($modulesBySection['admin'] as $sectionModule)
        {
        echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>";
        }
    }
?>.</span>
</div>

<div class="MainMenuItem">
<a href="../index.php"><?php echo lang('viewsite') ?></a>
</div>

<div class="MainMenuItem">
<a href="logout.php"><?php echo lang('logout') ?></a>
</div>

</div> <!-- end MainMenu -->

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
