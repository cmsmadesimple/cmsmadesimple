<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_function_recently_updated($params, &$smarty)
{
    $number = 10;
	if(!empty($params['number'])) $number = min(100,max(1,(int) $params['number']));

    $leadin = "Modified: ";
	if(!empty($params['leadin'])) $leadin = $params['leadin'];

    $showtitle='true';
	if(!empty($params['showtitle'])) $showtitle = $params['showtitle'];

	$dateformat = isset($params['dateformat']) ? $params['dateformat'] : "d.m.y h:m" ;
	$css_class = isset($params['css_class']) ? $params['css_class'] : "" ;

	if (isset($params['css_class'])) {
		$output = '<div class="'.$css_class.'"><ul>';
	}
	else {
		$output = '<ul>';
	}

    $gCms = CmsApp::get_instance();
	$hm = $gCms->GetHierarchyManager();
	$db = $gCms->GetDb();

	// Get list of most recently updated pages excluding the home page
	$q = "SELECT * FROM ".CMS_DB_PREFIX."content WHERE (type='content' OR type='link')
        AND default_content != 1 AND active = 1 AND show_in_menu = 1
        ORDER BY modified_date DESC LIMIT ".((int)$number);
	$dbresult = $db->Execute( $q );
	if( !$dbresult ) {
        // @todo: throw an exception here
		echo 'DB error: '. $db->ErrorMsg()."<br/>";
	}
	while ($dbresult && $updated_page = $dbresult->FetchRow())
	{
		$curnode = $hm->getNodeById($updated_page['content_id']);
		$curcontent = $curnode->GetContent();
		$output .= '<li>';
		$output .= '<a href="'.$curcontent->GetURL().'">'.$updated_page['content_name'].'</a>';
		if ((FALSE == empty($updated_page['titleattribute'])) && ($showtitle=='true')) {
			$output .= '<br />';
			$output .= $updated_page['titleattribute'];
		}
		$output .= '<br />';
		$output .= $leadin;
		$output .= date($dateformat,strtotime($updated_page['modified_date']));
		$output .= '</li>';
	}

	$output .= '</ul>';
	if (isset($params['css_class'])) $output .= '</div>';

	if( isset($params['assign']) ) {
		$smarty->assign(trim($params['assign']),$output);
		return;
	}
	return $output;
}

function smarty_cms_about_function_recently_updated() {
?>
	<p>Author: Elijah Lofgren &lt;elijahlofgren@elijahlofgren.com&gt; Olaf Noehring &lt;http://www.team-noehring.de&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>added new parameters:<br />
		&lt;leadin&gt;. The contents of leadin will be shown left of the modified date. Default is &lt;Modified:&gt;<br />
		$showtitle='true' - if true, the titleattribute of the page will be shown if it exists (true|false)<br />
		css_class<br />
		dateformat - default is d.m.y h:m , use the format you whish (php format)</li>
	</ul>
<?php
}
?>