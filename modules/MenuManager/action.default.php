<?php
if (!isset($gCms)) exit;

debug_buffer('', 'Start of Menu Manager Display');

$hm = $gCms->GetHierarchyManager();

$usefile = true;
$tpl_name = $this->GetPreference('default_template','simple_navigation.tpl');
if (isset($params['template']) && $params['template'] != '')
{
  $tpl_name = $params['template'];
}
if( endswith($tpl_name, '.tpl') )
  {
    $usefile = true;
  }
else
  {
    $usefile = false;
  }

// get the list of cachable templates.
$cachables = array();
{
  $tmp = base64_decode($this->getPreference('cachable_templates'));
  if( $tmp )
    {
      $cachables = unserialize($tmp);
    }
}

$mdid = md5($gCms->variables['content_id'].implode('|', $params));
$deep = 1;
if( isset($params['loadprops']) && $params['loadprops'] == 0 )
  $deep = 0;


$cached = 0;
$cachefn = '';
if( in_array($tpl_name,$cachables) && (!isset($params['nocache']) || (int)$params['nocache'] == 0) )
  {
    // we may be able to cache
    $content_obj = $gCms->variables['content_obj'];
    if( $content_obj->Cachable() )
      {
	// yup, looks like we can use a cache file if it exists.

	// todo, work on serial.
	$serial = md5($tpl_name.serialize($params).$content_obj->Id());
	$cachefn = cms_join_path(TMP_CACHE_LOCATION,'menu.'.$serial.'.cache');
	if( file_exists($cachefn) )
	  {
	    // woot, cache file exists.
	    $cached = 1;
	  }
      }
  }

