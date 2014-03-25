<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
#$Id$

/**
 * Miscellaneous support functions
 *
 * @package CMS
 * @license GPL
 */



/**
 * Redirects to relative URL on the current site.
 *
 * If headers have not been sent this method will use header based redirection.
 * Otherwise javascript redirection will be used.
 *
 * @author http://www.edoceo.com/
 * @since 0.1
 * @param string $to The url to redirect to
 */
function redirect($to)
{
  $_SERVER['PHP_SELF'] = null;

  $schema = 'http';
  if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) $schema = 'https';

  $host = $_SERVER['HTTP_HOST'];
  $components = parse_url($to);
  if(count($components) > 0) {
    $to =  (isset($components['scheme']) && startswith($components['scheme'], 'http') ? $components['scheme'] : $schema) . '://';
    $to .= isset($components['host']) ? $components['host'] : $host;
    $to .= isset($components['port']) ? ':' . $components['port'] : '';
    if(isset($components['path'])) {
      if(in_array(substr($components['path'],0,1),array('\\','/'))) {
	//Path is absolute, just append.
	$to .= $components['path'];
      }
      //Path is relative, append current directory first.
      else if (isset($_SERVER['PHP_SELF']) && !is_null($_SERVER['PHP_SELF'])) { //Apache
	$to .= (strlen(dirname($_SERVER['PHP_SELF'])) > 1 ?  dirname($_SERVER['PHP_SELF']).'/' : '/') . $components['path'];
      }
      else if (isset($_SERVER['REQUEST_URI']) && !is_null($_SERVER['REQUEST_URI'])) { //Lighttpd
	if (endswith($_SERVER['REQUEST_URI'], '/')) {
	  $to .= (strlen($_SERVER['REQUEST_URI']) > 1 ? $_SERVER['REQUEST_URI'] : '/') . $components['path'];
	}
	else {
	  $dn = dirname($_SERVER['REQUEST_URI']);
	  if( !endswith($dn,'/') ) $dn .= '/';
	  $to .= $dn . $components['path'];
	}
      }
    }
    $to .= isset($components['query']) ? '?' . $components['query'] : '';
    $to .= isset($components['fragment']) ? '#' . $components['fragment'] : '';
  }
  else {
    $to = $schema."://".$host."/".$to;
  }

  session_write_close();

  $debug = false;
  if( class_exists('CmsApp') ) {
    $config = cmsms()->GetConfig();
    $debug = $config['debug'];
  }

  if (headers_sent() && !$debug) {
    // use javascript instead
    echo '<script type="text/javascript">
          <!--location.replace("'.$to.'"); // -->
          </script>
          <noscript>
          <meta http-equiv="Refresh" content="0;URL='.$to.'">
          </noscript>';
    exit;
  }
  else {
    if ( $debug ) {
      echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
      echo "<a href=\"".$to."\">".$to."</a><br />";
      echo '<div id="DebugFooter">';
      foreach (cmsms()->get_errors() as $error) {
	echo $error;
      }
      echo '</div> <!-- end DebugFooter -->';
      exit();
    }
    else {
      header("Location: $to");
      exit();
    }
  }
}



/**
 * Given a page ID or an alias, redirect to it.
 * Retrieves the URL of the specified page, and performs a redirect
 *
 * @param mixed $alias An integer page id or a string page alias.
 */
function redirect_to_alias($alias)
{
  $manager = cmsms()->GetHierarchyManager();
  $node = $manager->sureGetNodeByAlias($alias);
  if( !$node ) {
	// put mention into the admin log
    audit('','Core','Attempt to redirect to invalid alias: '.$alias);
    return;
  }
  $content = $node->GetContent();
  if (!is_object($content)) {
    audit('','Core','Attempt to redirect to invalid alias: '.$alias);
    return;
  }
  if ($content->GetURL() != '') redirect($content->GetURL());
}



/**
 * Calculate the difference in seconds between two microtime() values.
 *
 * @since 0.3
 * @param string $a Earlier microtime value
 * @param string $b Later microtime value
 * @return int The difference.
 */
function microtime_diff($a, $b)
{
  list($a_dec, $a_sec) = explode(" ", $a);
  list($b_dec, $b_sec) = explode(" ", $b);
  return $b_sec - $a_sec + $b_dec - $a_dec;
}



/**
 * Joins a path together using platform specific directory separators.
 * Taken from: http://www.php.net/manual/en/ref.dir.php
 *
 * This method should NOT be used for building URLS.
 *
 * This method accepts a variable number of string arguments.
 * i.e: $out = cms_join_path($dir1,$dir2,$dir3,$filename);
 * or $out = cms_join_path($dir1,$dir2,$filename);
 *
 * @since 0.14
 * @return string
 */
