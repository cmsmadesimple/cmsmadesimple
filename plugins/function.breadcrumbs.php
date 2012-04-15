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
#

function smarty_function_breadcrumbs($params, &$template)
{
  $smarty = $template->smarty;
	$gCms = cmsms(); 
	$manager = $gCms->GetHierarchyManager();

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


	$endNode = $manager->sureGetNodeById($thispage);

# build path
	if (isset($endNode))
	{
	        $content = $endNode->getContent();
		$path=array($endNode);
		$currentNode = $endNode->getParentNode();
		while (isset($currentNode) && $currentNode->getLevel() >= 0)
		{
			$content = $currentNode->getContent();
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
			      $currentNode = $currentNode->getParentNode();
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

		if ($root!='##ROOT_NODE##') 
		  {
		    // check if the last added is root. if not, add it
		    $currentNode = $manager->sureGetNodeByAlias($root);
		    
		    if (isset($currentNode))
		      {
			$top_alias = '##garbage##';
			if( count($path) ) $top_alias = $path[count($path)-1]->get_tag('alias');
			
			if( strtolower($top_alias) != strtolower($root) )
			  {
			    $content = $currentNode->getContent();
			    if( is_object($content) && $content->Active() && $content->ShowInMenu() )
			      {
				$path[] = $currentNode;
			      }
			  }
		      }
		  }
		$classid=isset($params['classid'])?(' class="' . $params['classid'] . '"'):'';
		$currentclassid=isset($params['currentclassid'])?(' class="' . $params['currentclassid'] . '"'):'';

		// now create the trail (by iterating through the path we built, backwards)
		for ($i=count($path)-1;$i>=0;$i--) {
			$node = $path[$i];
			if (isset($node))
			{
				$onecontent = $node->getContent();
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
	if( isset($params['assign']) ){
	    $smarty->assign(trim($params['assign']),$trail);
	    return;
	}
	return $trail;  

}
	
function smarty_cms_help_function_breadcrumbs() {
  echo lang('help_function_breadcrumbs');
}

function smarty_cms_about_function_breadcrumbs() {
  echo lang('about_function_breadcrumbs');
}
# vim:ts=4 sw=4 noet
?>
