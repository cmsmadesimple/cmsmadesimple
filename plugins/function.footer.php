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

function smarty_function_footer() {
    global $CMS_VERSION;
    echo '<div class="bottom">'."\n";
    echo 'Site powered by <a href="http://cmsmadesimple.org">CMS Made Simple</a> Version: '."$CMS_VERSION<br />\n";
    echo 'Think you\'ve found a bug? <a href="http://trac.wishy.org/cms">Submit</a> it'."\n";
    echo "</div>\n";
} ## smarty_function_show_footer

?>
