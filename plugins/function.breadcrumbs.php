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

function smarty_cms_function_breadcrumbs($params, &$smarty)
{
	global $gCms; 
	
	$thispage = $gCms->variables['page'];
	$trail = "";

	#Make an array for all pages
	$allcontent = array();

	#Load current content
	$onecontent = ContentManager::LoadContentFromId($thispage);
	if ($onecontent !== FALSE)
	{
		array_push($allcontent, $onecontent);

		#Grab all parents and put them into the array as well
		while ($onecontent->ParentId() > 0) 
		{
			$onecontent = ContentManager::LoadContentFromId($onecontent->ParentId());
			array_push($allcontent, $onecontent);
		}

		#Pull them one by one in reverse order to construct a breadcrumb list
		while ($onecontent = array_pop($allcontent))
		{
			if ($onecontent->Id() != $thispage && $onecontent->Type() != 'seperator')
			{
				if ($onecontent->Type() == 'sectionheader')
				{
					if (getURL($thispage)!="")
					{
						$trail .= $onecontent->MenuText()." &gt;&gt;\n";
					}
					else
					{
						$trail .= $onecontent->MenuText()." &gt;&gt; \n";
					}
				}
				else
				{
					if (getURL($thispage)!="")
					{
						$trail .= "<a href=\"".$onecontent->getURL()."\">".$onecontent->Name()."</a> &gt;&gt;\n";
					}
					else
					{
						$trail .= $onecontent->Name()." &gt;&gt; \n";
					}
				}
			}
			else
			{
				$trail .= "<strong>".$onecontent->Name()."</strong>\n";
			}
		}
	}
	else
	{
		$trail = "No pages";
	}

	return $trail;

}

function smarty_cms_help_function_breadcrumbs() {
	?>
	<h3>What does this do?</h3>
	<p>Prints a breadcrumb trail .</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{breadcrumbs}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
		No parameters at the moment, a future release may allow you to change what goes between each page.
	</p>


	<?php
}

function smarty_cms_about_function_breadcrumbs() {
	?>
	<p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
	1.1 - Modified to use new content rewrite (wishy)<br />
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