function cms_join_path()
{
  $args = func_get_args();
  return implode(DIRECTORY_SEPARATOR,$args);
}



/**
 * Perform HTML entity conversion on a string.
 *
 * @see htmlentities
 * @param string $val The input string
 * @param string $param A flag indicating how quotes should be handled (see htmlentities) (ignored)
 * @param string $charset $val The input character set (ignored)
 * @param bool $convert_single_quotes A flag indicating wether single quotes should be converted to entities.
 * @return string the converted string.
 */
function cms_htmlentities($val, $param=ENT_QUOTES, $charset="UTF-8", $convert_single_quotes = false)
{
  if ($val == "") return "";

  $val = str_replace( "&#032;", " ", $val );
  $val = str_replace( "&"            , "&amp;"         , $val );
  $val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
  $val = str_replace( "-->"          , "--&#62;"       , $val );
  $val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
  $val = str_replace( ">"            , "&gt;"          , $val );
  $val = str_replace( "<"            , "&lt;"          , $val );
  $val = str_replace( "\""           , "&quot;"        , $val );
  $val = preg_replace( "/\\$/"      , "&#036;"        , $val );
  $val = str_replace( "!"            , "&#33;"         , $val );
  $val = str_replace( "'"            , "&#39;"         , $val );

  if ($convert_single_quotes) {
    $val = str_replace("\\'", "&apos;", $val);
    $val = str_replace("'", "&apos;", $val);
  }

  return $val;
}


/**
 * Convert a string into UTF-8 entities.
 *
 * @internal
 * @deprecated
 * @param string Input string
 * @return string
 * Rolf: used in admin/listmodules.php
 */
function cms_utf8entities($val)
{
  if ($val == "") return "";
  $val = str_replace( "&#032;", " ", $val );
  $val = str_replace( "&"  , "\u0026" , $val );
  $val = str_replace( ">"  , "\u003E" , $val );
  $val = str_replace( "<"  , "\u003C" , $val );

  $val = str_replace( "\"" , "\u0022" , $val );
  $val = str_replace( "!"  , "\u0021" , $val );
  $val = str_replace( "'"  , "\u0027" , $val );

  return $val;
}



/**
 * A function to output a backtrace into the generated log file.
 *
 * @see debug_to_log, debug_bt
 * Rolf: Looks like not used
 */
function debug_bt_to_log()
{
  if( cmsms()->config['debug_to_log'] || check_login(TRUE) ) {
      $bt=debug_backtrace();
      $file = $bt[0]['file'];
      $line = $bt[0]['line'];

    $out = array();
    $out[] = "Backtrace in $file on line $line";

    $bt = array_reverse($bt);
    foreach($bt as $trace) {
      if( $trace['function'] == 'debug_bt_to_log' ) continue;

      $file = '';
      $line = '';
      if( isset($trace['file']) ) {
	$file = $trace['file'];
	$line = $trace['line'];
      }
      $function = $trace['function'];
      $str = "$function";
      if( $file ) $str .= " at $file:$line";
      $out[] = $str;
    }

    $filename = TMP_CACHE_LOCATION . '/debug.log';
    foreach ($out as $txt) {
      error_log($txt . "\n", 3, $filename);
    }
  }
}



/**
 * A function to generate a backtrace in a readable format.
 *
 * This function does not return but echoes output.
 */
function debug_bt()
{
    $bt=debug_backtrace();
    $file = $bt[0]['file'];
    $line = $bt[0]['line'];

    echo "\n\n<p><b>Backtrace in $file on line $line</b></p>\n";

    $bt = array_reverse($bt);
    echo "<pre><dl>\n";
    foreach($bt as $trace) {
      $file = $trace['file'];
      $line = $trace['line'];
      $function = $trace['function'];
      $args = implode(',', $trace['args']);
      echo "
        <dt><b>$function</b>($args) </dt>
        <dd>$file on line $line</dd>
		";
    }
    echo "</dl></pre>\n";
}



