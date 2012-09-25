<?php
if( !isset($gCms) ) exit;

  // CreateFormStart sets up a proper form tag that will cause the submit to
  // return control to this module for processing.
$smarty->assign('startform', $this->CreateFormStart ($id, 'updateoptions', $returnid));
$smarty->assign('endform', $this->CreateFormEnd ());

$smarty->assign('title_formsubmit_emailaddress',$this->Lang('formsubmit_emailaddress'));
$smarty->assign('input_formsubmit_emailaddress',$this->CreateInputText($id,'formsubmit_emailaddress',$this->GetPreference('formsubmit_emailaddress',''),50,255));

$smarty->assign('title_email_subject',$this->Lang('email_subject'));
$smarty->assign('input_email_subject',$this->CreateInputText($id,'email_subject',$this->GetPreference('email_subject',''),50,255));
$smarty->assign('title_email_template',$this->Lang('email_template'));
$smarty->assign('input_email_template',$this->CreateTextArea(false,$id,
							     $this->GetTemplate('email_template'),'email_template'));

$categorylist = array();
$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
$dbresult = $db->Execute($query);

while ($dbresult && $row = $dbresult->FetchRow())
  {
    $categorylist[$row['long_name']] = $row['news_category_id'];
  }

$smarty->assign('title_default_category', $this->Lang('default_category'));
$smarty->assign('input_default_category', $this->CreateInputDropdown($id, 'default_category', $categorylist, -1, $this->GetPreference('default_category', '')));

$smarty->assign('title_allowed_upload_types',$this->Lang('allowed_upload_types'));
$smarty->assign('input_allowed_upload_types',
		$this->CreateInputText($id,'allowed_upload_types',
				       $this->GetPreference('allowed_upload_types',''),50));

$smarty->assign('title_auto_create_thumbnails',$this->Lang('auto_create_thumbnails'));

$smarty->assign('submit', $this->CreateInputSubmit ($id, 'optionssubmitbutton', $this->Lang('submit')));

$smarty->assign('title_hide_summary_field',$this->Lang('hide_summary_field'));
$smarty->assign('input_hide_summary_field',
		$this->CreateInputCheckbox($id,'hide_summary_field','1',
					   $this->GetPreference('hide_summary_field','0')));

$smarty->assign('title_allow_summary_wysiwyg',$this->Lang('allow_summary_wysiwyg'));
$smarty->assign('input_allow_summary_wysiwyg',
		$this->CreateInputCheckbox($id,'allow_summary_wysiwyg','1',
					   $this->GetPreference('allow_summary_wysiwyg',1)));

$smarty->assign('title_expiry_interval',$this->Lang('expiry_interval'));
$smarty->assign('input_expiry_interval',
		$this->CreateInputText($id,'expiry_interval',
				       $this->GetPreference('expiry_interval',180),4,4));

$smarty->assign('title_expired_searchable',$this->Lang('expired_searchable'));
$smarty->assign('input_expired_searchable',
		$this->CreateInputCheckbox($id,'expired_searchable','1',
					   $this->GetPreference('expired_searchable',1)));

$smarty->assign('title_expired_viewable',$this->Lang('expired_viewable'));
$smarty->assign('expired_viewable',$this->GetPreference('expired_viewable',1));
$smarty->assign('info_expired_viewable',$this->Lang('info_expired_viewable'));

$smarty->assign('title_fesubmit_status',$this->Lang('fesubmit_status'));
$statusdropdown = array();
$statusdropdown[$this->Lang('draft')] = 'draft';
$statusdropdown[$this->Lang('published')] = 'published';
$smarty->assign('input_fesubmit_status',
		$this->CreateInputDropdown($id,'fesubmit_status',$statusdropdown,-1,$this->GetPreference('fesubmit_status','draft')));

$smarty->assign('title_fesubmit_redirect',$this->Lang('fesubmit_redirect'));
$smarty->assign('input_fesubmit_redirect',
		$this->CreateInputText($id,'fesubmit_redirect',
				       $this->GetPreference('fesubmit_redirect',''),20,20));

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

// Display the populated template
echo $this->ProcessTemplate ('adminprefs.tpl');

?>