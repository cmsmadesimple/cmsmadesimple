<?php

namespace CmsJobManager;
use CmsJobManager;
use cms_utils;

final class JobQueue
{
    private $mod;

    private $db;

    public function __construct( CmsJobManager $mod )
    {
        $this->mod = $mod;
        $this->db = $mod->GetDb();
    }

    public function have_jobs()
    {
        return $this->get_jobs(TRUE);
    }

    public function get_all_jobs()
    {
        $db = $this->db;
        $now = time();
        $limit = 50; // hardcoded.... should never be more than 100 jobs in the queue for a site.

        $sql = 'SELECT * FROM '.$this->mod->table_name().' WHERE created < UNIX_TIMESTAMP() ORDER BY created ASC LIMIT ?';
        $list = $db->GetArray($sql,array($limit));
        if( !is_array($list) || count($list) == 0 ) return;

        $out = [];
        foreach( $list as $row ) {
            $mod = null;
            if( !empty($row['module']) ) {
                $mod = cms_utils::get_module($row['module']);
            }
            $obj = unserialize($row['data']);
            $obj->set_id($row['id']);
            $obj->errors = (int) $row['errors'];
            $obj->force_start = $row['start']; // in case this job was modified.
            $out[] = $obj;
        }

        return $out;
    }

    public function get_jobs($check_only = fALSE)
    {
        $db = $this->db;
        $now = time();
        $limit = 50; // hardcoded.... should never be more than 100 jobs in the queue for a site.
        if( $check_only ) $limit = 1;

        $sql = 'SELECT * FROM '.$this->mod->table_name().' WHERE start < UNIX_TIMESTAMP() AND created < UNIX_TIMESTAMP() ORDER BY errors ASC,created ASC LIMIT ?';
        $list = $db->GetArray($sql, $limit);
        if( !is_array($list) || count($list) == 0 ) return;
        if( $check_only ) return TRUE;

        $out = [];
        foreach( $list as $row ) {
            if( !empty($row['module']) ) {
                // we attempt to load the module because the job class may depend on the module to unserialize
                $mod = cms_utils::get_module($row['module']);
                if( !is_object($mod) ) {
                    $sql = 'UPDATE '.$this->mod->table_name().' SET errors = errors + 1 WHERE id = ?';
                    $db->Execute($sql, $row['id']);
                    cms_error(sprintf('Could not load module %s required by job %s',$row['module'],$row['name']), 'CmsJobManager');
                    continue;
                }
            }
            $obj = unserialize($row['data']);
            $obj->set_id($row['id']);
            $obj->errors = (int) $row['errors'];
            $obj->force_start = $row['start']; // in case this job was modified.
            $out[] = $obj;
        }

        return $out;
    }

    public function clear_bad_jobs()
    {
        $mod = $this->mod;
        $now = time();
        $lastrun = (int) $mod->GetPreference('last_badjob_run');
        if( $lastrun + 3600 >= $now ) return; // hardcoded

        $db = $mod->GetDb();
        $sql = 'SELECT * FROM '.$this->mod->table_name().' WHERE errors >= ?';
        $list = $db->GetArray($sql,array(10));  // hardcoded
        if( is_array($list) && count($list) ) {
            $idlist = [];
            foreach( $list as $row ) {
                $obj = unserialize($row['data']);
                if( !is_object($obj) ) {
                    debug_to_log(__METHOD__);
                    debug_to_log('Problem deserializing row');
                    debug_to_log($row);
                }
                else {
                    $obj->set_id($row['id']);
                    $idlist[] = (int) $row['id'];
                    $this->mod->cms->get_hook_manager()->emit(CmsJobManager::EVT_ONFAILEDJOB, [ 'job' => $obj ]);
                }
            }
            $sql = 'DELETE FROM '.$this->mod->table_name().' WHERE id IN ('.implode(',',$idlist).')';
            $db->Execute($sql);
            cms_notice('Cleared '.count($idlist).' bad jobs',$this->GetName());
        }
        $mod->SetPreference('last_badjob_run',$now);
    }
} // class
