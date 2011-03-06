<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (wishy@users.sf.net)
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
 * Redirects to relative URL on the current site
 *
 * @author http://www.edoceo.com/
 * @since 0.1
 * @param string The url to redirect to
 * @return void
 */
function redirect($to, $noappend=false)
{
     $_SERVER['PHP_SELF'] = null;

     $gCms = cmsms();
     if (isset($gCms))
       $config = $gCms->GetConfig();
     else
       $config = array();

    $schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
    $host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];

    $components = parse_url($to);
    if(count($components) > 0)
    {
        $to =  (isset($components['scheme']) && startswith($components['scheme'], 'http') ? $components['scheme'] : $schema) . '://';
        $to .= isset($components['host']) ? $components['host'] : $host;
        $to .= isset($components['port']) ? ':' . $components['port'] : '';
        if(isset($components['path']))
        {
            if(in_array(substr($components['path'],0,1),array('\\','/')))//Path is absolute, just append.
            {
                $to .= $components['path'];
            }
            //Path is relative, append current directory first.
			else if (isset($_SERVER['PHP_SELF']) && !is_null($_SERVER['PHP_SELF'])) //Apache
            {
                $to .= (strlen(dirname($_SERVER['PHP_SELF'])) > 1 ?  dirname($_SERVER['PHP_SELF']).'/' : '/') . $components['path'];
            }
			else if (isset($_SERVER['REQUEST_URI']) && !is_null($_SERVER['REQUEST_URI'])) //Lighttpd
            {
				if (endswith($_SERVER['REQUEST_URI'], '/'))
					$to .= (strlen($_SERVER['REQUEST_URI']) > 1 ? $_SERVER['REQUEST_URI'] : '/') . $components['path'];
				else
					$to .= (strlen(dirname($_SERVER['REQUEST_URI'])) > 1 ? dirname($_SERVER['REQUEST_URI']).'/' : '/') . $components['path'];
            }
        }
        $to .= isset($components['query']) ? '?' . $components['query'] : '';
        $to .= isset($components['fragment']) ? '#' . $components['fragment'] : '';
    }
    else
    {
        $to = $schema."://".$host."/".$to;
    }

    //If session trans-id is being used, and they is on yo website, add it.
	/*
    if (ini_get("session.use_trans_sid") != "0" && $noappend == false && strpos($to,$host) !== false)
    {
        if(strpos($to,'?') !== false)//If there are no arguments start a querystring
        {
            //$to = $to."?".session_name()."=".session_id();
        }
        else//There are arguments, print an arg seperator
        {
            //$to = $to.ini_get('arg_separator.input').session_name()."=".session_id();
        }
    }
	*/

    session_write_close();


    if (headers_sent() && !(isset($config) && $config['debug'] == true))
    {
        // use javascript instead
        echo '<script type="text/javascript">
            <!--
                location.replace("'.$to.'");
            // -->
            </script>
            <noscript>
                <meta http-equiv="Refresh" content="0;URL='.$to.'">
            </noscript>';
        exit;

    }
    else
    {
        if (isset($config['debug']) && $config['debug'] == true)
        {
            echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
            echo "<a href=\"".$to."\">".$to."</a><br />";
			echo '<div id="DebugFooter">';
			global $sql_queries;
			if (FALSE == empty($sql_queries))
			  {
				echo "<div>".$sql_queries."</div>\n";
			  }
			foreach (cmsms()->get_errors() as $error)
			{
				echo $error;
			}
			echo '</div> <!-- end DebugFooter -->';
            exit();
        }
        else
        {
            header("Location: $to");
            exit();
        }
	}
}


/**
 * Given a page ID or an alias, redirect to it
 * Retrieves the URL of the specified page, and performs a redirect
 *
 * @param mixed An integer page id or a string page alias.
 * @return void
 */
function redirect_to_alias($alias)
{
  $gCms = cmsms();
	$manager = $gCms->GetHierarchyManager();
	$node = $manager->sureGetNodeByAlias($alias);
	$content = $node->GetContent();
	if (isset($content))
	{
		if ($content->GetURL() != '')
		{
			redirect($content->GetURL());
		}
	}
}


/**
 * Calculate the difference in seconds between two microtime() values
 *
 * @since 0.3
 * @param string Microtime value A
 * @param string Microtime value B
 * @return integer The difference.
 */
function microtime_diff($a, $b) {
	list($a_dec, $a_sec) = explode(" ", $a);
	list($b_dec, $b_sec) = explode(" ", $b);
	return $b_sec - $a_sec + $b_dec - $a_dec;
}


/**
 * Joins a path together using proper directory separators
 * Taken from: http://www.php.net/manual/en/ref.dir.php
 *
 * This method accepts a variable number of string arguments.
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
 * Return the global gCms object
 *
 * @return object
 */
function &cmsms()
{
	global $gCms;
	return $gCms;
}


/**
 * Shows a very close approximation of an Apache generated 404 error.
 *
 * Shows a very close approximation of an Apache generated 404 error.
 * It also sends the actual header along as well, so that generic
 * browser error pages (like what IE does) will be displayed.
 *
 * @since 0.3
 * @deprecated
 * @internal
 */
#function ErrorHandler404($errno, $errmsg, $filename, $linenum, $vars)
function ErrorHandler404()
{
	#if ($errno == E_USER_WARNING) {
		@ob_end_clean();
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>';
		exit();
	#}
}

/**
 * Simple template parser
 *
 * @since 0.6.1

	function parse_template ($template, $tpl_array, $warn=0)
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
 */


/**
 * A method to perform HTML entity conversion on a string
 *
 * @see htmlentities
 * @param string The input string
 * @param string A flag indicating how quotes should be handled (see htmlentities) (ignored)
 * @param string The input character set (ignored)
 * @param boolean A flag indicating wether single quotes should be converted to entities.
 * @return string the converted string.
 */
function cms_htmlentities($string, $param=ENT_QUOTES, $charset="UTF-8", $convert_single_quotes = false)
{
	$result = "";
	#$result = htmlentities($string, $param, $charset);
	$result = my_htmlentities($string, $convert_single_quotes);
	return $result;
}


/**
 * @ignore
 */
define('CLEANED_FILENAME','BAD_FILE');

/**
 * Clean up the filename, and ensure that the filename resides underneath
 * the cms_root directory, if it does not replace it with the hardcoded
 * string CLEANED_FILENAME
 *
 * @internal
 * @param string The complete file specification
 * @return string the cleaned file path.
 */
