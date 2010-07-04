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
 * Translation functions/classes
 *
 * @package CMS
 */

/**
 * A function to return the current admin language
 *
 * @internal
 * @return string
 */
function cms_admin_current_language()
{
  global $gCms;
  global $nls;
  $nls = array();
  $lang = array();
  $current_language = '';

  #Read in all current languages...
  if( !is_array($nls) )
    {
      $dir = cms_join_path($gCms->config['root_path'],$gCms->config['admin_dir'],'/lang');
      
      $handle = opendir($dir);
      while (false!==($file = readdir($handle))) {
	if (is_file("$dir/$file") && strpos($file, "nls.php") != 0) {
	  include("$dir/$file");
	}
      }
      closedir($handle);
    }

  #Check to see if there is already a language in use...
  if (isset($_POST["default_cms_lang"]) )
    {
      $current_language = $_POST["default_cms_lang"];
      if ($current_language == '')
	{
	  setcookie("cms_language", '', time() - 3600);
	}
      else if (isset($_POST["change_cms_lang"]))
	{
	  setcookie("cms_language", $_POST["change_cms_lang"]);
	}
    }
  else if (isset($_SESSION['login_cms_language']))
    {
      debug_buffer('Setting language to: ' . $_SESSION['login_cms_language']);
      $current_language = $_SESSION['login_cms_language'];
      setcookie('cms_language', $_SESSION['login_cms_language']);
      unset($_SESSION['login_cms_language']);
    }
  else if (isset($_COOKIE["cms_language"]))
    {
      $tmp = trim(basename($_COOKIE["cms_language"]));
      $file = dirname(__FILE__) . "/lang/" . $tmp . "/admin.inc.php";
      if( !file_exists($file) )
	{
	  $file = dirname(__FILE__) . "/lang/ext/" . $tmp . "/admin.inc.php";
	  if( !file_exists($file) )
	    {
	      $$tmp = '';
	    }
	}
      $current_language = $tmp;
    }

  if ($current_language == '')
    {
      if (isset($gCms->config['locale']) && $gCms->config['locale'] != '')
	{
	  $current_language = $gCms->config['locale'];
	}
      else
	{
		
#No, take a stab at figuring out the default language...
#Figure out default language and set it if it exists
	  if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) 
	    {
	      $alllang = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
	      if (strpos($alllang, ";") !== FALSE)
		$alllang = substr($alllang,0,strpos($alllang, ";"));
	      $langs = explode(",", $alllang);

	      foreach ($langs as $onelang)
		{
#Check to see if lang exists...
		  if (isset($nls['language'][$onelang]))
		    {
		      $current_language = $onelang;
		      setcookie("cms_language", $onelang);
		      break;
		    }
#Check to see if alias exists...
		  if (isset($nls['alias'][$onelang]))
		    {
		      $alias = $nls['alias'][$onelang];
		      if (isset($nls['language'][$alias]))
			{
			  $current_language = $alias;
			  setcookie("cms_language", $alias);
			  break;
			}
		    }
		}
	    }
	}
    }

  return $current_language;
}


/**
 * A function to return the current frontend language
 *
 * @internal
 * @return string
 */
function cms_frontend_current_language()
{
  global $gCms;
  $curlang = get_site_preference('frontendlang','');
  if (isset($gCms->config['locale']) && $gCms->config['locale'] != '') {
    $curlang = $this->config['locale'];
  }
  if( $curlang == '' ) {
    $curlang = 'en_US';
  }

  return $curlang;
}


/**
 * A function to return the current language for the current request
 *
 * @internal
 * @return string
 */
function cms_current_language()
{
  global $CMS_ADMIN_PAGE;
  global $CMS_STYLESHEET;
  global $CMS_INSTALL_PAGE;

  if (isset($CMS_ADMIN_PAGE) || isset($CMS_STYLESHEET) || isset($CMS_INSTALL_PAGE))
    {
      return cms_admin_current_language();
    }
  return cms_frontend_current_language();
}


/**
 * Load a lang file for a specific realm.
 *
 * @since 1.8
 * @internal
 * @param string The realm
 * @param string An optional base directory
 * @param boolean Indicates that the language is indicated by the directory name
 * @param boolean Indicates that the lang file has a sub array for the realm.
 * @return void
 */
function cms_load_lang_realm($realm,$basedir = '',$filename = '',$lang_is_dir = 0,$has_realm = 0)
{
	global $gCms;
	global $lang;
	if( empty($realm) ) return fALSE;

	if( empty($basedir) )
	{
		$basedir = cms_join_path($gCms->config['root_path'],'lib','lang',$realm);
	}
	if( empty($filename) )
	{
		$filename = 'en_US.php';
	}

	$cur_lang = cms_current_language();

	// load the default (en_US) file first.
	$fn = cms_join_path($basedir,$filename);
	if( $lang_is_dir )
	{
		$fn = cms_join_path($basedir,'en_US',$filename);
	}
	if (!$lang[$realm])
	{
		if( @file_exists($fn) )
		{
			if( !$has_realm )
			{
				// the lang file doesn't create a subarray
				$hold_lang = $lang;
				$lang = array();
				include($fn);
				$hold_lang[$realm] = $lang;
				$lang = $hold_lang;
				unset($hold_lang);
			}
			else
			{
				// the lang file defines the sub-array itself.
				include($fn);
			}
			if( !isset($lang[$realm]) ) return FALSE;
		}

		// now load the lang file itself.
		if( $cur_lang != 'en_US' )
		{
			$fn = cms_join_path($basedir,'ext',$cur_lang.'.php');
			if( $lang_is_dir )
			{
				$fn = cms_join_path($basedir,'ext',$cur_lang,$filename);
			}
			if( @file_exists($fn) )
			{
				if( !$has_realm )
				{
					// the lang file doesn't create a subarray
					$hold_lang = $lang;
					$lang = array();
					include($fn);
					$hold_lang[$realm] = $lang;
					$lang = $hold_lang;
					unset($hold_lang);
				}
				else
				{
					// the lang file defines the sub-array itself.
					include($fn);
				}
				if( !isset($lang[$realm]) ) return FALSE;
			}
		}
	
	}

	return TRUE;
}


