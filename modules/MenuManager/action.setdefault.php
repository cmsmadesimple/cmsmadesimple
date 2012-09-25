<?php
if( !isset($gCms) ) return;

if( isset($params['template']) )
  {
    $tpl = trim($params['template']);
    $this->SetPreference('default_template',$tpl);
  }

$this->Redirect($id,'defaultadmin');
?>