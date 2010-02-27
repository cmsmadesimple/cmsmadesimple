<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: Link.inc.php 4225 2007-10-14 16:31:00Z calguy1000 $

class PageLink extends CmsContentBase
{
  public function __construct()
  {
    parent::__construct();
    $this->set_cachable(false);
  }

  public function is_copyable()
  {
    return TRUE;
  }

  public function is_viewable()
  {
    return FALSE;
  }

  public function friendly_name()
  {
    return lang('contenttype_pagelink');
  }

  public function has_usable_link()
  {
    return false;
  }

  public function get_url($rewrite = true, $lang = '')
  {
    $page = $this->get_property_value('page');
    $params = $this->get_property_value('params');
    
    $cms = cmsms();
    $contentops =& $gCms->GetContentOperations();
    $destcontent =& $contentops->LoadContentFromId($page);
    if( is_object( $destcontent ) ) 
      {
	$url = $destcontent->get_url();
	$url .= $params;
	return $url;
      }
  } 
}

# vim:ts=4 sw=4 noet
?>
