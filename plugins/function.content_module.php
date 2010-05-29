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

function smarty_cms_function_content_module($params, &$smarty)
{
  $result = '';
  $key = '';

  if( isset($params['block']) ) {
    $block = $params['block'];
  }
  global $gCms;
  $pageinfo = &$gCms->variables['pageinfo'];
  $manager =& $gCms->GetHierarchyManager();
  $node =& $manager->sureGetNodeById($pageinfo->content_id);
  if(is_object($node))
    {
      $contentobj =& $node->GetContent();
      if( is_object($contentobj) )
	{
	  $result = $contentobj->GetPropertyValue($block);
          if( $result == -1 ) $result = '';
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
function smarty_cms_help_function_content_module() {
  echo lang('help_function_content_module');
}

function smarty_cms_about_function_content_module() {
	?>
	<p>Author: Robert Campbell&lt;calguy1000@cmsmadesimple.org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