/**
* Debug function to display $var nicely in html.
*
* @param mixed $var The data to display
* @param string $title (optional) title for the output.  If null memory information is output.
* @param bool $echo_to_screen (optional) Flag indicating wether the output should be echoed to the screen or returned.
* @param bool $use_html (optional) flag indicating wether html or text should be used in the output.
* @param bool $showtitle (optional) flag indicating wether the title field should be displayed in the output.
* @return string
*/
function debug_display($var, $title="", $echo_to_screen = true, $use_html = true,$showtitle = TRUE)
{
  global $starttime;
  if( !$starttime ) $starttime = microtime();

  ob_start();

  if( $showtitle ) {
    $titleText = "Debug: ";
    if($title) $titleText = "Debug display of '$title':";
    $titleText .= '(' . microtime_diff($starttime,microtime()) . ')';
    if (function_exists('memory_get_usage')) $titleText .= ' - (usage: '.memory_get_usage().')';

    $memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():'');
    if( $memory_peak ) $titleText .= ' - (peak: '.$memory_peak.')';

    if ($use_html) {
      echo "<div><b>$titleText</b>\n";
    }
    else {
      echo "$titleText\n";
    }
  }

  if(FALSE == empty($var)) {
    if ($use_html) echo '<pre>';
    if(is_array($var)) {
      echo "Number of elements: " . count($var) . "\n";
      print_r($var);
    }
    elseif(is_object($var)) {
      print_r($var);
    }
    elseif(is_string($var)) {
      if( $use_html ) {
	print_r(htmlentities(str_replace("\t", '  ', $var)));
      }
      else {
	print_r($var);
      }
    }
    elseif(is_bool($var)) {
      echo $var === true ? 'true' : 'false';
    }
    else {
      print_r($var);
    }
    if ($use_html) echo '</pre>';
  }
  if ($use_html) echo "</div>\n";

  $output = ob_get_contents();
  ob_end_clean();

  if($echo_to_screen) echo $output;
  return $output;
}



/**
 * Display $var nicely only if $config["debug"] is set.
 *
 * @param mixed $var
 * @param string $title
 */
function debug_output($var, $title="")
{
  if(cmsms()->config["debug"] == true) debug_display($var, $title, true);
}



/**
 * Debug function to output debug information about a variable in a formatted matter
 * to a debug file.
 *
 * @param mixed $var    data to display
 * @param string $title optional title.
 * @param string $filename optional output filename
 */
function debug_to_log($var, $title='',$filename = '')
{
  if( cmsms()->config['debug_to_log'] || check_login(TRUE) ) {
    if( $filename == '' ) {
      $filename = TMP_CACHE_LOCATION . '/debug.log';
      $x = @filemtime($filename);
      if( $x !== FALSE && $x < (time() - 24 * 3600) ) @unlink($filename);
    }
    $errlines = explode("\n",debug_display($var, $title, false, false, false));
    foreach ($errlines as $txt) {
      error_log($txt . "\n", 3, $filename);
    }
  }
}



/**
 * Display $var nicely to the cmsms()->errors array if $config['debug'] is set.
 *
 * @param mixed $var
 * @param string $title
 */
function debug_buffer($var, $title="")
{
  $config = cmsms()->GetConfig();
  if($config["debug"] == true) cmsms()->add_error(debug_display($var, $title, false, true));
}



/**
 * Debug an sql command.
 *
 * @internal
 * @param string SQL query
 * @param bool (unused)
 * Rolf: only used in lib/adodb.functions.php
 */
function debug_sql($str, $newline = false)
{
  $config = cmsms()->GetConfig();
  if($config["debug"] == true) cmsms()->add_error(debug_display($str, '', false, true));
}



/**
* Return $value if it's set and same basic type as $default_value,
* Otherwise return $default_value. Note. Also will trim($value)	if $value is not numeric.
*
* @ignore
* @param string $value
* @param mixed $default_value
* @param mixed $session_key
* @deprecated
* @return mixed
*/
function _get_value_with_default($value, $default_value = '', $session_key = '')
{
    if($session_key != '') {
        if(isset($_SESSION['default_values'][$session_key])) $default_value = $_SESSION['default_values'][$session_key];
    }

    // set our return value to the default initially and overwrite with $value if we like it.
    $return_value = $default_value;

    if(isset($value)) {
        if(is_array($value)) {
            // $value is an array - validate each element.
            $return_value = array();
            foreach($value as $element) {
                $return_value[] = _get_value_with_default($element, $default_value);
            }
        }
        else {
            if(is_numeric($default_value)) {
                if(is_numeric($value)) {
                    $return_value = $value;
                }
            }
            else {
                $return_value = trim($value);
            }
        }
    }

    if($session_key != '') $_SESSION['default_values'][$session_key] = $return_value;
    return $return_value;
}



/**
 * Retrieve the $value from the $parameters array checking for $parameters[$value] and
 * $params[$id.$value].
 * Returns $default if $value is not in $params array.
 * Note: This function will also trim() string values.
 *
 * @param array $parameters
 * @param string $value
 * @param mixed $default_value
 * @param string $session_key
 * @return mixed
 */
