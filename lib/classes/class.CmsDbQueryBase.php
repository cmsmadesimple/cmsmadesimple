<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * @package CMS
 */

/**
 * An abstract class for building queries and managing results 
 *
 * @since 1.12
 * @author Robert Campbell <calguy1000@gmail.com>
 */
abstract class CmsDbQueryBase
{
  protected $_totalmatchingrows = null;
  protected $_offset = 0;
  protected $_limit = 1000;
  protected $_rs = null;
  protected $_args = array();

  public function __construct($args = '')
  {
    if( empty($args) ) return;
    
    if( is_array($args) ) {
      $this->_args = $args;
    }
    else if( is_string($args) ) {
      $this->_args = explode(',',$args);
    }
  }

  abstract public function execute();

  public function RecordCount()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->RecordCount();
  }

  public function MoveNext()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveNext();
  }

  public function MoveFirst()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveFirst();
  }

  public function Rewind()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveFirst();
  }

  public function MoveLast()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveLast();
  }

  public function EOF()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->EOF();
    return TRUE;
  }

  public function Close()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->Close();
    return TRUE;
  }

  abstract public function GetMatches();

  public function __get($key)
  {
    $this->execute();
    if( $key == 'fields' && $this->_rs && !$this->_rs->EOF() ) {
      return $this->_rs->fields;
    }
    if( $key == 'EOF' ) {
      return $this->_rs->EOF();
    }
    if( $key == 'limit' ) {
      return $this->_limit;
    }
    if( $key == 'offset' ) {
      return $this->_offset;
    }
    if( $key == 'totalrows' ) {
      return $this->_totalmatchingrows;
    }
    if( $key == 'numpages' ) {
      return ceil($this->_totalmatchingrows / $this->_limit);
    }
  }

} // end of class

#
# EOF
#
?>
