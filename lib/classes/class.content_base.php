<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id: class.cms_content_base.php 5976 2009-09-07 14:23:29Z wishy $

/**
 * A class to provide backwards compatibility
 *
 */
class ContentBase extends CmsContentBase
{
  /**
   * Get the list of tab names for this content type
   * from the content type profile.
   *
   * Other methods may override this function for
   * backwards compatibility
   */
  public function TabNames()
  {
    $profile = $this->get_profile();
  }


  /**
   * Get all of the fields for a particular tab as an array of arrays
   * 
   */
  public function EditAsArray($adding = false,$tab = 0,$showadmin = false)
  {
    return array(array('Error','Edit Not Defined!'));
  }
} // end of class


#
# EOF
#
?>