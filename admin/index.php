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
$CMS_ADMIN_TITLE='mainmenu';
$CMS_EXCLUDE_FROM_RECENT=1;

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

<div class="MainMenu">

<div class="MainMenuItem">
<a href="topcontent.php"><?php echo lang('content') ?></a>
<span class="description"><?php echo lang('contentdescription')." ".lang('subitems') ?>:
<a href="listcontent.php"><?php echo lang('pages') ?></a><?php if ($themeObject->HasPerm('htmlPerms'))
	      { ?>, <?php }
if ($themeObject->HasPerm('htmlPerms')) { ?><a href="listhtmlblobs.php"><?php echo lang('htmlblobs') ?></a><?php }
$themeObject->ListSectionModules('content');
?>.</span>
</div>

<?php if ($themeObject->HasPerm('filePerms')) { ?>
<div class="MainMenuItem">
<a href="topfiles.php"><?php echo lang('files') ?></a>
<span class="description"><?php echo lang('filemanagerdescription')." ".lang('subitems')
    ?>: <a href="files.php"><?php echo lang('filemanager') ?></a>, <a href="imagefiles.php"><?php echo lang('imagemanager') ?></a><?php
$themeObject->ListSectionModules('files');
?>.</span>
</div>
<?php } ?>

<?php  if ( $themeObject->HasPerm('layoutPerms')) { ?>
<div class="MainMenuItem">
<a href="toplayout.php"><?php echo lang('layout') ?></a>
<span class="description"><?php echo lang('layoutdescription')." ".lang('subitems') ?>:
<?php if ($themeObject->HasPerm('templatePerms'))
	{ ?>
    <a href="listtemplates.php"><?php echo lang('templates') ?></a><?php
        if ($themeObject->HasPerm('cssPerms')) { ?>, <?php }
        } if ($themeObject->HasPerm('cssPerms') || $themeObject->HasPerm('cssAssocPerms'))
    { ?><a href="listcss.php"><?php echo lang('stylesheets') ?></a><?php }
$themeObject->ListSectionModules('layout');
?>.</span>
</div>
<?php } ?>

<?php if ($themeObject->HasPerm('groupPerms') || $themeObject->HasPerm('userPerms') ||
    $themeObject->HasPerm('groupMemberPerms') || $themeObject->HasPerm('groupPermPerms')) { ?>
<div class="MainMenuItem">
<a href="topusers.php"><?php echo lang('usersgroups') ?></a>
<span class="description"><?php echo lang('usersgroupsdescription')." ".lang('subitems') ?>:
<?php if ($themeObject->HasPerm('userPerms')) { ?><a href="listusers.php"><?php echo lang('users') ?></a><?php if
($themeObject->HasPerm('groupPerms') || $themeObject->HasPerm('groupMemberPerms') ||
$themeObject->HasPerm('groupPermPerms')) {?>, <? }
} if ($themeObject->HasPerm('groupPerms')) { ?><a href="listgroups.php"><?php echo lang('groups') ?></a><?php if
($themeObject->HasPerm('groupPermPerms') || $themeObject->HasPerm('groupMemberPerms')) { ?>, <?php }
} if ($themeObject->HasPerm('groupPermPerms')) { ?><a href="changegroupperm.php"><?php echo lang('grouppermissions') ?></a><?php if
($themeObject->HasPerm('groupMemberPerms')) {?>,  <?php }
} if ($themeObject->HasPerm('groupMemberPerms')) { ?><a href="changegroupassign.php"><?php echo lang('groupassignments') ?></a><?php }
$themeObject->ListSectionModules('usersgroups');
?>.</span>
</div>
<?php } ?>

<div class="MainMenuItem">
<a href="topextensions.php"><?php echo lang('extensions') ?></a>
<span class="description"><?php echo lang('extensionsdescription')." ".lang('subitems') ?>:
<?php if ($themeObject->HasPerm('modulePerms')) {?><a href="listmodules.php"><?php echo lang('modules') ?></a>, <?php }
?><a href="listtags.php"><?php echo lang('tags') ?></a><?php
if ($themeObject->HasPerm('codeBlockPerms')) {?>, <a href="listusertags.php"><?php echo lang('usertags') ?></a><?php }
$themeObject->ListSectionModules('extensions');
?>.</span>
</div>

<div class="MainMenuItem">
<a href="editprefs.php"><?php echo lang('preferences') ?></a>
<span class="description"><?php echo lang('preferencedescription');
$themeObject->ListSectionModules('preferences', true);
?></span>
</div>
 
<div class="MainMenuItem">
<a href="topadmin.php"><?php echo lang('admin') ?></a>
<span class="description"><?php echo lang('admindescription')." ".lang('subitems') ?>: <?php if ($themeObject->HasPerm('sitePrefPerms')) {
?><a href="siteprefs.php"><?php echo lang('sitepreferences') ?></a>, <?php } ?><a href="adminlog.php"><?php echo lang('adminlog') ?></a><?php 
$themeObject->ListSectionModules('admin');
?>.</span>
</div>

<div class="MainMenuItem">
<a href="../index.php" target="_blank"><?php echo lang('viewsite') ?></a>
</div>

<div class="MainMenuItem">
<a href="logout.php"><?php echo lang('logout') ?></a>
</div>

</div> <!-- end MainMenu -->

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
