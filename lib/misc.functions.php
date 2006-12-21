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
#$Id$

/**
 * Misc functions
 *
 * @package CMS
 */

/**
 * Simple template parser
 *
 * @since 0.6.1
 */
function parse_template($template, $tpl_array, $warn=0)
{
	while ( list ($key,$val) = each ($tpl_array) )
	{
		if (!(empty($key)))
		{
			if(gettype($val) != "string")
			{
				settype($val,"string");
			}
			$template = eregi_replace('\{' . $key . '\}',$val,$template);
		}
	}

	if(!$warn)
	{
		// Silently remove anything not already found

		$template = ereg_replace('\{[A-Z0-9_]+\}', "", $template);
	}
	else
	{
		// Warn about unresolved template variables
		if (ereg('\{[A-Z0-9_]+\}',$template))
		{
			$unknown = split("\n",$template);
			while (list ($Element,$Line) = each($unknown) )
			{
				$UnkVar = $Line;
				if(!(empty($UnkVar)))
				{
					$this->show_unknowns($UnkVar);
				}
			}
		}
	}
	return $template;

}	// end parse_template();

function cms_html_entities($string, $param=ENT_QUOTES, $charset="UTF-8")
{
	$result = "";
	#$result = htmlentities($string, $param, $charset);
	$result = my_htmlentities($string);
	return $result;
}

function cms_htmlentities($string, $param=ENT_QUOTES, $charset="UTF-8")
{
	return cms_html_entities($string, $param, $charset);
}

/**
 * Enter description here...
 *
 * @param unknown $val
 * @param integer $quote_style
 * @return unknown
 * 
 * $quote_style may be one of:
 *     ENT_COMPAT   : Will convert double-quotes and leave single-quotes alone. 
 *     ENT_QUOTES   : Will convert both double and single quotes. 
 *     ENT_NOQUOTES : Will leave both double and single quotes unconverted. 
 */
function my_htmlentities($val)
{
	if ($val == "")
	{
		return "";
	}
	$val = str_replace( "&#032;", " ", $val ); 

	//Remove sneaky spaces 
	// $val = str_replace( chr(0xCA), "", $val );   

	$val = str_replace( "&"            , "&amp;"         , $val ); 
	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val ); 
	$val = str_replace( "-->"          , "--&#62;"       , $val ); 
	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val ); 
	$val = str_replace( ">"            , "&gt;"          , $val ); 
	$val = str_replace( "<"            , "&lt;"          , $val ); 
	
	
	$val = str_replace( "\""           , "&quot;"        , $val ); 

	// Uncomment it if you need to convert literal newlines 
	//$val = preg_replace( "/\n/"        , "<br>"          , $val ); 

	$val = preg_replace( "/\\$/"      , "&#036;"        , $val ); 

	// Uncomment it if you need to remove literal carriage returns 
	//$val = preg_replace( "/\r/"        , ""              , $val ); 

	$val = str_replace( "!"            , "&#33;"         , $val ); 
	$val = str_replace( "'"            , "&#39;"         , $val ); 
	 
	// Uncomment if you need to convert unicode chars 
	//$val = preg_replace("/&#([0-9]+);/s", "&#\1;", $val ); 

	// Strip slashes if not already done so. 

	//if ( get_magic_quotes_gpc() ) 
	//{ 
	//	$val = stripslashes($val); 
	//} 

	// Swop user inputted backslashes 

	//$val = preg_replace( "/\(?!&#|?#)/", "&#092;", $val );

	return $val;
}


/**
 * Enter description here...
 *
 * @param unknown $val
 * @return unknown
 */
function cms_utf8_entities($val)
{
	if ($val == "")
	{
		return "";
	}
	$val = str_replace( "&#032;", " ", $val ); 

	//Remove sneaky spaces 
	// $val = str_replace( chr(0xCA), "", $val );   

	$val = str_replace( "&"            , "\u0026"         , $val ); 
#	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val ); 
#	$val = str_replace( "-->"          , "--&#62;"       , $val ); 
#	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val ); 
	$val = str_replace( ">"            , "\u003E"          , $val ); 
	$val = str_replace( "<"            , "\u003C"          , $val ); 
	
	
	$val = str_replace( "\""           , "\u0022"        , $val ); 

	// Uncomment it if you need to convert literal newlines 
	//$val = preg_replace( "/\n/"        , "<br>"          , $val ); 

	#$val = preg_replace( "/\\$/"      , "&#036;"        , $val ); 

	// Uncomment it if you need to remove literal carriage returns 
	//$val = preg_replace( "/\r/"        , ""              , $val ); 

	$val = str_replace( "!"            , "\u0021"         , $val ); 
	$val = str_replace( "'"            , "\u0027"         , $val ); 
	 
	// Uncomment if you need to convert unicode chars 
	//$val = preg_replace("/&#([0-9]+);/s", "&#\1;", $val ); 

	// Strip slashes if not already done so. 

	//if ( get_magic_quotes_gpc() ) 
	//{ 
	//	$val = stripslashes($val); 
	//} 

	// Swop user inputted backslashes 

	//$val = preg_replace( "/\(?!&#|?#)/", "&#092;", $val );

	return $val;
}

