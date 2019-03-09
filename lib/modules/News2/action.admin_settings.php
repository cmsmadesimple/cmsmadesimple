<?php
namespace News2;
use News2;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') && !$this->CheckPermission(News2::MANAGE_PERM) ) exit;

echo $this->StartTabHeaders();
if( $this->CheckPermission( News2::MANAGE_PERM ) ) {
    echo $this->SetTabHeader('categories', $this->Lang('tab_categories'));
}
if( $this->CheckPermission('Modify Site Preferences') ) {
    echo $this->SetTabHeader('fielddefs', $this->Lang('tab_fielddefs'));
    echo $this->SetTabHeader('dmstuff', $this->Lang('tab_dmstuff'));
}
echo $this->EndTabHeaders();

echo $this->StartTabContent();
if( $this->CheckPermission( News2::MANAGE_PERM ) ) {
    echo $this->StartTab('categories',$params);
    include_once(__DIR__.'/action.admin_settings_categories_tab.php');
    echo $this->EndTab();
}
if( $this->CheckPermission('Modify Site Preferences') ) {
    echo $this->StartTab('fielddefs',$params);
    include_once(__DIR__.'/action.admin_settings_fielddefs_tab.php');
    echo $this->EndTab();

    echo $this->StartTab('dmstuff',$params);
    include_once(__DIR__.'/action.admin_settings_dmstuff_tab.php');
    echo $this->EndTab();
}
echo $this->EndTabContent();
