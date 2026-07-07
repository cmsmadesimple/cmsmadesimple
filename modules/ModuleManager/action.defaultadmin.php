<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;

if( isset($params['modulehelp']) ) {
    // this is done before permissions checks
    $params['mod'] = $params['modulehelp'];
    unset($params['modulehelp']);
    include(__DIR__.'/action.local_help.php');
    return;
}

if( !$this->VisibleToAdminUser() ) exit;
$tmp = ModuleOperations::get_instance()->GetQueueResults();
if( is_array($tmp) && count($tmp) ) {
    $tmp2 = array();
    foreach( $tmp as $key => $data ) {
        $msg = $data[1];
        if( !$msg ) {
            $msg = $this->Lang('unknown');
            if( $data[0] ) $msg = $this->Lang('success');
        }
        $tmp2[] = $key.': '.$msg;
    }
    echo $this->ShowMessage($tmp2);
}


$connection_ok = modmgr_utils::is_connection_ok();
if( !$connection_ok ) echo $this->ShowErrors($this->Lang('error_request_problem'));

// this is a bit ugly.
modmgr_utils::get_images();

$newversions = [];
if( $connection_ok ) {
    try {
        $newversions = modmgr_rep_client::get_newmoduleversions();
    }
    catch(ModuleNoDataException $e) {
      audit('', 'ModuleManager', 'No data from repository: ' . $e->GetMessage());
    }
    catch( Exception $e ) {
        echo $this->ShowErrors($e->GetMessage());
    }
}

echo $this->StartTabHeaders();
if( $this->CheckPermission(ModuleManager::MANAGE_PERM) ) {
    echo $this->SetTabHeader('installed',$this->Lang('installed'));
    if( $connection_ok ) {
        $num = ( is_array($newversions) ) ? count($newversions) : 0;
        echo $this->SetTabHeader('newversions',$num.' '.$this->Lang('tab_newversions') );
        echo $this->SetTabHeader('search',$this->Lang('search'));
        echo $this->SetTabHeader('modules',$this->Lang('availmodules'));
    }
}
if( $this->CheckPermission(ModuleManager::PREFS_PERM) ) echo $this->SetTabHeader('prefs',$this->Lang('prompt_settings'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();
if( $this->CheckPermission(ModuleManager::MANAGE_PERM) ) {
    echo $this->StartTab('installed',$params);
    include(dirname(__FILE__).'/function.admin_installed.php');
    echo $this->EndTab();

    if( $connection_ok ) {
        echo $this->StartTab('newversions',$params);
        include(dirname(__FILE__).'/function.newversionstab.php');
        echo $this->EndTab();

        echo $this->StartTab('search',$params);
        include(dirname(__FILE__).'/function.search.php');
        echo $this->EndTab();

        echo $this->StartTab('modules',$params);
        include(dirname(__FILE__).'/function.admin_modules_tab.php');
        echo $this->EndTab();
    }
}
if( $this->CheckPermission(ModuleManager::PREFS_PERM) ) {
    echo $this->StartTab('prefs',$params);
    include(dirname(__FILE__).'/function.admin_prefs_tab.php');
    echo $this->EndTab();
}
echo $this->EndTabContent();