function cms_cleanfile($filename)
{
	$realpath = realpath($filename);
	if( $realpath === FALSE ) {
		return CLEANED_FILENAME;
	}

	// This ensures that the file specified is somewhere
	// underneath the cms root path
	$config = cmsms()->GetConfig();
	if( strpos($realpath, $config['root_path']) !== 0 ) {
		return CLEANED_FILENAME;
	}
	return $realpath;
}

/**
 * Figures out the page name from the uri string.  Has to use different logic
 * based on the type of httpd server.
 *
 * @ignore
 * @access private
 * @return string
 */
function cms_calculate_url()
{
	$result = '';

	$config = cmsms()->GetConfig();

	//apache and lighttpd
	if (isset($_SERVER["REQUEST_URI"]) && !endswith($_SERVER['REQUEST_URI'], 'index.php'))
	{
		$matches = array();
		if (preg_match('/.*index\.php\/(.*?)$/', $_SERVER['REQUEST_URI'], $matches))
		{
			$result = $matches[1];
		}/*
		else
		{
			$result = $_SERVER['PHP_SELF'];
		}*/
	}

	//trim off the extension, if there is one set
	if ($config['page_extension'] != '' && endswith($result, $config['page_extension']))
	{
		$result = substr($result, 0, strlen($result) - strlen($config['page_extension']));
	}

	return $result;

}

/**
 * A replacement for the built in htmlentities method.
 *
 * @ignore
 * @param string  input string
 * @param boolean A flag wether or not to handle single quotes.
 * @return unknown
 */
function my_htmlentities($val, $convert_single_quotes = false)
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

	if ($convert_single_quotes)
	{
		$val = str_replace("\\'", "&apos;", $val);
		$val = str_replace("'", "&apos;", $val);
	}

	// Swop user inputted backslashes

	//$val = preg_replace( "/\(?!&#|?#)/", "&#092;", $val );

	return $val;
}


/**
 * A method to convert a string into UTF-8 entities
 *
 * @internal
 * @deprecated
 * @param string Input string
 * @return string
 */
function cms_utf8entities($val)
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


//Taken from http://www.webmasterworld.com/forum88/164.htm
/*
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
}*/


/**
 * A function to put a backtrace into the generated log file
 */
function debug_bt_to_log()
{
    $bt=debug_backtrace();
    $file = $bt[0]['file'];
    $line = $bt[0]['line'];

    $out = array();
    $out[] = "Backtrace in $file on line $line";

    $bt = array_reverse($bt);
    foreach($bt as $trace)
    {
      if( $elem['function'] == 'debug_bt_to_log' ) continue;

      $file = $trace['file'];
      $line = $trace['line'];
      $function = $trace['function'];
      $out[] = "$function at $file:$line"; 
    }

    $filename = TMP_CACHE_LOCATION . '/debug.log';
    foreach ($out as $txt)
      {
	error_log($txt . "\n", 3, $filename);
      }
}

/**
 * A function to generate a backtrace in a readable format.
 */
function debug_bt()
{
    $bt=debug_backtrace();
    $file = $bt[0]['file'];
    $line = $bt[0]['line'];

    echo "\n\n<p><b>Backtrace in $file on line $line</b></p>\n";

    $bt = array_reverse($bt);
    echo "<pre><dl>\n";
    foreach($bt as $trace)
    {
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
* @param mixed $var
* @param string $title (optional)
* @param boolean $echo_to_screen (optional)
* @return string
*/
function debug_display($var, $title="", $echo_to_screen = true, $use_html = true)
{
  $gCms = cmsms();
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
		$titleText .= ' - (usage: '.memory_get_usage().')';
	}

	$memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():'');
	if( $memory_peak )
	  {
	    $titleText .= ' - (peak: '.$memory_peak.')';
	  }

	ob_start();
	if ($use_html)
		echo "<div><b>$titleText</b>\n";

	if(FALSE == empty($var))
	{
		if ($use_html)
		{
			echo '<pre>';
		}
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
		  if( $use_html )
		    {
			print_r(htmlentities(str_replace("\t", '  ', $var)));
		    }
		  else
		    {
 		      print_r($var);
		    }
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
		{
			echo '</pre>';
		}
	}
	if ($use_html)
		echo "</div>\n";

	$output = ob_get_contents();
	ob_end_clean();

	if($echo_to_screen)
	{
		echo $output;
	}

	return $output;
}

/**
 * Display $var nicely only if $config["debug"] is set
 *
 * @param mixed $var
 * @param string $title
 */
function debug_output($var, $title="")
{
  if(cmsms()->config["debug"] == true)
	{
		debug_display($var, $title, true);
	}

}


/**
 * Debug function to output debug information about a variable in a formatted matter
 * to a debug file.
 *
 * @param mixed data to display
 * @param string Optional title.
 */
function debug_to_log($var, $title='')
{
	$errlines = explode("\n",debug_display($var, $title, false, false));
	$filename = TMP_CACHE_LOCATION . '/debug.log';
	foreach ($errlines as $txt)
	{
		error_log($txt . "\n", 3, $filename);
	}
}


/**
 * Display $var nicely to the $gCms->errors array if $config['debug'] is set
 *
 * @param mixed $var
 * @param string $title
 */
function debug_buffer($var, $title="")
{
  $gCms = cmsms();
	if ($gCms)
	{
		$config = $gCms->GetConfig();

		//debug_to_log($var, $title='');

		if($config["debug"] == true)
		{
		  cmsms()->add_error(debug_display($var, $title, false, true));
		}
	}
}


/**
 * Debug an sql command
 *
 * @internal
 * @param string SQL query
 * @param boolean (unused)
 */
function debug_sql($str, $newline = false)
{
  $gCms = cmsms();
	if ($gCms)
	{
		$config = $gCms->GetConfig();

		if($config["debug"] == true)
		{
		  cmsms()->add_error(debug_display($str, '', false, true));
		}
	}
}

/**
* Retrieve value from $_REQUEST. Returns $default_value if
*		value is not in $_REQUEST or is not the same basic type as
*		$default_value.
*		If $session_key is set, then will return session value in preference
*		to $default_value if $_REQUEST[$value] is not set.
*
* @param string $value
* @param mixed $default_value (optional)
* @param string $session_key (optional)
* @return mixed
*/
function get_request_value($value, $default_value = '', $session_key = '')
{
	if($session_key != '')
	{
		if(isset($_SESSION['request_values'][$session_key][$value]))
		{
			$default_value = $_SESSION['request_values'][$session_key][$value];
		}
	}
	if(isset($_REQUEST[$value]))
	{
		$result = get_value_with_default($_REQUEST[$value], $default_value);
	}
	else
	{
		$result = $default_value;
	}

	if($session_key != '')
	{
		$_SESSION['request_values'][$session_key][$value] = $result;
	}

	return $result;
}

