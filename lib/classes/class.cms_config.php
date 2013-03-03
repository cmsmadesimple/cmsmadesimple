<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
 * @package CMS
 */

/**
 * A singleton class for interacting with the CMSMS config.php file
 *
 * @since 1.9
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */
class cms_config implements ArrayAccess
{
  const TYPE_STRING = 'STRING';
  const TYPE_INT = 'INT';
  const TYPE_BOOL = 'BOOL';
  
  private static $_instance;
  private $_types;
  private $_data = array();
  private $_cache = array();
  private $_friends = array('CMSInstallerPage7','CMSUpgradePage3');

  // this is a singleton.
  private function __construct()  {}

  private function get_upload_size()
  {
    $maxFileSize = ini_get('upload_max_filesize');
    if (!is_numeric($maxFileSize))
      {
		  $l=strlen($maxFileSize);
		  $i=0;$ss='';$x=0;
		  while ($i < $l)
			  {
				  if (is_numeric($maxFileSize[$i]))
					  {$ss .= $maxFileSize[$i];}
				  else
					  {
						  if (strtolower($maxFileSize[$i]) == 'm') $x=1000000;
						  if (strtolower($maxFileSize[$i]) == 'k') $x=1000;
					  }
				  $i ++;
			  }
		  $maxFileSize=$ss;
		  if ($x >0) $maxFileSize = $ss * $x;
      }
    else
      {
		  $maxFileSize = 1000000;
      }
    return $maxFileSize;
  }

  private function load_config()
  {
    $this->_types = array();
    $this->_types['dbms'] = self::TYPE_STRING;
    $this->_types['db_hostname'] = self::TYPE_STRING;
    $this->_types['db_username'] = self::TYPE_STRING;
    $this->_types['db_password'] = self::TYPE_STRING;
    $this->_types['db_name'] = self::TYPE_STRING;
    $this->_types['db_port'] = self::TYPE_INT;
    $this->_types['db_prefix'] = self::TYPE_STRING;
    $this->_types['root_url'] = self::TYPE_STRING;
    $this->_types['ssl_url'] = self::TYPE_STRING;
    $this->_types['root_path'] = self::TYPE_STRING;
    $this->_types['admin_dir'] = self::TYPE_STRING;
    $this->_types['uploads_path'] = self::TYPE_STRING;
    $this->_types['uploads_url'] = self::TYPE_STRING;
    $this->_types['ssl_uploads_url'] = self::TYPE_STRING;
    $this->_types['image_uploads_path'] = self::TYPE_STRING;
    $this->_types['image_uploads_url'] = self::TYPE_STRING;
    $this->_types['ssl_image_uploads_url'] = self::TYPE_STRING;
    $this->_types['debug'] = self::TYPE_BOOL;
    $this->_types['timezone'] = self::TYPE_STRING;
    $this->_types['persist_db_conn'] = self::TYPE_BOOL;
    $this->_types['previews_path'] = self::TYPE_STRING;
    $this->_types['max_upload_size'] = self::TYPE_INT;
    $this->_types['default_upload_permission'] = self::TYPE_STRING;
    $this->_types['auto_alias_content'] = self::TYPE_BOOL;
    $this->_types['url_rewriting'] = self::TYPE_STRING;
    $this->_types['page_extension'] = self::TYPE_STRING;
    $this->_types['query_var'] = self::TYPE_STRING;
    $this->_types['image_manipulation_prog'] = self::TYPE_STRING;
    $this->_types['image_transform_lib_path'] = self::TYPE_STRING;
    $this->_types['locale'] = self::TYPE_STRING;
    $this->_types['default_encoding'] = self::TYPE_STRING;
    $this->_types['admin_encoding'] = self::TYPE_STRING;
    $this->_types['set_names'] = self::TYPE_BOOL;
    $this->_types['admin_url'] = self::TYPE_STRING;
    $this->_types['ignore_lazy_load'] = self::TYPE_BOOL;

    $config = array();
    if (file_exists(CONFIG_FILE_LOCATION))
      {
		  include(CONFIG_FILE_LOCATION);
		  unset($config['max_upload_size']);
		  unset($config['upload_max_filesize']);
      }

    $this->_data = $config;
  }


  /**
   * @ignore
   * @internal
   * @access private
   */
  public function merge($newconfig)
  {
    if( !is_array($newconfig) ) return;
    $trace = debug_backtrace(FALSE);
    $class = '';
    if( isset($trace[1]['class']) ) $class = $trace[1]['class'];
    if( $class && in_array($class,$this->_friends) )
      {
		  $this->_data = array_merge($this->_data,$newconfig);
      }
  }


