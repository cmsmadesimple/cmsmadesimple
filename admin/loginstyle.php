<?php
Header ("Content-type: text/css");
require_once("../include.php");
$theme=get_site_preference('logintheme', 'default');
//
if (file_exists(dirname(__FILE__)."/themes/".$theme."/css/style.css")) {  
	readfile(dirname(__FILE__)."/themes/".$theme."/css/style.css");
} else {
	readfile(dirname(__FILE__)."/themes/default/css/style.css");
}
?>
