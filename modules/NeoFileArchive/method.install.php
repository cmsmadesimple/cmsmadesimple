<?php
if (!isset($gCms)) exit;

$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('postinstall',$this->GetVersion()));

?>