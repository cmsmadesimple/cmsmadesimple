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

function smarty_function_last_modified_by($params, &$template) 
{
  $smarty = $template->smarty;
        $gCms = cmsms();
	$content_obj = $gCms->variables['content_obj'];

        $id = "";
 
	if (isset($content_obj) && $content_obj->LastModifiedBy() > -1)
	{
	  $id = $content_obj->LastModifiedBy();
	} else {
	  return "";
        }

	if(empty($params['format']))
	{
		$format = "id";
	}
	else
	{
		$format = $params['format'];
		$userops =& $gCms->GetUserOperations();
		$thisuser =& $userops->LoadUserByID($id);
	}



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

function smarty_cms_help_function_last_modified_by()
{
  echo lang('help_function_last_modified_by');
}

function smarty_cms_about_function_last_modified_by() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
        <ul>
	   <li>v1.1 - (calguy) Added assign param.</li>
        </ul>
	</p>
	<?php
}
?>
