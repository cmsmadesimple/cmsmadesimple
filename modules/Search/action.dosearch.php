<?php
if (!isset($gCms)) exit;

class SearchItemCollection
{
  var $_ary;
  var $maxweight;
  
  function SearchItemCollection()
  {
    $this->_ary = array();
    $this->maxweight = 1;
  }
  
  function AddItem($title, $url, $txt, $weight = 1, $module = '', $modulerecord = 0, $params = array())
  {
    if( $txt == '' ) { $txt = $url; }
    $exists = false;

    if (!isset($this->_ary[$url]) )
      {
		$newitem = new StdClass();
		$newitem->url = $url;
		$newitem->urltxt = $txt;
		$newitem->title = $title;
		$newitem->params = $params;
		$newitem->intweight = intval($weight);
		if (intval($weight) > $this->maxweight)
		  $this->maxweight = intval($weight);
		if (!empty($module) )
		  {
			$newitem->module = $module;
			if( intval($modulerecord) > 0 )
			  {
				$newitem->modulerecord = $modulerecord;
			  }
		  }
		$this->_ary[$url] = $newitem;
      }
  }
	
	function CalculateWeights()
	{
		reset($this->_ary);
		while (list($key) = each($this->_ary))
		{
			$oneitem =& $this->_ary[$key];
			$oneitem->weight = intval(($oneitem->intweight / $this->maxweight) * 100);
		}
	}

	function Sort()
	{
		function search_ary_cmp($a, $b)
		{
			if ($a->urltxt == $b->urltxt)
				return 0;
			
			return ($a->urltxt < $b->urltxt ? -1 : 1);
		}
		
		usort($this->_ary, 'search_ary_cmp');
	}
}

