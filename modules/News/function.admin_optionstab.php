<?php
if( !isset($gCms) ) exit;

  // CreateFormStart sets up a proper form tag that will cause the submit to
  // return control to this module for processing.
$smarty->assign('startform', $this->CreateFormStart ($id, 'updateoptions', $returnid));
$smarty->assign('endform', $this->CreateFormEnd ());

$smarty->assign('title_formsubmit_emailaddress',$this->Lang('formsubmit_emailaddress'));
$smarty->assign('formsubmit_emailaddress',$this->GetPreference('formsubmit_emailaddress',''));

$smarty->assign('title_email_subject',$this->Lang('email_subject'));
$smarty->assign('email_subject',$this->GetPreference('email_subject',''));

$smarty->assign('title_email_template',$this->Lang('email_template'));
$smarty->assign('email_template',$this->GetTemplate('email_template'));


$categorylist = array();
$query = "SELECT * FROM ".CMS_DB_PREFIX."module_news_categories ORDER BY hierarchy";
$dbresult = $db->Execute($query);

while ($dbresult && $row = $dbresult->FetchRow()) {
    $categorylist[$row['long_name']] = $row['news_category_id'];
}

$smarty->assign('title_default_category', $this->Lang('default_category'));
$smarty->assign('categorylist',array_flip($categorylist));
$smarty->assign('default_category',$this->GetPreference('default_category'));

$smarty->assign('title_allowed_upload_types',$this->Lang('allowed_upload_types'));
$smarty->assign('allowed_upload_types',$this->GetPreference('allowed_upload_types'));

$smarty->assign('title_auto_create_thumbnails',$this->Lang('auto_create_thumbnails'));

$smarty->assign('title_hide_summary_field',$this->Lang('hide_summary_field'));
$smarty->assign('hide_summary_field',$this->GetPreference('hide_summary_field',0));

$smarty->assign('title_allow_summary_wysiwyg',$this->Lang('allow_summary_wysiwyg'));
$smarty->assign('allow_summary_wysiwyg',$this->GetPreference('allow_summary_wysiwyg',1));

$smarty->assign('title_expiry_interval',$this->Lang('expiry_interval'));
$smarty->assign('expiry_interval',$this->GetPreference('expiry_interval',180));

$smarty->assign('title_expired_searchable',$this->Lang('expired_searchable'));
$smarty->assign('expired_searchable',$this->GetPreference('expired_searchable'));

$smarty->assign('title_expired_viewable',$this->Lang('expired_viewable'));
$smarty->assign('expired_viewable',$this->GetPreference('expired_viewable',1));
$smarty->assign('info_expired_viewable',$this->Lang('info_expired_viewable'));

$smarty->assign('title_fesubmit_status',$this->Lang('fesubmit_status'));
$statusdropdown = array();
$statusdropdown[$this->Lang('draft')] = 'draft';
$statusdropdown[$this->Lang('published')] = 'published';
$smarty->assign('statuses',array_flip($statusdropdown));
$smarty->assign('fesubmit_status',$this->GetPreference('fesubmit_status'));
$smarty->assign('input_fesubmit_status',
		$this->CreateInputDropdown($id,'fesubmit_status',$statusdropdown,-1,$this->GetPreference('fesubmit_status','draft')));

$smarty->assign('title_fesubmit_redirect',$this->Lang('fesubmit_redirect'));
$smarty->assign('fesubmit_redirect',$this->GetPreference('fesubmit_redirect'));

$contentops = $gCms->GetContentOperations();
$smarty->assign('title_detail_returnid',$this->Lang('title_detail_returnid'));
$smarty->assign('input_detail_returnid',
		$contentops->CreateHierarchyDropdown('',$this->GetPreference('detail_returnid',-1),
						     $id.'detail_returnid'));
$smarty->assign('info_detail_returnid',$this->Lang('info_detail_returnid'));

$smarty->assign('title_submission_settings',$this->Lang('title_submission_settings'));
$smarty->assign('title_fesubmit_settings',$this->Lang('title_fesubmit_settings'));
$smarty->assign('title_notification_settings',$this->Lang('title_notification_settings'));
$smarty->assign('title_detail_settings',$this->Lang('title_detail_settings'));
$smarty->assign('allow_fesubmit',$this->GetPreference('allow_fesubmit',0));
$smarty->assign('alert_drafts',$this->GetPreference('alert_drafts',0));

// Display the populated template
echo $this->ProcessTemplate ('adminprefs.tpl');

?>