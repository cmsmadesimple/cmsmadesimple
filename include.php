<?php

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
$this->root_path = "/var/www/localhost/htdocs";

#For using a particular querystring variable.  Turning off
#produces variables like: http://cms.wishy.org/index.php/somecontent
#where as setting to page would make:
#http://cms.wishy.org/?page=somecontent
$this->query_var = "";

#Install BBCodeParser from the PEAR library
#and then set this to true for BBCode usage in content
#and tables.
$this->use_bb_code = false;

include_once("config.php");

}
}

$config = new CMSConfig();
define('SMARTY_DIR', $config->root_path.'/smarty/');
ini_set('include_path', ini_get('include_path').":".$config->root_path.":".$config->root_path."/smarty/");
echo ini_get('include_dir');

require_once("lib/misc.functions.php");
require_once("lib/db.functions.php");
require_once("lib/page.functions.php");
require_once("lib/content.functions.php");

#Check for HTML_BBCodeParser
if ($config->use_bb_code == true) {
	if (include_once("PEAR.php")) {
		if (include_once("HTML/BBCodeParser.php")) {
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
}

session_start();

?>
