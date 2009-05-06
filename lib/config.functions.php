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
 * Functions relating to the config hash and config files
 *
 * @package CMS
 */
/**
 * Loads the config file with defaults and then with specific values from config.php
 *
 * @since 0.5
 */
function cms_config_load($loadLocal = true, $upgrade = false)
{
	$config = array();

	#Set some defaults, just in case the config file is corrupted or
	#we're coming from an upgrade
	$config['php_memory_limit'] = '';
	$config['process_whole_template'] = false;
	$config["dbms"] = "mysql";
	$config["db_hostname"] = "localhost";
	$config["db_username"] = "cms";
	$config["db_password"] = "cms";
	$config["db_name"] = "cms";
	$config["db_port"] = '';
	$config["db_prefix"] = "cms_";
	$config["root_url"] = "http://www.something.com";
	$config["root_path"] = dirname(dirname(__FILE__));
	$config["query_var"] = "page";
	$config["use_bb_code"] = false;
	$config["use_smarty_php_tags"] = false;
	$config["previews_path"] = $config["root_path"] . "/tmp/cache";
	$config["uploads_path"] = $config["root_path"] . "/uploads";
	$config["uploads_url"] = '/uploads'; 
	$config["max_upload_size"] = 1000000;
	$config["debug"] = false;
	$config['output_compression'] = false;
	$config['url_rewriting'] = 'none';
	//$config["assume_mod_rewrite"] = false; //Not being used in core
	//$config['internal_pretty_urls'] = false; //Not being used in core
	$config['use_hierarchy'] = true; //Now true by default
	$config["auto_alias_content"] = true;
	$config["image_manipulation_prog"] = "GD";
	$config["image_transform_lib_path"] = "/usr/bin/ImageMagick/";
	$config["use_Indite"] = true;
	$config["image_uploads_path"] = $config["root_path"] . "/uploads/images";
	$config["image_uploads_url"] = '/uploads/images'; 
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
	$config['set_names'] = false; //Default to false for pre-1.6 compatibility.  New installs get true.

	#Don't set it yet
	#$config["admin_encoding"] = "utf-8";

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
				global $gCms;
				$db =& $gCms->GetDb();
				$encoding = $db->GetOne('SELECT encoding FROM '.cms_db_prefix().'templates WHERE default_template = 1');
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
	
	//Handle upgrade to url_rewriting parameter
	if ($upgrade)
	{
		//Do they exist?  We're probably going from pre 1.6 to 1.6
		if (isset($config['assume_mod_rewrite']) && isset($config['internal_pretty_urls']))
		{
			//If internal is on, then set it
			if ($config['internal_pretty_urls'] == true)
			{
				$config['url_rewriting'] = 'internal';
			}
			
			//mod_rewrite trumps internal, so reset it if necessary.
			if ($config['assume_mod_rewrite'] == true)
			{
				$config['url_rewriting'] = 'mod_rewrite';
			}
		}
	}
	
	//For backwards compat on the url_rewriting parameter
	switch ($config['url_rewriting'])
	{
		case 'mod_rewrite':
			$config['internal_pretty_urls'] = false;
			$config['assume_mod_rewrite'] = true;
			break;
		case 'internal':
			$config['internal_pretty_urls'] = true;
			$config['assume_mod_rewrite'] = false;
			break;
		default:
			$config['internal_pretty_urls'] = false;
			$config['assume_mod_rewrite'] = false;
	}

	return $config;
}

function cms_config_text($config)
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
EOF;
	return $result;
}

/**
 * Saves config.php
 * 
 * Loops through the config hash given and save it's values
 * out to the config.php in the filesystem.  Files quietly
 * if there is no access to the file;
 *
 * @since 0.5
 */
function cms_config_save($config)
{
	$newfiledir = dirname(CONFIG_FILE_LOCATION);
	$newfilename = CONFIG_FILE_LOCATION;
	if (is_writable($newfilename) || is_writable($newfiledir))
	{
		// try to backup config file
		$oldconfig = @file_get_contents($newfilename);
		$bakupg = false;
		$handle = @fopen($newfilename .'.bakupg', "w");
		if ($handle)
		{
			@fwrite($handle, $oldconfig);
			fclose($handle);
			$bakupg = true;
		}

		$handle = fopen($newfilename, "w");
		if ($handle)
		{
			fwrite($handle, "<?php\n\n");
			fwrite($handle, cms_config_text($config));
			fwrite($handle, "\n?>");
			fclose($handle);

			$new_config = cms_config_upgrade();
			if ( ($new_config) && ($bakupg) )
			{
				@unlink($newfilename .'.bakupg');
			}
			elseif ($bakupg)
			{
				// failed, restore old config file (usefull in upgrade)
				@copy($newfilename .'.bakupg', $newfilename);
				@unlink($newfilename .'.bakupg');
			}
			return true;
		}
	}
	return false;
}

/**
 * Upgrades config.php from the old style
 *
 * @since 0.5
 */
function cms_config_upgrade()
{
	#Get my lazy on and just do some regex magic...
	$newfiledir = dirname(CONFIG_FILE_LOCATION);
	$newfilename = CONFIG_FILE_LOCATION;
	$oldconfiglines = array();
	$oldconfig = cms_config_load();
	if (is_writable($newfilename) || is_writable($newfiledir))
	{
		$handle = fopen($newfilename, "r");
		if ($handle)
		{
			while (!feof($handle))
			{
				$oldconfiglines[] = fgets($handle, 4096);
			}
			fclose($handle);
			$handle = fopen($newfilename, "w");
			if ($handle)
			{
				foreach ($oldconfiglines as $oneline)
				{
					/** from some ancient version? */
					$newline = trim(preg_replace('/\$this-\>(\S+)/', '$config["$1"]', $oneline));

					/* uploads_url, remove root url */
					if(preg_match( '/\$config\[\'uploads_url\'\]/' , $oneline) ) {
						$newline = str_replace($oldconfig['root_url'], '', $oneline);
					}

					/* image_uploads_url, remove root url */
					if(preg_match('/\$config\[\'image_uploads_url\'\]/',$oneline)) {
						$newline = str_replace($oldconfig['root_url'], '', $oneline);
					}
			
					if ($newline != "?>")
					{
						$newline .= "\n";
					}
					fwrite($handle, $newline);
				}
				fclose($handle);
				return true;
			}
		}
	}
	return false;
}

# vim:ts=4 sw=4 noet
?>
