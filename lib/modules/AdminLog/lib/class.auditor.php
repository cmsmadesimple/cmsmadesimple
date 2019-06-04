<?php
namespace AdminLog;

class auditor implements \CMSMS\IAuditManager
{

    private $_mod;

    private $_storage;

    public function __construct( \AdminLog $mod, storage $store )
    {
        $this->_mod = $mod;
        $this->_storage = $store;
    }

    protected function get_event_parms()
    {
        $parms = [];
        $parms['uid']= get_userid(FALSE);
        $parms['username'] = get_username(FALSE);
        if( $parms['uid'] && !cmsms()->is_cli() ) $parms['ip_addr'] = \cms_utils::get_real_ip();
        return $parms;
    }

    public function audit( string $subject, string $msg, $item_id = null )
    {
        $parms = $this->get_event_parms();
        $parms = array_merge($parms,[ 'severity'=>event::TYPE_MSG, 'subject'=>$subject, 'msg'=>$msg, 'item_id'=>$item_id ] );

        $ev = new event( $parms );
        $this->_storage->save( $ev );
    }

    protected function error_log( $severity, $msg )
    {
        $sevmsg = null;
        switch( $severity ) {
            case event::TYPE_WARNING:
                $sevmsg = 'WARNING';
		break;
            case event::TYPE_ERROR:
                $sevmsg = 'ERROR';
		break;
            case event::TYPE_NOTICE:
            default:
                $sevmsg = 'NOTICE';
                break;
        }
        $sitename = \cms_siteprefs::get('sitename','CMSMS');
        $msg = "$sitename $sevmsg: $msg";
        @error_log( $msg, 4 );
    }

    public function notice( string $msg, string $subject = null )
    {
        $parms = $this->get_event_parms();
        $parms = array_merge($parms,[ 'severity'=>event::TYPE_NOTICE, 'msg'=>$msg, 'subject'=>$subject ]);
        $ev = new event( $parms );

        $this->_storage->save( $ev );
        $this->error_log( $ev::TYPE_NOTICE, $msg );
    }

    public function warning( string $msg, string $subject = null )
    {
        $parms = $this->get_event_parms();
        $parms = array_merge($parms,[ 'severity'=>event::TYPE_WARNING, 'msg'=>$msg, 'subject'=>$subject ]);
        $ev = new event( $parms );

        $this->_storage->save( $ev );
        $this->error_log( $ev::TYPE_WARNING, $msg );
    }

    public function error( string $msg, string $subject = null )
    {
        $parms = $this->get_event_parms();
        $parms = array_merge($parms,[ 'severity'=>event::TYPE_ERROR, 'msg'=>$msg, 'subject'=>$subject ]);
        $ev = new event( $parms );

        $this->_storage->save( $ev );
        $this->error_log( $ev::TYPE_ERROR, $msg );
    }
} // end of class
