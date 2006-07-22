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

function smarty_cms_function_recently_updated($params, &$smarty)
{
  if(empty($params['number']))
    {
      $number = 10;
    }
  else
    {
      $number = $params['number'];
    }

$output .= '<ul>';
global $gCms;
$hm =& $gCms->GetHierarchyManager();
$db = &$gCms->db;
// Get list of most recently updated pages excluding the home page
$q = "SELECT * FROM ".cms_db_prefix()."content WHERE (type='content' OR type='link')
AND default_content != 1 AND active = 1 AND show_in_menu = 1 
ORDER BY modified_date DESC LIMIT ".$number;
$dbresult = $db->Execute( $q );
if( !$dbresult )
{
    echo 'DB error: '. $db->ErrorMsg()."<br/>";
}
while ($dbresult && $updated_page = $dbresult->FetchRow())
{
    $curnode =& $hm->getNodeById($updated_page['content_id']);
    $curcontent =& $curnode->GetContent();
    $output .= '<li>';
    $output .= '<a href="'.$curcontent->GetURL().'">'.$updated_page['content_name'].'</a>';
    if (FALSE == empty($updated_page['titleattribute']))
      {
	$output .= '<br />';
	$output .= $updated_page['titleattribute'];
      }
    $output .= '<br />';
    $output .= 'Modified: ' .$updated_page['modified_date'];
    $output .= '</li>';
}
$output .= '</ul>';
return $output;
}

function smarty_cms_help_function_recently_updated() {
	?>
	<h3>What does this do?</h3>
	<p>Outputs a list of recently updated pages.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{recently_updated}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
											 <li><p><em>(optional)</em> number='10' - Number of updated pages to show.</p><p>Example: <pre>{recently_updated number='15'}</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_recently_updated() {
	?>
	<p>Author: Elijah Lofgren &lt;elijahlofgren@elijahlofgren.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
