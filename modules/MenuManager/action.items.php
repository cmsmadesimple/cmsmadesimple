<?php
if( !isset($gCms) ) exit;
if( !isset($params['items']) ) return;

// init.
$mdid = md5($gCms->variables['content_id'].implode('|', $params));
$showparents = array();
$nodelist = array();
$origdepth = $prevdepth = 0;
$count = 0;
$deep = 1;
$hm = $gCms->GetHierarchyManager();
$origdepth = $prevdepth = 1;
$cachefn = '';
$cached = 0;

//
// setup
//
// get hte template info.
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

if( !$cached )
  {
    // info is not cached... must read it.
    if( isset($params['loadprops']) && $params['loadprops'] == 0 )
      $deep = 0;
    $items = explode(',',$params['items']);
    if( count($items) <= 0 ) return;
    
    reset($items);
    for( $idx = 0; $idx < count($items); $idx++ )
      {
	$key = trim($items[$idx]);
	$curnode = $hm->sureGetNodeByAlias($key);
	if( !$curnode ) continue;
  
	$content = $curnode->GetContent();
	if( !is_object($content) ) continue;
	if( !$content->Active() ) continue;

	// add this item to the nodelist
	$this->FillNode($content, $curnode, $nodelist, $count, $prevdepth, $origdepth, $deep);
  
	// only one node, correct the depth
	$mnode = $nodelist[count($nodelist)-1];
	$mnode->depth = 1;
	if( count($nodelist) > 1 )
	  {
	    $mnode->prevdepth = $nodelist[count($nodelist)-2]->depth;
	  }
	if( $idx == 0 )
	  {
	    $mnode->first = 1;
	  }
	else if( $idx == count($items) - 1 )
	  {
	    $mnode->last = 1;
	  }

	if( !isset($params['number_of_levels']) || ($params['number_of_levels'] > 1) )
	  {
	    $ocount = $count;
	    // we are getting some children.
	    $this->GetChildNodes($curnode,$nodelist,$gCms,$prevdepth,$count,$params,$origdepth,$showparents,$deep);

	    if( $count > $ocount )
	      {
		// there were children.
		$bdepth = $nodelist[$ocount]->depth;
		for( $i = $ocount; $i < count($nodelist); $i++ )
		  {
		    $nodelist[$i]->depth = $nodelist[$i]->depth - $bdepth + $mnode->depth + 1;;
		    $nodelist[$i]->prevdepth = $nodelist[$i-1]->depth;
		  }
		$prevdepth = $nodelist[$i-1]->depth;
	      }
	  }
      } // for

    if( count($nodelist) > 0 )
      {
	// pass it thru smarty.
	$smarty->assign('menuparams',$params);
	$smarty->assign('count',count($nodelist));
	$smarty->assign('nodelist',$nodelist);

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
    // data is cached, and we can use it.
    $txt = $file_get_contents($cachefn);
    echo $txt;
  }    

?>