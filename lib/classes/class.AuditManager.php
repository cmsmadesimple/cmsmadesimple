<?php
namespace CMSMS {

    interface IAuditManager
    {
        public function audit( $subject, $msg, $item_id = null );
        public function notice( $msg, $subject = null );
        public function warning( $msg, $subject = null );
        public function error( $msg, $subject = null );
    }

    class HttpErrorLogAuditor implements IAuditManager
    {
        public function audit( $subject, $msg, $itemid = null )
        {
            $userid = get_userid(FALSE);
            $username = get_username(FALSE);
            $ip_addr = null;
            if( $userid < 1 ) $userid = null;

            $out = "CMSMS MSG: ADMINUSER=$username ($userid), ITEMID=$itemid: SUBJECT=$subject, MSG=$msg";
            $this->notice( $out );
        }

        public function notice( $msg, $subject = null )
        {
            $msg = "CMSMS NOTICE: SUBJECT=$subject, $msg";
            @error_log( $msg, 0, TMP_CACHE_LOCATION.'/audit_log' );
        }

        public function warning( $msg, $subject = null )
        {
            $msg = "CMSMS WARNING: SUBJECT=$subject, $msg";
            @error_log( $msg, 0, TMP_CACHE_LOCATION.'/audit_log' );
        }

        public function error( $msg, $subject = null )
        {
            $msg = "CMSMS ERROR: SUBJECT=$subject, $msg";
            @error_log( $msg, 0, TMP_CACHE_LOCATION.'/audit_log' );
        }
    }

    final class AuditManager
    {
        private static $_instance;
        private static $_std_mgr;
        private static $_opt_mgr;

        protected function __construct() {}

        public static function init() {} // does nothing... just so we can audoload the thing.

        public static function set_auditor( IAuditManager $mgr )
        {
            if( self::$_opt_mgr  ) throw new \LogicException('Sorry only one audit manager can be set');
            self::$_opt_mgr = $mgr;
        }

        protected static function get_auditor()
        {
            if( self::$_opt_mgr ) return self::$_opt_mgr;
            if( !self::$_std_mgr ) self::$_std_mgr = new HttpErrorLogAuditor();
            return self::$_std_mgr;
        }

        public static function audit( $item, $msg, $item_id )
        {
            self::get_auditor()->audit( $item, $msg, $item_id );
        }

        public static function notice( $msg )
        {
            self::get_auditor()->notice( $msg );
        }

        public static function warning( $msg )
        {
            self::get_auditor()->warning( $msg );
        }

        public static function error( $msg )
        {
            self::get_auditor()->error( $msg );
        }

    } // end of class
} // namespace


namespace  {
    function audit( $item_id, $item, $action ) {
        \CMSMS\AuditManager::audit( $item, $action, $item_id );
    }

    function cms_notice( $msg, $subject = null ) {
        \CMSMS\AuditManager::notice( $msg, $subject );
    }

    function cms_warning( $msg, $subject = null ) {
        \CMSMS\AuditManager::warning( $msg, $subject );
    }

    function cms_error( $msg, $subject = null ) {
        \CMSMS\AuditManager::error( $msg, $subject );
    }

}
