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
$CMS_TOP_MENU=1;

require_once("../include.php");

check_login();

include_once("header.php");

?>

<div class="DashboardCallout">

<?php

if (file_exists(dirname(dirname(__FILE__)) . '/install'))
{
	echo '<p><em><strong>Warning:</strong></em> install directory still exists.  Please remove it completely.</p>';
}

?>

</div> <!-- end DashboardCallout -->

<!-- 

<div class="DashboardCallout">

<p class="DashboardCalloutTitle">News Items</p>

</div> --> <!-- end DashboardCallout -->

<div class="MainMenu">

<div class="MainMenuItem">
<a href="topcontent.php">Content</a>
<span class="description">This is where we add and edit content. Subitems: <a href="listcontent.php">content</a>, <a href="listhtmlblobs.php">html blobs</a></span>
</div>

<div class="MainMenuItem">
<a href="topfiles.php">Files</a>
<span class="description">This is where we add and remove files. Subitems: <a href="files.php">file manager</a>, <a href="imagefiles.php">image manager</a></span>
</div>

<div class="MainMenuItem">
<a href="toplayout.php">Layout</a>
<span class="description">Site layout options. Subitems: <a href="listtemplates.php">templates</a>, <a href="listhtmlblobs.php">html blobs</a></span>
</div>

<div class="MainMenuItem">
<a href="topusers.php">Users/Groups</a>
<span class="description">User and Group related items. Subitems: <a href="listusers.php">users</a>, <a href="listgroups.php">groups</a>, <a href="#">permissions</a></span>
</div>

<div class="MainMenuItem">
<a href="topextensions.php">Extensions</a>
<span class="description">Modules, tags and other assorted fun. Subitems: <a href="listmodules.php">modules</a>, <a href="listtags.php">tags</a>, <a href="listusertags.php">user defined tags</a></span>
</div>

<div class="MainMenuItem">
<a href="editprefs.php">Preferences</a>
<span class="description">This is where you will find user preferences.</span>
</div>

<div class="MainMenuItem">
<a href="topadmin.php">Site Admin</a>
<span class="description">Site Administration functions. Subitems: <a href="siteprefs.php">site preferences</a>, <a href="adminlog.php">admin log</a></span>
</div>

<div class="MainMenuItem">
<a href="../index.php">View Site</a>
</div>

<div class="MainMenuItem">
<a href="logout.php">Logout</a>
</div>

</div> <!-- end MainMenu -->

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
