<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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

function smarty_function_cms_stylesheet($params, $smarty)
{
	#---------------------------------------------
	# Initials
	#---------------------------------------------

    $gCms = CmsApp::get_instance();
	$config = $gCms->GetConfig();

	global $CMS_LOGIN_PAGE;
	global $CMS_STYLESHEET;
	$CMS_STYLESHEET = 1;
	$name = null;
	$design_id = -1;
	$use_https = 0;
	$cache_dir = $config['css_path'];
	$stylesheet = '';
	$combine_stylesheets = true;
	$fnsuffix = '';
	$trimbackground = FALSE;
	$minify = FALSE;
	$root_url = $config['css_url'];
	$auto_https = 1;
	$userid = get_userid(false);

	#---------------------------------------------
	# Trivial Exclusion
	#---------------------------------------------

	if( isset($CMS_LOGIN_PAGE) ) return;

	#---------------------------------------------
	# Read parameters
	#---------------------------------------------

    try {
        if (!empty($params['names'])) {
            $name = array_map('trim', explode(',', $params['names']));
        }
        else if (!empty($params['name'])) {
            $name = array_map('trim', explode(',', $params['name']));
        }
        else if (!empty($params['designid'])) {
            $design_id = (int)$params['designid'];
        } else {
            $content_obj = $gCms->get_content_object();
            if( !is_object($content_obj) ) return;
            $design_id = (int) $content_obj->GetPropertyValue('design_id');
            $use_https = (int) $content_obj->Secure();
        }
        if( !$name && $design_id < 1 ) throw new \RuntimeException('Invalid parameters, or there is no design attached to the content page');

        // @todo: change this stuff to just use // instead of protocol specific URL.
        if( isset($params['auto_https']) && $params['auto_https'] == 0 ) $auto_https = 0;
        if( isset($params['https']) ) $use_https = cms_to_bool($params['https']);
        if( $auto_https && $gCms->is_https_request() ) $use_https = 1;

        if($use_https && isset($config['ssl_url'])) $root_url = $config['ssl_css_url'];
        if( isset($params['nocombine']) ) $combine_stylesheets = !cms_to_bool($params['nocombine']);

        if( isset($params['stripbackground']) )	{
            $trimbackground = cms_to_bool($params['stripbackground']);
            $fnsuffix = '_e_';
        }
        if( !empty($params['minify']) ) $minify = cms_to_bool($params['minify']);
        
        if($userid) {
            $minify = FALSE;
            $params['cache'] = '0';
            $params['preload'] = '0';
        }

        #---------------------------------------------
        # Build query
        #---------------------------------------------

        $query = null;
        if( is_array($name) && !empty($name) ) {
            // Handle stylesheet names (always array now)
            $res = array();
            foreach( $name as $single_name ) {
                $single_query = new CmsLayoutStylesheetQuery( [ 'fullname'=>$single_name ] );
                $matches = $single_query->GetMatches();
                if( $matches ) $res = array_merge($res, $matches);
            }
            if( empty($res) ) throw new \RuntimeException('No stylesheets matched the criteria specified');
            $nrows = count($res);
        } else if( $design_id > 0 ) {
            // stylesheet by design id
            $query = new \CmsLayoutStylesheetQuery( [ 'design'=>$design_id ] );
        }
        if( !$query && !is_array($name) ) throw new \RuntimeException('Problem: Could not build a stylesheet query with the provided data');

        #---------------------------------------------
        # Execute
        #---------------------------------------------

        if( !is_array($name) ) {
            $nrows = $query->TotalMatches();
            if( !$nrows ) throw new \RuntimeException('No stylesheets matched the criteria specified');
            $res = $query->GetMatches();
        }
        // $res is already populated for multiple names case

        // we have some output, and the stylesheet objects have already been loaded.

        // Handle inline parameter
        if( !empty($params['inline']) && cms_to_bool($params['inline']) ) {
            $css_content = '';
            foreach( $res as $one ) {
                $smarty->left_delimiter = '[[';
                $smarty->right_delimiter = ']]';
                $tmp = $smarty->force_compile;
                $smarty->force_compile = 1;
                $css_content .= $smarty->fetch('cms_stylesheet:'.$one->get_name());
                $smarty->force_compile = $tmp;
                $smarty->left_delimiter = '{';
                $smarty->right_delimiter = '}';
            }
            if($minify) {
                $css_content = preg_replace('/\/\*[^*]*\*+([^\/*][^*]*\*+)*\//', '', $css_content);
                $css_content = preg_replace('/\s+/', ' ', $css_content);
                $css_content = preg_replace('/;\s*}/', '}', $css_content);
                $css_content = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;'], ['{', '{', '}', '}', ':', ':', ';', ';'], $css_content);
                $css_content = trim($css_content);
            }
            $stylesheet = '<style>' . $css_content . '</style>';
        } else {

        // Combine stylesheets
        if($combine_stylesheets) {

            // Group queries & types
            $all_media = array();
            $all_timestamps = array();
            foreach( $res as $one ) {
                $mq = $one->get_media_query();
                $mt = implode(',',$one->get_media_types());
                if( !empty($mq) ) {
                    $key = md5($mq);
                    $all_media[$key][] = $one;
                    $all_timestamps[$key][] = $one->get_modified();
                } else if( !$mt ) {
                    $all_media['all'][] = $one;
                    $all_timestamps['all'][] = $one->get_modified();
                } else {
                    $key = md5($mt);
                    $all_media[$key][] = $one;
                    $all_timestamps[$key][] = $one->get_modified();
                }

            }

            // media parameter...
            if( isset($params['media']) && strtolower($params['media']) != 'all' ) {
                // media parameter is deprecated.

                // combine all matches into one stylesheet
                $hash_params = $params;

                if(isset($params['cache']) && !cms_to_bool($params['cache'])) $hash_params['_nocache_time'] = time();
                $filename = 'stylesheet_combined_'.md5($design_id.$use_https.serialize($hash_params).serialize($all_timestamps).$fnsuffix).'.css';
                $fn = cms_join_path($cache_dir,$filename);

                if( !file_exists($fn) || (isset($params['cache']) && !cms_to_bool($params['cache'])) ) {
                    $list = array();
                    foreach ($res as $one) {
                        if( in_array($params['media'],$one->get_media_types()) ) $list[] = $one->get_name();
                    }

                    cms_stylesheet_writeCache($fn, $list, $trimbackground, $smarty, $minify);
                }

                cms_stylesheet_toString($filename, $params['media'], '', $root_url, $stylesheet, $params);

            } else {

                foreach($all_media as $hash=>$onemedia) {

                    // combine all matches into one stylesheet.
                    $hash_params = $params;
                    if(isset($params['cache']) && !cms_to_bool($params['cache'])) $hash_params['_nocache_time'] = time();
                    $filename = 'stylesheet_combined_'.md5($design_id.$use_https.serialize($hash_params).serialize($all_timestamps[$hash]).$fnsuffix).'.css';
                    $fn = cms_join_path($cache_dir,$filename);

                    // Get media_type and media_query
                    $media_query = $onemedia[0]->get_media_query();
                    $media_type = implode(',',$onemedia[0]->get_media_types());

                    if( !is_file($fn) || (isset($params['cache']) && !cms_to_bool($params['cache'])) ) {
                        $list = array();

                        foreach( $onemedia as $one ) {
                            $list[] = $one->get_name();
                        }

                        cms_stylesheet_writeCache($fn, $list, $trimbackground, $smarty, $minify);
                    }

                    cms_stylesheet_toString($filename, $media_query, $media_type, $root_url, $stylesheet, $params);
                }
            }

            // Do not combine stylesheets
        } else {
            foreach ($res as $one) {

                if (isset($params['media'])) {
                    if( !in_array($params['media'],$one->get_media_types()) ) continue;
                    $media_query = '';
                    $media_type = $params['media'];
                } else {
                    $media_query = $one->get_media_query();
                    $media_type  = implode(',',$one->get_media_types());
                }

                $hash_base = 'single'.$one->get_id().$use_https.$one->get_modified().$fnsuffix;
                if(isset($params['cache']) && !cms_to_bool($params['cache'])) $hash_base .= time();
                $filename = 'stylesheet_'.md5($hash_base).'.css';
                $fn = cms_join_path($cache_dir,$filename);

                if (!file_exists($fn) || (isset($params['cache']) && !cms_to_bool($params['cache'])) ) cms_stylesheet_writeCache($fn, $one->get_name(), $trimbackground, $smarty, $minify);

                cms_stylesheet_toString($filename, $media_query, $media_type, $root_url, $stylesheet, $params);
            }
        }

        } // end inline else

        #---------------------------------------------
        # Cleanup & output
        #---------------------------------------------

        if( strlen($stylesheet) ) {
            $stylesheet = preg_replace("/\{\/?php\}/", "", $stylesheet);

            // Remove last comma at the end when $params['nolinks'] is set
            if( isset($params['nolinks']) && cms_to_bool($params['nolinks']) && endswith($stylesheet,',') ) {
                $stylesheet = substr($stylesheet,0,strlen($stylesheet)-1);
            }
        }
    } catch( \Exception $e ) {
        audit('','cms_stylesheet',$e->GetMessage());
        $stylesheet = '<!-- cms_stylesheet error: '.$e->GetMessage().' -->';
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

function cms_stylesheet_writeCache($filename, $list, $trimbackground, &$smarty, $minify = true)
{
	$_contents = '';
    if( is_string($list) && !is_array($list) ) $list = array($list);

	// Smarty processing
	$smarty->left_delimiter = '[[';
	$smarty->right_delimiter = ']]';

	try {
        foreach( $list as $name ) {
            // force the stylesheet to compile because of smarty bug:  https://github.com/smarty-php/smarty/issues/72
            $tmp = $smarty->force_compile;
            $smarty->force_compile = 1;
            $_contents .= $smarty->fetch('cms_stylesheet:'.$name);
            $smarty->force_compile = $tmp;
        }
	}
	catch (SmartyException $e) {
            // why not just re-throw the exception as it may have a smarty error in it.
            debug_to_log('Error Processing Stylesheet');
            debug_to_log($e->GetMessage());
            audit('','Plugin: cms_stylesheet', 'Smarty Compile process failed, an error in the template?');
            return;
	}

	$smarty->left_delimiter = '{';
	$smarty->right_delimiter = '}';

	// Fix background
	if($trimbackground) {

		$_contents = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $_contents);
		$_contents = preg_replace('/\w*?(background(-image)?[\s\w]*\:[\#\s\w]*)url\(.*\)/','$1;',$_contents);
		$_contents = preg_replace('/\w*?(background(-image)?[\s\w]*\:[\s]*\;)/','',$_contents);
		$_contents = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '\\1transparent\\2', $_contents);
		$_contents = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $_contents);
		$_contents = preg_replace('/(\w*?background.*?\:\w*?).*?(;.*?)/', '', $_contents);
	}

    \CMSMS\HookManager::do_hook('Core::StylesheetPostRender', [ 'content' => &$_contents ] );

	// Minify CSS
	if($minify) {
		$_contents = preg_replace('/\/\*[^*]*\*+([^\/*][^*]*\*+)*\//', '', $_contents); // Remove comments
		$_contents = preg_replace('/\s+/', ' ', $_contents); // Collapse whitespace
		$_contents = preg_replace('/;\s*}/', '}', $_contents); // Remove semicolon before closing brace
		$_contents = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;'], ['{', '{', '}', '}', ':', ':', ';', ';'], $_contents);
		$_contents = trim($_contents);
	}

	// Write file
	$fh = fopen($filename,'w');
	fwrite($fh, $_contents);
	fclose($fh);

} // end of writeCache

