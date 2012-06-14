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

function smarty_function_created_date($params, &$template)
{
  $smarty = $template->smarty;
	$gCms = cmsms();
	$content_obj = $gCms->variables['content_obj'];

	if(empty($params['format']))
	{
		$format = "%x %X";
	}
	else
	{
		$format = $params['format'];
	}

	if (is_object($content_obj) && $content_obj->GetCreationDate() > -1)
	{
	  $time = $content_obj->GetCreationDate();
	  $str = cms_htmlentities(strftime($format, $time));;

	  if( isset($params['assign']) )
	    {
	      $smarty->assign(trim($params['assign']),$str);
	      return;
	    }
	  return $str;
	}

}

function smarty_cms_help_function_created_date()
{
  echo lang('help_function_created_date');
}

function smarty_cms_about_function_created_date() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	</p>
	<?php
}
?>
