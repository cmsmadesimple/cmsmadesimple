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

class dm_design_reader extends dm_reader_base
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

    $__get_in = function() use ($in) {
      global $in;
      if( ($n = count($in)) ) {
				return $in[$n-1];
      }
    };

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
						if( $__get_in() != 'design' ) {
							// validity error.
						}
						$name = $this->_xml->localName;
						$this->_xml->read();
						$this->_design_info[$name] = $this->_xml->value;
						break;

					case 'tkey':
						if( $__get_in() != 'template' ) {
							// validity error.
						}
						$this->_xml->read();
						$cur_key = $this->_xml->value;
						$this->_tpl_info[$cur_key] = array('key'=>$cur_key);
						break;

					case 'tdesc':
						if( $__get_in() != 'template' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_tpl_info[$cur_key]['desc'] = $this->_xml->value;
						break;

					case 'tdata':
						if( $__get_in() != 'template' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_tpl_info[$cur_key]['data'] = $this->_xml->value;
						break;

					case 'csskey':
						if( $__get_in() != 'stylesheet' ) {
							// validity error.
						}
						$this->_xml->read();
						$cur_key = $this->_xml->value;
						$this->_css_info[$cur_key] = array('key'=>$cur_key);
						break;

					case 'cssdesc':
						if( $__get_in() != 'stylesheet' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_css_info[$cur_key]['desc'] = $this->_xml->value;
						break;

					case 'cssdata':
						if( $__get_in() != 'stylesheet' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_css_info[$cur_key]['data'] = $this->_xml->value;
						break;

					case 'cssmediatype':
						if( $__get_in() != 'stylesheet' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_css_info[$cur_key]['mediatype'] = $this->_xml->value;
						break;

					case 'cssmediaquery':
						if( $__get_in() != 'stylesheet' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_css_info[$cur_key]['mediaquery'] = $this->_xml->value;
						break;

					case 'fkey':
						if( $__get_in() != 'file' ) {
							// validity error.
						}
						$this->_xml->read();
						$cur_key = $this->_xml->value;
						$this->_file_map[$cur_key] = array('key'=>$cur_key);
						break;

					case 'fvalue':
						if( $__get_in() != 'file' || !$cur_key ) {
							// validity error.
						}
						$this->_xml->read();
						$this->_file_map[$cur_key]['value'] = $this->_xml->value;
						break;

					case 'fdata':
						if( $__get_in() != 'file' || !$cur_key ) {
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

  private function _get_name($key)
  {
    if( isset($this->_file_map[$key]) ) {
      return $this->_file_map[$key]['value'];
    }
  }

  public function get_design_info()
  {
    $this->_scan();
    return $this->_design_info;
  }

  public function get_template_list()
  {
    $this->_scan();
    $out = array();
    foreach( $this->_tpl_info as $key => $one ) {
      $name = $this->_get_name($key);
      $rec = array();
      $rec['name'] = $name;
      $rec['key'] = $key;
      $rec['desc'] = base64_decode($one['desc']);
      $rec['data'] = base64_decode($one['data']);
      $out[] = $rec;
    }
    return $out;
  }

  public function get_stylesheet_list()
  {
    $this->_scan();
    $out = array();
    foreach( $this->_css_info as $key => $one ) {
      $name = $this->_get_name($key);
      $rec = array();
      $rec['name'] = $name;
      $rec['key'] = $key;
      $rec['desc'] = base64_decode($one['desc']);
      $rec['data'] = base64_decode($one['data']);
      $rec['mediatype'] = base64_decode($one['mediatype']);
      $rec['medisaquery'] = base64_decode($one['mediaquery']);
      $out[] = $rec;
    }
    return $out;
  }

	protected function validate_template_names()
	{
		$this->_scan();

		$templates = CmsLayoutTemplate::template_query(array('as_list'=>1));
		$tpl_names = array_values($templates);

		foreach( $this->_file_map as $key => $rec ) {
			if( !startswith($key,'__TPL::') ) continue;

			if( in_array($rec['value'],$tpl_names) ) {
				// gotta come up with a new name
				$orig_name = $rec['value'];
				$n = 1;
				while( $n < 10 ) {
          $n++;
					$new_name = $orig_name.' '.$n;
					if( !in_array($new_name,$tpl_names) ) {
						$rec['old_value'] = $rec['value'];
						$rec['value'] = $new_name;
					}
				}
			}
		}
	}


	protected function validate_stylesheet_names()
	{
		$this->_scan();

		$stylesheets = CmsLayoutStylesheet::get_all(TRUE);
		$css_names = array_values($stylesheets);

		foreach( $this->_file_map as $key => $rec ) {
			if( !startswith($key,'__CSS::') ) continue;

			if( in_array($rec['value'],$css_names) ) {
				// gotta come up with a new name
				$orig_name = $rec['value'];
				$n = 1;
				while( $n < 10 ) {
          $n++;
					$new_name = $orig_name.' '.$n;
					if( !in_array($new_name,$css_names) ) {
						$rec['old_value'] = $rec['value'];
						$rec['value'] = $new_name;
					}
				}
			}
		}
	}

	public function get_new_name()
	{
		$name = $this->get_suggested_name();
		if( !$name ) {
			// no suggested name... get one from the design.
			$info = $this->get_design_info();
			$name = $info['name'];
		}
		if( !$name ) {
			// still no name... try to use the filename.
			$t = $this->get_filename();
			$x = strpos($t,'.');
			$name = substr($t,$x);
		}
		
		// now see if it's a duplicate name
		$list = CmsLayoutCollection::get_list();
		$orig_name = $name;
		if( is_array($list) && count($list) ) {
			$name_list = array_values($list);
			$n = 1;
			while( $n < 100 ) {
				if( !in_array($name,$name_list) ) {
					break;
				}
				$n++;
				$name = "$orig_name $n";
			}
			if( $n >= 100 ) {
				throw new CmsException('Could not determine a new name for this design');
			}
		}
		
		return $name;
	}

	public function get_destination_dir()
	{
		$name = $this->get_new_name();
		$config = cmsms()->GetConfig();
		$dirname = munge_string_to_url($name);
		$dir = cms_join_path($config['uploads_dir'],'designs',$dirname);
		@mkdir($dir,0777,TRUE);
		if( !is_dir($dir) || !is_writable($dir) ) {
			throw new CmsException('Could not create directory, or could not write in directory '.$dir);
		}

		return $dirname;
	}

	public function import()
	{
		$this->validate_template_name();
		$this->validate_stylesheet_name();

		$config = cmsms()->GetConfig();
		$newname = $this->get_new_name();
		$destdir = $this->get_destination_dir();
		$info    = $this->get_design_info();

		// create new design... fill it with info
		$design = new CmsLayoutCollection();
		$design->set_name($newname);
		// todo
		
		// expand URL FILES to become real files
		foreach( $this->_file_map as $key => &$rec ) {
			if( !startswith($key,'__URL::') ) continue;
			if( !isset($rec['data']) || $rec['data'] == '' ) continue;

			$destfile = cms_join_path($config['uploads_url'],'designts',$destdir,$rec['value']);
			file_put_contents($destfile,base64_decode($rec['data']));
			$rec['tpl_url'] = "{uploads_url}/designs/$destdir/{$rec['value']}";
			$rec['css_url'] = "[[uploads_url]]/designs/$destdir/{$rec['value']}";			
		}

		foreach( $this->list_stylesheets() as $one )
    // 3.  create stylesheets
		//     foreach FILE
    //        if url
                replace <key> with [[uploads_url]]/designs/$dirname/$filename
           save stylesheet
           add to design
       4.  create templates
           foreach FILE
              if not CSS
                replace <KEY> with new name
           save template
           add to design
       5.  save design
		*/
	}
} // end of class

#
# EOF
#
?>