function cms_stylesheet_toString($filename, $media_query = '', $media_type = '', $root_url = '', &$stylesheet = '', &$params = [])
{
	if( !endswith($root_url,'/') ) $root_url .= '/';
	if( isset($params['nolinks']) )	{
		$stylesheet .= $root_url.$filename.',';
	} else {
		if( isset($params['preload']) && cms_to_bool($params['preload']) ) {
			if (!empty($media_query)) {
				$stylesheet .= '<link rel="preload" href="'.$root_url.$filename.'" as="style" media="'.$media_query.'" onload="this.onload=null;this.rel=\'stylesheet\'" />'."\n";
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" media="'.$media_query.'" />'."\n";
			} elseif (!empty($media_type)) {
				$stylesheet .= '<link rel="preload" href="'.$root_url.$filename.'" as="style" media="'.$media_type.'" onload="this.onload=null;this.rel=\'stylesheet\'" />'."\n";
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" media="'.$media_type.'" />'."\n";
			} else {
				$stylesheet .= '<link rel="preload" href="'.$root_url.$filename.'" as="style" onload="this.onload=null;this.rel=\'stylesheet\'" />'."\n";
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" />'."\n";
			}
		} else {
			if (!empty($media_query)) {
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" media="'.$media_query.'" />'."\n";
			} elseif (!empty($media_type)) {
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" media="'.$media_type.'" />'."\n";
			} else {
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.$filename.'" />'."\n";
			}
		}
	}

} // end of toString

