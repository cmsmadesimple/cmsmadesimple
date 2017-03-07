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

function smarty_function_last_modified_by($params, &$smarty)
{
    $gCms = CmsApp::get_instance();
    $content_obj = $gCms->get_content_object();
	$id = "";

	if (isset($content_obj) && $content_obj->LastModifiedBy() > -1)	{
		$id = $content_obj->LastModifiedBy();
	} else {
		return;
	}

    $format = "id";
	if(!empty($params['format'])) $format = $params['format'];
    $userops = UserOperations::get_instance();
    $thisuser = $userops->LoadUserByID($id);
    if( !$thisuser ) return; // could not find user record.

    $output = '';
    if($format==="id") {
        $output = $id;
    } else if ($format==="username") {
        $output = cms_htmlentities($thisuser->username);
    } else if ($format==="fullname") {
        $output = cms_htmlentities($thisuser->firstname ." ". $thisuser->lastname);
    }

    if( isset($params['assign']) ) {
        $smarty->assign(trim($params['assign']),$output);
        return;
    }
    return $output;
}

function smarty_cms_about_function_last_modified_by() {
?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>

	<ul>Change History:</p>
		<ul>
			<li>Added assign parameter (Calguy)</li>
        </ul>
	</p>
<?php
}
?>