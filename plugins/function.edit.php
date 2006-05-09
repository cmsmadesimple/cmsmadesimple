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

function smarty_cms_function_edit($params, &$smarty)
{
	global $gCms;

	if (!check_permission(get_userid(false), 'Modify Any Page')) return;

	$text = isset($params['text']) ? $params['text']:'Edit This Page';
	if (isset($params["showbutton"]))
	{
		return '<a href="'.$gCms->config['root_url'].'/'.$gCms->config['admin_dir'].'/editcontent.php?content_id='.$gCms->variables['content_id'].'"><img border="0" src="'.$gCms->config['root_url'].'/images/cms/editbutton.png" alt="'.$text.'"/></a>';
	}
	else
	{
		return '<a href="'.$gCms->config['root_url'].'/'.$gCms->config['admin_dir'].'/editcontent.php?content_id='.$gCms->variables['content_id'].'">'.$text.'</a>';
	}
	/*
	global $gCms;
		
	$userid = get_userid(false);
	if(!$userid) return;

	$access = check_permission($userid, 'Modify Any Page');
	if (!$access) return;

	$text = 'Edit This Page';

	if (!empty($params['text']))
	{
		$text = $params['text'];
	}

	//will this work if using htaccess? (Yes! -Wishy)
	if (isset($params["showbutton"]))
	{
		return '<a href="'.$gCms->config['root_url'].'/'.$gCms->config['admin_dir'].'/editcontent.php?content_id='.$gCms->variables['content_id'].'"><img border="0" src="'.$gCms->config['root_url'].'/images/cms/editbutton.png" alt="'.$text.'"/></a>';
	}
	else
	{
		return '<a href="'.$gCms->config['root_url'].'/'.$gCms->config['admin_dir'].'/editcontent.php?content_id='.$gCms->variables['content_id'].'">'.$text.'</a>';
	}
	*/
}

function smarty_cms_help_function_edit() {
	?>
	<h3>What does this do?</h3>
	<p>Creates a link to edit the page</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{edit}</code><br></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>showbutton - Set to "true" and will show a edit graphic instead of a text link.</li>
        </ul>
	<?php
}

function smarty_cms_about_function_edit() {
	?>
	<p>Author: Sorin Sbârnea&lt;sorin2000@intersol.ro&gt; (remove 2000 from address)</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>
