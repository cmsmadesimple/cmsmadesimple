<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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
#
#$Id$

class CmsConfig extends CmsObject implements ArrayAccess
{
	var $params;
	static private $instance = NULL;

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
		$config["root_url"] = "http://www.something.com";
		$config["root_path"] = dirname(dirname(__FILE__));
		$config["query_var"] = "page";
		$config["use_bb_code"] = false;
		$config["smarty_security"] = false;
		$config["previews_path"] = $config["root_path"] . "/tmp/cache";
		$config["uploads_path"] = $config["root_path"] . "/uploads";
		$config["uploads_url"] = $config["root_url"] . "/uploads";
		$config["max_upload_size"] = 1000000;
		$config["debug"] = false;
		$config["assume_mod_rewrite"] = false;
		$config['internal_pretty_urls'] = false;
		$config['use_hierarchy'] = false;
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
		$config["use_adodb_lite"] = true;
		$config["locale"] = "";
		$config['old_stylesheet'] = true;
		$config['wiki_url'] = "http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel";
		$config['backwards_compatible'] = true;
		$config['timezone'] = 'Etc/GMT+0';

		if ($loadLocal == true)
		{
			//if (file_exists(CONFIG_FILE_LOCATION) && !cms_config_check_old_config())
			if (file_exists(CONFIG_FILE_LOCATION))
			{
				include(CONFIG_FILE_LOCATION);
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
		
		$this->params = $config;
	}
	
	function save()
	{
		$newfiledir = dirname(CONFIG_FILE_LOCATION);
		$newfilename = CONFIG_FILE_LOCATION;
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
#e.g. http://blah.com
\$config['root_url'] = '{$config['root_url']}';

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
\$config['uploads_url'] = '{$config['uploads_url']}';

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

#Should smarty be tightened up a bit?  This is only really necessary if you don't trust
#your users 100%.
\$config['smarty_security'] = ${$config['smarty_security']?'true':'false'};

#CMSMS Debug Mode?  Turn is on to get a better error when you
#see {nocache} errors.
\$config['debug'] = ${$config['debug']?'true':'false'};

#Automatically assign alias based on page title?
\$config['auto_alias_content'] = ${$config['auto_alias_content']?'true':'false'};

#------------
#URL Settings
#------------

#Show mod_rewrite URLs in the menu? You must enable 'use_hierarchy' for this to work for modules
\$config['assume_mod_rewrite'] = ${$config['assume_mod_rewrite']?'true':'false'};

#Extension to use if you're using mod_rewrite for pretty URLs.
\$config['page_extension'] = '{$config['page_extension']}';

#If you don't use mod_rewrite, then would you like to use the built-in
#pretty url mechanism?  This will not work with IIS and the {metadata} tag
#should be in all of your templates before enabling.
\$config['internal_pretty_urls'] = ${$config['internal_pretty_urls']?'true':'false'};

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
\$config['image_uploads_url'] = '{$config['image_uploads_url']}'; 

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


#Timezone for this site.
#Check http://php.net/manual/en/timezones.php for available timezones
\$config['timezone'] = 'Etc/GMT+0';


#---------------------------------------------
#Use the old stylesheet logic?  It's much slower, but it works with older
#versions of CMSMS.  You'll also need this set to true if there is a module
#that uses a stylesheet callback.  Leave it as false instead you really
#need it.
\$config['old_stylesheet'] = ${$config['old_stylesheet']?'true':'false'};

# URL of the Admin Panel section of the User Handbook
\$config['wiki_url'] = '{$config['wiki_url']}';

#Enable backwards compatibility mode?  This basically will allow some 
#modules written before 1.0 was released to work.  Keep in mind that this 
#will use a lot more memory and isn't guaranteed to fix the problem.
\$config['backwards_compatible'] = ${$config['backwards_compatible']?'true':'false'};

#Not used anymore... kept around, just in case
\$config['disable_htmlarea_translation'] = false;
\$config['use_Indite'] = true;
EOF;
	return $result;
	}
	
}

?>