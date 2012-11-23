<?php

  //require_once('cms_test_base.php');
require_once(CMSMS.'/lib/adodb_lite/adodb.inc.php');

function &cmsms()
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

  public static function &get_instance()
  {
    if( !self::$_instance ) {
      self::$_instance = new CmsApp;
    }
    return self::$_instance;
  }

  protected function __construct()
  {
  }

  public function &GetDb()
  {
    if( !$this->_db ) {
      $config = $this->GetConfig();
      $this->_db = ADONewConnection('mysqli','pear:date:extend');
      $r = $this->_db->Connect($config['db_host'],$config['db_user'],$config['db_pass'],$config['db_name']);
      if( !$r ) {
	$str = "Attempt to connect to database {$config['db_name']} on {$config['db_user']}@{$config['db_host']} failed";
	throw new Exception($str);
      }
      $this->_db->SetFetchMode(ADODB_FETCH_ASSOC);
    }
    return $this->_db;
  }

  public function GetConfig()
  {
    global $test_settings;
    return $test_settings;
  }


} // end of class

?>