$origdepth = 0;
if (!$cached)
{
	$nodelist = array();
	$count = 0;
	$getchildren = true;

	$rootnode = null;

	$prevdepth = 1;

	if (isset($params['childrenof']) )
	{
	  $parent = $hm->sureGetNodeByAlias($params['childrenof']);;
	  if( $parent )
	    {
	      // get the children.
	      $children = $parent->GetChildren($deep);
	      if( !is_array($rootnode) )  $rootnode = array();
              if( is_array($children) && count($children) )
              {
	        foreach( $children as $onechild )
	        {
		  $obj = $onechild->GetContent();
		  if( is_object($obj) && $obj->Active() && ($obj->ShowInMenu() || (isset($params['show_all']) && $params['show_all'])) )
		  {
		    $rootnode[] = $onechild;
		  }
	        }
              }
	    }
	}
	else if (isset($params['start_page']) || isset($params['start_element']))
	{
		if (isset($params['start_page']))
			$rootnode = $hm->sureGetNodeByAlias($params['start_page']);
		else
			$rootnode = $hm->getNodeByHierarchy($params['start_element']);

		if (isset($rootnode))
		{
			$content = $rootnode->GetContent();
			if (isset($content))
			{
				if (isset($params['show_root_siblings']) && $params['show_root_siblings'] == '1')
				{
					if ($rootnode->getLevel() == 0)
					{
					  $rootnode = $rootnode->getParentNode();
						$prevdepth = 1;
					}
					else
					{
						#Set original depth first before getting parent node
						#This is slightly hackish, but it works nicely
					        #+1 and +2 fix HM changes of root node level
					        #even more hackish ;)
						$origdepth = $rootnode->getLevel()+1;
						$rootnode  = $rootnode->getParentNode();
						$prevdepth = $rootnode->getLevel()+2;
					}
				}
				else
				{
					//Show a page if it's active and set to show in menu...
					//Or if show_all is set and the page isn't a "system" page
				  if ($content->Active() && ($content->ShowInMenu() || (isset($params['show_all']) && $params['show_all'] == 1 && !$content->IsSystemPage())))
					{
						$prevdepth = count(explode('.', $content->Hierarchy()));
						$this->FillNode($content, $rootnode, $nodelist, $count, $origdepth, $prevdepth, $deep, $params);
						if (isset($params['number_of_levels']) && $params['number_of_levels'] == '1')
							$getchildren = false;
					}
				}
			}
		}
	}
	else if (isset($params['start_level']) && intval($params['start_level']) > 1)
	{
		$curnode = $hm->sureGetNodeById($gCms->variables['content_id']);
		if (isset($curnode))
		{
			$curcontent = $curnode->GetContent();
			if( $curcontent )  {
			  $properparentpos = $this->nthPos($curcontent->Hierarchy() . '.', '.', intval($params['start_level']) - 1);
			  if ($properparentpos > -1)
			  {
			    $prevdepth = intval($params['start_level']);
			    $rootnode = $hm->getNodeByHierarchy(substr($curcontent->Hierarchy(), 0, $properparentpos));
			  }
                        }
		}
	}
	else if (isset($params['items']))
	  {
	    if( !isset($params['number_of_levels']) )
	      {
		$getchildren = false;
		$params['number_of_levels'] = 1;
	      }

	    $items = explode(',', $params['items']);
	    if (count($items) > 0)
	      {
		reset($items);
		while (list($key) = each($items))
		  {
		    $oneitem = $items[$key];
		    $curnode = $hm->sureGetNodeByAlias(trim($oneitem));
		    if( !$curnode ) continue;
		    $content = $curnode->GetContent();
		    if( !is_object($content) ) continue;
		    if( !$content->Active() ) continue;

		    if( $getchildren )
		      {
			$rootnode[] = $curnode;
		      }
		    else
		      {
			$prevdepth = 1;
			$mnode = $this->FillNode($content,$curnode,$nodelist,$count,$prevdepth,1,$deep,$params);
			$mnode->depth = 1;
		      }
		  } // while
	      }
	  }
	else
	  {
	    // load all content
	    $rootnode =& $hm;
	    $prevdepth = 1;
	  }


	$showparents = array();

	if (isset($params['collapse']) && $params['collapse'] == '1')
	{
		$newpos = '';
		if (isset($gCms->variables['friendly_position']))
		{
			foreach (explode('.', $gCms->variables['friendly_position']) as $level)
			{
				$newpos .= $level . '.';
				$showparents[] = $newpos;
			}
		}
	}

	#See if origdepth was ever set...  if not, then get it from the prevdepth set earlier
	if ($origdepth == 0)
		$origdepth = $prevdepth;

	if (isset($rootnode) && $getchildren)
	  {
	    if( is_array($rootnode) && count($rootnode) )
	      {
		$first = 1;
		for( $n = 0; $n < count($rootnode); $n++ )
		  {
		    $onenode = $rootnode[$n];
		    $content = $onenode->GetContent();
		    if( $first )
		      {
			$prevdepth = $origdepth = $onenode->GetLevel() + 1;
			$first = 0;
		      }
		    if( $content )
		      {
			$mnode = $this->FillNode($content, $onenode, $nodelist, $count, $prevdepth, $prevdepth, $deep);
			if( $n == 0 )
			  {
			    $mnode->first = 1;
			  }
			if( $n >= count($rootnode) - 1 )
			  {
			    $mnode->last = 1;
			  }
			if( !isset($params['number_of_levels']) )
			  {
			    $params['number_of_levels'] = 99;
			  }
			if( $params['number_of_levels'] > 1 )
			  {
			    // we are getting more than one level.
			    $res = $this->GetChildNodes($onenode,$nodelist,$gCms,$prevdepth,$count,$params,
						 $origdepth,$showparents,$deep);
			    if( $res )
			      {
				$mnode->haschildren = true;
			      }
			  }
		      }
		  }
	      }
	    else if( $rootnode )
	      {
		$this->GetChildNodes($rootnode, $nodelist, $gCms, $prevdepth, $count, $params, 
				     $origdepth, $showparents, $deep);
		
	      }
	  }

	if (count($nodelist) > 0)
	{
		$smarty = $this->smarty;
		$smarty->assign('menuparams',$params);
		$smarty->assign('count', count($nodelist));
		$smarty->assign('nodelist', $nodelist);
		if ($usefile)
		  $txt = $this->ProcessTemplate($tpl_name, $mdid, false, $gCms->variables['content_id']);
		else
		  $txt = $this->ProcessTemplateFromDatabase($tpl_name, $mdid, false);

		if( $cachefn != '' )
		  {
		    // put the stuff in the cache.
		    file_put_contents($cachefn,$txt);
		  }

		echo $txt;
	}
}
else
{
  // the data is cached, and we can use it.
  debug_buffer('Menu Manager Display used the Cache from '.$cachefn);
  $txt = file_get_contents($cachefn);
  echo $txt;
}
debug_buffer('', 'End of Menu Manager Display');
?>
