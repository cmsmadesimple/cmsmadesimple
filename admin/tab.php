<?php

//CHANGED
Header ("Content-type: text/css");
require_once("../include.php");
$theme=get_preference(get_userid(),"admintheme",CmsAdminThemeBase::GetDefaultTheme());
if (file_exists(dirname(__FILE__)."/themes/$theme/tab.css")) {
	echo file_get_contents(dirname(__FILE__)."/themes/$theme/tab.css");
} else {
	echo file_get_contents(dirname(__FILE__)."/themes/default/tab.css");
}
//STOP

?>
