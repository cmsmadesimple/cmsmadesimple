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

$info = ModuleManagerModuleInfo::get_module_info($module);
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('local_missingdeps.tpl'), null, null, $smarty);
$tpl->assign('back_url',$this->create_url($id,'defaultadmin',$returnid));
$tpl->assign('info',$info);
$tpl->display();
#
# EOF
#
?>