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
#$Id: index.php 1307 2005-02-16 03:23:04Z wishy $

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU=3;

require_once("../include.php");

check_login();

include_once("header.php");

?>

<div class="MainMenu">

<?php if ($filePerms) { ?>
<div class="MainMenuItem">
<a href="files.php">File Manager</a>
<span class="description">Upload files and such.</span>
</div>
<?php } ?>

<div class="MainMenuItem">
<a href="imagefiles.php">Image Manager</a>
<span class="description">Upload/edit and remove images.</span>
</div>

<div class="MainMenuItem">
<a href="index.php">Main Menu</a>
</div>

</div> <!-- end MainMenu -->

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
