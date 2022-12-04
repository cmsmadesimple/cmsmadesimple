<?php

  //require_once('cms_test_base.php');
require_once(CMSMS.'/lib/adodb_lite/adodb.inc.php');

function cmsms()
{
  return CmsApp::get_instance();
}

function cms_db_prefix()
{
  $config = cmsms()->GetConfig();
  return $config['db_prefix'];
}

final class CmsApp
{
  private $_db;
  private static $_instance;

  public static function get_instance()
  {
    if( !self::$_instance ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  protected function __construct()
  {
  }

  public function GetDb()
  {
    if( !$this->_db ) {
      $config = $this->GetConfig();
      if( empty($config['db_port']) ) {
        $mysqli = new mysqli($config['db_hostname'], $config['db_username'],
           $config['db_password'], $config['db_name']);
      }
      else {
        $mysqli = new mysqli($config['db_hostname'], $config['db_username'],
           $config['db_password'], $config['db_name'], (int)$config['db_port']);
      }
      if( !$mysqli || $mysqli->connect_errno ) {
        $str = "Attempt to connect to database {$config['db_name']} on {$config['db_user']}@{$config['db_host']} failed";
        throw new Exception($str);
      }
      $this->_db = $mysqli;
    }
    return $this->_db;
  }

  public function GetConfig()
  {
    global $test_settings;
    return $test_settings;
  }

} // end of class
