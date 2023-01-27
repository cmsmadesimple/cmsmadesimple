<?php
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
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

class dm_xml_reader extends XMLReader
{
  private $_setup;
  private $_old_err_handler;
  private $_old_internal_errors;

  public static function __errhandler($errno,$errstr,$errfile,$errline)
  {
    if( strpos($errstr,'XMLReader') !== FALSE ) {
      audit('','DesignManager/dm_xml_reader',$errstr);
      $mod = cms_utils::get_module('DesignManager');
      throw new CmsXMLErrorException($mod->Lang('error_xmlstructure').':<br/>'.$errstr);
      return TRUE;
    }
  }

  public function __setup()
  {
    if( !$this->_setup ) {
      $this->_old_internal_errors = libxml_use_internal_errors(FALSE);
      $this->_old_err_handler = set_error_handler(array($this,'__errhandler'));
      $this->_setup = 1;
    }
  }

  public function __destruct()
  {
    if( $this->_old_err_handler )
      set_error_handler($this->_old_err_handler);
  }

  public function read()
  {
    $this->__setup();
    return parent::read();
  }
} // end of class

#
# EOF
#
?>