function get_parameter_value($parameters, $value, $default_value = '', $session_key = '')
{
    if($session_key != '') {
        if(isset($_SESSION['parameter_values'][$session_key])) $default_value = $_SESSION['parameter_values'][$session_key];
    }

    // set our return value to the default initially and overwrite with $value if we like it.
    $return_value = $default_value;
    if(isset($parameters[$value])) {
        if(is_bool($default_value)) {
            // want a bool return_value
            if(isset($parameters[$value])) $return_value = (bool)$parameters[$value];
        }
        else {
            // is $default_value a number?
            $is_number = false;
            if(is_numeric($default_value)) $is_number = true;

            if(is_array($parameters[$value])) {
                // $parameters[$value] is an array - validate each element.
                $return_value = array();
                foreach($parameters[$value] as $element) {
                    $return_value[] = _get_value_with_default($element, $default_value);
                }
            }
            else {
                if(is_numeric($default_value)) {
                    // default value is a number, we only like $parameters[$value] if it's a number too.
                    if(is_numeric($parameters[$value])) $return_value = $parameters[$value];
                }
                elseif(is_string($default_value)) {
                    $return_value = trim($parameters[$value]);
                }
                else {
                    $return_value = $parameters[$value];
                }
            }
        }
    }

    if($session_key != '') $_SESSION['parameter_values'][$session_key] = $return_value;
    return $return_value;
}



/**
 * A method to remove a permission from the database.
 *
 * @internal
 * @ignore
 * @access private
 * @param string The permission name
 * @deprecated
 */
function cms_mapi_remove_permission($permission_name)
{
    try {
        $perm = CmsPermission::load($permission_name);
        $perm->delete();
    }
    catch( Exception $e ) {
    }
}



/**
 * A method to add a permission to the CMSMS permissions table.
 *
 * @internal
 * @ignore
 * @access private
 * @param unknown (ignored)
 * @param string  The permission name
 * @param string  The permission human readable text.
 * @deprecated
 */
function cms_mapi_create_permission($cms, $permission_name, $permission_text)
{
    try {
        $perm = new CmsPermission();
        $perm->originator = 'Other';
        $perm->name = $permission_name;
        $perm->text = $permission_text;
        $perm->save();
        return true;
    }
    catch( Exception $e ) {
        return false;
    }
}



/**
 * Check the permissions of a directory recursively to make sure that
 * we have write permission to all files.
 *
 * @param  string  $path Start directory.
 * @return bool
 */
function is_directory_writable( $path )
{
    if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) $path .= '/' ;

    $result = TRUE;
    if( $handle = @opendir( $path ) ) {
        while( false !== ( $file = readdir( $handle ) ) ) {
            if( $file == '.' || $file == '..' ) continue;

            $p = $path.$file;
            if( !@is_writable( $p ) ) return FALSE;

            if( @is_dir( $p ) ) {
                $result = is_directory_writable( $p );
                if( !$result ) return FALSE;
            }
        }
        @closedir( $handle );
    }
    else {
        return FALSE;
    }

    return TRUE;
}


/**
 * Return an array containing a list of files in a directory
 * performs a non recursive search.
 *
 * @internal
 * @param path - path to search
 * @param extensions - include only files matching these extensions
 *                     case insensitive, comma delimited
 * Rolf: only used in this file
 */
function get_matching_files($dir,$extensions = '',$excludedot = true,$excludedir = true, $fileprefix='',$excludefiles=1)
{
  $dh = @opendir($dir);
  if( !$dh ) return false;

  if( !empty($extensions) ) $extensions = explode(',',strtolower($extensions));
  $results = array();
  while( false !== ($file = readdir($dh)) ) {
    if( $file == '.' || $file == '..' ) continue;
    if( startswith($file,'.') && $excludedot ) continue;
    if( is_dir(cms_join_path($dir,$file)) && $excludedir ) continue;
    if( !empty($fileprefix) ) {
      if( $excludefiles == 1 && startswith($file,$fileprefix) ) continue;
      if( $excludefiles == 0 && !startswith($file,$fileprefix) ) continue;
    }

    $ext = strtolower(substr($file,strrpos($file,'.')+1));
    if( is_array($extensions) && count($extensions) && !in_array($ext,$extensions) ) continue;

    $results[] = $file;
  }
  closedir($dh);
  if( !count($results) ) return false;
  return $results;
}


