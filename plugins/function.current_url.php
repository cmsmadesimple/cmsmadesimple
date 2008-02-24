<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

function smarty_cms_function_current_url($params, &$smarty)
{
	return CmsRequest::get_requested_uri();
}

function smarty_cms_help_function_current_url()
{
	?>
	<h3>What does this do?</h3>
	<p>Prints the current requested url.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{current_url}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
		None
	</p>
	<?php
}

function smarty_cms_about_function_current_url()
{
	?>
	<p>Author: Ted Kulp&lt;ted (at) cmsmadesimple (dot) org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
