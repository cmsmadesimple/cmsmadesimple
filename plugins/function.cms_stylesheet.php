<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_cms_stylesheet($params, &$template)
{
	#---------------------------------------------
	# Initials
	#---------------------------------------------

	$smarty = $template->smarty;
	$config = cmsms()->GetConfig();
	$db = cmsms()->GetDb();
	
	global $CMS_ADMIN_PAGE;
	global $CMS_LOGIN_PAGE;
	global $CMS_STYLESHEET;
	$CMS_STYLESHEET = 1;
	$design_id = -1;
	$use_https = 0;
	$cache_dir = $config['css_path'];
	$stylesheet = '';
	$combine_stylesheets = true;
	$fnsuffix = '';
	$trimbackground = FALSE;	
	$forceblackandwhite = FALSE;	
	$root_url = $config['css_url'];
	$auto_https = 1;

	#---------------------------------------------
	# Trivial Exclusion
	#---------------------------------------------	
	
	if( isset($CMS_LOGIN_PAGE) ) return;

	#---------------------------------------------
	# Read parameters
	#---------------------------------------------	

	if (isset($params['designid']) && $params['designid']!='') {
		$design_id = (int)$params['designid'];
	} else {
	  $content_obj = cmsms()->get_content_object();
		if( !is_object($content_obj) ) return;
		$design_id = $content_obj->GetPropertyValue('design_id');
		$use_https = (int)$content_obj->Secure();
	}

	if( isset($params['auto_https']) && $params['auto_https'] == 0 ) {
		$auto_https = 0;
	}

	if(isset($params['https'])) {
		$use_https = (int)$params['https'];
	}
	
	if( $auto_https ) {
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') {
			$use_https = 1;
		}
	}

	if($use_https && isset($config['ssl_url'])) {
		$root_url = $config['ssl_css_url'];
	}

	if(isset($params['nocombine']) && $params['nocombine'] == 1) {
		$combine_stylesheets = FALSE;
	}	

	if( isset($params['stripbackground']) )
	{
		$trimbackground = (int)$params['stripbackground'];
	}
	if( isset($params['forceblackonwhite']) )
	{
		$forceblackandwhite = (int)$params['forceblackonwhite'];
	}
	if( $trimbackground || $forceblackandwhite )
	{
		$fnsuffix = '_e_';
	}	
	
	#---------------------------------------------
	# Build query
	#---------------------------------------------	

	$qparms = array();
	$where = array();
	$query = '';
	$order = '';

	if (isset($params['name']) && $params['name'] != '') {
	
		$query = 'SELECT DISTINCT A.id,A.name,A.content,A.modified,A.media_type,A.media_query 
					FROM '.cms_db_prefix().'layout_stylesheets A';
		$where[] = 'A.name = ?';
		$qparms[] = trim($params['name']);
	
	} else {

  	    $query = 'SELECT DISTINCT A.id,A.name,A.content,A.modified,
                      A.media_type,A.media_query,B.item_order
   	                FROM '.cms_db_prefix().'layout_stylesheets A 
                    LEFT JOIN '.cms_db_prefix().'layout_design_cssassoc B ON A.id = B.css_id';
		$where[] = 'B.design_id = ?';
		$qparms = array($design_id);

		if( isset($params['media']) && strtolower($params['media']) != 'all' ) {
		
			$types = explode(',',$params['media']); 
			$expr = array();
			foreach($types as $type)
			{
				$expr[] = 'A.media_type LIKE ?';
				$qparms[] = '%'.trim($type).'%';
			}
			
			$expr[] = 'A.media_type LIKE ?';
			$qparms[] = '%all%';

			$where[] = '('.implode(' OR ',$expr).')';
		}
       	
		$order = 'ORDER BY B.item_order';
	}
	
	$query .= " WHERE ".implode(' AND ',$where).' '.$order;
	#---------------------------------------------
	# Execute
	#---------------------------------------------		
	
	$res = $db->GetArray($query, $qparms);
	if($res) {
	
		// Combine stylesheets
		if($combine_stylesheets) {

			// Group queries & types
			$all_media = array();
			$all_timestamps = array();
			$all_timestamps_string = '';
			foreach($res as $one) {
			
				if(!empty($one['media_query'])) {
				
					$key = md5($one['media_query']);
					$all_media[$key][] = $one;
					$all_timestamps[$key][] = $one['modified'];
					
				} elseif(!empty($one['media_type'])) {
			
					$key = md5($one['media_type']);
					$all_media[$key][] = $one;
					$all_timestamps[$key][] = $one['modified'];
					
				} else {
				
					$all_media['all'][] = $one;
					$all_timestamps['all'][] = $one['modified'];
				}

				$all_timestamps_string .= $one['modified']; // <- This is for media param
			}			

			// media parameter...
			if (isset($params['media'])) {

				// combine all matches into one stylesheet
				$filename = 'stylesheet_combined_'.md5($design_id.$use_https.serialize($params).$all_timestamps_string.$fnsuffix).'.css';
				$fn = cms_join_path($cache_dir,$filename);	
	
				if(!file_exists($fn)) {			
				
					$text = '';
					foreach ($res as $one) {
					
							$text .= $one['content'];
							// moved this to bottom, comments on top of stylesheets cause invalid css when using @charset
							$text .= "\n/* End of Stylesheet: ".$one['name']." Modified On ".strftime('%x %X',$one['modified'])." */\n";
							if( !endswith($text,"\n") ) $text .= "\n";
					}

					cms_stylesheet_writeCache($fn, $text, $trimbackground, $smarty, $forceblackandwhite);
				}

				cms_stylesheet_toString($filename, $params['media'], '', $root_url, $stylesheet, $params);
					
			} else {
		
				// Group timestamps
				foreach($all_timestamps as $k=>$v) {
				
					$all_timestamps[$k] = implode($v);
				}

				foreach($all_media as $hash=>$onemedia) {
				
					// combine all matches into one stylesheet.
					$filename = 'stylesheet_combined_'.md5($design_id.$use_https.serialize($params).$all_timestamps[$hash].$fnsuffix).'.css';
					$fn = cms_join_path($cache_dir,$filename);
					
					// Get media_type and media_query
					$media_query = $onemedia[0]['media_query'];
					$media_type = $onemedia[0]['media_type'];
					
					if(!file_exists($fn)) {
					
						$text = '';
						foreach($onemedia as $one) {
						
							$text .= $one['content'];
							// moved this to bottom, comments on top of stylesheets cause invalid css when using @charset
							$text .= "\n/* Stylesheet: ".$one['name']." Modified On ".strftime('%x %X',$one['modified'])." */\n";
							if( !endswith($text,"\n") ) $text .= "\n";
						}

						cms_stylesheet_writeCache($fn, $text, $trimbackground, $smarty, $forceblackandwhite);
					}

					cms_stylesheet_toString($filename, $media_query, $media_type, $root_url, $stylesheet, $params);
				}
			}
			
		// Do not combine stylesheets	
		} else {

			foreach ($res as $one) {
			
			    if (isset($params['media'])) {
				
			        $media_query = $params['media'];				
			        $media_type  = '';
                } else {
				
                    $media_query = $one['media_query'];				
                    $media_type  = $one['media_type'];
                }
				
				$filename = 'stylesheet_'.md5('single'.$one['id'].$use_https.$one['modified'].$fnsuffix).'.css';
				$fn = cms_join_path($cache_dir,$filename);
				
				if (!file_exists($fn)) {
		
					cms_stylesheet_writeCache($fn, $one['content'], $trimbackground, $smarty, $forceblackandwhite);					
				}

				cms_stylesheet_toString($filename, $media_query, $media_type, $root_url, $stylesheet, $params);
			}
		}
	}

	#---------------------------------------------
	# Cleanup & output
	#---------------------------------------------		
	
	$stylesheet = preg_replace("/\{\/?php\}/", "", $stylesheet);

	// Remove last comma at the end when $params['nolinks'] is set
	if( isset($params['nolinks']) && endswith($stylesheet,',') ) {
	
		$stylesheet = substr($stylesheet,0,strlen($stylesheet)-1);
	}

	// Notify core that we are no longer at stylesheet, pretty ugly way to do this. -Stikki-
	$CMS_STYLESHEET = 0;
	unset($CMS_STYLESHEET);
	unset($GLOBALS['CMS_STYLESHEET']);
	
	if( isset($params['assign']) ){
	
	    $smarty->assign(trim($params['assign']), $stylesheet);
	    return;
    }
	
	return $stylesheet;
	
} // end of main