/**********************************************************
	Help functions
**********************************************************/

function smarty_cms_about_function_cms_stylesheet()
{
	?>
	<p>Author: jeff&lt;jeff@ajprogramming.com&gt;</p>
	<p>Enhanced by: Magal Hezi (v3.0)</p>

	<h3>Version 3.0 New Features:</h3>
	<ul>
		<li><strong>names parameter:</strong> Load multiple stylesheets: <code>{cms_stylesheet names="style1,style2,style3"}</code></li>
		<li><strong>CSS Minification:</strong> Enabled by default (use <code>nominify=1</code> to disable)</li>
		<li><strong>cache parameter:</strong> Use <code>cache=0</code> to force cache regeneration for development</li>
		<li><strong>preload parameter:</strong> Use <code>preload=1</code> for performance optimization with preload links</li>
		<li><strong>inline parameter:</strong> Use <code>inline=1</code> to output CSS as &lt;style&gt; tags instead of &lt;link&gt; tags</li>
		<li><strong>Admin Detection:</strong> Automatically disables minification, caching, and preload for logged-in users</li>
	</ul>

	<h3>Examples:</h3>
	<ul>
		<li>Multiple stylesheets: <code>{cms_stylesheet names="header,footer,main"}</code></li>
		<li>Development mode: <code>{cms_stylesheet names="style" cache=0 nominify=1}</code></li>
		<li>Performance mode: <code>{cms_stylesheet names="critical" preload=1}</code></li>
		<li>Inline CSS: <code>{cms_stylesheet names="critical" inline=1}</code></li>
		<li>Combined options: <code>{cms_stylesheet names="app,theme" preload=1 assign="my_css"}</code></li>
	</ul>

	<h3>Parameters:</h3>
	<ul>
		<li><code>names</code> - Comma-separated list of stylesheet names</li>
		<li><code>cache</code> - Set to 0 to disable caching (default: 1)</li>
		<li><code>nominify</code> - Set to 1 to disable minification (default: 0)</li>
		<li><code>preload</code> - Set to 1 to enable preload links (default: 0)</li>
		<li><code>inline</code> - Set to 1 to output as &lt;style&gt; tags (default: 0)</li>
		<li><code>assign</code> - Assign output to Smarty variable instead of displaying</li>
	</ul>

	<p>Change History:</p>
	<ul>
		<li>v3.0 (2025-12-16): Added multiple stylesheet support, minification, preload, inline, and admin detection</li>
		<li>Rework from {stylesheet}</li>
		<li>(Stikki and Calguy1000) Code cleanup, Added grouping by media type / media query, Fixed cache issues</li>
	</ul>
	<?php
} // end of about
?>
