<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * @package CMS
 */

/**
 * A simple class for assisting with content management
 *
 * @package CMS
 * @internal
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class content_assistant
{
  /**
   * A utility function to test if we are allowed to auto create url paths
   *
   * @return boolean
   */
  public static function auto_create_url()
  {
    return get_site_preference('content_autocreate_urls',0);
  }


  /**
   * A utility function to test if the supplied url path is valid for the supplied content id
   *
   * @param string The partial url path to test
   * @return boolean
   */ 
  public static function is_valid_url($url,$content_id = '')
  {
    // check for starting or ending slashes
    if( startswith($url,'/') || endswith($url,'/') )
      {
		  return FALSE;
      }

    // first check for invalid chars.
	// strip off any extension (that is like 5 chars or less)
	$pos = strrpos($url,'.');
	if( $pos !== FALSE )
	{
		// have an extension.
		$ext = substr($url,$pos+1);
		if( strlen($ext) >= 5 || munge_string_to_url($ext,false,true) != strtolower($ext) )
			{
				return FALSE;
			}
		$tmp = substr($url,0,$pos);
		if( munge_string_to_url($tmp,false,true) != strtolower($tmp) )
			{
				return FALSE;
			}
	}
	else
	{
		$translated = munge_string_to_url($url,false,true);
		if( strtolower($translated) != strtolower($url) )
			{
				return FALSE;
			}
	}

    cms_route_manager::load_routes();
    $route = cms_route_manager::find_match($url,TRUE);
    if( !$route ) return TRUE;
    if( $route->is_content() )
      {
		  if($content_id == '' || ($route->get_content() == $content_id))
			  {
				  return TRUE;
			  }
      }
    return FALSE;
  }
} // end of class

?>