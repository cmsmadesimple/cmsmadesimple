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
$CMS_TOP_MENU='extensions';
$CMS_ADMIN_TITLE='extensions';

require_once("../include.php");

check_login();

include_once("header.php");

?>

<div class="MainMenu">

<?php if ($modulePerms) { ?>
<div class="MainMenuItem">
<a href="listmodules.php"><?php echo lang('modules') ?></a>
<span class="description"><?php echo lang('moduledescription') ?></span>
</div>
<?php } ?>

<div class="MainMenuItem">
<a href="listtags.php"><?php echo lang('tags') ?></a>
<span class="description"><?php echo lang('tagdescription') ?></span>
</div>

<?php if ($codeBlockPerms) { ?>
<div class="MainMenuItem">
<a href="listusertags.php"><?php echo lang('usertags') ?></a>
<span class="description"><?php echo lang('usertagdescription') ?></span>
</div>
<?php }

if (isset($sectionCount['extensions']) && $sectionCount['extensions'] > 0)
    {
    foreach($modulesBySection['extensions'] as $sectionModule)
        {
        echo "<div class=\"MainMenuItem\">\n";
        echo "<a href=\"moduleinterface.php?module=".$sectionModule['key']."\">".$sectionModule['key']."</a>\n";
        if ($sectionModule['description'] != '')
            {
            echo '<span class="description">'.$sectionModule['description'].'</span>';
            }
        echo "</div>\n";
        }
    }
?>
<div class="MainMenuItem">
<a href="index.php"><?php echo lang('mainmenu') ?></a>
</div>

</div> <!-- end MainMenu -->

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