/**
 * A method to return a translation for a specific string in a specific realm.
 * called with the realm first, followed by the key, this method will attempt
 * to load the specific realm data if necessary before doing translation.
 *
 * This method accepts a variable number of arguments.  Any arguments after
 * the realm and the key are passed to the key via vsprintf
 *
 * i.e: lang_by_realm('tasks','my_string');
 *
 * @since 1.8
 * @param string The realm
 * @param string The lang key
 * @return string
 */
function lang_by_realm()
{
  global $gCms;
  global $lang;

  $name = '';
  $realm = 'admin';
  $params = array();
  $result = '';

  $num = func_num_args();
  if( $num == 0 ) return '';

  $name = func_get_arg(0);
  if( $num > 1 )
    {
      $realm = func_get_arg(1);
      
      if( $num > 2 && is_array(func_get_arg(2)) )
	{
	  $params = func_get_arg(2);
	}
      else if( $num > 2 )
	{
	  $params = array_slice(func_get_args(), 2);
	}
    }

  // we now have a name, a realm, and possible additonal arguments.
  if( !isset($lang[$realm]) )
    {
      cms_load_lang_realm($realm);
    }

  // do processing.
  if (isset($lang[$realm][$name]))
    {
      if (count($params))
	{
	  $result = vsprintf($lang[$realm][$name], $params);
	}
      else
	{
	  $result = $lang[$realm][$name];
	}
    }
  else
    {
      $result = "--Add Me - $name --";
    }

  // conversion.
  if (isset($gCms->config['admin_encoding']) && isset($gCms->variables['convertclass']))
    {
      if (strcasecmp(get_encoding('', false),$gCms->config['admin_encoding']) )
	{
	  $class =& $gCms->variables['convertclass'];
	  $result = $class->Convert($result, get_encoding('', false), $gCms->config['admin_encoding']);
	}
    }

  return $result;

}


/**
 * Return a translated string for the default 'admin' realm.
 * This function is merely a wrapper around the lang_by_realm function
 * that assumes the realm is 'admin'.
 *
 * This method will throw a notice if it is called from a frontend request
 *
 * @param string The key to translate
 * @return string
 */
function lang()
{
  // uses the default admin realm.
	global $gCms;
	global $lang;
	global $nls;

	$dir = cms_join_path($gCms->config['root_path'],$gCms->config['admin_dir'],'lang');

	if( !isset($lang['admin']) )
	  {
	    cms_load_lang_realm('admin',$dir,'admin.inc.php',1,1);
	  }
	$name = '';
	$params = array();
	$realm = 'admin';

	if (func_num_args() > 0)
	{
		$name = func_get_arg(0);
		if (func_num_args() == 2 && is_array(func_get_arg(1)))
		{
			$params = func_get_arg(1);
		}
		else if (func_num_args() > 1)
		{
			$params = array_slice(func_get_args(), 1);
		}
	}
	else
	{
		return '';
	}

	// quick test to see if we are on the frontend or admin.
	global $CMS_ADMIN_PAGE;
	global $CMS_STYLESHEET;
	global $CMS_INSTALL_PAGE;

	if (!isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE))
	  {
  stack_trace(); die();
	    trigger_error('Attempt to load admin realm from non admin action');
	    return '';
	  }

	// todo:
	return lang_by_realm($name,'admin',$params);
}

function get_encoding($charset='', $defaultoverrides=true)
{
	global $nls;
	global $current_language;
	global $gCms;
	$variables =& $gCms->variables;
	$config =& $gCms->GetConfig();

	if ($charset != '')
	{
		return $charset;
	}
        else if (isset($variables['current_encoding']) && $variables['current_encoding'] != "" )
        {
	  return $variables['current_encoding'];
        }
	else if (isset($config['default_encoding']) && $config['default_encoding'] != "" && $defaultoverrides == true)
	{
		return $config['default_encoding'];
	}
	else if (isset($nls['encoding'][$current_language]))
	{
		return $nls['encoding'][$current_language];
	}
	else
	{
		return "UTF-8"; //can't hurt
	}
}


/**
 * Set the current encoding
 *
 * @internal
 * @access private
 */
function set_encoding($charset)
{
  global $gCms;
  $variables =& $gCms->variables;
  
  if( $charset == '' ) 
    {
      if( isset($variables['current_encoding']) )
	unset($variables['current_encoding']);
      return;
    }
  $variables['current_encoding'] =  $charset;
}

/**
 * Test wether a string is valid UTF-8
 *
 * @internal
 * @param string The string to test
 * @return boolean
 */
function is_utf8($string)
{
   // From http://w3.org/International/questions/qa-forms-utf-8.html
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);
   
} // function is_utf8

# vim:ts=4 sw=4 noet
?>
