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

include_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."config.php");

	}
}

require_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."version.php");

$config = new CMSConfig();
#define('SMARTY_DIR', $config->root_path.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR);
define('SMARTY_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR .'smarty'. DIRECTORY_SEPARATOR );

#ini_set('include_path', ini_get('include_path'). DIRECTORY_SEPARATOR .$config->root_path. DIRECTORY_SEPARATOR .$config->root_path.DIRECTORY_SEPARATOR."smarty".DIRECTORY_SEPARATOR);
#echo ini_get('include_dir');

require_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."lib". DIRECTORY_SEPARATOR ."misc.functions.php");
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."lib". DIRECTORY_SEPARATOR ."db.functions.php");
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."lib". DIRECTORY_SEPARATOR ."page.functions.php");
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."lib". DIRECTORY_SEPARATOR ."content.functions.php");
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."lib". DIRECTORY_SEPARATOR ."GetText.php");

#initialiase GetText package
GetText::init();

#add the translation domain we wrote files for
GetText::addDomain('cmsmadesimple', dirname(__FILE__). DIRECTORY_SEPARATOR ."locale");

#Check for HTML_BBCodeParser
if ($config->use_bb_code == true) {
	if (include_once(dirname(__FILE__). DIRECTORY_SEPARATOR ."lib". DIRECTORY_SEPARATOR ."PEAR.php")) {
		if (include_once("HTML". DIRECTORY_SEPARATOR ."BBCodeParser.php")) {
			$parser = new HTML_BBCodeParser();
			$config->bbcodeparser = new HTML_BBCodeParser();
		}
	}
}

#Stupid magic quotes...
if(get_magic_quotes_gpc())
{
	strip_slashes($_GET);
	strip_slashes($_POST);
	strip_slashes($_COOKIES);
	strip_slashes($_SESSIONS);
}

# vim:ts=4 sw=4 noet
?>
