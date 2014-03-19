<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_function_page_image($params, &$template)
{
	$smarty = $template->smarty;
	$result = '';
	$propname = 'image';

	if( isset($params['thumbnail']) )
    {
		$propname = 'thumbnail';
    }

	$contentobj = cms_utils::get_current_content();
	if( is_object($contentobj) )
    {
		$result = $contentobj->GetPropertyValue($propname);
		if( $result == -1 ) $result = '';
    }

	if( isset($params['assign']) )
    {
		$smarty->assign($params['assign'],$result);
		return;
    }
	return $result;
}

function smarty_cms_about_function_page_image() {
?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>Fix for CMSMS 1.9</li>
	</ul>
<?php
}
?>