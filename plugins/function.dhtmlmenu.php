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

function smarty_cms_function_dhtmlmenu($params, &$smarty)
{
	return "The dhtmlmenu plugin is deprecated.  Replace it with the PHPLayers module ({cms_module module='phplayers'}).";
}

function smarty_cms_help_function_dhtmlmenu()
{
	?>
	<h3>What does this do?</h3>
	<p>Nothing.  It is now deprecated.  Use the PHPLayers module for similar functionality.</p>
	<?php
}

function smarty_cms_about_function_dhtmlmenu()
{
	?>
	<p>Author: Julien Lancien&lt;calexico@cmsmadesimple.org&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
	1.1 - Deprecated in favor of the PHPLayers module.<br />
	1.0 - Initial Version<br />
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
