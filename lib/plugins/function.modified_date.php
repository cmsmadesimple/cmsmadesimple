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

function smarty_function_modified_date($params, $smarty)
{
	$content_obj = CmsApp::get_instance()->get_content_object();
	if( is_object($content_obj) && $content_obj->GetModifiedDate() > -1 ) {
		$time = $content_obj->GetModifiedDate();

		$format = "%x %X";
		if(!empty($params['format'])) $format = $params['format'];

		if( strpos($format, '%') !== false ) {
			require_once __DIR__.DIRECTORY_SEPARATOR.'modifier.localedate_format.php';
			$str = smarty_modifier_localedate_format($time, $format);
		}
		else {
			$str = date($format, $time);
		}
		$str = cms_htmlentities($str);

		if( isset($params['assign']) ) {
			$smarty->assign($params['assign'],$str);
			return;
		}
		return $str;
	}
}

function smarty_cms_about_function_modified_date() {
?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>

	<p>Change History:</p>
		<ul>
			<li>Added assign paramater (calguy1000)</li>
		</ul>
<?php
}
?>
