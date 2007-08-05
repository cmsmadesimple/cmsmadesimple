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

function smarty_cms_function_last_modified_by($params, &$smarty) 
{
        global $gCms;
	$pageinfo = $gCms->variables['pageinfo'];

        $id = "";
 
	if (isset($pageinfo) && $pageinfo->content_last_modified_by_id > -1)
	{
		$id = $pageinfo->content_last_modified_by_id;
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
		global $gCms;
		$userops =& $gCms->GetUserOperations();
		$thisuser =& $userops->LoadUserByID($id);
	}



  if($format==="id") {
     return $id;
  } else if ($format==="username") {
     return cms_htmlentities($thisuser->username);
  } else if ($format==="fullname") {
     return cms_htmlentities($thisuser->firstname ." ". $thisuser->lastname);
  } else {
     return "";
  }
}

function smarty_cms_help_function_last_modified_by()
{
        ?>
        <h3>What does this do?</h3>
        <p>Prints last person that edited this page.  If no format is given, it will default to a ID number of user .</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{last_modified_by format="fullname"}</code></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>format - id, username, fullname</li>
        </ul>
        <?php
}

function smarty_cms_about_function_last_modified_by() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	</p>
	<?php
}
?>
