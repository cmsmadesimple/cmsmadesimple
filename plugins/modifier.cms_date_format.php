<?php


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
      if( isset($CMS_ADMIN_PAGE) && $uid )
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