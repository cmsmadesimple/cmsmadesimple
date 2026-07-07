<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('adminprefs.tpl'), null, null, $smarty);
if( isset($config['developer_mode']) ) {
  $tpl->assign('developer_mode',1);
  $tpl->assign('module_repository',$this->GetPreference('module_repository'));
  $tpl->assign('disable_caching',$this->GetPreference('disable_caching',0));
}
$tpl->assign('dl_chunksize',$this->GetPreference('dl_chunksize',256));
$tpl->assign('latestdepends',$this->GetPreference('latestdepends',1));
$tpl->assign('allowuninstall',$this->GetPreference('allowuninstall',0));
$tpl->assign('show_beta',$this->GetPreference('show_beta',0));
$tpl->assign('show_incompatible',$this->GetPreference('show_incompatible',0));
$tpl->display();

#
# EOF
#
?>