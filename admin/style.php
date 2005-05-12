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
if (file_exists(dirname(__FILE__)."/themes/".$theme."/css/".$style))
	{
	readfile(dirname(__FILE__)."/themes/".$theme."/css/".$style);
	}
else if (file_exists(dirname(__FILE__)."/themes/default/css/".$style))
	{
	readfile(dirname(__FILE__)."/themes/default/css/".$style);
}
?>
