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

function smarty_cms_function_print($params, &$smarty)
{
	global $gCms;

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
	if (true == $gCms->config['assume_mod_rewrite'])
	{
		$hm =& $gCms->GetHierarchyManager();
		$curnode =& $hm->getNodeById($gCms->variables['content_id']);
		if (isset($curnode))
		{
			$curcontent =& $curnode->GetContent();
			$page_url = $curcontent->GetURL().'?'.$gCms->config['query_var'].'=true';
		}
	}
	else
	{
		$page_url = $gCms->config['root_url'].'/index.php?'.$gCms->config['query_var'].'='.$gCms->variables['content_id'].'&amp;print=true';
	}

	//will this work if using htaccess? (Yes! -Wishy)
	$output = '<a class="'. $class .'" href="' . $page_url . $goback . $js . '"'. $target . '>';
	if (isset($params["showbutton"]))
	{
		$output .= '<img src="'.$gCms->config['root_url'].'/images/cms/printbutton.gif" alt="'.$text.'"/>';
	}
	else
	{
		$output .=  $text;
	}
	return $output.'</a>';
}

function smarty_cms_help_function_print() {
	?>
	<h3>What does this do?</h3>
	<p>Creates a link to only the content of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{print}</code><br></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em> goback - Set to "true" to show a "Go Back" link on the page to print.</li>
                <li><em>(optional)</em> popup - Set to "true" and page for printing will by opened in new window.</li>
                <li><em>(optional)</em> script - Set to "true" and in print page will by used java script for run print of page.</li>
                <li><em>(optional)</em> showbutton - Set to "true" and will show a printer graphic instead of a text link.</li>
                <li><em>(optional)</em> class - class for the link, defaults to "noprint".</li>
                <li><em>(optional</em> text - Text to use instead of "Print This Page" for the print link.
                    <p>Example:</p>
                     <pre>{print text="Printable Page"}</pre>      
                     </li>
        </ul>
	<?php
}

function smarty_cms_about_function_print() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@classicwebdevelopment.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	1.1 - Modified to customize print page (roman)
	</p>
	<?php
}

?>
