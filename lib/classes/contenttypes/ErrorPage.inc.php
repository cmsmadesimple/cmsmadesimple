<?php
# CMS - CMS Made Simple
# (c)2004-2009 by Ted Kulp (ted@cmsmadesimple.org)
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

require_once('Content.inc.php');
class ErrorPage extends Content
{
  var $error_types;
	
  public function __construct()
  {
    parent::__construct();
    $this->error_types = array('404' => lang('404description'));
    $this->set_alias('error404');
  }

  function handles_alias()
  {
    return true;
  }

  public function friendly_name()
  {
    return lang('contenttype_errorpage');
  }	

  public function is_copyable()
  {
    return FALSE;
  }

  public function is_default_possible()
  {
    return FALSE;
  }

  function has_usable_link()
  {
    return false;
  }

  function wants_children()
  {
    return false;
  }

  function is_system_page()
  {
    return true;
  }

}

# vim:ts=4 sw=4 noet
?>
