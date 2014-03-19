<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_modifier_cms_date_format($string, $format = '', $default_date = '')
{
  if( $format == '' ) {
    $format = get_site_preference('defaultdateformat');
    if( $format == '' ) $format = '%b %e, %Y';

    if( !cmsms()->get_content_id() ) {
      $uid = get_userid(false);
      if( $uid ) {
	$tmp = get_preference($uid,'date_format_string');
	if( $tmp != '' ) $format = $tmp;
      }
    }
  }

  $config = cmsms()->GetConfig();
  $fn = cms_join_path($config['root_path'],'lib','smarty','plugins','modifier.date_format.php');

  if( !file_exists($fn) ) die();
  require_once( $fn );
  return smarty_modifier_date_format($string,$format,$default_date);
}
?>