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
 * @package CMS
 * @param string $to The url to redirect to
 */
function redirect(string $to)
{
    $app = cmsms();
    if( $app->is_cli() ) die("ERROR: no redirect on cli based scripts ---\n");
    $_SERVER['PHP_SELF'] = null;

    $schema = 'http';
    if( $app->is_https_request() ) $schema = 'https';

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

    // this could be used in install/upgrade routines where config is not set yet
    // so cannot use constants.
    $debug = false;
    if( class_exists('CmsApp') ) {
        $config = CmsApp::get_instance()->GetConfig();
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
            echo "<a accesskey=\"r\" href=\"".$to."\">".$to."</a><br />";
            echo '<div id="DebugFooter">';
            foreach (CmsApp::get_instance()->get_errors() as $error) {
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
function redirect_to_alias(string $alias)
{
  $manager = CmsApp::get_instance()->GetHierarchyManager();
  $node = $manager->sureGetNodeByAlias($alias);
  if( !$node ) {
	// put mention into the admin log
    cms_warning('Core: Attempt to redirect to invalid alias: '.$alias);
    return;
  }
  $content = $node->GetContent();
  if (!is_object($content)) {
    cms_warning('Core: Attempt to redirect to invalid alias: '.$alias);
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
function microtime_diff(string $a, string $b)
{
    list($a_dec, $a_sec) = explode(' ', $a);
    list($b_dec, $b_sec) = explode(' ', $b);
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
 * Return the relative portion of a path
 *
 * @since 2.2
 * @author Robert Campbell
 * @param string $in The input path or file specification
 * @param string $relative_to The optional path to compute relative to.  If not supplied the cmsms root path will be used.
 * @return string The relative portion of the input string.
 */
function cms_relative_path(string $in,string $relative_to = null)
{
    $in = realpath(trim($in));
    if( !$relative_to ) $relative_to = CMS_ROOT_PATH;
    $to = realpath(trim($relative_to));

    if( !$in ) return;
    if( !$to ) return;
    if( !startswith($in,$to) ) return;

    return substr($in,strlen($to));
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
function cms_htmlentities(string $val = null, string $param=ENT_QUOTES, string $charset="UTF-8", bool $convert_single_quotes = false)
{
  if( $val == '') return '';

  $val = str_replace( "&#032;", " ", $val );
  $val = str_replace( "&"            , "&amp;"         , $val );
  $val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
  $val = str_replace( "-->"          , "--&#62;"       , $val );
  $val = str_ireplace( "<script"     , "&#60;script"   , $val );
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
 * A function to output a backtrace into the generated log file.
 *
 * @see debug_to_log, debug_bt
 * Rolf: Looks like not used
 */
function debug_bt_to_log()
{
    if( CmsApp::get_instance()->config['debug_to_log'] || (function_exists('get_userid') && get_userid(FALSE)) ) {
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
function debug_display($var, string $title="", bool $echo_to_screen = true, bool $use_html = true, bool $showtitle = TRUE)
{
    global $starttime, $orig_memory;
    if( !$starttime ) $starttime = microtime();

    ob_start();

    if( $showtitle ) {
        $titleText = "Debug: ";
        if($title) $titleText = "Debug display of '$title':";
        $titleText .= '(' . microtime_diff($starttime,microtime()) . ')';
        if (function_exists('memory_get_usage')) {
            $net = memory_get_usage() - $orig_memory;
            $titleText .= ' - (net usage: '.$net.')';
        }

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
function debug_output($var, string $title="")
{
    $config = \cms_config::get_instance();
    if( $config["debug"] == true) debug_display($var, $title, true);
}



/**
 * Debug function to output debug information about a variable in a formatted matter
 * to a debug file.
 *
 * @param mixed $var    data to display
 * @param string $title optional title.
 * @param string $filename optional output filename
 */
function debug_to_log($var, string $title='',string $filename = '')
{
    $config = \cms_config::get_instance();
    if( $config['debug_to_log'] || (function_exists('get_userid') && get_userid(FALSE)) ) {
        if( $filename == '' ) {
            $filename = TMP_CACHE_LOCATION . '/debug.log';
            $x = (is_file($filename)) ? @filemtime($filename) : time();
            if( $x !== FALSE && $x < (time() - 24 * 3600) ) unlink($filename);
        }
        $errlines = explode("\n",debug_display($var, $title, false, false, true));
        foreach ($errlines as $txt) {
            error_log($txt . "\n", 3, $filename);
        }
    }
}



/**
 * Display $var nicely to the CmsApp::get_instance()->errors array if $config['debug'] is set.
 *
 * @param mixed $var
 * @param string $title
 */
function debug_buffer($var, string $title="")
{
    if( !defined('CMS_DEBUG') || CMS_DEBUG == 0 ) return;
    CmsApp::get_instance()->add_error(debug_display($var, $title, false, true));
}


/**
* Return $value if it's set and same basic type as $default_value,
* Otherwise return $default_value. Note. Also will trim($value)	if $value is not numeric.
*
* @ignore
* @param mixed $value
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
function get_parameter_value(array $parameters, string $value, $default_value = '', string $session_key = '')
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
 * Check the permissions of a directory recursively to make sure that
 * we have write permission to all files.
 *
 * @param  string  $path Start directory.
 * @return bool
 */
function is_directory_writable( string $path )
{
    if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) $path .= '/' ;

    if( !is_dir($path) ) return FALSE;
    $result = TRUE;
    if( $handle = opendir( $path ) ) {
        while( false !== ( $file = readdir( $handle ) ) ) {
            if( $file == '.' || $file == '..' ) continue;

            $p = $path.$file;
            if( !@is_writable( $p ) ) return FALSE;

            if( @is_dir( $p ) ) {
                $result = is_directory_writable( $p );
                if( !$result ) return FALSE;
            }
        }
        closedir( $handle );
        return TRUE;
    }
    return FALSE;
}


/**
 * Return an array containing a list of files in a directory
 * performs a non recursive search.
 *
 * @internal
 * @param string $dir path to search
 * @param string 4extension include only files matching these extensions. case insensitive, comma delimited
 * Rolf: only used in this file
 */
function get_matching_files(string $dir,string $extensions = '',bool $excludedot = true,bool $excludedir = true, string $fileprefix='',bool $excludefiles = true)
{
    if( !is_dir($dir) ) return false;
    $dh = opendir($dir);
    if( !$dh ) return false;

    if( !empty($extensions) ) $extensions = explode(',',strtolower($extensions));
    $results = array();
    while( false !== ($file = readdir($dh)) ) {
        if( $file == '.' || $file == '..' ) continue;
        if( startswith($file,'.') && $excludedot ) continue;
        if( is_dir(cms_join_path($dir,$file)) && $excludedir ) continue;
        if( !empty($fileprefix) ) {
            if( $excludefiles && startswith($file,$fileprefix) ) continue;
            if( !$excludefiles && !startswith($file,$fileprefix) ) continue;
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
function get_recursive_file_list ( string $path ,array $excludes, int $maxdepth = -1, string $mode = "FULL" , int $d = 0 )
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
function recursive_delete( string $dirname )
{
    // all subdirectories and contents:
    if( !is_dir($dirname) ) return true;
    $dir_handle=opendir($dirname);
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
function chmod_r( string $path, int $mode )
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
function startswith( string $str, string $sub )
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
function endswith( string $str, string $sub )
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
function munge_string_to_url(string $alias, bool $tolower = false, bool $withslash = false)
{
  if ($tolower == true) $alias = mb_strtolower($alias);

  // remove invalid chars
  $expr = '/[^\p{L}_\-\.\ \d]/u';
  if( $withslash ) $expr = '/[^\p{L}_\.\-\ \d\/]/u';
  $tmp = trim( preg_replace($expr,'',$alias) );

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
function cleanValue(string $val) {
  if ($val == '') return $val;
  return filter_var( $val, FILTER_SANITIZE_STRING );
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
 * A convenience function to return a bool variable given a php ini key that represents a bool.
 *
 * @param string $str The php ini key
 * @return int
 */
function ini_get_boolean(string $str)
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
function cms_move_uploaded_file( string $tmpfile, string $destination )
{
   $config = CmsApp::get_instance()->GetConfig();

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
function cms_ipmatches(string $ip,array $checklist)
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
	  if ( ($ipocts[$i] > $regs[2]) || ($ipocts[$i] < $regs[1])) return 0;
	}
	else {
	  if ($maskocts[$i] != $ipocts[$i]) return 0;
	}
      }
    }
    return 1;
  }; // _testip

  if( !is_array($checklist) ) $checklist = explode(',',$checklist);
  foreach( $checklist as $one ) {
    $one = trim($one);
    if( !$one ) continue;
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
function is_email( string $email, bool $checkDNS=false )
{
   if( !filter_var($email,FILTER_VALIDATE_EMAIL) ) return FALSE;
   if ($checkDNS && function_exists('checkdnsrr')) {
       list($user,$domain) = explode('@',$email,2);
       if( !$domain ) return FALSE;
       if ( !(checkdnsrr($domain, 'A') || checkdnsrr($domain, 'MX'))) return FALSE;	// Domain doesn't actually exist
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
function cms_to_bool(string $str)
{
  if( is_numeric($str) ) return ((int)$str != 0)?TRUE:FALSE;

  $str = strtolower($str);
  if( $str == '1' || $str == 'y' || $str == 'yes' || $str == 'true' || $str === 'on' ) return TRUE;
  return FALSE;
}


/**
 * @ignore
 * @since 2.0.2
 */
function setup_session(bool $cachable = FALSE)
{
    global $CMS_INSTALL_PAGE, $CMS_ADMIN_PAGE;
    static $_setup_already = false;
    if( $_setup_already ) return;

    $_f = $_l = null;
    if( headers_sent( $_f, $_l) ) throw new \LogicException("Attempt to set headers, but headers were already sent at: $_f::$_l");

    if( $cachable ) {
        if( $_SERVER['REQUEST_METHOD'] != 'GET' || isset($CMS_ADMIN_PAGE) || isset($CMS_INSTALL_PAGE) ) $cachable = FALSE;
    }
    if( $cachable ) $cachable = (int) cms_siteprefs::get('allow_browser_cache',0);
    if( !$cachable ) {
        // admin pages can't be cached... period, at all.. never.
        @session_cache_limiter('nocache');
    }
    else {
        // frontend request
        $expiry = (int)max(0,cms_siteprefs::get('browser_cache_expiry',60));
        session_cache_expire($expiry);
        session_cache_limiter('public');
        @header_remove('Last-Modified');
    }

    #Setup session with different id and start it
    $session_name = 'CMSSESSID'.substr(md5(__DIR__.CMS_VERSION), 0, 12);
    if( !isset($CMS_INSTALL_PAGE) ) {
        @session_name($session_name);
        @ini_set('url_rewriter.tags', '');
        @ini_set('session.use_trans_sid', 0);
    }

    if( isset($_COOKIE[$session_name]) ) {
        // validate the contents of the cookie.
        if (!preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $_COOKIE[$session_name]) ) {
            session_id( uniqid() );
            session_start();
            session_regenerate_id();
        }
    }
    if(!@session_id()) session_start();
    $_setup_already = true;
}

/**
 * Test if a string is a base64 encoded string
 *
 * @since 2.2
 * @param string $s The input string
 * @return bool
 */
function is_base64(string $s)
{
    return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
}


/**
 * Create a unique GUID.
 *
 * @since 2.3
 * @return string
 */
function cms_create_guid()
{
    if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
