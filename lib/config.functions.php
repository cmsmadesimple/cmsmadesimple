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
function cms_config_load($loadLocal = true)
{
	$config = array();

	#Database connection information
	$config["dbms"] = "mysql";
	$config["db_hostname"] = "localhost";
	$config["db_username"] = "cms";
	$config["db_password"] = "cms";
	$config["db_name"] = "cms";

	#If app needs to coexist with other tables in the same db,
	#put a prefix here.  e.g. "cms_"
	$config["db_prefix"] = "cms_";

	#Document root as seen from the webserver.  No slash at the end
	#e.g. http://blah.com
	$config["root_url"] = "http://www.something.com";

	#Path to document root
	#e.g. /var/www/localhost
	$config["root_path"] = dirname(dirname(__FILE__));

	#For using a particular querystring variable.  Turning off
	#produces variables like: http://cms.wishy.org/index.php/somecontent
	#where as setting to page would make:
	#http://cms.wishy.org/?page=somecontent
	$config["query_var"] = "page";

	#Install BBCodeParser from the PEAR library
	#and then set this to true for BBCode usage in content
	#and tables.
	$config["use_bb_code"] = false;

	#Allow smarty {php} tags?  These could be dangerous.
	$config["use_smarty_php_tags"] = false;

	#Where do previews get saved?
	$config["previews_path"] = $config["root_path"] . "/tmp/cache";

	#Where are uploaded files put?
	$config["uploads_path"] = $config["root_path"] . "/uploads";

	#Where is the url to this directory?
	$config["uploads_url"] = $config["root_url"] . "/uploads";

	#Maxium upload size (in bytes)?
	$config["max_upload_size"] = 1000000;

	#CMS Debug Mode?
	$config["debug"] = false;

	#Show mod_rewrite URLs in the menu?
	$config["assume_mod_rewrite"] = false;

	#Automatically assign alias based on page title?
	$config["auto_alias_content"] = true;

	$config["image_manipulation_prog"] = "GD";
	$config["image_transform_lib_path"] = "/usr/bin/ImageMagick/";
	$config["use_Indite"] = true;

	$config["image_uploads_path"] = $config["root_path"] . "/uploads/images";
	$config["image_uploads_url"] = $config["root_url"] . "/uploads/images";

	$config["default_encoding"] = "";

	$config["disable_htmlarea_translation"] = false;

	$config["admin_dir"] = "admin";

	$config["persistent_db_conn"] = true;

	$config["default_upload_permission"] = '664';
	
	$config["page_extension"] = ".shtml";

	if ($loadLocal == true)
	{
		if (file_exists(dirname(dirname(__FILE__))."/config.php") && !cms_config_check_old_config())
		{
			include(dirname(dirname(__FILE__))."/config.php");
		}
	}

	return $config;
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
	$newfiledir = dirname(dirname(__FILE__));
	$newfilename = "$newfiledir/config.php";
	if (is_writable($newfilename) || is_writable($newfiledir))
	{
		$handle = fopen($newfilename, "w");
		if ($handle)
		{
			fwrite($handle, "<?php\n\n");
			foreach ($config as $key=>$value)
			{
				if (is_string($value))
				{
					fwrite($handle, "\$config['$key'] = '$value';\n");
				}
				else if (is_bool($value))
				{
					fwrite($handle, "\$config['$key'] = ".($value?"true":"false").";\n");
				}
				else
				{
					fwrite($handle, "\$config['$key'] = ".strval($value).";\n");
				}
			}
			fwrite($handle, "\n?>");
			fclose($handle);
		}
	}
}

/**
 * Upgrades config.php from the old style
 *
 * @since 0.5
 */
function cms_config_upgrade()
{
	#Get my lazy on and just do some regex magic...
	$newfiledir = dirname(dirname(__FILE__));
	$newfilename = "$newfiledir/config.php";
	$oldconfiglines = array();
	if (is_writable($newfilename) || is_writable($newfiledir))
	{
		$handle = fopen($newfilename, "r");
		if ($handle)
		{
			while (!feof($handle))
			{
				array_push($oldconfiglines,fgets($handle, 4096));
			}
			fclose($handle);
			$handle = fopen($newfilename, "w");
			if ($handle)
			{
				foreach ($oldconfiglines as $oneline)
				{
					$newline = trim(preg_replace('/\$this-\>(\S+)/', '$config["$1"]', $oneline));
					if ($newline != "?>")
					{
						$newline .= "\n";
					}
					fwrite($handle, $newline);
				}
				fclose($handle);
			}
		}
	}
}

/**
 * Checks to see if the config file is still of the old style
 *
 * @since 0.5
 */
function cms_config_check_old_config()
{
	$result = false;

	$newfiledir = dirname(dirname(__FILE__));
	$newfilename = "$newfiledir/config.php";
	$handle = fopen($newfilename, "r");
	if ($handle)
	{
		while (!feof($handle))
		{
			if (preg_match('/^\$this->.*/', fgets($handle, 4096)))
			{
				$result = true;
				break;
			}
		}
		fclose($handle);
	}

	return $result;
}

# vim:ts=4 sw=4 noet
?>