/**
 * Return an array containing a list of files in a directory performs a recursive search.
 *
 * @param  string  $path     Start Path.
 * @param  array   $excludes Array of regular expressions indicating files to exclude.
 * @param  int     $maxdepth How deep to browse (-1=unlimited)
 * @param  string  $mode     "FULL"|"DIRS"|"FILES"
 * @param  d       $d        for internal use only
 * @return string[]
**/
function get_recursive_file_list ( $path , $excludes, $maxdepth = -1 , $mode = "FULL" , $d = 0 )
{
    $fn = function( $file, $excludes ) {
        // strip the path from the file
        if( empty($excludes) ) return false;
        foreach( $excludes as $excl ) {
            if( @preg_match( "/".$excl."/i", basename($file) ) ) return true;
        }
        return false;
    };

    if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
    $dirlist = array () ;
    if ( $mode != "FILES" ) { $dirlist[] = $path ; }
    if ( $handle = opendir ( $path ) ) {
        while ( false !== ( $file = readdir ( $handle ) ) ) {
            if( $file == '.' || $file == '..' ) continue;
            if( $fn( $file, $excludes ) ) continue;

            $file = $path . $file ;
            if ( ! @is_dir ( $file ) ) { if ( $mode != "DIRS" ) { $dirlist[] = $file ; } }
            elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) ) {
                $result = get_recursive_file_list ( $file . '/' , $excludes, $maxdepth , $mode , $d + 1 ) ;
                $dirlist = array_merge ( $dirlist , $result ) ;
            }
        }
        closedir ( $handle ) ;
    }
    if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
    return ( $dirlist ) ;
}


/**
 * A function to recursively delete all files and folders in a directory; synonymous with rm -r.
 *
 * @param string $dirname The directory name
 * @return bool
 */
function recursive_delete( $dirname )
{
    // all subdirectories and contents:
    if(is_dir($dirname))$dir_handle=opendir($dirname);
    while($file=readdir($dir_handle)) {
        if($file!="." && $file!="..") {
            if(!is_dir($dirname."/".$file)) {
                if( !@unlink ($dirname."/".$file) ) {
                    closedir( $dir_handle );
                    return false;
                }
            }
            else {
                recursive_delete($dirname."/".$file);
            }
        }
    }
    closedir($dir_handle);
    if( ! @rmdir($dirname) ) return false;
    return true;
}



/**
 * A function to recursively chmod all files and folders in a directory.
 *
 * @see chmod
 * @param string $path The start location
 * @param int $mode The octal mode
 * Rolf: only used in admin/listmodules.php
 */
function chmod_r( $path, $mode )
{
    if( !is_dir( $path ) ) return chmod( $path, $mode );

    $dh = @opendir( $path );
    if( !$dh ) return FALSE;

    while( $file = readdir( $dh ) ) {
        if( $file == '.' || $file == '..' ) continue;

        $p = $path.DIRECTORY_SEPARATOR.$file;
        if( is_dir( $p ) ) {
            if( !@chmod_r( $p, $mode ) ) {
                closedir( $dh );
                return false;
            }
        }
        else if( !is_link( $p ) ) {
            if( !@chmod( $p, $mode ) ) {
                closedir( $dh );
                return false;
            }
        }
    }
    @closedir( $dh );
    return @chmod( $path, $mode );
}



/**
 * A convenience function to test wether one string starts with another.
 *
 * i.e:  startswith('The Quick Brown Fox','The');
 *
 * @param string $str The string to test against
 * @param string $sub The search string
 * @return bool
 */
function startswith( $str, $sub )
{
    return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}



/**
 * Similar to the startswith method, this function tests with string A ends with string B.
 *
 * i.e: endswith('The Quick Brown Fox','Fox');
 *
 * @param string $str The string to test against
 * @param string $sub The search string
 * @return bool
 */
function endswith( $str, $sub )
{
  return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}



/**
 * Convert a human readable string into something that is suitable for use in URLS.
 *
 * @param string $alias String to convert
 * @param bool $tolower Indicates whether output string should be converted to lower case
 * @param bool $withslash Indicates wether slashes should be allowed in the input.
 * @return string
 */
function munge_string_to_url($alias, $tolower = false, $withslash = false)
{
  if ($tolower == true) $alias = mb_strtolower($alias);

  // remove invalid chars
  $expr = '/[^\p{L}_\-\ \d]/u';
  if( $withslash ) $expr = '/[^\p{L}\/]/u';
  $tmp = preg_replace($expr,'',$alias);

  // remove extra dashes and spaces.
  $tmp = str_replace(' ','-',$tmp);
  $tmp = str_replace('---','-',$tmp);
  $tmp = str_replace('--','-',$tmp);

  return trim($tmp);
}


/**
 * Sanitize input to prevent against XSS and other nasty stuff.
 * Taken from cakephp (http://cakephp.org)
 * Licensed under the MIT License
 *
 * @internal
 * @param string $val input value
 * @return string
 */
