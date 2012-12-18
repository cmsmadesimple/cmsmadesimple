<?php

function smarty_cms_modifier_cms_date_format($string, $format = '',
					 $default_date = '')
{
  $gCms = cmsms();
  if( $format == '' )
    {
      $format = get_site_preference('defaultdateformat');
      if( $format == '' )
	{
	  $format = '%b %e, %Y';
	}
      if( !isset($gCms->variables['page_id']) )
        {
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
    }

  $config = $gCms->GetConfig();
  $fn = cms_join_path($config['root_path'],'lib','smarty','plugins','modifier.date_format.php');
  if( !file_exists($fn) ) die();
  require_once( $fn );

  return smarty_modifier_date_format($string,$format,$default_date);
}
// EOF
?>
