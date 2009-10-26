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
#$Id$

class Separator extends CmsContentBase
{

  public function __construct()
  {
    parent::__construct();
    $this->set_cachable(false);
    $this->set_name(CMS_CONTENT_HIDDEN_NAME);
  }


  public function friendly_name()
  {
    return lang('contenttype_separator');
  }
  
  public function has_usable_link()
  {
    return false;
  }

  public function wants_children()
  {
    return false;
  }


  public function get_url($rewrite = true, $lang = '')
  {
    return '#';
  }
}

# vim:ts=4 sw=4 noet
?>
