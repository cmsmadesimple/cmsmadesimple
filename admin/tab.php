<?php

//CHANGED
Header ("Content-type: text/css");

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

$theme=get_preference(get_userid(),"admintheme");
if (file_exists(dirname(__FILE__)."/themes/$theme/tab.css")) {
	readfile(dirname(__FILE__)."/themes/$theme/tab.css");
} else {
	readfile(dirname(__FILE__)."/themes/default/tab.css");
}
//STOP

?>
