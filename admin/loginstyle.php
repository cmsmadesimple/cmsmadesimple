<?php 
header("Content-type: text/css");
if (file_exists(dirname(__FILE__)."/themes/default/css/style.css"))
	{
	readfile(dirname(__FILE__)."/themes/default/css/style.css");
}
?>