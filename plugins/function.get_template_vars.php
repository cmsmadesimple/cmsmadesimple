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

function smarty_cms_function_get_template_vars($params, &$smarty)
{
	global $gCms;
	
	$tpl_vars = $gCms->smarty->get_template_vars();
	$str = '';
	foreach( $tpl_vars as $key => $value )
	  {
	    if( !is_object($value) )
             {
	       $str .= "$key = $value<br/>";
             }
            else
             {
               $str .= "$key = Object<br/>";
             }
	  }
	return $str;
}

function smarty_cms_help_function_get_template_vars() {
	?>
	<h3>What does this do?</h3>
	<p>Dumps all the known smarty variables into your page</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{get_template_vars}</code></p>
	<h3>What parameters does it take?</h3>
											  <p>None at this time</p>
	<?php
}

function smarty_cms_about_function_get_template_vars() {
	?>
	<p>Author: Robert Campbell&lt;calguy1000@hotmail.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
