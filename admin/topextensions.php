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
$CMS_TOP_MENU=6;

require_once("../include.php");

check_login();

include_once("header.php");

?>

<div class="MainMenu">

<div class="MainMenuItem">
<a href="listmodules.php">Modules</a>
<span class="description">Modules majorly extend CMS Made Simple to provide all kinds of functionality.</span>
</div>

<div class="MainMenuItem">
<a href="listtags.php">Tags</a>
<span class="description">Tags are little bits of functionality that can be added to your content and/or templates.</span>
</div>

<div class="MainMenuItem">
<a href="listusertags.php">User Defined Tags</a>
<span class="description">Tags that you can create and modify yourself to perform specific tasks, right from your browser.</span>
</div>

<?php
	# Is there any modules with an admin interface?
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
			echo '<div class="MainMenuItem">';
			echo '<a href="moduleinterface.php?module='.$key.'">'.$key.'</a>';
			if ($cmsmodules[$key]['object']->GetAdminDescription() != '')
			{
				echo '<span class="description">'.$cmsmodules[$key]['object']->GetAdminDescription().'</span>';
			}
			echo '</div>';
		}
	}
?>

<div class="MainMenuItem">
<a href="index.php">Main Menu</a>
</div>

</div> <!-- end MainMenu -->

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
