<?php 
$theme=get_site_preference('logintheme', 'default');
header("Content-type: text/css");
if (file_exists(dirname(__FILE__)."/themes/$theme/css/style.css"))
  {
    readfile(dirname(__FILE__)."/themes/$theme/css/style.css");
  }
else
  {
    readfile(dirname(__FILE__)."/themes/default/css/style.css");
  }
?>