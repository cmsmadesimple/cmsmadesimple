<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Modules') ) return;

try {
    $allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info($connection_ok);
    $moduledir = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'modules';
    $icons = array();
    foreach( $allmoduleinfo as $name => $info ) {
        if( file_exists($moduledir . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'icon.png') ) {
            $icons[$name] = CMS_ROOT_URL . '/modules/' . $name . '/images/icon.png';
        }
    }
    $smarty->assign('module_icons', $icons);
    uksort($allmoduleinfo,'strnatcasecmp');
    $smarty->assign('module_info',$allmoduleinfo);
}
catch( Exception $e ) {
    debug_to_log($e);
    echo $this->ShowErrors($e->GetMessage()); return;
}
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_installed.tpl'), null, null, $smarty);
$tpl->assign($this->GetName(),$this);
$tpl->assign('allow_export',isset($config['developer_mode'])?1:0);
$tpl->assign('allow_modman_uninstall',$this->GetPreference('allowuninstall',0));
$tpl->display();

?>
