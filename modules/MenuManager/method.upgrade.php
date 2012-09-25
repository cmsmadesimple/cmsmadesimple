<?php
if (!isset($gCms)) exit;

$current_version = $oldversion;
$db = cmsms()->GetDb();
switch($current_version)
{
	case "1.0":
	case "1.1":
		$this->CreatePermission('Manage Menu', 'Manage Menu');

		# Setup permissions
		$perm_id = $db->GetOne("SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = 'Manage Menu'");
		$group_id = $db->GetOne("SELECT group_id FROM ".cms_db_prefix()."groups WHERE group_name = 'Admin'");

		$count = $db->GetOne("SELECT count(*) FROM " . cms_db_prefix() . "group_perms WHERE group_id = ? AND permission_id = ?", array($group_id, $perm_id));
		if (isset($count) && intval($count) == 0)
		{
			$new_id = $db->GenID(cms_db_prefix()."group_perms_seq");
			$query = "INSERT INTO " . cms_db_prefix() . "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (".$new_id.", ".$group_id.", ".$perm_id.", ". $db->DBTimeStamp(time()) . ", " . $db->DBTimeStamp(time()) . ")";
			$db->Execute($query);
		}

		$group_id = $db->GetOne("SELECT group_id FROM ".cms_db_prefix()."groups WHERE group_name = 'Designer'");

		$count = $db->GetOne("SELECT count(*) FROM " . cms_db_prefix() . "group_perms WHERE group_id = ? AND permission_id = ?", array($group_id, $perm_id));
		if (isset($count) && intval($count) == 0)
		{
			$new_id = $db->GenID(cms_db_prefix()."group_perms_seq");
			$query = "INSERT INTO " . cms_db_prefix() . "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (".$new_id.", ".$group_id.", ".$perm_id.", ". $db->DBTimeStamp(time()) . ", " . $db->DBTimeStamp(time()) . ")";
			$db->Execute($query);
		}

		$current_version = '1.2';

	case '1.6.3':
	case '1.6.4':
	case '1.6.5':
		$this->AddEventHandler('Core','ContentEditPost',false);
		$this->AddEventHandler('Core','ContentDeletePost',false);
}

if( version_compare($oldversion,'1.8.2') < 1 ) {

    $this->RegisterModulePlugin(true);
    $this->RegisterSmartyPlugin('menu','function','function_plugin');
    $this->RegisterSmartyPlugin('cms_breadcrumbs','function','smarty_cms_breadcrumbs');
}

?>
