<?php

/**
 * Classes and utilities to define auditing and logging in CMSMS.
 * @package CMS
 * @license GPL
 */

namespace CMSMS {

    /**
     * An abstract interface for classes that provide auditing.
     *
     * @package CMS
     * @license GPL
     * @since   2.3
     * @author  Robert Campbell
     */
    interface IAuditManager
    {
        /**
         * Generate A "message" level auditing message.
         *
         * This is primarily provided to indicate something has happened on a specific data item.
         *
         * @param string $subject The message subject.  This would usually be a module name.
         * @param string $msg The message content
         * @param mixed  $item_id The specific item, if applicable, being adjusted.
         */
        public function audit( string $subject, string $msg, $item_id = null );

        /**
         * Generate a low priority message (but highter than audit) that indicates that something has occurred.
         *
         * @param string $msg The message content
         * @param string $subject An optional subject
         */
        public function notice( string $msg, string $subject = null );

        /**
         * Generate a warning message that indicates that something has occurred.
         *
         * @param string $msg The message content
         * @param string $subject An optional subject
         */
        public function warning( string $msg, string $subject = null );

        /**
         * Generate an error message that indicates that something has occurred.
         *
         * @param string $msg The message content
         * @param string $subject An optional subject
         */
        public function error( string $msg, string $subject = null );
    }

    /**
     * A basic error log auditor that uses PHP's error log to output formatted messages.
     */
    class HttpErrorLogAuditor implements IAuditManager
    {
        /**
         * @ignore
         */
        public function audit( string $subject, string $msg, $itemid = null )
        {
            $userid = get_userid(FALSE);
            $username = get_username(FALSE);
            $ip_addr = null;
            if( $userid < 1 ) $userid = null;

            $out = "CMSMS MSG: ADMINUSER=$username ($userid)";
            if( $itemid ) $out .= ", ITEMID=$itemid,";
            if( $subject ) $out .= ", SUBJECT=$subject,";
            $out .= " MSG=$msg\n";
            $this->notice( $out );
        }

        /**
         * @ignore
         */
        public function notice( string $msg, string $subject = null )
        {
            $out = "CMSMS NOTICE: ";
            if( $subject ) $out .= "SUBJECT=$subject,";
            $out .= " $msg\n";
            @error_log( $out, 0, TMP_CACHE_LOCATION.'/audit_log' );
        }

        /**
         * @ignore
         */
        public function warning( string $msg, string $subject = null )
        {
            $out = "CMSMS WARNING: ";
            if( $subject ) $out .= "SUBJECT=$subject,";
            $out .= " $msg\n";
            @error_log( $out, 0, TMP_CACHE_LOCATION.'/audit_log' );
        }

        /**
         * @ignore
         */
        public function error( string $msg, string $subject = null )
        {
            $out = "CMSMS ERROR: ";
            if( $subject ) $out .= "SUBJECT=$subject,";
            $out .= " $msg\n";
            @error_log( $out, 0, TMP_CACHE_LOCATION.'/audit_log' );
        }
    }

    /**
     * A manager contains links to the standard auditor and an optional audit handler.
     * so that CMSMS can output it's messages somewhere.
     */
    final class AuditManager
    {

        /**
         * @ignore
         */
        private static $_instance;

        /**
         * @ignore
         */
        private static $_std_mgr;

        /**
         * @ignore
         */
        private static $_opt_mgr;

        /**
         * @ignore
         */
        protected function __construct()
        {
            // nothing here
        }

        /**
         * @ignore
         */
        public static function init()
        {
            // does nothing... just so we can audoload the thing.
        }

        /**
         * Set an auditor for further messages.
         *
         * If set, the optional auditor will b used instead of the HttpErrorLogAuditor
         *
         * @param IAuditManager $mgr
         */
        public static function set_auditor( IAuditManager $mgr )
        {
            if( self::$_opt_mgr  ) throw new \LogicException('Sorry only one audit manager can be set');
            self::$_opt_mgr = $mgr;
        }

        /**
         * Get the current auditor.
         *
         * @return IAuditManager
         */
        protected static function get_auditor()
        {
            if( self::$_opt_mgr ) return self::$_opt_mgr;
            if( !self::$_std_mgr ) self::$_std_mgr = new HttpErrorLogAuditor();
            return self::$_std_mgr;
        }

        /**
         * Create an audit messge using the current auditor.
         *
         * @param string $subj
         * @param string $msg
         * @param int|null $item_id
         */
        public static function audit( string $subj, string $msg, $item_id )
        {
            if( !empty($item_id) ) $item_id = (int) $item_id;
            self::get_auditor()->audit( $subj, $msg, $item_id );
        }

        /**
         * Create a notice message using the current auditor.
         *
         * @param string $msg
         * @param string $subject
         */
        public static function notice( string $msg, string $subject = null )
        {
            self::get_auditor()->notice( $msg, $subject );
        }

        /**
         * Create a warning message using the current auditor.
         *
         * @param string $msg
         * @param string $subject
         */
        public static function warning( string $msg, string $subject = null )
        {
            self::get_auditor()->warning( $msg, $subject );
        }


        /**
         * Create an error message using the current auditor.
         *
         * @param string $msg
         * @param string $subject
         */
        public static function error( string $msg, string $subject = null )
        {
            self::get_auditor()->error( $msg, $subject );
        }
    } // end of class
} // namespace


namespace  {
    /**
     * A convenience function in the root namespace to create a message using the current audit manager.
     *
     * @param int|null $item_id
     * @param string $subject
     * @param string $message
     */
    function audit( $item_id, string $subject, string $message ) {
        \CMSMS\AuditManager::audit( $subject, $message, $item_id );
    }

    /**
     * A convenience function in the root namespace to create a notice message using the current audit manager.
     *
     * @param string $msg
     * @param string $subject
     */
    function cms_notice( string $msg, string $subject = null ) {
        \CMSMS\AuditManager::notice( $msg, $subject );
    }

    /**
     * A convenience function in the root namespace to create a warning message using the current audit manager.
     *
     * @param string $msg
     * @param string $subject
     */
    function cms_warning( string $msg, string $subject = null ) {
        \CMSMS\AuditManager::warning( $msg, $subject );
    }

    /**
     * A convenience function in the root namespace to create an error message using the current audit manager.
     *
     * @param string $msg
     * @param string $subject
     */
    function cms_error( string $msg, string $subject = null ) {
        \CMSMS\AuditManager::error( $msg, $subject );
    }

} // namespace
