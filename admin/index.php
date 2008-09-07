<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
$gCms = cmsms();
$db = cms_db();

include_once("header.php");
// Display a warning if CMSMS needs upgrading
$current_version = $CMS_SCHEMA_VERSION;
$query = "SELECT version from ".cms_db_prefix()."version";
$row = $db->GetRow($query);
if ($row)
{
	$current_version = $row["version"];
}
if ($current_version < $CMS_SCHEMA_VERSION)
{
	echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror"><em><strong>Warning:</strong></em> CMSMS is in need of an upgrade.</p><p>You are now running schema version '.$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.'.</p><p>Please click the following link: <a href="'.$config['root_url'].'/install/upgrade.php">Start upgrade process</a>.</p></div></div>';
}

// Display a warning if CMSMS needs upgrading
$current_version = $CMS_SCHEMA_VERSION;
$query = "SELECT version from ".cms_db_prefix()."version";
$row = $db->GetRow($query);
if ($row)
{
	$current_version = $row["version"];
}
if ($current_version < $CMS_SCHEMA_VERSION)
{
	echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror"><em><strong>Warning:</strong></em> CMSMS is in need of an upgrade.</p><p>You are now running schema version '.$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.'.</p><p>Please click the following link: <a href="'.$config['root_url'].'/install/upgrade.php">Start upgrade process</a>.</p></div></div>';
}
$themeObject->display_all_section_pages();
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
