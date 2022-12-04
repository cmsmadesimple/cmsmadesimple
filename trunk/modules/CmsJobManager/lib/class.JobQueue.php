<?php

namespace CmsJobManager;

final class JobQueue
{
    private function __construct() {}

    public static function have_jobs()
    {
        return self::get_jobs(TRUE);
    }

    public static function get_all_jobs()
    {
        $db = \CmsApp::get_instance()->GetDb();

        $now = time();
        $limit = 50; // hardcoded.... should never be more than 100 jobs in the queue for a site.

        $sql = 'SELECT * FROM '.\CmsJobManager::table_name().' WHERE created < UNIX_TIMESTAMP() ORDER BY created ASC LIMIT ?';
        $list = $db->GetArray($sql,array($limit));
        if( !is_array($list) || count($list) == 0 ) return;

        $out = [];
        foreach( $list as $row ) {
            if( !empty($row['module']) ) {
                $mod = \cms_utils::get_module($row['module']);
                if( !is_object($mod) ) throw new \RuntimeException('Job '.$row['name'].' requires module '.$row['module'].' That could not be loaded');
            }
            $obj = unserialize($row['data']);
            $obj->set_id($row['id']);
            $obj->force_start = $row['start']; // in case this job was modified.
            $out[] = $obj;
        }

        return $out;
    }

    public static function get_jobs($check_only = fALSE)
    {
        $db = \CmsApp::get_instance()->GetDb();

        $now = time();
        $limit = 50; // hardcoded.... should never be more than 100 jobs in the queue for a site.
        if( $check_only ) $limit = 1;

        $sql = 'SELECT * FROM '.\CmsJobManager::table_name().' WHERE start < UNIX_TIMESTAMP() AND created < UNIX_TIMESTAMP() ORDER BY errors ASC,created ASC LIMIT ?';
        $list = $db->GetArray($sql,array($limit));
        if( !is_array($list) || count($list) == 0 ) return;
        if( $check_only ) return TRUE;

        $out = [];
        foreach( $list as $row ) {
            if( !empty($row['module']) ) {
                $mod = \cms_utils::get_module($row['module']);
                if( !is_object($mod) ) {
                    audit('','CmsJobManager',sprintf('Could not load module %s required by job %s',$row['module'],$row['name']));
                    continue;
                }
            }
            $obj = unserialize($row['data']);
            $obj->set_id($row['id']);
            $obj->force_start = $row['start']; // in case this job was modified.
            $out[] = $obj;
        }

        return $out;
    }

    public static function clear_bad_jobs()
    {
        $mod = \cms_utils::get_module('CmsJobManager');
        $now = time();
        $lastrun = (int) $mod->GetPreference('last_badjob_run');
        if( $lastrun + 3600 >= $now ) return; // hardcoded

        $db = $mod->GetDb();
        $sql = 'SELECT * FROM '.\CmsJobManager::table_name().' WHERE errors >= ?';
        $list = $db->GetArray($sql,array(10));  // hardcoded
        if( is_array($list) && count($list) ) {
            $idlist = [];
            foreach( $list as $row ) {
                $obj = unserialize($row['data']);
                if( !is_object($obj) ) {
                    debug_to_log(__METHOD__);
                    debug_to_log('Problem deserializing row');
                    debug_to_log($row);
                    continue;
                }
                $obj->set_id($row['id']);
                $idlist[] = (int) $row['id'];
                \CMSMS\HookManager::do_hook(\CmsJobManager::EVT_ONFAILEDJOB, [ 'job' => $obj ]);
            }
            $sql = 'DELETE FROM '.\CmsJobManager::table_name().' WHERE id IN ('.implode(',',$idlist).')';
            $db->Execute($sql);
            audit('',$mod->GetName(),'Cleared '.count($idlist).' bad jobs');
        }
        $mod->SetPreference('last_badjob_run',$now);
    }

}