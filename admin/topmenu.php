<?php
#CMS - CMS Made Simple
#(c)2004-5 by Ted Kulp (wishy@cmsmadesimple.org)
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
?>

<div id="TopMenu">

<a href="index.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==1)?' id="TopMenuSelected"':'') ?>>Main</a>
<a href="topcontent.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==2)?' id="TopMenuSelected"':'') ?>>Content</a>
<a href="listcontent.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==9)?' id="TopMenuSelected"':'') ?>>Pages</a>
<?php if ($filePerms)
   { ?><a href="topfiles.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==3)?' id="TopMenuSelected"':'') ?>>Files</a><?php
   }
if ($templatePerms || $cssPerms)
   { ?><a href="toplayout.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==4)?' id="TopMenuSelected"':'') ?>>Layout</a><?php
   }
if ($userPerms || $groupPerms || $groupPermPerms || $groupMemberPerms)
   { ?><a href="topusers.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==5)?' id="TopMenuSelected"':'') ?>>Users/Groups</a><?php
   } ?>
<a href="topextensions.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==6)?' id="TopMenuSelected"':'') ?>>Extensions</a>
<a href="editprefs.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==7)?' id="TopMenuSelected"':'') ?>>Preferences</a>
<a href="topadmin.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU==8)?' id="TopMenuSelected"':'') ?>>Site Admin</a>
<a href="../index.php">View Site</a>
<a href="logout.php">Logout</a>
<a href="makebookmark.php">[+]</a>

</div> <!-- end TopMenu -->