function cleanValue($val) {
  if ($val == "") return $val;
  //Replace odd spaces with safe ones
  $val = str_replace(" ", " ", $val);
  $val = str_replace(chr(0xCA), "", $val);
  //Encode any HTML to entities (including \n --> <br />)
  $_cleanHtml = function($string,$remove = false) {
    if ($remove) {
      $string = strip_tags($string);
    } else {
      $patterns = array("/\&/", "/%/", "/</", "/>/", '/"/', "/'/", "/\(/", "/\)/", "/\+/", "/-/");
      $replacements = array("&amp;", "&#37;", "&lt;", "&gt;", "&quot;", "&#39;", "&#40;", "&#41;", "&#43;", "&#45;");
      $string = preg_replace($patterns, $replacements, $string);
    }
    return $string;
  };
  $val = $_cleanHtml($val);
  //Double-check special chars and remove carriage returns
  //For increased SQL security
  $val = preg_replace("/\\\$/", "$", $val);
  $val = preg_replace("/\r/", "", $val);
  $val = str_replace("!", "!", $val);
  $val = str_replace("'", "'", $val);
  //Allow unicode (?)
  $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val);
  //Add slashes for SQL
  //$val = $this->sql($val);
  //Swap user-inputted backslashes (?)
  $val = preg_replace("/\\\(?!&amp;#|\?#)/", "\\", $val);
  return $val;
}



/**
 * A function to test if permissions, and php configuration is setup correctly
 * to allow an administrator to upload files to CMSMS.
 *
 * @internal
 * @return bool
 */
function can_admin_upload()
{
  # first, check to see if safe mode is enabled
  # if it is, then check to see the owner of the index.php, moduleinterface.php
  # and the uploads and modules directory.  if they all match, then we
  # can upload files.
  # if safe mode is off, then we just have to check the permissions.
  $config = cmsms()->GetConfig();
  $file_index = $config['root_path'].DIRECTORY_SEPARATOR.'index.php';
  $file_moduleinterface = $config['root_path'].DIRECTORY_SEPARATOR.
    $config['admin_dir'].DIRECTORY_SEPARATOR.'moduleinterface.php';
  $dir_uploads = $config['uploads_path'];
  $dir_modules = $config['root_path'].DIRECTORY_SEPARATOR.'modules';

  $stat_index = @stat($file_index);
  $stat_moduleinterface = @stat($file_moduleinterface);
  $stat_uploads = @stat($dir_uploads);
  $stat_modules = @stat($dir_modules);

  $my_uid = @getmyuid();

  if( $my_uid === FALSE || $stat_index == FALSE ||
      $stat_moduleinterface == FALSE || $stat_uploads == FALSE ||
      $stat_modules == FALSE ) {
    // couldn't get some necessary information.
    return FALSE;
  }

  $safe_mode = ini_get_boolean('safe_mode');
  if( $safe_mode ) {
    // we're in safe mode.
    if( ($stat_moduleinterface[4] != $stat_modules[4]) ||
	($stat_moduleinterface[4] != $stat_uploads[4]) ||
	($my_uid != $stat_moduleinterface[4]) ) {
      // owners don't match
      return FALSE;
    }
  }

  // now check to see if we can write to the directories
  if( !is_writable( $dir_modules ) ) return FALSE;
  if( !is_writable( $dir_uploads ) ) return FALSE;

  // It all worked.
  return TRUE;
}


/**
 * A convenience function to return a bool variable given a php ini key that represents a bool.
 *
 * @param string $str The php ini key
 * @return int
 */
function ini_get_boolean($str)
{
  $val1 = ini_get($str);
  $val2 = strtolower($val1);

  $ret = 0;
  if( $val2 == 1 || $val2 == '1' || $val2 == 'yes' || $val2 == 'true' || $val2 == 'on' ) $ret = 1;
  return $ret;
}


/**
 * Another convenience function to output a human readable function stack trace.
 *
 * This method uses echo.
 */
function stack_trace()
{
  $stack = debug_backtrace();
  foreach( $stack as $elem ) {
    if( $elem['function'] == 'stack_trace' ) continue;
    if( isset($elem['file'])  ) {
      echo $elem['file'].':'.$elem['line'].' - '.$elem['function'].'<br/>';
    }
    else {
      echo ' - '.$elem['function'].'<br/>';
    }
  }
}


/**
 * A wrapper around move_uploaded_file that attempts to ensure permissions on uploaded
 * files are set correctly.
 *
 * @param string $tmpfile The temporary file specification
 * @param string $destination The destination file specification
 * @return bool.
 */
function cms_move_uploaded_file( $tmpfile, $destination )
{
   $config = cmsms()->GetConfig();

   if( !@move_uploaded_file( $tmpfile, $destination ) ) return false;
   @chmod($destination,octdec($config['default_upload_permission']));
   return true;
}


