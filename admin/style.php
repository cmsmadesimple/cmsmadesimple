<?php

//CHANGED
Header ("Content-type: text/css");
require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");
$theme=get_preference(get_userid(),"admintheme");
$style="style";
if (isset($_GET['ie']))
    {
    $style.="_ie";
    }
$style .= ".css";
//
if (file_exists(dirname(__FILE__)."/themes/".$theme."/".$style)) {
	readfile(dirname(__FILE__)."/themes/".$theme."/".$style);
	//include(dirname(__FILE__)."/themes/".$theme."/style.css");
} else {
	readfile(dirname(__FILE__)."/themes/default/".$style);
	//include(dirname(__FILE__)."/themes/default/style.css");
}
//STOP

?>
