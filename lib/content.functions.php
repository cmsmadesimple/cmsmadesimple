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
#
#$Id$

/**
 * Handles content related functions
 *
 * @package CMS
 */

/**
 * A convenience function to test if the site is marked as down according to the config panel.
 * This method includes handling the preference that indicates that site-down behaviour should
 * be disabled for certain IP address ranges.
 *
 * @return boolean
 */
function is_sitedown()
{
  global $CMS_INSTALL_PAGE;
  if( isset($CMS_INSTALL_PAGE) ) return TRUE;

  if( get_site_preference('enablesitedownmessage') !== '1' ) return FALSE;

  if( get_site_preference('sitedownexcludeadmins') ) {
    $uid = get_userid(FALSE);
    if( $uid ) return FALSE;
  }

  if( !isset($_SERVER['REMOTE_ADDR']) ) return TRUE;
  $excludes = get_site_preference('sitedownexcludes','');
  if( empty($excludes) ) return TRUE;

  $tmp = explode(',',$excludes);
  $ret = cms_ipmatches($_SERVER['REMOTE_ADDR'],$excludes);
  if( $ret ) return FALSE;
  return TRUE;
}

?>