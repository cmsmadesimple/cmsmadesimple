<?php
final class ReduceAdminlogTask implements CmsRegularTask
{
    const  LASTEXECUTE_SITEPREF   = 'ReduceAdminlog_lastexecute';
    private $_queue = [];

    public function get_name()
    {
        return get_class();
    }

    public function get_description()
    {
        return lang_by_realm('tasks','reduceadminlog_taskdescription');
    }

    public function test($time = '') {
        // do we need to do this task.
        // we do it every 3 hours
        if (!$time) $time = time();
        $last_execute = \cms_siteprefs::get(self::LASTEXECUTE_SITEPREF, 0);
        if ($last_execute >= ($time - 3 * 3600) ) return FALSE; // hardcoded
        return TRUE;
    }

    protected function is_same($a,$b)
    {
        if( !is_array($a) || !is_array($b) ) return FALSE;

        // ignore the timestamp
        foreach( $a as $key => $val ) {
            switch( $key ) {
            case 'timestamp':
                if( abs($b['timestamp'] - $a['timestamp']) > 3600 ) return FALSE;
                break;
            default:
                if( $a[$key] != $b[$key] ) return FALSE;
                break;
            }
        }
        return TRUE;
    }

    protected function queue_for_deletion($row)
    {
        $this->_queue[] = $row;
    }

    protected function have_queued()
    {
        return (count($this->_queue) > 1);
    }

    protected function adjust_last() {
        if( !$this->have_queued() ) return;

        $n = count($this->_queue);
        $lastrec = $this->_queue[$n - 1];
        $this->_queue = array_slice($this->_queue,0,-1);

        $db = \CmsApp::get_instance()->GetDB();
        $lastrec['action'] = $lastrec['action'] . sprintf(" (repeated %d times)",$n);
        $sql = 'UPDATE '.cms_db_prefix().'adminlog SET action = ?
                WHERE timestamp = ? AND user_id = ? AND username = ? AND item_id = ? AND item_name = ? AND ip_addr = ?';
        $db->Execute($sql,array($lastrec['action'],$lastrec['timestamp'],$lastrec['user_id'],$lastrec['username'],
                                $lastrec['item_id'],$lastrec['item_name'],$lastrec['ip_addr']));
    }

    public function clear_queued()
    {
        $n = count($this->_queue);
        if( $n < 1 ) return;

        $db = \CmsApp::get_instance()->GetDB();
        $sql = 'DELETE FROM '.cms_db_prefix().'adminlog
                WHERE timestamp = ? AND user_id = ? AND username = ? AND item_id = ? AND item_name = ? AND action = ? AND ip_addr = ?';
        for( $i = 0; $i < $n; $i++ ) {
            $rec = $this->_queue[$i];
            $db->Execute($sql,array($rec['timestamp'],$rec['user_id'],$rec['username'],
                                    $rec['item_id'],$rec['item_name'],$rec['action'],$rec['ip_addr']));
        }
        $this->_queue = [];
    }

    public function execute($time = '')
    {
        if( !$time ) $time = time();
        $db = \CmsApp::get_instance()->GetDB();

        $table = cms_db_prefix().'adminlog';
        $last_execute = \cms_siteprefs::get(self::LASTEXECUTE_SITEPREF, 0);
        $mintime = max($last_execute - 60,$time - 24 * 3600);
        $sql = "SELECT * FROM $table WHERE timestamp >= ? ORDER BY timestamp ASC";
        $dbr = $db->Execute($sql,array($mintime));

        $prev = null;
        while( $dbr && !$dbr->EOF() ) {
            $row = $dbr->fields;
            if( $prev && $this->is_same($prev,$row) ) {
                $this->queue_for_deletion($prev);
            } else {
                if( $this->have_queued() ) {
                    $this->adjust_last();
                    $this->clear_queued();
                }
            }
            $prev = $row;
            $dbr->MoveNext();
        }
        if( $this->have_queued() ) {
            $this->adjust_last();
            $this->clear_queued();
        }
        return TRUE;
    }

    public function on_success($time = '')
    {
        if( !$time ) $time = time();
        \cms_siteprefs::set(self::LASTEXECUTE_SITEPREF,$time);
    }

    public function on_failure($time = '')
    {
        if( !$time ) $time = time();
        // nothing here.
    }
}