if ($params['searchinput'] != '')
{
	// Fix to prevent XSS like behaviour. See: http://www.securityfocus.com/archive/1/455417/30/0/threaded
  $params['searchinput'] = htmlspecialchars(trim($params['searchinput']));
  @$this->SendEvent('SearchInitiated', array(trim($params['searchinput'])));

	$searchstarttime = microtime();

	$smarty->assign('phrase', $params['searchinput']);

	// Update the search words table
    $term = trim($params['searchinput']);
    $q = 'SELECT count FROM '.cms_db_prefix().'module_search_words WHERE word = ?';
    $tmp = $db->GetOne($q,array($term));
    if( $tmp )
      {
		$q = 'UPDATE '.cms_db_prefix().'module_search_words SET count=count+1 WHERE word = ?';
		$db->Execute($q,array($term));
      }
    else
      {
		$q = 'INSERT INTO '.cms_db_prefix().'module_search_words (word,count) VALUES (?,1)';
		$db->Execute($q,array($term));
      }

	$query = '+('.$params['searchinput'].')';
	if (!empty($params['modules']))
	{
		$search_module = explode(',', $params['modules']);
		$query .= ' +module_name:('.implode(' OR ',$search_module).')';
	}
	$parms = array();
	foreach( $params as $key => $value )
	{
		$str = 'customfields_';
		if( preg_match( "/$str/", $key ) > 0 )
		{
		  	$name = substr($key,strlen($str));
			$params[$name] = $value;
		  	if( $name != '' )
			{
				if ( is_array($value) )
				{
					$query .= ' +'.$name.':('.implode(' OR ',$value).')';
				}else
				{
					$query .= ' +'.$name.':('.$value.')';
				}
			}
		}
	}	

	$hits = CmsSearch::get_instance()->find($query, 'module_name', 'extra_attr', 'object_id');

	$col = new SearchItemCollection();
	$current_module = '';
	$custom_search = '';
	foreach($hits as $result)
	{
		$doc = $result->getDocument();
		if ( $custom_search && $doc->module_name != $current_module && $current_module != '')
		{
			$parms = $params;
		  	foreach( $params as $key => $value )
			    {
			      $str1 = 'passthru_'.$current_module.'_';
			      $str2 = 'passthru_';
			      if( preg_match( "/$str1/", $key ) > 0 )
					{
					  $name = substr($key,strlen($str1));
					  if( $name != '' )
						{
						  $parms[$name] = $value;
						}
					}elseif( preg_match( "/$str2/", $key ) > 0 )
					{
					  $name = substr($key,strlen($str2));
					  if( $name != '' )
						{
						  $parms[$name] = $value;
						}
					}
			    }
			$parms['results'] = $module_results;
			$parms['collection'] = $col;
			$moduleobj =& $this->GetModuleInstance($current_module);
			$moduleobj->DoAction('advance_search',$id,$parms);

			$module_results = array();
		}
		if ( $doc->module_name != $current_module)
		{
			$current_module = $doc->module_name;
			$search_file = cms_join_path($gCms->config['root_path'],'modules',
					  $current_module,'action.advance_search.php');
			if( is_readable( $search_file ) )
			{
				$custom_search = true;echo "true";
			}else
			{
				$custom_search = false;echo "false";
			}
		}
		$newitem = array();
		$newitem['score'] = $result->score;
		$newitem['title'] = $doc->getFieldValue('title');
		$newitem['extra_attr'] = $doc->getFieldValue('extra_attr');
		$newitem['object_id'] = $doc->getFieldValue('object_id');
		$newitem['url'] = $doc->getFieldValue('url');
		$newitem['pretty_url'] = $doc->getFieldValue('pretty_url');
		$newitem['teaser'] = $doc->getFieldValue('teaser');
		$newitem['language'] = $doc->getFieldValue('language');
		$customnames = $doc->getFieldValue('customnames');
		if (!empty($customnames))
		{
			$temp_names = explode(',',$customnames);
			foreach($temp_names as $one)
			{
				$newitem[$one] = $doc->getFieldValue($one);
			}
		}
		if ( $custom_search )
		{
			$module_results[] = $newitem;
		}else
		{
			$col->AddItem($newitem['title'],$newitem['url'],$newitem['title'],$newitem['score'],$current_module,$newitem['object_id'],$newitem);
		}
	}

	$col->CalculateWeights();
	
	if ($this->GetPreference('alpharesults', 'false') == 'true')
	{
		$col->Sort();
	}
	
	/**
	* @todo Replace with Zend Hiliting
	*
	*/
	/* now we're gonna do some post processing on the results
	// and replace the search terms with <span class="searchhilite">term</span>
	
	$results = $col->_ary;
	$newresults = array();
	foreach( $results as $result )
	{
	  $title = cms_htmlentities($result->title);
	  $txt = cms_htmlentities($result->urltxt);
		foreach( $words as $word )
		{
		  $word = preg_quote($word);
		  $title = preg_replace('/('.$word.')/i', '<span class="searchhilite">$1</span>', $title);
		  $txt = preg_replace('/('.$word.')/i', '<span class="searchhilite">$1</span>', $txt);
		}
		$result->title = $title;
		$result->urltxt = $txt;
		$newresults[] = $result;
	}
	$col->_ary = $newresults;
	*/

	@$this->SendEvent('SearchCompleted', array(&$params['searchinput'], &$col->_ary));

	$smarty->assign('results', $col->_ary);
	$smarty->assign('itemcount', count($col->_ary));

	$searchendtime = microtime();
	$smarty->assign('timetook', ($searchendtime - $searchstarttime));
}
else 
{
	$smarty->assign('phrase', '');
	$smarty->assign('results', 0);
	$smarty->assign('itemcount', 0);
	$smarty->assign('timetook', 0);
}

$smarty->assign('searchresultsfor', $this->Lang('searchresultsfor'));
$smarty->assign('noresultsfound', $this->Lang('noresultsfound'));
$smarty->assign('timetaken', $this->Lang('timetaken'));

echo $this->Template->process_from_database('result');

?>
