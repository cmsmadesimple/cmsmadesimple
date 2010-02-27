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

function smarty_cms_function_title($params, &$smarty)
{
	global $gCms;
	$pageinfo = &$gCms->variables['pageinfo'];
	$config = &$gCms->GetConfig();
	$result = '';
	if (isset($pageinfo) && $pageinfo->content_id == -1)
	{
		#We've a custom error message...  set a current timestamp
		$result = "404 Error";
	}
	else
	{
		$result = cms_htmlentities($pageinfo->content_title);
		if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
		{
      if (version_compare(phpversion(),"5.3.0","<")) {
			  $result = ereg_replace("\{\/?php\}", "", $result);
      } else {
        $result = preg_replace("/\{\/?php\}/", "", $result);
      }
		}
	}

	if( isset($params['assign']) )
	{
	    $smarty =& $gCms->GetSmarty();
	    $smarty->assign($params['assign'],$result);
	    return;
        }
        return $result;
}

function smarty_cms_help_function_title() {
  echo lang('help_function_title');
}

function smarty_cms_about_function_title() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
