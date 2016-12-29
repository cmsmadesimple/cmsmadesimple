<?php
#-------------------------------------------------------------------------
# Module: FilePicker - A CMSMS addon module to provide file picking capabilities.
# (c) 2016 by Fernando Morgado <jomorg@cmsmadesimple.org>
# (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# BEGIN_LICENSE
#-------------------------------------------------------------------------
# This file is part of FilePicker
# FilePicker is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# FilePicker is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
# END_LICENSE
#-------------------------------------------------------------------------
//namespace CmsFilePicker;

class ProfileParameter implements \ArrayAccess 
{
  
  private $_data = array();
  
  private $_valid_keys = array(
                                'name',
                                'value',
                                'type',
                                'options'
                              );
                              
  const TYPE_TEXTINPUT    = 0;
  const TYPE_TEXTAREA     = 1;
  const TYPE_DROPDOWN     = 2;
  const TYPE_MULTISELECT  = 3;
  const TYPE_CHECKBOX     = 4;
  
  public function __construct($data)
  {
    foreach($this->_valid_keys as $key)
    {
      $this->_data[$key] = isset($data[$key]) ? $data[$key] : NULL;
    }
  }

  public function offsetSet($offset, $value) 
  {
    if( is_null($offset) ) return; 
    if( !in_array($offset, $this->_valid_keys) ) return; 

    $this->_data[$offset] = $value;
  }

  public function offsetExists($offset) 
  {
    return isset($this->_data[$offset]);
  }

  public function offsetUnset($offset) 
  {
    unset($this->_data[$offset]);
  }

  public function offsetGet($offset) 
  {
    return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
  }
}
?>