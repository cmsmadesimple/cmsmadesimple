<?php
namespace AdminLog;
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$fn = __DIR__.'/css/admin_styles.css';
if( is_file($fn) ) {
    $txt = file_get_contents($fn);
    if( $txt ) {
        $txt = "<style>\n".$txt."</style>";
        $this->AddAdminHeaderText($txt);
    }
}

include(__DIR__.'/action.admin_log_tab.php');
