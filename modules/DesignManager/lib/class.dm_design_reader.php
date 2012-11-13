<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
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

class dm_design_reader 
{
  private $_xml;
  private $_scanned;
  private $_design_info = array();
  private $_tpl_info = array();
  private $_css_info = array();
  private $_file_map = array();

  public function __construct($fn)
  {
    $this->_xml = new dm_xml_reader();
    $this->_xml->open($fn);
    $this->_xml->SetParserProperty(XMLReader::VALIDATE,TRUE);
  }

  public function validate()
  {
    while( $this->_xml->read() ) {
      if( !$this->_xml->isValid() ) {
	throw new CmsException('Invalid XML FILE ');
      }
    }
    // it validates.
  }

  private function _scan()
  {
    $in = array();
    $cur_key = null;

    function __get_in()
    {
      global $in;
      if( ($n = count($in)) ) {
	return $in[$n-1];
      }
    }

    if( !$this->_scanned ) {
      $this->_scanned = TRUE;
      while( $this->_xml->read() ) {
	switch( $this->_xml->nodeType ) {
	case XmlReader::ELEMENT:
	  switch( $this->_xml->localName ) {
	  case 'design':
	  case 'template':
	  case 'stylesheet':
	  case 'file':
	    $in[] = $this->_xml->localName;
	    break;

	  case 'name':
	  case 'description':
	  case 'generated':
	  case 'cmsversion':
	    if( __get_in() != 'design' ) {
	      // validity error.
	    }
	    $name = $this->_xml->localName;
	    $this->_xml->read();
	    $this->_design_info[$name] = $this->_xml->value;
	    break;

	  case 'tkey':
	    if( __get_in() != 'template' ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $cur_key = $this->_xml->value;
	    $this->_tpl_info[$cur_key] = array('key'=>$cur_key);
	    break;

	  case 'tdesc':
	    if( __get_in() != 'template' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_tpl_info[$cur_key]['desc'] = $this->_xml->value;
	    break;

	  case 'tdata':
	    if( __get_in() != 'template' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_tpl_info[$cur_key]['data'] = $this->_xml->value;
	    break;

	  case 'csskey':
	    if( __get_in() != 'stylesheet' ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $cur_key = $this->_xml->value;
	    $this->_css_info[$cur_key] = array('key'=>$cur_key);
	    break;

	  case 'cssdesc':
	    if( __get_in() != 'stylesheet' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_css_info[$cur_key]['desc'] = $this->_xml->value;
	    break;

	  case 'cssdata':
	    if( __get_in() != 'stylesheet' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_css_info[$cur_key]['data'] = $this->_xml->value;
	    break;

	  case 'cssmediatype':
	    if( __get_in() != 'stylesheet' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_css_info[$cur_key]['mediatype'] = $this->_xml->value;
	    break;

	  case 'cssmediaquery':
	    if( __get_in() != 'stylesheet' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_css_info[$cur_key]['mediaquery'] = $this->_xml->value;
	    break;

	  case 'fkey':
	    if( __get_in() != 'file' ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $cur_key = $this->_xml->value;
	    $this->_file_map[$cur_key] = array('key'=>$cur_key);
	    break;

	  case 'fvalue':
	    if( __get_in() != 'file' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_file_map[$cur_key]['value'] = $this->_xml->value;
	    break;

	  case 'fdata':
	    if( __get_in() != 'file' || !$cur_key ) {
	      // validity error.
	    }
	    $this->_xml->read();
	    $this->_file_map[$cur_key]['data'] = $this->_xml->value;
	    break;
	  }
	  break;

	case XmlReader::END_ELEMENT:
	  switch( $this->_xml->localName ) {
	  case 'design':
	  case 'template':
	  case 'stylesheet':
	  case 'file':
	    if( count($in) ) {
	      array_pop($in);
	    }
	    $cur_key = null;
	    break;
	  }
	}
      }
    }
  }

  public function get_design_info()
  {
    $this->_scan();
    debug_display($this); die();
  }
} // end of class

#
# EOF
#
?>