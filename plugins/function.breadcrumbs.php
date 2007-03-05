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
#

function smarty_cms_function_breadcrumbs($params, &$smarty)
{

	global $gCms; 
	$manager = &$gCms->GetHierarchyManager();

	$thispage = $gCms->variables['content_id'];

	$trail = "";

#Check if user has specified a delimiter, otherwise use default
	if (isset($params['delimiter'])) {
		$delimiter = $params['delimiter'];
	}	else {
		$delimiter = "&gt;&gt;";
	}

#Check if user has requested an initial delimiter
	if (isset($params['initial'])) {
		if ($params['initial'] == "1") {
			$trail .= $delimiter . " ";
		}
	}

	$root='##ROOT_NODE##';
#Check if user has requested the list to start with a specific page
	if (isset($params['root']))	{
		$root = $params['root'];
	}
	$root_url='';
#Check if user has requested to overrided the root URL
	if (isset($params['root_url']))	{
		$root_url = $params['root_url'];
	}


	$endNode = &$manager->sureGetNodeById($thispage);

# build path
	if (isset($endNode))
	{
	        $content =& $endNode->getContent();
		$path=array($endNode);
		$currentNode = &$endNode->getParentNode();
		while (isset($currentNode) && $currentNode->getLevel() >= 0)
		{
			$content = &$currentNode->getContent();
			if (isset($content))
			{
			  //Add current node to the path and then check to see if
			  //current node is the set root
			  //as long as it's not hidden
			  if( $content->ShowInMenu() && $content->Active() )
			    {
				$path[] = $currentNode;
			    }
			  if (strtolower($content->Alias())!=strtolower($root))
			    {
			      //Get the parent node and loop
			      $currentNode = &$currentNode->getParentNode();
			    }
			  else
			    {
			      //No need to get the parent node -- we're the set root already
			      break;
			    }
			}
			else
			{
			  //There are more serious problems here, dump out while we can
			  break;
			}
		}

		if ($root!='##ROOT_NODE##') {
	# check if the last added is root. if not, add id
                    $currentNode = &$manager->sureGetNodeByAlias($root);
			if (isset($currentNode))
			{
				$content = &$currentNode->getContent();
				if (isset($content) && (strtolower($content->Alias()) == strtolower($root)))
				{
					$node = &$manager->sureGetNodeByAlias($root);
					if (isset($node)) {
						$content = &$node->getContent();
						if ($content->Id()!=$thispage) 
                                                      $path[] = $node; # do not add if this is the current page
					}
				}
			}
		}
		$classid=isset($params['classid'])?(' class="' . $params['classid'] . '"'):'';
		$currentclassid=isset($params['currentclassid'])?(' class="' . $params['currentclassid'] . '"'):'';
	# now create the trail (by iterating through the path we built, backwards)
		for ($i=count($path)-1;$i>=0;$i--) {
			$node = &$path[$i];
			if (isset($node))
			{
				$onecontent = &$node->getContent();
				if ($onecontent->Id() != $thispage && $onecontent->Type() != 'seperator') {
					if (($onecontent->getURL() != "") && ($onecontent->Type() != 'sectionheader')) {
					  if ($onecontent->DefaultContent() && false == empty($root_url))
					    {
					      $trail .= '<a href="' . $root_url . '"';     
					    }
					      else
						{
						  $trail .= '<a href="' . $onecontent->getURL() . '"';
						}
						$trail .= $classid;
						$trail .= '>';
						$trail .= cms_htmlentities($onecontent->MenuText()!=''?$onecontent->MenuText():$onecontent->Name());
						$trail .= '</a> ';
					} else {
						$trail .= "<span $classid>";
						$trail .= cms_htmlentities($onecontent->MenuText()!=''?$onecontent->MenuText():$onecontent->Name());
						$trail .= '</span>';
						$trail .= ' ';
					}
					$trail .= $delimiter . ' ';
				} else {
					if (isset($params['currentclassid'])) {
						$trail .= "<span $currentclassid>";
					} else {
						$trail .= '<span class="lastitem">';
					}
					$trail .= cms_htmlentities($onecontent->MenuText()!=''?$onecontent->MenuText():$onecontent->Name());
					if (isset($params['currentclassid'])) {
						$trail .= '</span>';
					} else {
						$trail .= '</span>';
					}
				}
			}
		}
	}

	if (isset($params['starttext']) && $params['starttext'] != '')
	{
		$trail = $params['starttext'] . ': ' . $trail;
	}

	return $trail;  

}
	
function smarty_cms_help_function_breadcrumbs() {
// tdh added the classid help text
?>
<h3>What does this do?</h3>
<p>Prints a breadcrumb trail .</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your template/page like: <code>{breadcrumbs}</code></p>
<h3>What parameters does it take?</h3>
<p>
<ul>
<li><em>(optional)</em> <tt>delimiter</tt> - Text to seperate entries in the list (default "&gt;&gt;").</li>
<li><em>(optional)</em> <tt>initial</tt> - 1/0 If set to 1 start the breadcrumbs with a delimiter (default 0).</li>
<li><em>(optional)</em> <tt>root</tt> - Page alias of a page you want to always appear as the first page in
    the list. Can be used to make a page (e.g. the front page) appear to be the root of everything even though it is not.</li>
<li><em>(optional)</em> <tt>root_url</tt> - Override the URL of the root page. Useful for making link be to '/' instead of '/home/'. This requires that the root page be set as the default page.</li>

<li><em>(optional)</em> <tt>classid</tt> - The CSS class for the non current page names, i.e. the first n-1 pages in the list. If the name is a link it is added to the &lt;a href&gt; tags, otherwise it is added to the &lt;span&gt; tags.</li>
<li><em>(optional)</em> <tt>currentclassid</tt> - The CSS class for the &lt;span&gt; tag surrounding the current page name.</li>
<li><em>(optional)</em> <tt>starttext</tt> - Text to append to the front of the breadcrumbs list, something like &quot;You are here&quot;.</li>
</ul>
</p>
<?php
}

function smarty_cms_about_function_breadcrumbs() {
?>
<p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
<p>Version: 1.7</p>
<p>
Change History:<br/>
1.1 - Modified to use new content rewrite (wishy)<br />
1.2 - Added parameters: delimiter, initial, and root (arl)<br />
1.3 - Added parameter: classid (tdh / perl4ever)<br />
1.4 - Added parameter currentclassid and fixed some bugs (arl)<br />
1.5 - Modified to use new hierarchy manager<br />
1.6 - Modified to skip any parents that are marked to be "not shown in menu" except for root<br />
1.7 - Added root_url parameter (elijahlofgren)<br />
</p>
<?php
}
# vim:ts=4 sw=4 noet
?>