/**
* Return $value if it's set and same basic type as $default_value,
*			otherwise return $default_value. Note. Also will trim($value)
*			if $value is not numeric.
*
* @param string $value
* @param mixed $default_value
* @return mixed
*/
function get_value_with_default($value, $default_value = '', $session_key = '')
{
	if($session_key != '')
	{
		if(isset($_SESSION['default_values'][$session_key]))
		{
			$default_value = $_SESSION['default_values'][$session_key];
		}
	}

	// set our return value to the default initially and overwrite with $value if we like it.
	$return_value = $default_value;

	if(isset($value))
	{
		if(is_array($value))
		{
			// $value is an array - validate each element.
			$return_value = array();
			foreach($value as $element)
			{
				$return_value[] = get_value_with_default($element, $default_value);
			}
		}
		else
		{
			if(is_numeric($default_value))
			{
				if(is_numeric($value))
				{
					$return_value = $value;
				}
			}
			else
			{
				$return_value = trim($value);
			}
		}
	}

	if($session_key != '')
	{
		$_SESSION['default_values'][$session_key] = $return_value;
	}

	return $return_value;
}


/**
 * Retrieve the $value from the $parameters array checking for
 * $parameters[$value] and $params[$id.$value]. Returns $default
 * if $value is not in $params array.
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
	if($session_key != '')
	{
		if(isset($_SESSION['parameter_values'][$session_key]))
		{
			$default_value = $_SESSION['parameter_values'][$session_key];
		}
	}

	// set our return value to the default initially and overwrite with $value if we like it.
	$return_value = $default_value;
	if(isset($parameters[$value]))
	{
		if(is_bool($default_value))
		{
			// want a boolean return_value
			if(isset($parameters[$value]))
			{
				$return_value = (boolean)$parameters[$value];
			}
		}
		else
		{
			// is $default_value a number?
			$is_number = false;
			if(is_numeric($default_value))
			{
				$is_number = true;
			}

			if(is_array($parameters[$value]))
			{
				// $parameters[$value] is an array - validate each element.
				$return_value = array();
				foreach($parameters[$value] as $element)
				{
					$return_value[] = get_value_with_default($element, $default_value);
				}
			}
			else
			{
				if(is_numeric($default_value))
				{
					// default value is a number, we only like $parameters[$value] if it's a number too.
					if(is_numeric($parameters[$value]))
					{
						$return_value = $parameters[$value];
					}
				}
				elseif(is_string($default_value))
				{
					$return_value = trim($parameters[$value]);
				}
				else
				{
					$return_value = $parameters[$value];
				}
			}
		}
	}

	if($session_key != '')
	{
		$_SESSION['parameter_values'][$session_key] = $return_value;
	}

	return $return_value;
}

/**
 * A convenience functo to create a dropdown control with supported encodings.
 *
 * @internal
 * @param string A name for the control
 * @param string The name of the option that should be selected.
 * @return string
 */
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


/**
 * A method to remove a permission from the database.
 *
 * @internal
 * @access private
 * @param string The permission name
 */
function cms_mapi_remove_permission($permission_name)
{
  $db = cmsms()->GetDB();

  $query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = ?";
  $row = &$db->GetRow($query, array($permission_name));

  if ($row)
    {
      $id = $row["permission_id"];

      $query = "DELETE FROM ".cms_db_prefix()."group_perms WHERE permission_id = ?";
      $db->Execute($query, array($id));

      $query = "DELETE FROM ".cms_db_prefix()."permissions WHERE permission_id = ?";
      $db->Execute($query, array($id));
    }
}


/**
 * A method to add a permission to the CMSMS permissions table
 *
 * @internal
 * @access private
 * @param unknown (ignored)
 * @param string  The permission name
 * @param string  The permission human readable text.
 */
function cms_mapi_create_permission($cms, $permission_name, $permission_text)
{
  $db = cmsms()->GetDb();

	$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name =" . $db->qstr($permission_name);
	$result = $db->Execute($query);

	if ($result && $result->RecordCount() < 1) {

		$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
		$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$db->Execute($query);
	}

	if ($result)
	{
		$result->Close();
		return true;
	}
	return false;
}


/**
 * A function to test if a file specification matches an array of
 * file specifications that indicate it should be excluded
 *
 * @internal
 * @deprecated
 * @access private
 * @param string Flle specification
 * @param array  Array of regular expressions.
 * @return boolean
 */