/**
 * A function to test wether an IP address matches a list of expressions.
 * Credits to J.Adams <jna@retins.net>
 *
 * Expressions can be of the form
 *   xxx.xxx.xxx.xxx        (exact)
 *   xxx.xxx.xxx.[yyy-zzz]  (range)
 *   xxx.xxx.xxx.xxx/nn    (nn = # bits, cisco style -- i.e. /24 = class C)
 *
 * @param string $ip IP address to test
 * @param array  $checklist Array of match expressions
 * @return bool
 * Rolf: only used in lib/content.functions.php
 */
function cms_ipmatches($ip,$checklist)
{
  $_testip = function($range,$ip) {
    $result = 1;

    // IP Pattern Matcher
    // J.Adams <jna@retina.net>
    //
    // Matches:
    //
    // xxx.xxx.xxx.xxx        (exact)
    // xxx.xxx.xxx.[yyy-zzz]  (range)
    // xxx.xxx.xxx.xxx/nn    (nn = # bits, cisco style -- i.e. /24 = class C)
    //
    // Does not match:
    // xxx.xxx.xxx.xx[yyy-zzz]  (range, partial octets nnnnnot supported)

    $regs = array();
    if (preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)\/([0-9]+)/",$range,$regs)) {
      // perform a mask match
      $ipl = ip2long($ip);
      $rangel = ip2long($regs[1] . "." . $regs[2] . "." . $regs[3] . "." . $regs[4]);

      $maskl = 0;

      for ($i = 0; $i< 31; $i++) {
	if ($i < $regs[5]-1) $maskl = $maskl + pow(2,(30-$i));
      }

      if (($maskl & $rangel) == ($maskl & $ipl)) {
	return 1;
      } else {
	return 0;
      }
    } else {
      // range based
      $maskocts = explode('.',$range);
      $ipocts = explode('.',$ip);

      if( count($maskocts) != count($ipocts) && count($maskocts) != 4 ) return 0;

      // perform a range match
      for ($i=0; $i<4; $i++) {
	if (preg_match("/\[([0-9]+)\-([0-9]+)\]/",$maskocts[$i],$regs)) {
	  if ( ($ipocts[$i] > $regs[2]) || ($ipocts[$i] < $regs[1])) $result = 0;
	}
	else {
	  if ($maskocts[$i] <> $ipocts[$i]) $result = 0;
	}
      }
    }
    return $result;
  }; // _testip

  if( !is_array($checklist) ) $checklist = explode(',',$checklist);
  foreach( $checklist as $one ) {
    if( $_testip(trim($one),$ip) ) return TRUE;
  }
  return FALSE;
}


/**
 * Test if the string provided is a valid email address.
 *
 * @return bool
 * @param string  $email
 * @param bool $checkDNS
*/
function is_email( $email, $checkDNS=false )
{
  if( !filter_var($email,FILTER_VALIDATE_EMAIL) ) return FALSE;
  if ($checkDNS && function_exists('checkdnsrr')) {
    if (!(checkdnsrr($domain, 'A') || checkdnsrr($domain, 'MX'))) return FALSE;	// Domain doesn't actually exist
  }

  return TRUE;
}



/**
 * A convenience method to output the secure param tag that is used on all admin links.
 *
 * @internal
 * @access private
 * @return string
 * Rolf: only used in admin/imagefiles.php
 */
function get_secure_param()
{
  $urlext = '?';
  $str = strtolower(ini_get('session.use_cookies'));
  if( $str == '0' || $str == 'off' ) $urlext .= htmlspecialchars(SID).'&';
  $urlext .= CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
  return $urlext;
}



/**
 * A simple function to convert a string to a bool.
 * accepts, 'y','yes','true',1 as TRUE (case insensitive) all other values represent FALSE.
 *
 * @param string $str Input string to test.
 * Rolf: only used in lib/classes/contenttypes/Content.inc.php
 */
function cms_to_bool($str)
{
  if( is_numeric($str) ) return ((int)$str != 0)?TRUE:FALSE;

  $str = strtolower($str);
  if( $str == '1' || $str == 'y' || $str == 'yes' || $str == 'true' ) return TRUE;
  return FALSE;
}