function cms_utf8entities($val)
{
	return cms_utf8_entities($val);
}

//Taken from http://www.webmasterworld.com/forum88/164.htm
function nl2pnbr( $text )
{
	// Use \n for newline on all systems
	$text = preg_replace("/(\r\n|\n|\r)/", "\n", $text);

	// Only allow two newlines in a row.
	$text = preg_replace("/\n\n+/", "\n\n", $text);

	// Put <p>..</p> around paragraphs
	$text = preg_replace('/\n?(.+?)(\n\n|\z)/s', "<p>$1</p>", $text);

	// Convert newlines not preceded by </p> to a <br /> tag
	$text = preg_replace('|(?<!</p>)\s*\n|', "<br />", $text);

	return $text;
}

/**
* Debug function to display $var nicely in html.
* 
* @param mixed $var
* @param string $title (optional)
* @param boolean $echo_to_screen (optional)
* @return string
*/
function debug_display($var, $title="", $echo_to_screen = true, $use_html = true)
{
	global $gCms;
	$variables =& $gCms->variables;

	$starttime = microtime();
	if (isset($variables['starttime']))
		$starttime = $variables['starttime'];
	else
		$variables['starttime'] = $starttime;
	
	$titleText = "Debug: ";
	if($title)
	{
		$titleText = "Debug display of '$title':";
	}
	$titleText .= '(' . microtime_diff($starttime,microtime()) . ')';
	
	if (function_exists('memory_get_usage'))
	{
		$titleText .= ' - ('.memory_get_usage().')';
	}

	ob_start();
	if ($use_html)
		echo "<div><b>$titleText</b><pre>\n";

	if(is_array($var))
	{
		echo "Number of elements: " . count($var) . "\n";
		print_r($var);
	}
	elseif(is_object($var))
	{
		print_r($var);
	}
	elseif(is_string($var))
	{
		print_r(htmlentities(str_replace("\t", '  ', $var)));
	}
	elseif(is_bool($var))
	{
		echo $var === true ? 'true' : 'false';
	}
	else
	{
		print_r($var);
	}

	if ($use_html)
		echo "</pre></div>\n";

	$output = ob_get_contents();
	ob_end_clean();

	if($echo_to_screen)
	{
		echo $output;
	}

	return $output;
}

/**
 * Display $var nicely to the $gCms->errors array if $config['debug'] is set
 *
 * @param mixed $var
 * @param string $title
 */
function debug_buffer($var, $title="")
{
	global $gCms;
	if ($gCms)
	{
		$config =& $gCms->GetConfig();
		//debug_to_log($var, $title='');
		if($config["debug"] == true)
		{
			$gCms->errors[] = debug_display($var, $title, false, true);
		}
	}
}

function debug_sql($str, $newline)
{
	global $gCms;
	if ($gCms)
	{
		$config =& $gCms->GetConfig();
		if($config["debug"] == true)
		{
			$gCms->errors[] = debug_display($str, '', false, true);
		}
	}
}

function create_encoding_dropdown($name = 'encoding', $selected = '')
{
	$result = '';

	$encodings = array(''=>'Default','UTF-8'=>'Unicode','ISO-8859-1'=>'Latin 1/West European','ISO-8859-2'=>'Latin 2/Central European','ISO-8859-3'=>'Latin 3/South European','ISO-8859-4'=>'Latin 4/North European','ISO-8859-5'=>'Cyrilic','ISO-8859-6'=>'Arabic','ISO-8859-7'=>'Greek','ISO-8859-8'=>'Hebrew','ISO-8859-9'=>'Latin 5/Turkish','ISO-8859-11'=>'TIS-620/Thai','ISO-8859-14'=>'Latin 8','ISO-8859-15'=>'Latin 9','Big5'=>'Taiwanese','GB2312'=>'Chinese','EUC-JP'=>'Japanese','EUC-KR'=>'Korean','KOI8-R'=>'Russian','Windows-1250'=>'Central Europe','Windows-1251'=>'Cyrilic','Windows-1252'=>'Latin 1','Windows-1253'=>'Greek','Windows-1254'=>'Turkish','Windows-1255'=>'Hebrew','Windows-1256'=>'Arabic','Windows-1257'=>'Baltic','Windows-1258'=>'Vietnam');

	$result .= '<select name="'.$name.'">';
	foreach ($encodings as $key=>$value)
	{
		$result .= '<option value="'.$key.'"';
		if ($selected == $key)
		{
			$result .= ' selected="selected"';
		}
		$result .= '>'.$key.($key!=''?' - ':'').$value.'</option>';
	}
	$result .= '</select>';

	return $result;
}

