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

function smarty_cms_function_metadata($params, &$smarty)
{
	global $gCms;
	$config = $gCms->GetConfig();
	$content_obj =& $gCms->variables['content_obj'];

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
	  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
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
		$smarty->_compile_source('metadata template', $result, $_compiled);
		@ob_start();
		$smarty->_eval('?>' . $_compiled);
		$result = @ob_get_contents();
		@ob_end_clean();
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
