<?php
status_msg('Upgrading schema for CMSMS 2.2');

//$gCms = cmsms();
$dbdict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

$sqlarray = $dbdict->AddColumnSQL(CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME,'help_content_cb C(255), one_only I1');
$dbdict->ExecuteSQLArray($sqlarray);

verbose_msg(ilang('upgrading_schema',202));
$query = 'UPDATE '.CMS_DB_PREFIX.'version SET version = 202';
$db->Execute($query);

$type = \CmsLayoutTemplateType::load('__CORE__::page');
$type->set_help_callback('CmsTemplateResource::template_help_callback');
$type->save();

$type = \CmsLayoutTemplateType::load('__CORE__::generic');
$type->set_help_callback('CmsTemplateResource::template_help_callback');
$type->save();