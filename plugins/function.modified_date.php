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

function smarty_cms_function_modified_date($params, &$smarty)
{
	global $gCms;
	$pageinfo = $gCms->variables['pageinfo'];

	if(empty($params['format']))
	{
		$format = "%x %X";
	}
	else
	{
		$format = $params['format'];
	}

	$str = '';
	if (isset($pageinfo) && isset($pageinfo->content_modified_date) && $pageinfo->content_modified_date > -1)
	{
		$str = htmlentities(strftime($format, $pageinfo->content_modified_date));
	}

	if( isset($params['assign']) )
	  {
	    $smarty = $gCms->GetSmarty();
	    $smarty->assign($params['assign'],$str);
	    return;
	  }
	return $str;
}

function smarty_cms_help_function_modified_date()
{
  echo lang('help_function_modified_date');
}

function smarty_cms_about_function_modified_date() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
        <ul>
          <li>v1.1 - (calguy1000) Added assign param</li>
        </ul>
	None
	</p>
	<?php
}
?>