/**********************************************************
	Misc functions
**********************************************************/

function cms_stylesheet_writeCache($filename, $string, $trimbackground, &$smarty, $forceblackandwhite = false)
{
	$_contents = '';

	// Smarty processing
	$smarty->left_delimiter = '[[';
	$smarty->right_delimiter = ']]';

	try {
		$_contents = $smarty->fetch('string:'.$string);
	}
	catch (SmartyException $e)
	{
	  debug_to_log('Error Processing Stylesheet');
	  debug_to_log($e->GetMessage());
	  audit('','Plugin: cms_stylesheet', 'Smarty Compile process failed, unable to write cache file');
	  return;
	}

	$smarty->left_delimiter = '{';
	$smarty->right_delimiter = '}';					

	// Fix background
	if($trimbackground) {
	
		$_contents = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $_contents);
		$_contents = preg_replace('/\w*?(background[-image]*[\s\w]*\:[\#\s\w]*)url\(.*\)/','$1;',$_contents);
		$_contents = preg_replace('/\w*?(background[-image]*[\s\w]*\:[\s]*\;)/','',$_contents);
		$_contents = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '\\1transparent\\2', $_contents);
		$_contents = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $_contents);
		$_contents = preg_replace('/(\w*?background.*?\:\w*?).*?(;.*?)/', '', $_contents);
	}

	if( $forceblackandwhite ) {
		$_contents .= 'body.mceContentBody { background: #fff; color: #000; !important }'."\n";
	}

	// Write file
	$fh = fopen($filename,'w');
	fwrite($fh, $_contents);
	fclose($fh);

} // end of writeCache

function cms_stylesheet_toString($filename, $media_query = '', $media_type = '', $root_url, &$stylesheet, &$params)
{
	if( !endswith($root_url,'/') ) $root_url .= '/';
	if( isset($params['nolinks']) )
	{
		$stylesheet .= $root_url.$filename.',';
	} else {

		if (!empty($media_query)) {

			$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" media="'.$media_query.'" />'."\n";
		} elseif (!empty($media_type)) {

			$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" media="'.$media_type.'" />'."\n";
		} else {

			$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" />'."\n";
		}
	}
	
} // end of toString

/**********************************************************
	Help functions
**********************************************************/

function smarty_cms_help_function_cms_stylesheet()
{
	echo lang('help_function_cms_stylesheet');
} // end of help

function smarty_cms_about_function_cms_stylesheet()
{
	?>
	<p>Author: jeff&lt;jeff@ajprogramming.com&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>Rework from {stylesheet}</li>
		<li>(Stikki and Calguy1000) Code cleanup, Added grouping by media type / media query, Fixed cache issues</li>
	</ul>
	<?php
} // end of about
?>
