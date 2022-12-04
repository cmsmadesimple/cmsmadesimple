<?php

set_time_limit(90);
$dn = $destdir.'/admin/themes/default';
if( is_dir($dn) ) {
    status_msg('Making sure that default admin theme gets removed (it causes problems)');
    \__appbase\utils::rrmdir($dn);
}
status_msg('done upgrades for 2.0.1');
