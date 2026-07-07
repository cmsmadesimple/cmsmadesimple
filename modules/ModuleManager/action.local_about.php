<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
$module = get_parameter_value($params,'mod');

$ops = ModuleOperations::get_instance();
$modinstance = $ops->get_module_instance($module,'',TRUE);
if( !is_object($modinstance) ) {
  $this->SetError($this->Lang('error_getmodule',$module));
  $this->RedirectToAdminTab();
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('local_about.tpl'), null, null, $smarty);
$tpl->assign('back_url',$this->create_url($id,'defaultadmin',$returnid));
$tpl->assign('about_page',$modinstance->GetAbout());
$tpl->assign('about_title',$this->Lang('about_title',$modinstance->GetName()));
$tpl->display();
?>