  /**
   * Retrieve the global instance of the cms_config class
   * This method will instantiate the object if necessary
   *
   * @return object
   */
  public static function &get_instance()
  {
    if (!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;

      // now load the config
      self::$_instance->load_config();
    }

    return self::$_instance;
  }


  public function offsetExists($key)
  {
    return isset($this->_types[$key]) || isset($this->_data[$key]);
  }


  public function offsetGet($key)
  {
	  // hardcoded config vars
	  // usually old values valid in past versions.
	  switch( $key )
		  {
		  case 'use_adodb_lite':
		  case 'use_hierarchy':
			  // deprecated, backwards compat only
			  return TRUE;

		  case 'use_smarty_php_tags':
		  case 'output_compression':
			  // deprecated, backwards compat only
			  return FALSE;

		  case 'default_upload_permission':
			  // deprecated, backwards compat only
			  return '664';

		  case 'assume_mod_rewrite':
			  // deprecated, backwards compat only
			  return ($this['url_rewriting'] == 'mod_rewrite')?true:false;

		  case 'internal_pretty_urls':
			  // deprecated, backwards compat only
			  return ($this['url_rewriting'] == 'internal')?true:false;
		  }

	  // from the config file.
	  if( isset($this->_data[$key]) )
	  {
		  return $this->_data[$key];
	  }

	  // cached, calculated values.
	  if( isset($this->_cache[$key]) )
	  {
		  // this saves recursion and dynamic calculations all the time.
		  return $this->_cache[$key];
	  }

	  // it's not explicitly specified in the config file.
	  //$calculated_root_path = dirname(dirname(dirname(__FILE__)));
	  switch( $key )
	  {
	  case 'dbms':
	  case 'db_hostname':
	  case 'db_username':
	  case 'db_password':
	  case 'db_name':
		  // these guys have to be set
		  stack_trace();
		  die('FATAL ERROR: Could not find database connection key '.$key.' in the config file');
		  break;

	  case 'persist_db_conn':
		  return false;

	  case 'set_names':
		  return true;

	  case 'root_path':
		  $out = dirname(dirname(dirname(__FILE__)));
                  $this->_cache[$key] = $out;
		  return $out;

	  case 'root_url':
		  $parts = parse_url($_SERVER['PHP_SELF']);
		  $path = '';
		  if( !empty($parts['path']) ) {
			  $path = dirname($parts['path']);
			  if( endswith($path,'install') ) {
				  $path = substr($path,0,strlen($path)-strlen('install')-1);
			  }
			  else if( endswith($path,$this->offsetGet('admin_dir')) ) {
				  $path = substr($path,0,strlen($path)-strlen($this->offsetGet('admin_dir'))-1);
			  }
			  else if (strstr($path,'/lib') !== FALSE) {
				  while( strstr($path,'/lib') !== FALSE ) {
					  $path = dirname($path);
				  }
			  }
			  while(endswith($path, DIRECTORY_SEPARATOR)) {
				  $path = substr($path,0,strlen($path)-1);
			  }
			  if( ($pos = strpos($path,'/index.php')) !== FALSE ) {
				  $path = substr($path,0,$pos);
			  }
		  }
		  $str = 'http://'.$_SERVER['HTTP_HOST'].$path;
		  $this->_cache[$key] = $str;
		  return $str;
		  break;
		  
	  case 'ssl_url':
		  $this->_cache[$key] = str_replace('http://','https://',$this->offsetGet('root_url'));
		  return $this->_cache[$key];
		  
	  case 'uploads_path':
		  $this->_cache[$key] = cms_join_path($this->offsetGet('root_path'),'uploads');
		  return $this->_cache[$key];

	  case 'uploads_url':
		  $this->_cache[$key] = $this->offsetGet('root_url').'/uploads';
		  return $this->_cache[$key];
		  
	  case 'ssl_uploads_url':
		  $this->_cache[$key] = str_replace('http://','https://',$this->offsetGet('uploads_url'));
		  return $this->_cache[$key];
		  
	  case 'image_uploads_path':
		  $this->_cache[$key] = cms_join_path($this->offsetGet('uploads_path'),'images');
		  return $this->_cache[$key];

	  case 'image_uploads_url':
		  $this->_cache[$key] = $this->offsetGet('uploads_url').'/images';
		  return $this->_cache[$key];

	  case 'ssl_image_uploads_url':
		  $this->_cache[$key] = str_replace('http://','https://',$this->offsetGet('image_uploads_url'));
		  return $this->_cache[$key];
		  
	  case 'previews_path':
		  return TMP_CACHE_LOCATION;

	  case 'admin_dir':
		  return 'admin';

	  case 'debug':
		  return false;

	  case 'timezone':
		  return '';

	  case 'db_port':
		  return '';

	  case 'max_upload_size':
	  case 'upload_max_filesize':
		  return $this->get_upload_size();

	  case 'auto_alias_content':
		  return true;

	  case 'url_rewriting':
		  return 'none';

	  case 'page_extension':
		  return '';

	  case 'query_var':
		  return 'page';

	  case 'image_manipulation_prog':
		  return 'GD';

	  case 'image_transform_lib_path':
		  return ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) ? 'C:/Program Files/VisualMagick/bin/' : '/usr/bin/ImageMagick/';

	  case 'locale':
		  return '';

	  case 'default_encoding':
	  case 'admin_encoding':
		  return 'utf-8';

	  case 'admin_path':
		  return cms_join_path($this->offsetGet('root_path'),$this->offsetGet('admin_dir'));

	  case 'admin_url':
		  return $this->offsetGet('root_url').'/'.$this->offsetGet('admin_dir');

	  case 'ignore_lazy_load':
		  return false;

	  case 'css_path':
		  return TMP_CACHE_LOCATION.'/';

	  case 'css_url':
		  return $this->offsetGet('root_url').'/tmp/cache/';

	  case 'ssl_css_url':
		  return $this->offsetGet('ssl_url').'/tmp/cache/';

	  default:
		  // not a mandatory key for the config.php file... and one we don't understand.
		  return null;
	  }
  }

  public function offsetSet($key,$value)
  {
    $tmp = debug_backtrace();
    $parent = '';
    if( isset($tmp[1]) && isset($tmp[1]['class']) )
      {
	$class = $tmp[1]['class'];
	$parent = get_parent_class($class);
      }
    if( $parent != 'CMSInstallerPage' )
      {
	trigger_error('Modification of config variables is deprecated',E_USER_ERROR);
	return;
      }
    $this->_data[$key] = $value;
  }


  public function offsetUnset($key)
  {
    trigger_error('Unsetting config variable '.$key.' is invalid',E_USER_ERROR);
  }


  private function _printable_value($key,$value)
  {
	  $type = self::TYPE_STRING;

	  if( isset($this->_types[$key]) )
		  {
			  $type = $this->_types[$key];
		  }

	  $str = '';
	  switch( $type )
		  {
		  case self::TYPE_STRING:
			  $str = "'".$value."'";
			  break;

		  case self::TYPE_BOOL:
			  $str = ($value)?'true':'false';
			  break;

		  case self::TYPE_INT:
			  $str = (int)$value;
			  break;
		  }
	  return $str;
  }


  /**
   * A function to save the current state of the config.php file.  Any existing file is backed up
   * before overwriting.
   *
   *
   * @param boolean indicates wether comments should be stored in the config.php file. 
   * @param string  An optional complete file specification.  If not specified the standard config file location will be used.
   */
  public function save($verbose = true,$filename = '')
  {
    if( !$filename )
      {
		  $filename = CONFIG_FILE_LOCATION;
      }

    // backup the original config.php file (just in case)
    if( file_exists($filename) )
      {
		  @copy($filename,cms_join_path(TMP_CACHE_LOCATION,basename($filename).time().'.bak'));
      }

    $output = "<?php\n# CMS Made Simple Configuration File\n# Documentation: /doc/CMSMS_config_reference.pdf\n#\n";
    // output header to the config file.

    foreach( $this->_data as $key => $value )
    {
      $outvalue = $this->_printable_value($key,$value);
      $output .= "\$config['{$key}'] = $outvalue;\n";
    }

    $output .= "?>\n";

    // and write it.
    $fh = fopen($filename,'w');
    if( $fh )
      {
		  fwrite($fh,$output);
		  fclose($fh);
      }
  }

  public function smart_root_url()
  {
	  if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) {
		  return $this->offsetGet('ssl_url');
	  }
	  return $this->offsetGet('root_url');
  }

  public function smart_uploads_url()
  {
	  if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) {
		  return $this->offsetGet('ssl_uploads_url');
	  }
	  return $this->offsetGet('uploads_url');
  }

  public function smart_image_uploads_url()
  {
	  if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) {
		  return $this->offsetGet('ssl_image_uploads_url');
	  }
	  return $this->offsetGet('image_uploads_url');
  }
} // end of class

?>
