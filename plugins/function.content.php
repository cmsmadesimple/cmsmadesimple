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

#WARNING!!!  This is a hack!!!
#This content tag doesn't actually do anything.  It's only here if for some strange reason
#the {content} tag doesn't get parsed out before hand, or in some strange instances when
#a content type doesn't properly handle multiple content blocks.  It returns nothing!!!
#WARNING!!!  This is a hack!!!

function smarty_cms_function_content() {
    return '';
}

function smarty_cms_help_function_content() {
	?>
	<h3>What does this do?</h3>
	<p>Nothing.  It gets used if for some reason a content type doesn't handle multiple content blocks properly.  Ignore this!!!</p>
	<?php
}

function smarty_cms_about_function_content() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>