function filespec_is_excluded( $file, $excludes )
{
  // strip the path from the file
  if( empty($excludes) ) return false;
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
 *
 * @param  string  Start directory.
 * @return boolean
 */
function is_directory_writable( $path )
{
  if ( substr ( $path , strlen ( $path ) - 1 ) != '/' )
    {
      $path .= '/' ;
    }

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
 * performs a non recursive search
 *
 * @internal
 * @param path - path to search
 * @param extensions - include only files matching these extensions
 *                     case insensitive, comma delimited
 */
function get_matching_files($dir,$extensions = '',$excludedot = true,$excludedir = true,
			    $fileprefix='',$excludefiles=1)
{

  $dh = @opendir($dir);
  if( !$dh ) return false;

  if( !empty($extensions) )
    {
      $extensions = explode(',',strtolower($extensions));
    }
  $results = array();
  while( false !== ($file = readdir($dh)) )
    {
      if( $file == '.' || $file == '..' ) continue;
      if( startswith($file,'.') && $excludedot ) continue;
      if( is_dir(cms_join_path($dir,$file)) && $excludedir ) continue;
      if( !empty($fileprefix) )
	{
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
 * Return an array containing a list of files in a directory
 * performs a recursive search
 *
 * @internal
 * @param  string    Start Path.
 * @param  array     Array of regular expressions indicating files to exclude.
 * @param  int       How deep to browse (-1=unlimited)
 * @param  string    "FULL"|"DIRS"|"FILES"
 * @param  d         for internal use only
 * @return  array
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
	 if( $file == '.' || $file == '..' ) continue;
	 if( filespec_is_excluded( $file, $excludes ) ) continue;

	 $file = $path . $file ;
	 if ( ! @is_dir ( $file ) ) { if ( $mode != "DIRS" ) { $dirlist[] = $file ; } }
	 elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) )
	   {
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
 * A function to recursively delete all files and folders in a directory
 * synonymous with rm -r
 *
 * @param string The directory name
 * @return boolean
 */
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


/**
 * A function to recursively chmod all files and folders in a directory
 *
 * @see chmod
 * @param string The start location
 * @param integer The mode
 */
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

/**
 * A method to serialize an object and encode it for storage or transport.
 *
 * @internal
 * @param object The object to serialize
 * @return string
 */
function SerializeObject(&$object)
{
	return base64_encode(serialize($object));
}

/**
 * A function unserialize data into an object
 *
 * @internal
 * @param string The serialized text.
 * @return object  A valid object on success, or null
 */
function UnserializeObject(&$serialized)
{
	return  unserialize(base64_decode($serialized));
}


/**
 * A convenience function to test wether one string starts with another
 *
 * i.e:  startswith('The Quick Brown Fox','The');
 *
 * @param string The string to test against
 * @param string The search string
 * @return boolean
 */
function startswith( $str, $sub )
{
	return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}

/**
 * Similar to the startswith method, this function tests with string A ends with string B
 *
 * i.e: endswith('The Quick Brown Fox','Fox');
 *
 * @param string The string to test against
 * @param string The search string
 * @return boolean
 */
function endswith( $str, $sub )
{
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

/**
 * A convenience function to display the memory usage
 *
 * @internal
 * @param string A prefix to display before the memory output
 * @return void
 */
function showmem($string = '')
{
	var_dump($string . ' -- ' . memory_get_usage());
}


/**
 * convert a human readable string into something that is suitable for use in URLS
 * because many browsers don't support UTF-8 characters in URLS
 *
 * @internal
 * @param string String to convert
 * @param boolean indicates whether output string should be converted to lower case
 * @param boolean indicates wether slashes should be allowed in the input.
 * @return string
 */
function munge_string_to_url($alias, $tolower = false, $withslash = false)
{
	// replacement.php is encoded utf-8 and must be the first modification of alias
	include(dirname(__FILE__) . '/replacement.php');
	$alias = str_replace($toreplace, $replacement, $alias);

	// lowercase only on empty aliases
	if ($tolower == true)
	{
		$alias = strtolower($alias);
	}

	$expr = '/[^a-z0-9-_]+/i';
	if( $withslash )
	  {
	    $expr = '/[^a-z0-9-_\/]+/i';
	  }
	$alias = preg_replace($expr,'-',$alias);
	//$alias = preg_replace("/[^\w-]+/", "-", $alias);
	$alias = trim($alias, '-');

	return $alias;
}

// I'm not gonna document these.
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}

if(!function_exists("file_get_contents"))
{
   function file_get_contents($filename)
   {
	   if(($contents = file($filename)))
	   {
		   $contents = implode('', $contents);
		   return $contents;
	   }
	   else
		   return false;
   }
}


function cms_readfile($filename)
{
  @ob_start();
  echo file_get_contents($filename);
  $result = @ob_get_contents();
  @ob_end_clean();
  if( !empty($result) ) {
    echo $result;
    return TRUE;
  }
  return FALSE;
}


// create the array_walk_recursive function in PHP4
// from http://www.php.net/manual/en/function.array-walk-recursive.php
if (!function_exists('array_walk_recursive'))
{
   function array_walk_recursive(&$input, $funcname, $userdata = "")
   {
       if (!is_callable($funcname))
       {
           return false;
       }

       if (!is_array($input))
       {
           return false;
       }

       foreach ($input AS $key => $value)
       {
           if (is_array($input[$key]))
           {
               array_walk_recursive($input[$key], $funcname, $userdata);
           }
           else
           {
               $saved_value = $value;
               if (!empty($userdata))
               {
                   $funcname($value, $key, $userdata);
               }
               else
               {
                   $funcname($value, $key);
               }

               if ($value != $saved_value)
               {
                   $input[$key] = $value;
               }
           }
       }
       return true;
	}
}

if (!function_exists("stripos")) {
  function stripos($str,$needle,$offset=0)
  {
      return strpos(strtolower($str),strtolower($needle),$offset);
  }
}

/*
 * Sanitize input to prevent against XSS and other nasty stuff.
 * Taken from cakephp (http://cakephp.org)
 * Licensed under the MIT License
 * 
 * @param string input
 * @return string
 */
function cleanValue($val) {
	if ($val == "") {
		return $val;
	}
	//Replace odd spaces with safe ones
	$val = str_replace(" ", " ", $val);
	$val = str_replace(chr(0xCA), "", $val);
	//Encode any HTML to entities (including \n --> <br />)
	$val = cleanHtml($val);
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

/*
 * Method to sanitize incoming html.
 * Take from cakephp (http://cakephp.org)
 * Licensed under the MIT License
 *
 * @param string Input HTML code.
 * @param boolean Wether HTML tags should be removed.
 * @return string
 */
function cleanHtml($string, $remove = false) {
	if ($remove) {
		$string = strip_tags($string);
	} else {
		$patterns = array("/\&/", "/%/", "/</", "/>/", '/"/', "/'/", "/\(/", "/\)/", "/\+/", "/-/");
		$replacements = array("&amp;", "&#37;", "&lt;", "&gt;", "&quot;", "&#39;", "&#40;", "&#41;", "&#43;", "&#45;");
		$string = preg_replace($patterns, $replacements, $string);
	}
	return $string;
}


define('CLEAN_INT','CLEAN_INT');
define('CLEAN_FLOAT','CLEAN_FLOAT');
define('CLEAN_NONE','CLEAN_NONE');
define('CLEAN_STRING','CLEAN_STRING');
define('CLEAN_REGEXP','regexp:');
define('CLEAN_FILE','CLEAN_FILE');

/**
 * Method to sanitize all entries in a hash
 * This method is called by the module api to clean incomming parameters in the frontend.
 * It uses the map created with the SetParameterType() method in the module api.
 *
 * @internal
 * @param string Module Name
 * @param array  Hash data
 * @param array  A map of param names and type information
 * @param boolean A flag indicating wether unknown keys in the input data should be allowed.
 * @param boolean A flag indicating wether keys should be treated as strings and cleaned.
*/
function cleanParamHash($modulename,$data,$map = false,
			$allow_unknown = false,$clean_keys = true)
{
  $mappedcount = 0;
  $result = array();
  foreach( $data as $key => $value )
	{
	  $mapped = false;
	  $paramtype = '';
	  if( is_array($map) )
		{
		  if( isset($map[$key]) )
			{
				$paramtype = $map[$key];
			}
		  else {
			  // Key not found in the map
			  // see if one matches via regular expressions
			  foreach( $map as $mk => $mv ) {
				  if(strstr($mk,CLEAN_REGEXP) === FALSE) continue;

				  // mk is a regular expression
				  $ss = substr($mk,strlen(CLEAN_REGEXP));
				  if( $ss !== FALSE ) {
					  if( preg_match($ss, $key) ) {
						  // it matches, we now know what type to use
						  $paramtype = $mv;
						  break;
					  }
				  }
			  }
		  } // else

		  if( $paramtype != '' ) {
			  switch( $paramtype ) {
			  case 'CLEAN_INT':
				  $mappedcount++;
				  $mapped = true;
				  $value = (int) $value;
				  break;
			  case 'CLEAN_FLOAT':
				  $mappedcount++;
				  $mapped = true;
				  $value = (float) $value;
				  break;
			  case 'CLEAN_NONE':
				  // pass through without cleaning.
				  $mappedcount++;
				  $mapped = true;
				  break;
			  case 'CLEAN_STRING':
				  $value = cms_htmlentities($value);
				  $mappedcount++;
				  $mapped = true;
				  break;
			  case 'CLEAN_FILE':
				  $value = cms_cleanfile($value);
				  $mappedcount++;
				  $mapped = true;
				  break;
			  default:
				  $mappedcount++;
				  $mapped = true;
				  $value = cms_htmlentities($value);
				  break;
			  } // switch
		  } // if $paramtype

		}

	  // we didn't clean this yet
	  if( $allow_unknown && !$mapped )
		{
		  // but we're allowing unknown stuff so we'll just clean it.
		  $value = cms_htmlentities($value);
		  $mappedcount++;
		  $mapped = true;
		}

	  if( $clean_keys )
		{
		  $key = cms_htmlentities($key);
		}

	  if( !$mapped && !$allow_unknown )
		{
		  trigger_error('Parameter '.$key.' is not known by module '.$modulename.' dropped',E_USER_WARNING);
		  continue;
		}
	  $result[$key]=$value;
	}
  return $result;
}


/**
 * Returns all parameters sent that are destined for the module with
 * the given $id.  This method reads the parameters directly from the $_REQUEST
 *
 * @internal
 * @param string ID (the module instance ID to use for extracting parameters)
 * @return array
 */
function GetModuleParameters($id)
{
  $params = array();

  if ($id != '')
    {
      foreach ($_REQUEST as $key=>$value)
	{
	  if (strpos($key, (string)$id) !== FALSE && strpos($key, (string)$id) == 0)
	    {
	      $key = str_replace($id, '', $key);
	      if( $key == 'id' || $key == 'returnid' )
		{
		  $value = (int)$value;
		}
	      $params[$key] = $value;
	    }
	}
    }

  return $params;
}


/**
 * A function to test if permissions are set correctly, and the server is set properly
 * to allow users to upload to CMSMS.
 *
 * @internal
 * @return boolean
 */
function can_users_upload()
{
  # first, check to see if safe mode is enabled
  # if it is, then check to see the owner of the index.php, moduleinterface.php
  # and the uploads and modules directory.  if they all match, then we
  # can upload files.
  # if safe mode is off, then we just have to check the permissions.
  $gCms = cmsms();
  $file_index = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'index.php';
  $dir_uploads = $gCms->config['uploads_path'];

  $stat_index = @stat($file_index);
  $stat_uploads = @stat($dir_uploads);

  if( $stat_index == FALSE || $stat_uploads == FALSE )
    {
      // couldn't get some necessary information.
      return false;
    }

  $safe_mode = ini_get_boolean('safe_mode');
  if( $safe_mode )
    {
      // we're in safe mode.
      if( $stat_index[4] != $stat_uploads[4] )
	{
	  return FALSE;
	}
    }

  // now check to see if we can write to the directories
  if( !is_writable( $dir_modules ) )
    {
      return FALSE;
    }
  if( !is_writable( $dir_uploads ) )
    {
      return FALSE;
    }

  // It all worked.
  return TRUE;
}


/**
 * A function to test if permissions, and php configuration is setup correctly
 * to allow an administrator to upload files to CMSMS
 *
 * @internal
 * @return boolean
 */
function can_admin_upload()
{
  # first, check to see if safe mode is enabled
  # if it is, then check to see the owner of the index.php, moduleinterface.php
  # and the uploads and modules directory.  if they all match, then we
  # can upload files.
  # if safe mode is off, then we just have to check the permissions.
  $gCms = cmsms();
  $file_index = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'index.php';
  $file_moduleinterface = $gCms->config['root_path'].DIRECTORY_SEPARATOR.
    $gCms->config['admin_dir'].DIRECTORY_SEPARATOR.'moduleinterface.php';
  $dir_uploads = $gCms->config['uploads_path'];
  $dir_modules = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'modules';

  $stat_index = @stat($file_index);
  $stat_moduleinterface = @stat($file_moduleinterface);
  $stat_uploads = @stat($dir_uploads);
  $stat_modules = @stat($dir_modules);

  $my_uid = @getmyuid();

  if( $my_uid === FALSE || $stat_index == FALSE ||
      $stat_moduleinterface == FALSE || $stat_uploads == FALSE ||
      $stat_modules == FALSE )
    {
      // couldn't get some necessary information.
      return FALSE;
    }

  $safe_mode = ini_get_boolean('safe_mode');
  if( $safe_mode )
    {

      // we're in safe mode.
      if( ($stat_moduleinterface[4] != $stat_modules[4]) ||
	  ($stat_moduleinterface[4] != $stat_uploads[4]) ||
	  ($my_uid != $stat_moduleinterface[4]) )
	{
	  // owners don't match
	  return FALSE;
	}
    }

  // now check to see if we can write to the directories
  if( !is_writable( $dir_modules ) )
    {
      return FALSE;
    }
  if( !is_writable( $dir_uploads ) )
    {
      return FALSE;
    }

  // It all worked.
  return TRUE;
}


/** 
 * A convenience function to interpret octal permissions, and return 
 * a human readable string.  Uses the lang() function for translation.
 *
 * @internal
 * @param int The permissions to test.
 * @return string
 */
function interpret_permissions($perms)
{
  $owner = array();
  $group = array();
  $other = array();

  if( $perms & 0400 )
    {
      $owner[] = lang('read');
    }
  if( $perms & 0200 )
    {
      $owner[] = lang('write');
    }
  if( $perms & 0100 )
    {
      $owner[] = lang('execute');
    }

  if( $perms & 0040 )
    {
      $group[] = lang('read');
    }
  if( $perms & 0020 )
    {
      $group[] = lang('write');
    }
  if( $perms & 0010 )
    {
      $group[] = lang('execute');
    }

  if( $perms & 0004 )
    {
      $other[] = lang('read');
    }
  if( $perms & 0002 )
    {
      $other[] = lang('write');
    }
  if( $perms & 0001 )
    {
      $other[] = lang('execute');
    }

  return array($owner,$group,$other);
}


/**
 * A convenience function to return a boolean variable given a php ini key that represents a boolean
 *
 * @param string  The php ini key
 * @return integer
 */
function ini_get_boolean($str)
{
  $val1 = ini_get($str);
  $val2 = strtolower($val1);

  $ret = 0;
  if( $val2 == 1 || $val2 == '1' || $val2 == 'yes' || $val2 == 'true' || $val2 == 'on' )
     $ret = 1;
  return $ret;
}

/**
 * Another convenience function to output a human readable function stack trace
 *
 * @return void
 */
function stack_trace()
{
  $stack = debug_backtrace();
  foreach( $stack as $elem )
    {
      if( $elem['function'] == 'stack_trace' ) continue;
      if( isset($elem['file'])  )
	{
	  echo $elem['file'].':'.$elem['line'].' - '.$elem['function'].'<br/>';
	}
      else
	{
	  echo ' - '.$elem['function'].'<br/>';
	}
    }
}


/** 
 * A wrapper around move_uploaded_file that attempts to ensure permissions on uploaded
 * files are set correctly.
 *
 * @param string The temporary file specification
 * @param string The destination file specification
 * @return boolean.
 */
function cms_move_uploaded_file( $tmpfile, $destination )
{
   $config = cmsms()->GetConfig();

   if( !@move_uploaded_file( $tmpfile, $destination ) )
   {
      return false;
   }

   @chmod($destination,octdec($config['default_upload_permission']));
   return true;
}


/**
 * A function to read CSS cache information
 *
 * @deprecated
 * @internal
 * @param string Filename containing cash information
 * @return array of css cache information
 */
function csscache_csvfile_to_hash($filename)
{
  if( !is_readable($filename) ) return false;
  $tmp = @file($filename);
  if( !is_array($tmp) || !count($tmp) ) return false;

  $result = array();
  foreach( $tmp as $one )
    {
      $vals = explode(',',trim($one));
      $result[$vals[0]] = $vals[1];
    }
  return $result;
}

/**
 * A function to take hash information representing CSS cache data and store it in a CSV file
 *
 * @deprecated
 * @internal
 * @param string Filename to output information to
 * @param array  Hash of CSS cache information.
 * @return void
 */
function csscache_hash_to_csvfile($filename,$hash)
{
  $fh = @fopen($filename,'w');
  if( !$fh ) return false;
  foreach( $hash as $key => $val )
    {
      $key = trim($key); $val = trim($val);
      $line = "$key,$val\n";
      fwrite($fh,$line);
    }
  fclose($fh);
}

/**
 * A function to clear clear the cached CSS information
 *
 * @deprecated
 * @internal
 * @param string Filename containing CSS cache information
 * @return void
 */
function css_cache_clear($filename)
{
  @unlink($filename);
}


/**
 * A function to test wether an IP address matches a list of expressions
 * Credits to J.Adams <jna@retins.net>
 *
 * Expressions can be of the form
 *   xxx.xxx.xxx.xxx        (exact)
 *   xxx.xxx.xxx.[yyy-zzz]  (range)
 *   xxx.xxx.xxx.xxx/nn    (nn = # bits, cisco style -- i.e. /24 = class C)
 *
 * @param string IP address to test
 * @param array  Array of match expressions
 * @return boolean
 */
function cms_ipmatches($ip,$checklist)
{
  if( !function_exists('__testip') ) {
  function __testip($range,$ip)
  {
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
    // xxx.xxx.xxx.xx[yyy-zzz]  (range, partial octets not supported)

    $regs = array();
    if (preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)\/([0-9]+)/",$range,$regs)) {
      // perform a mask match
      $ipl = ip2long($ip);
      $rangel = ip2long($regs[1] . "." . $regs[2] . "." . $regs[3] . "." . $regs[4]);

      $maskl = 0;

      for ($i = 0; $i< 31; $i++) {
	if ($i < $regs[5]-1) {
	  $maskl = $maskl + pow(2,(30-$i));
	}
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

      if( count($maskocts) != count($ipocts) && count($maskocts) != 4 )
	{
	  return 0;
	}

      // perform a range match
      for ($i=0; $i<4; $i++) {
	if (preg_match("/\[([0-9]+)\-([0-9]+)\]/",$maskocts[$i],$regs)) {
	  if ( ($ipocts[$i] > $regs[2]) || ($ipocts[$i] < $regs[1])) {
	    $result = 0;
	  }
	}
	else
	  {
	    if ($maskocts[$i] <> $ipocts[$i]) {
	      $result = 0;
	    }
	  }
      }
    }
    return $result;
  } // __testip
  } // if

  if( !is_array($checklist) )
    {
      $checklist = explode(',',$checklist);
    }
  foreach( $checklist as $one )
    {
      if( __testip(trim($one),$ip) ) return TRUE;
    }
  return FALSE;
}


/**
 * @author	Dominic Sayers <dominic_sayers@hotmail.com>
 * @copyright	2009 Dominic Sayers
 * @license	http://www.opensource.org/licenses/cpal_1.0 Common Public Attribution License Version 1.0 (CPAL) license
 * @link	http://www.dominicsayers.com/isemail
 * @version	1.9 - Minor modifications to make it compatible with PHPLint
 * @return boolean
 * @param string  $email
 * @param boolean $checkDNS
*/
function is_email( $email, $checkDNS=false ) {
	// Check that $email is a valid address. Read the following RFCs to understand the constraints:
	// 	(http://tools.ietf.org/html/rfc5322)
	// 	(http://tools.ietf.org/html/rfc3696)
	// 	(http://tools.ietf.org/html/rfc5321)
	// 	(http://tools.ietf.org/html/rfc4291#section-2.2)
	// 	(http://tools.ietf.org/html/rfc1123#section-2.1)

	// the upper limit on address lengths should normally be considered to be 256
	// 	(http://www.rfc-editor.org/errata_search.php?rfc=3696)
	// 	NB I think John Klensin is misreading RFC 5321 and the the limit should actually be 254
	// 	However, I will stick to the published number until it is changed.
	//
	// The maximum total length of a reverse-path or forward-path is 256
	// characters (including the punctuation and element separators)
	// 	(http://tools.ietf.org/html/rfc5321#section-4.5.3.1.3)
	$emailLength = strlen($email);
	if ($emailLength > 256)	return false;	// Too long

	// Contemporary email addresses consist of a "local part" separated from
	// a "domain part" (a fully-qualified domain name) by an at-sign ("@").
	// 	(http://tools.ietf.org/html/rfc3696#section-3)
	$atIndex = strrpos($email,'@');

	if ($atIndex === false) return false;	// No at-sign
	if ($atIndex === 0) return false;	// No local part
	if ($atIndex === $emailLength) return false;	// No domain part

	// Sanitize comments
	// - remove nested comments, quotes and dots in comments
	// - remove parentheses and dots from quoted strings
	$braceDepth	= 0;
	$inQuote	= false;
	$escapeThisChar	= false;

	for ($i = 0; $i < $emailLength; ++$i) {
		$char = $email[$i];
		$replaceChar = false;

		if ($char === '\\') {
			$escapeThisChar = !$escapeThisChar;	// Escape the next character?
		} else {
			switch ($char) {
			case '(':
				if ($escapeThisChar) {
					$replaceChar = true;
				} else {
					if ($inQuote) {
						$replaceChar = true;
					} else {
						if ($braceDepth++ > 0) $replaceChar = true;	// Increment brace depth
					}
				}

				break;
			case ')':
				if ($escapeThisChar) {
					$replaceChar = true;
				} else {
					if ($inQuote) {
						$replaceChar = true;
					} else {
						if (--$braceDepth > 0) $replaceChar = true;	// Decrement brace depth
						if ($braceDepth < 0) $braceDepth = 0;
					}
				}

				break;
			case '"':
				if ($escapeThisChar) {
					$replaceChar = true;
				} else {
					if ($braceDepth === 0) {
						$inQuote = !$inQuote;	// Are we inside a quoted string?
					} else {
						$replaceChar = true;
					}
				}

				break;
			case '.':	// Dots don't help us either
				if ($escapeThisChar) {
					$replaceChar = true;
				} else {
					if ($braceDepth > 0) $replaceChar = true;
				}

				break;
			default:
			}

			$escapeThisChar = false;
			if ($replaceChar) $email[$i] = 'x';	// Replace the offending character with something harmless
		}
	}

	$localPart	= substr($email, 0, $atIndex);
	$domain		= substr($email, $atIndex + 1);
	$FWS		= "(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t]+)|(?:[ \\t]+(?:(?:\\r\\n)[ \\t]+)*))";	// Folding white space
	// Let's check the local part for RFC compliance...
	//
	// local-part      =       dot-atom / quoted-string / obs-local-part
	// obs-local-part  =       word *("." word)
	// 	(http://tools.ietf.org/html/rfc5322#section-3.4.1)
	//
	// Problem: need to distinguish between "first.last" and "first"."last"
	// (i.e. one element or two). And I suck at regexes.
	$dotArray = preg_split('/\\.(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/m', $localPart);
	$partLength	= 0;

	foreach ($dotArray as $element) {
		// Remove any leading or trailing FWS
		$element = preg_replace("/^$FWS|$FWS\$/", '', $element);

		// Then we need to remove all valid comments (i.e. those at the start or end of the element
		$elementLength = strlen($element);

		if ($element[0] === '(') {
			$indexBrace = strpos($element, ')');
			if ($indexBrace !== false) {
				if (preg_match('/(?<!\\\\)[\\(\\)]/', substr($element, 1, $indexBrace - 1)) > 0) {
					return false;	// Illegal characters in comment
				}
				$element = substr($element, $indexBrace + 1, $elementLength - $indexBrace - 1);
				$elementLength = strlen($element);
			}
		}

		if ($element[$elementLength - 1] === ')') {
			$indexBrace = strrpos($element, '(');
			if ($indexBrace !== false) {
				if (preg_match('/(?<!\\\\)(?:[\\(\\)])/', substr($element, $indexBrace + 1, $elementLength - $indexBrace - 2)) > 0) {
					return false;	// Illegal characters in comment
				}
				$element = substr($element, 0, $indexBrace);
				$elementLength = strlen($element);
			}
		}

		// Remove any leading or trailing FWS around the element (inside any comments)
		$element = preg_replace("/^$FWS|$FWS\$/", '', $element);

		// What's left counts towards the maximum length for this part
		if ($partLength > 0) $partLength++;	// for the dot
		$partLength += strlen($element);

		// Each dot-delimited component can be an atom or a quoted string
		// (because of the obs-local-part provision)
		if (preg_match('/^"(?:.)*"$/s', $element) > 0) {
			// Quoted-string tests:
			//
			// Remove any FWS
			$element = preg_replace("/(?<!\\\\)$FWS/", '', $element);
			// My regex skillz aren't up to distinguishing between \" \\" \\\" \\\\" etc.
			// So remove all \\ from the string first...
			$element = preg_replace('/\\\\\\\\/', ' ', $element);
			if (preg_match('/(?<!\\\\|^)["\\r\\n\\x00](?!$)|\\\\"$|""/', $element) > 0)	return false;	// ", CR, LF and NUL must be escaped, "" is too short
		} else {
			// Unquoted string tests:
			//
			// Period (".") may...appear, but may not be used to start or end the
			// local part, nor may two or more consecutive periods appear.
			// 	(http://tools.ietf.org/html/rfc3696#section-3)
			//
			// A zero-length element implies a period at the beginning or end of the
			// local part, or two periods together. Either way it's not allowed.
			if ($element === '') return false;	// Dots in wrong place

			// Any ASCII graphic (printing) character other than the
			// at-sign ("@"), backslash, double quote, comma, or square brackets may
			// appear without quoting.  If any of that list of excluded characters
			// are to appear, they must be quoted
			// 	(http://tools.ietf.org/html/rfc3696#section-3)
			//
			// Any excluded characters? i.e. 0x00-0x20, (, ), <, >, [, ], :, ;, @, \, comma, period, "
			if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]/', $element) > 0) return false;	// These characters must be in a quoted string
		}
	}

	if ($partLength > 64) return false;	// Local part must be 64 characters or less

	// Now let's check the domain part...
	// The domain name can also be replaced by an IP address in square brackets
	// 	(http://tools.ietf.org/html/rfc3696#section-3)
	// 	(http://tools.ietf.org/html/rfc5321#section-4.1.3)
	// 	(http://tools.ietf.org/html/rfc4291#section-2.2)
	if (preg_match('/^\\[(.)+]$/', $domain) === 1) {
		// It's an address-literal
		$addressLiteral = substr($domain, 1, strlen($domain) - 2);
		$matchesIP = array();

		// Extract IPv4 part from the end of the address-literal (if there is one)
		if (preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressLiteral, $matchesIP) > 0) {
			$index = strrpos($addressLiteral, $matchesIP[0]);

			if ($index === 0) {
				// Nothing there except a valid IPv4 address, so...
				return true;
			} else {
				// Assume it's an attempt at a mixed address (IPv6 + IPv4)
				if ($addressLiteral[$index - 1] !== ':') return false;	// Character preceding IPv4 address must be ':'
				if (substr($addressLiteral, 0, 5) !== 'IPv6:') return false;	// RFC5321 section 4.1.3
				$IPv6 = substr($addressLiteral, 5, ($index ===7) ? 2 : $index - 6);
				$groupMax = 6;
			}
		} else {
			// It must be an attempt at pure IPv6
			if (substr($addressLiteral, 0, 5) !== 'IPv6:') return false;	// RFC5321 section 4.1.3
			$IPv6 = substr($addressLiteral, 5);
			$groupMax = 8;
		}

		$groupCount	= preg_match_all('/^[0-9a-fA-F]{0,4}|\\:[0-9a-fA-F]{0,4}|(.)/', $IPv6, $matchesIP);
		$index = strpos($IPv6,'::');

		if ($index === false) {
			// We need exactly the right number of groups
			if ($groupCount !== $groupMax) return false;	// RFC5321 section 4.1.3
		} else {
			if ($index !== strrpos($IPv6,'::')) return false;	// More than one '::'
			$groupMax = ($index === 0 || $index === (strlen($IPv6) - 2)) ? $groupMax : $groupMax - 1;
			if ($groupCount > $groupMax) return false;	// Too many IPv6 groups in address
		}

		// Check for unmatched characters
		array_multisort($matchesIP[1], SORT_DESC);
		if ($matchesIP[1][0] !== '') return false;	// Illegal characters in address

		// It's a valid IPv6 address, so...
		return true;
	} else {
		// It's a domain name...

		// The syntax of a legal Internet host name was specified in RFC-952
		// One aspect of host name syntax is hereby changed: the
		// restriction on the first character is relaxed to allow either a
		// letter or a digit.
		// 	(http://tools.ietf.org/html/rfc1123#section-2.1)
		//
		// NB RFC 1123 updates RFC 1035, but this is not currently apparent from reading RFC 1035.
		//
		// Most common applications, including email and the Web, will generally not
		// permit...escaped strings
		// 	(http://tools.ietf.org/html/rfc3696#section-2)
		//
		// the better strategy has now become to make the "at least one period" test,
		// to verify LDH conformance (including verification that the apparent TLD name
		// is not all-numeric)
		// 	(http://tools.ietf.org/html/rfc3696#section-2)
		//
		// Characters outside the set of alphabetic characters, digits, and hyphen MUST NOT appear in domain name
		// labels for SMTP clients or servers
		// 	(http://tools.ietf.org/html/rfc5321#section-4.1.2)
		//
		// RFC5321 precludes the use of a trailing dot in a domain name for SMTP purposes
		// 	(http://tools.ietf.org/html/rfc5321#section-4.1.2)
		$dotArray = preg_split('/\\.(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/m', $domain);
		$partLength = 0;
		if (count($dotArray) === 1) return false;	// Mail host can't be a TLD

		foreach ($dotArray as $element) {
			// Remove any leading or trailing FWS
			$element = preg_replace("/^$FWS|$FWS\$/", '', $element);

			// Then we need to remove all valid comments (i.e. those at the start or end of the element
			$elementLength = strlen($element);

			if ($element[0] === '(') {
				$indexBrace = strpos($element, ')');
				if ($indexBrace !== false) {
					if (preg_match('/(?<!\\\\)[\\(\\)]/', substr($element, 1, $indexBrace - 1)) > 0) {
						return false;	// Illegal characters in comment
					}
					$element = substr($element, $indexBrace + 1, $elementLength - $indexBrace - 1);
					$elementLength = strlen($element);
				}
			}

			if ($element[$elementLength - 1] === ')') {
				$indexBrace = strrpos($element, '(');
				if ($indexBrace !== false) {
					if (preg_match('/(?<!\\\\)(?:[\\(\\)])/', substr($element, $indexBrace + 1, $elementLength - $indexBrace - 2)) > 0) {
						return false;	// Illegal characters in comment
					}
					$element = substr($element, 0, $indexBrace);
					$elementLength = strlen($element);
				}
			}

			// Remove any leading or trailing FWS around the element (inside any comments)
			$element = preg_replace("/^$FWS|$FWS\$/", '', $element);

			// What's left counts towards the maximum length for this part
			if ($partLength > 0) $partLength++;	// for the dot
			$partLength += strlen($element);

			// The DNS defines domain name syntax very generally -- a
			// string of labels each containing up to 63 8-bit octets,
			// separated by dots, and with a maximum total of 255
			// octets.
			// 	(http://tools.ietf.org/html/rfc1123#section-6.1.3.5)
			if ($elementLength > 63) return false;	// Label must be 63 characters or less

			// Each dot-delimited component must be atext
			// A zero-length element implies a period at the beginning or end of the
			// local part, or two periods together. Either way it's not allowed.
			if ($elementLength === 0) return false;	// Dots in wrong place

			// Any ASCII graphic (printing) character other than the
			// at-sign ("@"), backslash, double quote, comma, or square brackets may
			// appear without quoting.  If any of that list of excluded characters
			// are to appear, they must be quoted
			// 	(http://tools.ietf.org/html/rfc3696#section-3)
			//
			// If the hyphen is used, it is not permitted to appear at
			// either the beginning or end of a label.
			// 	(http://tools.ietf.org/html/rfc3696#section-2)
			//
			// Any excluded characters? i.e. 0x00-0x20, (, ), <, >, [, ], :, ;, @, \, comma, period, "
			//if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]|^-|-$/', $element) > 0) {
			if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]$/', $element) > 0) {
				return false;
			}
		}

		if ($partLength > 255) return false;	// Local part must be 64 characters or less
		if (preg_match('/^[0-9]+$/', $element) > 0)	return false;	// TLD can't be all-numeric

		// Check DNS?
		if ($checkDNS && function_exists('checkdnsrr')) {
			if (!(checkdnsrr($domain, 'A') || checkdnsrr($domain, 'MX'))) {
				return false;	// Domain doesn't actually exist
			}
		}
	}

	// Eliminate all other factors, and the one which remains must be the truth.
	// (Sherlock Holmes, The Sign of Four)
	return true;
}


/**
 * A convenience method to output the secure param tag that is used on all admin links
 *
 * @internal
 * @access private
 * @return string
 */
function get_secure_param()
{
  $urlext = '?';
  $str = strtolower(ini_get('session.use_cookies'));
  if( $str == '0' || $str == 'off' ) {
    $urlext .= htmlspecialchars(SID).'&';
  }
  $urlext .= CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
  return $urlext;
}
# vim:ts=4 sw=4 noet
?>
