<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

function smarty_function_metadata($params, &$template)
{
  $smarty = $template->smarty;
	$gCms = cmsms();
	$config = $gCms->GetConfig();
	$content_obj = $gCms->variables['content_obj'];

	$result = '';	

	$showbase = true;
	
	#Show a base tag unless showbase is false in config.php
	#It really can't hinder, only help.
	if( isset($config['showbase']))  $showbase = $config['showbase'];

        # but allow a parameter to override it.
	if (isset($params['showbase']))
	{
		if ($params['showbase'] == 'false')
		{
			$showbase = false;
		}
	}

	if ($showbase)
	{
	  $base = $config['root_url'];
	  if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')
	  {
	    $base = $config['ssl_url'];
	  }

	  $result .= "\n<base href=\"".$base."/\" />\n";
	}

	$result .= get_site_preference('metadata', '');

	if (is_object($content_obj) && $content_obj->Metadata() != '')
	{
	  $result .= "\n" . $content_obj->Metadata();
	}

	if ((!strpos($result,$smarty->left_delimiter) === false) and (!strpos($result,$smarty->right_delimiter) === false))
	{
	  $result = $smarty->fetch('string:'.$result);
	}
	if( isset($params['assign']) ){
		$smarty->assign(trim($params['assign']),$result);
		return;
	}
	return $result;
}

function smarty_cms_help_function_metadata() {
  echo lang('help_function_metadata');
}

function smarty_cms_about_function_metadata() {
	?>
	<p>Author: Ted Kulp&lt;ted@cmsmadesimple.org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
