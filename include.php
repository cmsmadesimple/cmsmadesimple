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

set_magic_quotes_runtime(false);

session_name("CMSSESSID");
if(!session_id()) {
	session_start();
}

class CMSConfig {

	function CMSConfig() {

#Database connection information
$this->db_hostname = "localhost";
$this->db_username = "cms";
$this->db_password = "cms";
$this->db_name = "cms";

#If app needs to coexist with other tables in the same db,
#put a prefix here.  e.g. "cms_"
$this->db_prefix = "";

#Document root as seen from the webserver.  No slash at the end
#e.g. http://blah.com
$this->root_url = "http://www.something.com";

#Path to document root
#e.g. /var/www/localhost
$this->root_path = dirname(__FILE__);

#For using a particular querystring variable.  Turning off
#produces variables like: http://cms.wishy.org/index.php/somecontent
#where as setting to page would make:
#http://cms.wishy.org/?page=somecontent
$this->query_var = "";

#Install BBCodeParser from the PEAR library
#and then set this to true for BBCode usage in content
#and tables.
$this->use_bb_code = false;

include_once(dirname(__FILE__)."/config.php");

	}
}

require_once(dirname(__FILE__)."/version.php");

$config = new CMSConfig();

#Setup db connection
include_once(dirname(__FILE__)."/adodb/adodb.inc.php");

if (!$DONT_LOAD_DB) {
	$dbnew = &ADONewConnection('mysql');
	$dbnew->PConnect($config->db_hostname,$config->db_username,$config->db_password,$config->db_name);
	if (!$dbnew) die("Connection failed");
	$dbnew->SetFetchMode(ADODB_FETCH_ASSOC);
	$config->db = &$dbnew;
}

require_once(dirname(__FILE__)."/lib/db.functions.php");
require_once(dirname(__FILE__)."/lib/misc.functions.php");
require_once(dirname(__FILE__)."/lib/page.functions.php");
require_once(dirname(__FILE__)."/lib/content.functions.php");
require_once(dirname(__FILE__)."/lib/module.functions.php");

define('SMARTY_DIR', dirname(__FILE__).'/smarty/');

#Stupid magic quotes...
if(get_magic_quotes_gpc())
{
	strip_slashes($_GET);
	strip_slashes($_POST);
	strip_slashes($_COOKIE);
	strip_slashes($_SESSIONS);
}

#Setup the object sent to modules
$modulecmsobj->pluginnum = 1;
$modulecmsobj->page = $page;
$modulecmsobj->db = &$dbnew;
$modulecmsobj->config = &$config;

#Setup hash for storing all modules
$cmsmodules = array();

#Load all installed module code
load_modules();

#Debug
#foreach ($cmsmodules as $key=>$val) {
#	echo "$key - $val<br />";
#	foreach ($val as $key2=>$val2) {
#		echo "$key2 - $val2<br />";
#	}
#}

#Only do language stuff for admin pages
if (isset($CMS_ADMIN_PAGE)) {

	#Setup gettext
	require_once(dirname(__FILE__)."/lib/GetText.php");
	if (function_exists('gettext')) {
		$gettext = new GetText_NativeSupport();
	} else {
		$gettext = new GetText_PHPSupport();
	}
	$gettext->addDomain('cmsmadesimple', dirname(__FILE__)."/locale");

	#Setup defaults
	$nls['language']['en_US'] = "English";
	$nls['alias']['en_CA'] = "en_US";
	$nls['alias']['en_GB'] = "en_US";

	#Read in all current languages...
	$dir = dirname(__FILE__)."/locale";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_dir("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
			if (is_file("$dir/$file/nls.php")) {
				include("$dir/$file/nls.php");
			}
		}
	}
	#Check to see if there is already a language in use...
	if (isset($_POST["change_cms_lang"])) {
		$err = $gettext->setLanguage($_POST["change_cms_lang"]);
		if (PEAR::isError($err))
		{
			echo $err->toString(), "\n";
		}
		setcookie("cms_language", $_POST["change_cms_lang"]);
	}
	else if (isset($_COOKIE["cms_language"])) {
		$err = $gettext->setLanguage($_COOKIE["cms_language"]);
		if (PEAR::isError($err))
		{
			echo $err->toString(), "\n";
		}
	} else {

		#No, take a stab at figuring out the default language...
		#Figure out default language and set it if it exists
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$svrstring = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
			$alllang = substr($svrstring,0,strpos($svrstring, ";"));
			$langs = explode(",", $alllang);

			foreach ($langs as $onelang) {
				#Check to see if lang exists...
				if (isset($nls['language'][$onelang])) {
					$err = $gettext->setLanguage($onelang);
					if (PEAR::isError($err))
					{
						echo $err->toString(), "\n";
					}
					setcookie("cms_language", $onelang);
					break;
				}
				#Check to see if alias exists...
				if (isset($nls['alias'][$onelang])) {
					$alias = $nls['alias'][$onelang];
					if (isset($nls['language'][$alias])) {
						$err = $gettext->setLanguage($alias);
						if (PEAR::isError($err))
						{
							echo $err->toString(), "\n";
						}
						setcookie("cms_language", $alias);
						break;
					}
				}
			}
		}
	}
}

#Check for HTML_BBCodeParser
if ($config->use_bb_code == true) {
	if (include_once(dirname(__FILE__)."/lib/PEAR.php")) {
		if (include_once("HTML/BBCodeParser.php")) {
			$parser = new HTML_BBCodeParser();
			$config->bbcodeparser = new HTML_BBCodeParser();
		}
	}
}

# vim:ts=4 sw=4 noet
?>
