<?php

//CHANGED
Header ("Content-type: text/css");
require_once("../include.php");
$theme=get_preference($userid,"admintheme");
if (file_exists(dirname(__FILE__)."/themes/$theme/tab.css")) {
	readfile(dirname(__FILE__)."/themes/$theme/tab.css");
} else {
	readfile(dirname(__FILE__)."/themes/default/tab.css");
}
//STOP

?>