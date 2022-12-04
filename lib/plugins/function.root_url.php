<?php
#CMS - CMS Made Simple
#(c)2004-2011 by Ted Kulp (wishy@users.sf.net)
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

function smarty_function_root_url($params, $smarty)
{
    $str = CMS_ROOT_URL;

	if( !isset($params['autossl']) || $params['autossl'] != 0 )	{
        $config = CmsApp::get_instance()->GetConfig();
		$str = $config->smart_root_url();
	}

    if( isset($params['assign']) ) {
		$smarty->assign(trim($params['assign']),$str);
		return;
    }

	return $str;
}

function smarty_cms_about_function_root_url() {
?>
	<p>Author: Ted Kulp&lt;ted@cmsmadesimple.org&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>Initial Version</li>
		<li>Added assign parameter for CMSMS 1.10</li>
		<li>Added autossl parameter for CMSMS 1.10</li>
    </ul>
<?php
}
?>