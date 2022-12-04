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

function smarty_function_metadata($params, $smarty)
{
    $gCms = CmsApp::get_instance();
	$config = \cms_config::get_instance();
	$content_obj = $gCms->get_content_object();

	$result = '';
	$showbase = true;

	// Show a base tag unless showbase is false in config.php
    // It really can't hinder, only help
	if( isset($config['showbase']))  $showbase = $config['showbase'];

    // But allow a parameter to override it
	if (isset($params['showbase']))	{
		if ($params['showbase'] == false || $params['showbase'] == 'false')	$showbase = false;
	}

    \CMSMS\HookManager::do_hook('metadata_prerender', [ 'content_id'=>$content_obj->Id(), 'showbase'=>&$showbase, 'html'=>&$result ]);

	if ($showbase)	{
        $base = CMS_ROOT_URL;
        if( $gCms->is_https_request() ) $base = $config['ssl_url'];
		$result .= "\n<base href=\"".$base."/\" />\n";
	}

	$result .= get_site_preference('metadata', '');

	if (is_object($content_obj) && $content_obj->Metadata() != '') $result .= "\n" . $content_obj->Metadata();

	if ((!strpos($result,$smarty->left_delimiter) === false) and (!strpos($result,$smarty->right_delimiter) === false))	{
        $result = $smarty->fetch('string:'.$result);
    }

    \CMSMS\HookManager::do_hook('metadata_postrender', [ 'content_id'=>$content_obj->Id(), 'html'=>&$result ]);
	if( isset($params['assign']) )	{
		$smarty->assign(trim($params['assign']),$result);
		return;
	}
	return $result;
}

function smarty_cms_about_function_metadata() {
?>
	<p>Author: Ted Kulp&lt;ted@cmsmadesimple.org&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>None</li>
	</ul>
<?php
}
?>