/**
 * A function to return the appropriate HTML tags to include the CMSMS included jquery in a web page.
 *
 * CMSMS is distributed with a recent version of jQuery, jQueryUI and various other jquery based
 * libraries.  This function generates the HTML code that will include these scripts.
 *
 * See the {cms_jquery} smarty plugin for a convenient way of including the CMSMS provided jquery
 * libraries from within a smarty template.
 *
 * Known libraries:
 *   jquery
 *   jquery-ui
 *   nestedSortable
 *   json
 *   migrate
 *
 * @since 1.10
 * @param string $exclude A comma separated list of script names or aliases to exclude.
 * @param bool $ssl Force use of the ssl_url for the root url to necessary scripts.
 * @param bool $cdn Force the use of a CDN url for the libraries if one is known
 * @param string  $append A comma separated list of library URLS to the output
 * @param string  $custom_root A custom root URL for all scripts (when using local mode).  If this is spefied the $ssl param will be ignored.
 * @param bool $include_css Optionally output stylesheet tags for the included javascript libraries.
 */
function cms_get_jquery($exclude = '',$ssl = null,$cdn = false,$append = '',$custom_root='',$include_css = TRUE)
{
  $config = cms_config::get_instance();
  $scripts = array();
  $base_url = $config->smart_root_url();
  if( $ssl === true || $ssl === TRUE ) $base_url = $config['ssl_url'];
  $basePath=$custom_root!=''?trim($custom_root,'/'):$base_url;

  // Scripts to include
  $scripts['jquery'] = array('cdn'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js',
			     'local'=>$basePath.'/lib/jquery/js/jquery-1.11.0.min.js',
			     'aliases'=>array('jquery.min.js','jquery',));
  $scripts['jquery-ui'] = array('cdn'=>'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
				'local'=>$basePath.'/lib/jquery/js/jquery-ui-1.10.4.custom.min.js',
				'aliases'=>array('jquery-ui.min.js','ui'),
				'css'=>$basePath.'/lib/jquery/css/smoothness/jquery-ui-1.10.4.custom.min.css');
  $scripts['nestedSortable'] = array('local'=>$basePath.'/lib/jquery/js/jquery.mjs.nestedSortable.js');
  $scripts['json'] = array('local'=>$basePath.'/lib/jquery/js/jquery.json-2.4.min.js');
  $scripts['migrate'] = array('local'=>$basePath.'/lib/jquery/js/jquery-migrate-1.2.1.min.js');

  if( cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) {
    global $CMS_LOGIN_PAGE;
    if( isset($_SESSION[CMS_USER_KEY]) && !isset($CMS_LOGIN_PAGE) ) {
      $url = $config['admin_url'];
      $scripts['cms_js_setup'] = array('local'=>$url.'/cms_js_setup.php?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY]);
    }
    $scripts['cms_admin'] = array('local'=>$basePath.'/lib/jquery/js/jquery.cms_admin.js');
    $scripts['cms_dirtyform'] = array('local'=>$basePath.'/lib/jquery/js/jquery.cmsms_dirtyform.js');
    $scripts['cms_lock'] = array('local'=>$basePath.'/lib/jquery/js/jquery.cmsms_lock.js');
    $scripts['cms_hiersel'] = array('local'=>$basePath.'/lib/jquery/js/jquery.cmsms_hierselector.js');
  }

  // Check if we need to exclude some script
  if(!empty($exclude)) {
    $exclude_list = explode(",", trim(str_replace(' ','',$exclude)));
    foreach($exclude_list as $one) {
      // find a match
      $found = null;
      foreach( $scripts as $key => $rec ) {
	if( strtolower($one) == strtolower($key) ) {
	  $found = $key;
	  break;
	}
	if( isset($rec['aliases']) && is_array($rec['aliases']) ) {
	  foreach( $rec['aliases'] as $alias ) {
	    if( strtolower($one) == strtolower($alias) ) {
	      $found = $key;
	      break;
	    }
	  }
	  if( $found ) break;
	}
      }

      if( $found ) unset($scripts[$found]);
    }
  }

  // let them add scripts to the end ie: a jQuery plugin
  if(!empty($append)) {
    $append_list = explode(",", trim(str_replace(' ','',$append)));
    foreach($append_list as $key => $item) {
      $scripts['user_'+$key] = array('local'=>$item);
    }
  }

  // Output
  $output = '';
  $fmt_js = '<script type="text/javascript" src="%s"></script>';
  $fmt_css = '<link rel="stylesheet" type="text/css" href="%s"/>';
  foreach($scripts as $script) {
    $url_js = $script['local'];
    if( $cdn && isset($script['cdn']) ) $url_js = $script['cdn'];
    $output .= sprintf($fmt_js,$url_js)."\n";
    if( isset($script['css']) && $script['css'] != '' ) {
      $url_css = $script['css'];
      if( $cdn && isset($script['css_cdn']) ) $url_css = $script['css_cdn'];
      if( $include_css ) $output .= sprintf($fmt_css,$url_css)."\n";
    }
  }
  return $output;
}

# vim:ts=4 sw=4 noet
?>
