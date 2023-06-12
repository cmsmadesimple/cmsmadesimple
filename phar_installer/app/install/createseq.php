<?php

$table_ids = array(
    'additional_users' => array('id' => 'additional_users_id'),
    'admin_bookmarks'  => array('id' => 'bookmark_id'),
    'content'          => array('id' => 'content_id'),
    'content_props'    => array('id' => 'content_id'),
    'event_handlers'   => array('id' => 'handler_id', 'seq' => 'event_handler_seq'),
    'events'           => array('id' => 'event_id'),
    'group_perms'      => array('id' => 'group_perm_id'),
    'groups'           => array('id' => 'group_id'),
    'permissions'      => array('id' => 'permission_id'),
    'userplugins'      => array('id' => 'userplugin_id'),
    'users'            => array('id' => 'user_id')
);

if (isset($CMS_INSTALL_DROP_TABLES)) {
//  status_msg(ilang(c.f. 'install_dropping_tables'));
    $pref = CMS_DB_PREFIX;
    $fmt = "DROP TABLE IF EXISTS `{$pref}%s`";
    foreach ($table_ids as $tablename => $tableinfo) {
        $seqname = (isset($tableinfo['seq'])) ? $tableinfo['seq'] : $tablename . '_seq';
//      verbose_msg(ilang('install_dropseqTODO',CMS_DB_PREFIX.$seqname));
        $sql = sprintf($fmt, $seqname);
        $db->Execute($sql);
        usleep(20000);
    }
}

if (isset($CMS_INSTALL_CREATE_TABLES)) {
    $pref = CMS_DB_PREFIX;
    foreach ($table_ids as $tablename => $tableinfo) {
        $fname = $tableinfo['id'];
        $sql = "SELECT COALESCE(MAX(`$fname`),0) AS maxid FROM {$pref}{$tablename}";
        $max = $db->GetOne($sql);
        $seqname = (isset($tableinfo['seq'])) ? $tableinfo['seq'] : $tablename . '_seq';
        verbose_msg(ilang('install_updateseq',CMS_DB_PREFIX.$seqname));
        $db->CreateSequence(CMS_DB_PREFIX.$seqname,$max);
    }
}

?>
