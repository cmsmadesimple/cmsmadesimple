<?php
$current_version = $oldversion;
$this->SetPreference("uploadboxes","5");
switch($current_version) {
	case "0.1.0":
	case "0.1.1":
	case "0.1.2":
	case "0.1.3":
	case "0.1.4": $this->Install(true);
}

$this->RemovePermission('Use Filemanager');

$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));
?>
