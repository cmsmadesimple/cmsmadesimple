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

function smarty_cms_function_var_dump($params, &$smarty)
{
	if (isset($params['var']))
		var_dump($params['var']);
}

function smarty_cms_help_function_var_dump() {
	?>
	<h3>What does this do?</h3>
	<p>This tag is used to dump a smarty param in a way similar to var_dump in php.</p>
	<h3>How do I use it?</h3>
	<p>This is just a basic tag plugin.  You would insert it into your template or page like so: <code>{var_dump var='myval'}</code>
	<?php
}

function smarty_cms_about_function_var_dump() {
	?>
	<p>Author: Ted Kulp&lt;ted@cmsmadesimple.org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>
