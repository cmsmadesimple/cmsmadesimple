<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.cms_config.php 5313 2008-12-04 20:04:11Z calguy1000 $

/**
 * Class to hold the configuration values found in the config.php file.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsConfig extends CmsObject implements ArrayAccess
{
	var $params;
	static private $instance = NULL;
	static private $config_file_location = '';

	function __construct()
	{
		parent::__construct();
		$this->params = array();
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsConfig();
			self::$instance->load();
		}
		return self::$instance;
	}
	
	static public function get_config_filename()
	{
		if (self::$config_file_location == '')
		{
			$filename = dirname(CONFIG_FILE_LOCATION) . DS . 'config-' . CMS_VERSION . '.php';
			if (file_exists($filename))
			{
				self::$config_file_location = $filename;
			}
			else
			{
				self::$config_file_location = CONFIG_FILE_LOCATION;
			}
		}
		return self::$config_file_location;
	}
	
	static public function exists($key)
	{
		$config = CmsConfig::get_instance();
		return array_key_exists($key, $config->params);
	}
	
	static public function get($key)
	{
		$config = CmsConfig::get_instance();
		return $config[$key];
	}
	
	function offsetSet($key, $value)
	{
		$this->params[$key] = $value;
	}

	function offsetGet($key)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
	}

	function offsetUnset($key)
	{
		if (array_key_exists($key, $this->params))
		{
			unset($this->params[$key]);
		}
	}

	function offsetExists($offset)
	{
		return array_key_exists($offset, $this->params);
	}
	
	function load($loadLocal = true, $upgrade = false)
	{
		$config = array();

		#Set some defaults, just in case the config file is corrupted or
		#we're coming from an upgrade
		$config["dbms"] = "mysql";
		$config["db_hostname"] = "localhost";
		$config["db_username"] = "cms";
		$config["db_password"] = "cms";
		$config["db_name"] = "cms";
		$config["db_prefix"] = "cms_";
		$config["root_url"] = CmsRequest::get_calculated_url_base();
		$config["root_path"] = dirname(dirname(dirname(__FILE__)));
		$config["query_var"] = "page";
		$config["use_bb_code"] = false;
		$config["smarty_security"] = false;
		$config["previews_path"] = $config["root_path"] . "/tmp/cache";
		$config["uploads_path"] = $config["root_path"] . "/uploads";
		$config["uploads_url"] = $config["root_url"] . "/uploads";
		$config["max_upload_size"] = 1000000;
		$config["debug"] = false;
		$config["assume_mod_rewrite"] = false;
		$config['internal_pretty_urls'] = true;
		$config["auto_alias_content"] = true;
		$config["image_manipulation_prog"] = "GD";
		$config["image_transform_lib_path"] = "/usr/bin/ImageMagick/";
		$config["use_Indite"] = true;
		$config["image_uploads_path"] = $config["root_path"] . "/uploads/images";
		$config["image_uploads_url"] = $config["root_url"] . "/uploads/images";
		$config["default_encoding"] = "";
		$config["disable_htmlarea_translation"] = false;
		$config["admin_dir"] = "admin";
		$config["persistent_db_conn"] = false;
		$config["default_upload_permission"] = '664';
		$config["page_extension"] = "";
		$config["locale"] = "";
		$config['old_stylesheet'] = true;
		$config['wiki_url'] = "http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel";
		$config['backwards_compatible'] = true;
		$config['timezone'] = 'Etc/GMT+0';
		$config['function_caching'] = true;
		$config['full_page_caching'] = false;
		$config['use_memcache'] = false;
		$config['memcache_server'] = 'localhost';
		$config['memcache_port'] = '11211';

		if ($loadLocal == true)
		{
			//if (file_exists(CONFIG_FILE_LOCATION) && !cms_config_check_old_config())
			if (file_exists(self::get_config_filename()))
			{
				include(self::get_config_filename());
			}
		}

		#Check to see if this exists in the config.php yet
		if (!isset($config["admin_encoding"]))
		{
			#So this is our first setting of it.  Is default_encoding set already?
			#If so, set admin_encoding to match (and admin encodings should behave as before)
			if (isset($config["default_encoding"]) && $config["default_encoding"])
			{
				$config['admin_encoding'] = $config['default_encoding'];
			}
			else
			{
				#Ok, so last ditch effort.  Look for the encoding of the default template
				if ($upgrade == true)
				{
					$db = cms_db();
					$encoding = $db->GetOne('select encoding from '.cms_db_prefix().'templates where default_template = 1');
					if (isset($encoding) && $encoding != '')
					{
						$config["admin_encoding"] = $encoding;
					}
					else
					{
						$config["admin_encoding"] = "utf-8";
					}
				}
				else
				{
					$config["admin_encoding"] = "utf-8";
				}
			}
		}
		
		// Begin Multisite Modifications
		$subsite_config = $config['root_path'].DIRECTORY_SEPARATOR.'sites'.DIRECTORY_SEPARATOR.$_SERVER['HTTP_HOST'].DIRECTORY_SEPARATOR.'config.php';
		if(file_exists($subsite_config))
		{
			include $subsite_config;
			$config['is_subsite'] = true;
		}
		// End Multisite Modifications		

		$this->params = $config;

	}
	
	function save()
	{
		$newfiledir = dirname(self::get_config_filename());
		$newfilename = self::get_config_filename();
		if (is_writable($newfilename) || is_writable($newfiledir))
		{
			$handle = fopen($newfilename, "w");
			if ($handle)
			{
				fwrite($handle, "<?php\n\n");
				fwrite($handle, $this->config_text($this->params));
				fwrite($handle, "\n?>");
				fclose($handle);
			}
		}
	}
	
	function config_text($config)
	{
		$true = 'true';
		$false = 'false';
		$result = <<<EOF

#CMS Made Simple Configuration File
#Please clear the cache (Site Admin->Global Settings in the admin panel)
#after making any changes to path or url related options

#-----------------
#Behaviour Settings
#-----------------

# These settings will effect the overall behaviour of the CMS application, please
# use extreme caution when editing these.  Additionally, some settings may have 
# no effect on servers with significantly restricted configurability.

# If you are experiencing propblems with php memory limit errors, then you may
# want to try enabling and/or adjusting this setting.  
# Note: Your server may not allow the application to override memory limits.
\$config['php_memory_limit'] = '{$config['php_memory_limit']}';

# In versions of CMS Made Simple prior to version 1.4, the page template was processed
# in it's entirety.  This behaviour was later changed to process the head portion of the
# page template after the body.  If you are working with a highly configured site that
# relies significantly on the old order of smarty processing, you may want to try
# setting this parameter to false.
\$config['process_whole_template'] = ${$config['process_whole_template']?'true':'false'};

# CMSMS Debug Mode?  Turn it on to get a better error when you
# see {nocache} errors, or to allow seeing php notices, warnings, and errors in the html output.
# This setting will also disable browser css caching.
\$config['debug'] = ${$config['debug']?'true':'false'};

# Output compression?
# Turn this on to allow CMS to do output compression
# this is not needed for apache servers that have mod_deflate enabled
# and possibly other servers.  But may provide significant performance
# increases on some sites.  Use caution when using this as there have
# been reports of incompatibilities with some browsers.
\$config['output_compression'] = ${$config['output_compression']?'true':'false'};

#-----------------
#Database Settings
#-----------------

#This is your database connection information.  Name of the server,
#username, password and a database with proper permissions should
#all be setup before CMS Made Simple is installed.
\$config['dbms'] = '{$config['dbms']}';
\$config['db_hostname'] = '{$config['db_hostname']}';
\$config['db_username'] = '{$config['db_username']}';
\$config['db_password'] = '{$config['db_password']}';
\$config['db_name'] = '{$config['db_name']}';
#Change this param only if you know what you are doing
\$config["db_port"] = '{$config['db_port']}';


#If app needs to coexist with other tables in the same db,
#put a prefix here.  e.g. "cms_"
\$config['db_prefix'] = '{$config['db_prefix']}';

#Use persistent connections?  They're generally faster, but not all hosts
#allow them.
\$config['persistent_db_conn'] = ${$config['persistent_db_conn']?'true':'false'};

#Use ADODB Lite?  This should be true in almost all cases.  Note, slight
#tweaks might have to be made to date handling in a "regular" adodb
#install before it can be used.
\$config['use_adodb_lite'] = ${$config['use_adodb_lite']?'true':'false'};

#-------------
#Path Settings
#-------------

#Document root as seen from the webserver.  No slash at the end
#If page is requested with https use https as root url
#e.g. http://blah.com
\$config['root_url'] = '{$config['root_url']}';
if(isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS']=='on')
{
  \$config['root_url'] = str_replace('http','https',\$config['root_url']);
}

#Path to document root. This should be the directory this file is in.
#e.g. /var/www/localhost
\$config['root_path'] = '{$config['root_path']}';

#Name of the admin directory
\$config['admin_dir'] = '{$config['admin_dir']}';

#Where do previews get stored temporarily?  It defaults to tmp/cache.
\$config['previews_path'] = '{$config['previews_path']}';

#Where are uploaded files put?  This defaults to uploads.
\$config['uploads_path'] = '{$config['uploads_path']}';

#Where is the url to this uploads directory?
\$config['uploads_url'] = \$config['root_url'] . '{$config['uploads_url']}';

#---------------
#Upload Settings
#---------------

#Maxium upload size (in bytes)?
\$config['max_upload_size'] = {$config['max_upload_size']};

#Permissions for uploaded files.  This only really needs changing if your
#host has a weird permissions scheme.
\$config['default_upload_permission'] = '{$config['default_upload_permission']}';

#------------------
#Usability Settings
#------------------

#Allow smarty {php} tags?  These could be dangerous if you don't trust your users.
\$config['use_smarty_php_tags'] = ${$config['use_smarty_php_tags']?'true':'false'};

#Automatically assign alias based on page title?
\$config['auto_alias_content'] = ${$config['auto_alias_content']?'true':'false'};

#------------
#URL Settings
#------------

#What type of URL rewriting should we be using for pretty URLs?  Valid options are: 
#'none', 'internal', and 'mod_rewrite'.  'internal' will not work with IIS some CGI
#configurations. 'mod_rewrite' requires proper apache configuration, a valid 
#.htaccess file and most likely {metadata} in your page templates.  For more 
#information, see:
#http://wiki.cmsmadesimple.org/index.php/FAQ/Installation/Pretty_URLs#Pretty_URL.27s
\$config['url_rewriting'] = '{$config['url_rewriting']}';

#Extension to use if you're using mod_rewrite for pretty URLs.
\$config['page_extension'] = '{$config['page_extension']}';

#If you're using the internal pretty url mechanism or mod_rewrite, would you like to
#show urls in their hierarchy?  (ex. http://www.mysite.com/parent/parent/childpage)
\$config['use_hierarchy'] = ${$config['use_hierarchy']?'true':'false'};

#If using none of the above options, what should we be using for the query string
#variable?  (ex. http://www.mysite.com/index.php?page=somecontent)
\$config['query_var'] = '{$config['query_var']}';

#--------------
#Image Settings
#--------------

#Which program should be used for handling thumbnails in the image manager.
#See http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Content/Image_Manager for more
#info on what this all means
\$config['image_manipulation_prog'] = '{$config['image_manipulation_prog']}';
\$config['image_transform_lib_path'] = '{$config['image_transform_lib_path']}';

#Default path and URL for uploaded images in the image manager
\$config['image_uploads_path'] = '{$config['image_uploads_path']}';
\$config['image_uploads_url'] = \$config['root_url'] . '{$config['image_uploads_url']}'; 

#------------------------
#Locale/Encoding Settings
#------------------------

#Locale to use for various default date handling functions, etc.  Leaving
#this blank will use the server's default.  This might not be good if the
#site is hosted in a different country than it's intended audience.
\$config['locale'] = '{$config['locale']}';

#In almost all cases, default_encoding should be empty (which defaults to utf-8)
#and admin_encoding should be utf-8.  If you'd like this to be different, change
#both.  Keep in mind, however, that the admin interface translations are all in
#utf-8, and will be converted on the fly to match the admin_encoding.  This
#could seriously slow down the admin interfaces for users.
\$config['default_encoding'] = '{$config['default_encoding']}';
\$config['admin_encoding'] = '{$config['admin_encoding']}';

#This is a mysql specific option that is generally defaulted to true.  Only 
#disable this for backwards compatibility or the use of non utf-8 databases.
\$config['set_names'] = ${$config['set_names']?'true':'false'};

# URL of the Admin Panel section of the User Handbook
# Set none if you want hide the link from Error 
\$config['wiki_url'] = '{$config['wiki_url']}';

#-----------------
#Memcache Settings
#-----------------

#This section allows areas of caching to be handled by Memcached instead
#of files or the database. This generally speeds up cache reads and writes
#greatly, but requires special configuration, the memcached daemon to be
#available and the memache module to be installed in your PHP. We recommend
#this for production environments where you have control over the items that
#are compiled and installed into your PHP. See http://memcached.org/ and
#http://us3.php.net/manual/en/book.memcache.php for more details.
#
\$config['use_memcache'] = ${$config['use_memcache']?'true':'false'};
\$config['memcache_server'] = '{$config['memcache_server']}';
\$config['memcache_port'] = '{$config['memcache_port']}';

EOF;
		return $result;
	}
	
}

?>