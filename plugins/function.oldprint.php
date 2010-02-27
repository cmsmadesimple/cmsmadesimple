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

function smarty_cms_function_oldprint($params, &$smarty)
{
	global $gCms;
	$page_url = ''; // Initialize var to prevent errors on preview
	$text = 'Print This Page';

	if (!empty($params['text']))
	{
		$text = $params['text'];
	}

	$js = '';
	if (!empty($params['script']) and $params['script'])
	{
		$js = '&amp;js=1';
	}

        $class = 'noprint';
        if (!empty($params['class']) and $params['class'])
        {
          $class = $params['class'];
        }

	$target = '';
	if (!empty($params['popup']) and $params['popup'])
	{
		$target = ' target="_blank"';
		$goback = '&amp;goback=0';
	}
	else
	{
		$goback = '&amp;goback=0';
		if (!empty($params['goback']) and $params['goback'])
		{
		$goback = '&amp;goback=1';
		}
	}

        if (!empty($params['title']) and $params['title'])
        {
                $title = ' title="'.$params['title'].'"';
        }
        else
        {
                $title = ' title="'.$text.'"';
        }

        $more = '';
        if (!empty($params['more']) and $params['more'])
        {
                $more = ' '.$params['more'];
        }

        $src_img = $gCms->config['root_url'].'/images/cms/printbutton.gif';
        if (!empty($params['src_img']) and $params['src_img'])
        {
                $src_img = $params['src_img'];
        }

        $class_img = '';
        if (!empty($params['class_img']) and $params['class_img'])
        {
                $class_img = ' class="'. $params['class_img'] .'"';
        }


	if ('mod_rewrite' == $gCms->config['url_rewriting'])
	{
		$hm =& $gCms->GetHierarchyManager();
		$curnode =& $hm->getNodeById($gCms->variables['content_id']);
		if (isset($curnode))
		{
			$curcontent =& $curnode->GetContent();
                        $page_url = $curcontent->GetURL().'?print=true';
		}
	}
	else
	{
		$page_url = $gCms->config['root_url'].'/index.php?'.$gCms->config['query_var'].'='.$gCms->variables['content_id'].'&amp;print=true';
	}

	//will this work if using htaccess? (Yes! -Wishy)
        $output = '<a class="'. $class .'" href="' . $page_url . $goback . $js . '"'. $target . $title . $more . '>';
	if (isset($params['showbutton']))
	{
               $output .= '<img src="'.$src_img.'" alt="'.$text.'"' . $title . $class_img . ' />';

	}
	else
	{
		$output .=  $text;
	}
	return $output.'</a>';
}

function smarty_cms_help_function_oldprint() {
  echo lang('help_function_oldprint');
}

function smarty_cms_about_function_oldprint() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@classicwebdevelopment.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	1.1 - Modified to customize print page (roman)
	1.2 - Modified for xhtml, accessibility and personal image file (alby)
	</p>
	<?php
}

?>
