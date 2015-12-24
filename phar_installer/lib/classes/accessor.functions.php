<?php

namespace __appbase;

function &smarty()
{
  return cms_smarty::get_instance();
}

function &nls()
{
  return nlstools::get_instance();
}

function &translator()
{
  return langtools::get_instance();
}


function &new_db_connection($dbtype = 'mysqli')
{
  require_once(dirname(__DIR__).'/adodb_lite/adodb.inc.php');
  $obj = ADONewConnection( $dbtype,'pear:date:extend');
  $obj->SetFetchMode(ADODB_FETCH_ASSOC);
  if( !is_object($obj) ) throw new \Exception('Error creating database connection of type '.$dbtype);
  return $obj;
}

?>