function filespec_is_excluded( $file, $excludes )
{
	// strip the path from the file
	foreach( $excludes as $excl )
	{
		if( @preg_match( "/".$excl."/i", basename($file) ) )
		{
			return true;
		}
	}
	return false;
}

/**
 * Check the permissions of a directory recursively to make sure that
 * we have write permission to all files
 * &param  path      start path
*/
function is_directory_writable( $path )
{
	if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }     
	$result = true;
	if( $handle = @opendir( $path ) )
	{
		while( false !== ( $file = readdir( $handle ) ) )
		{
			if( $file == '.' || $file == '..' )
			{
				continue;
			}

			$p = $path.$file;

			if( !@is_writable( $p ) )
			{
				return false;
			}

			if( @is_dir( $p ) )
			{
				$result = is_directory_writable( $p );
				if( !$result )
				{
					return false;
				}
			}
		}
		@closedir( $handle );
	}
	else
	{
		return false;
	}

	return true;
}

/**
* Return an array containing a list of files in a directory
* performs a recursive serach
* @param  path      start path
* @param  maxdepth  how deep to browse (-1=unlimited)
* @param  mode      "FULL"|"DIRS"|"FILES"
* @param  d         for internal use only
**/
function get_recursive_file_list ( $path , $excludes, $maxdepth = -1 , $mode = "FULL" , $d = 0 )
{
	if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }     
	$dirlist = array () ;
	if ( $mode != "FILES" ) { $dirlist[] = $path ; }
	if ( $handle = opendir ( $path ) )
	{
		while ( false !== ( $file = readdir ( $handle ) ) )
		{
			$excluded = filespec_is_excluded( $file, $excludes );
			if ( $file != '.' && $file != '..' && $excluded == false )
			{
				$file = $path . $file ;
				if ( ! @is_dir ( $file ) ) { if ( $mode != "DIRS" ) { $dirlist[] = $file ; } }
				elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) )
				{
					$result = get_recursive_file_list ( $file . '/' , $excludes, $maxdepth , $mode , $d + 1 ) ;
					$dirlist = array_merge ( $dirlist , $result ) ;
				}
			}
		}
		closedir ( $handle ) ;
	}
	if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
	return ( $dirlist ) ;
}

function recursive_delete( $dirname )
{
	// all subdirectories and contents:
	if(is_dir($dirname))$dir_handle=opendir($dirname);
	while($file=readdir($dir_handle))
	{
		if($file!="." && $file!="..")
		{
			if(!is_dir($dirname."/".$file))
			{
				if( !@unlink ($dirname."/".$file) )
				{
					closedir( $dir_handle );
					return false;
				}
			}
			else 
			{
				recursive_delete($dirname."/".$file);
			}
		}
	}
	closedir($dir_handle);
	if( ! @rmdir($dirname) )
	{
		return false;
	}
	return true;
}

function chmod_r( $path, $mode )
{
	if( !is_dir( $path ) )
	return chmod( $path, $mode );

	$dh = @opendir( $path );
	if( !$dh ) return FALSE;

	while( $file = readdir( $dh ) )
	{
		if( $file == '.' || $file == '..' ) continue;

		$p = $path.DIRECTORY_SEPARATOR.$file;
		if( is_dir( $p ) )
		{
			if( !@chmod_r( $p, $mode ) )
			{
				closedir( $dh );
				return false;
			}
		}
		else if( !is_link( $p ) )
		{
			if( !@chmod( $p, $mode ) )
			{
				closedir( $dh );
				return false;
			}
		}
	}
	@closedir( $dh );
	return @chmod( $path, $mode );
}

function serialize_object(&$object)
{
	return base64_encode(serialize($object));
}

function unserialize_object(&$serialized)
{
	return unserialize(base64_decode($serialized));
}

function show_mem($string = '')
{
	var_dump($string . ' -- ' . memory_get_usage());
}

function showmem($string = '')
{
	return show_mem($string);
}

function munge_string_to_url($alias, $tolower = false)
{
	// replacement.php is encoded utf-8 and must be the first modification of alias
	include(dirname(__FILE__) . '/replacement.php');
	$alias = str_replace($toreplace, $replacement, $alias);
	
	// lowercase only on empty aliases
	if ($tolower == true)
	{
		$alias = strtolower($alias);
	}
		
	$alias = preg_replace("/[^\w-]+/", "-", $alias);
	$alias = trim($alias, '-');

	return $alias;
}

/**
 * Returns all parameters sent that are destined for the module with
 * the given $id
 */
function get_module_parameters($id)
{
	$params = array();

	if ($id != '')
	{
		foreach ($_REQUEST as $key=>$value)
		{
			if (strpos($key, (string)$id) !== FALSE && strpos($key, (string)$id) == 0)
			{
				$key = str_replace($id, '', $key);
				$params[$key] = $value;
			}
		}
	}

	return $params;
}

function GetModuleParameters($id)
{
	return get_module_parameters($id);
}

# vim:ts=4 sw=4 noet
?>