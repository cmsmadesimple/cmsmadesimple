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

<a href="index.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='main')?' id="TopMenuSelected"':'') ?>><?php echo lang('main')?></a>
<a href="topcontent.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='content')?' id="TopMenuSelected"':'') ?>><?php echo lang('content')?></a>
<a href="listcontent.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='pages')?' id="TopMenuSelected"':'') ?>><?php echo lang('pages')?></a>
<?php if ($filePerms)
   { ?><a href="topfiles.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='files')?' id="TopMenuSelected"':'') ?>><?php echo lang('files')?></a><?php
   }
if ($layoutPerms)
   { ?><a href="toplayout.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='layout')?' id="TopMenuSelected"':'') ?>><?php echo lang('layout')?></a><?php
   }
if ($usersGroupsPerms)
   { ?><a href="topusers.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='usersgroups')?' id="TopMenuSelected"':'') ?>><?php echo lang('usersgroups')?></a><?php
   }
if ($extensionsPerms)
   { ?><a href="topextensions.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='extensions')?' id="TopMenuSelected"':'') ?>><?php echo lang('extensions')?></a><?php
   } ?>
<a href="editprefs.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='preferences')?' id="TopMenuSelected"':'') ?>><?php echo lang('preferences')?></a>
<?php if ($adminPerms)
    { ?><a href="topadmin.php"<?php echo ((isset($CMS_TOP_MENU) && $CMS_TOP_MENU=='admin')?' id="TopMenuSelected"':'') ?>><?php echo lang('admin')?></a><?php
    } ?>
<a href="../index.php"><?php echo lang('viewsite')?></a>
<a href="logout.php"><?php echo lang('logout')?></a>
<a href="makebookmark.php?title=<?php echo urlencode($pagetitle) ?>">[+]</a>

</div> <!-- end TopMenu -->
