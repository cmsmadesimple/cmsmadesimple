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

function smarty_cms_function_footer() {
    global $CMS_VERSION;
    $result = "";
    $result .= '<div class="bottom">'."\n";
    $result .= 'Site powered by <a href="http://cmsmadesimple.org">CMS Made Simple</a> Version: '."$CMS_VERSION<br />\n";
    $result .= 'Think you\'ve found a bug? <a href="http://bugs.cmsmadesimple.org">Submit</a> it'."\n";
    $result .= "</div>\n";
    return $result;
} ## smarty_function_show_footer

function smarty_cms_help_function_footer()
{
	?>
	<h3>What does this do?</h3>
	<p>Prints out the current version of CMS, and also a link to the bug reporting system.  It's used in the default template and is a nice example of how a plugin works.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{footer}</code></p>
	<?php
}

function smarty_cms_about_function_footer()
{
	?>
	<p>Author: Ted Kulp &lt;<a href="mailto:wishy@cmsmadesimple.org">wishy@cmsmadesimple.org</a>&gt;</p>
	<p>Version: 1.0</p>
	<?php
}

?>
