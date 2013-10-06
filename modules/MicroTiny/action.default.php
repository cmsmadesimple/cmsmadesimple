<?php

if( !cmsms() ) exit();

$action="";
if (isset($params["action"])) $action=$params["action"];

switch ($params["action"]) {
  case "headerjs" : {
    echo $this->WYSIWYGGenerateHeader("", true);
    break;
  }
  default : {
    return $this->WYSIWYGTextarea('','','','','','', $params);
    break;
  }
}

?>