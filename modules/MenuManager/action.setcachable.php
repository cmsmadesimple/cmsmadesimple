<?php
if( !isset($gCms) ) return;

if( !isset($params['template']) || !isset($params['state']) )
  {
    return;
  }

$tpl = trim($params['template']);
$state = (int)$params['state'];

$cachables = array();
{
  $tmp = base64_decode($this->GetPreference('cachable_templates'));
  if( $tmp )
    {
      $cachables = unserialize($tmp);
    }
}

if( $state )
  {
    if( !in_array($tpl,$cachables) )
      {
	$cachables[] = $tpl;
      }
  }
else
  {
    if( in_array($tpl,$cachables) )
      {
	$tmp = array();
	foreach( $cachables as $one )
	  {
	    if( $one != $tpl ) $tmp[] = $one;
	  }
	$cachables = $tmp;
      }
  }

$tmp = serialize($cachables);
$this->SetPreference('cachable_templates',base64_encode($tmp));

$this->Redirect($id,'defaultadmin');

?>