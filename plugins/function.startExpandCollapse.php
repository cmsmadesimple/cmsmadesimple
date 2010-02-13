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

function smarty_cms_function_startExpandCollapse($params, &$smarty)
{
	static $firstExpandCollapse = true;//only gets set one time per page
	
	global $gCms;
	$config =& $gCms->GetConfig();

	if (!empty($params['id']) && !empty($params['title'])) {
		$id = $params['id'];
		$title = $params['title'];
	}
	else {
		echo 'Error: The expand/collapse plugin requires that both parameters (id,title) are used.';
		return;
	}

	if ($firstExpandCollapse) {
		echo '<script type="text/javascript" language="javascript" src="'.$config['root_url'].'/lib/helparea.js"></script>';
		$firstExpandCollapse = false;
	}
	$url = cms_htmlentities($_SERVER['REQUEST_URI']);
	$url = str_replace('&amp;','***',$url);
	$url = str_replace('&', '&amp;', $url);
	$url = str_replace('***','&amp;',$url);
	//$url = str_replace('&', '&amp;',  $_SERVER['REQUEST_URI']);
	echo '<a href="'. $url .'#'. $id .'" onclick="expandcontent(\''.$id.'\')" style="cursor:hand; cursor:pointer">'.$title.'</a><br />
	<div id="'.$id.'" class="expand">';
}

function smarty_cms_help_function_startExpandCollapse() {
  echo lang('help_function_startexpandcollapse');
}

function smarty_cms_about_function_startExpandCollapse() {
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
