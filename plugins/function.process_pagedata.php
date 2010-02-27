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

function smarty_cms_function_process_pagedata($params,&$smarty)
{
  global $gCms;
  $manager =& $gCms->GetHierarchyManager();
  $node =& $manager->getNodeById($gCms->variables['content_id']);
  if( !isset($node) || $node === FALSE ) return;
  $content =& $node->Getcontent();

  if ( empty($content)) return;

  $tpl = $content->GetPropertyValue('pagedata','');
  if( empty($tpl) ) return;

  $smarty->_compile_source('preprocess template', $tpl, $_compiled);
  @ob_start();
  $smarty->_eval('?>' . $_compiled);
  $result = @ob_get_contents();
  @ob_end_clean();

  return $result;
}

function smarty_cms_help_function_process_pagedata() {
  echo lang('help_function_process_pagedata');
}

function smarty_cms_about_function_process_pagedata() {
  ?>
  <p>Author: Robert Campbell&lt;calguy1000@cmsmadesimple.org&gt;</p>
  <p>Version: 1.0</p>
  <p>
  Change History:<br/>
  None
  </p>
  <?php
}
#
# EOF
#
?>
