<?php

global $gCms;
$smarty =& $gCms->GetSmarty();
$config =& $gCms->GetConfig();
$fn = cms_join_path($config['root_path'],'lib','smarty','plugins','modifier.date_format.php');
if( !file_exists($fn) ) die();
require_once( $fn );

function smarty_cms_modifier_cms_date_format($string, $format = '',
					     $default_date = '')
{

  if( $format == '' )
    {
      $format = get_site_preference('defaultdateformat');
      if( $format == '' )
	{
	  $format = '%b %e, %Y';
	}
      $uid = get_userid(false);
      if( $uid )
	{
	  $tmp = get_preference($uid,'date_format_string');
	  if( $tmp != '' )
	    {
	      $format = $tmp;
	    }
	}
    }

  return smarty_modifier_date_format($string,$format,$default_date);
}
// EOF
?>
