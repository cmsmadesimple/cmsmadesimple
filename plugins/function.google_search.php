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

function smarty_cms_function_google_search($params, &$smarty) {
	$domain = $_SERVER['SERVER_NAME'];
	$buttonText = 'Search Site';
	if (!empty($params['domain']))
		$domain = $params['domain'];
	if (!empty($params['buttonText'])) 
		$buttonText = $params['buttonText'];

	return '<form method="get" action="http://www.google.com/search">
	<div>
	<input type="hidden" name="ie" value="utf-8" />
	<input type="hidden" name="oe" value="utf-8" />
	<input type="hidden" name="sitesearch" value="'.$domain.'" />
	<input type="text" id="textSearch" name="q" maxlength="255" value="" />
	<input type="submit" id="buttonSearch" value="'.$buttonText.'" />
	</div>
	</form>';

}

function smarty_cms_help_function_google_search() {
  echo lang('help_function_google_search');
}

function smarty_cms_about_function_google_search() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@classicwebdevelopment.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>
