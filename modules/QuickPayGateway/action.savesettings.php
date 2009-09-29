<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) {
	echo $this->lang("nopermission");
	return;
}

if (isset($params["merchantid"])) $this->SetPreference("merchantid",$params["merchantid"]);

$this->Redirect($id, 'defaultadmin', '', array("module_message"=>"Indstillingerne blev gemt","tab"=>"settings"));
?>