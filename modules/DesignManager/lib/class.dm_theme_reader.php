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

class dm_theme_reader extends dm_reader_base
{
  private $_xml;
  private $_scanned;
  private $_design_info = array();
  private $_tpl_info = array();
  private $_css_info = array();
  private $_ref_map = array();

  public function __construct($fn)
  {
    $this->_xml = new dm_xml_reader();
    $this->_xml->open($fn);
    //$this->_xml->SetParserProperty(XMLReader::VALIDATE,TRUE);
  }

  private function _scan()
  {
    $in = array();
    $cur_key = null;

    $__get_in = function() use ($in) {
      global $in;
      if( ($n = count($in)) ) return $in[$n-1];
    };

    if( $this->_scanned ) return;

    $cur_key = null;
    while( $this->_xml->read() ) {
      switch( $this->_xml->nodeType ) {
        case XmlReader::ELEMENT:
          switch( $this->_xml->localName ) {
          case 'theme':
          case 'template':
          case 'assoc':
          case 'stylesheet':
          case 'reference':
          case 'mmtemplate':
            $in[] = $this->_xml->localName;
            break;

          case 'name':
            if( $__get_in() != 'theme' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_design_info['name'] = $this->_xml->value;
            break;

          case 'tname':
            if( $__get_in() != 'template' ) {
              // validity error.
            }
            $this->_xml->read();
            $cur_key = $this->_xml->value;
            if( !isset($this->_tpl_info[$cur_key]) ) $this->_tpl_info[$cur_key] = array();
            if( isset($this->_tpl_info[$cur_key]) ) {
              // error, duplicate template name in XML file
            }
            $this->_tpl_info[$cur_key]['name'] = $cur_key;
            $p = strpos($cur_key,'.');
            if( $p !== FALSE ) {
              $tmp = substr($cur_key,0,$p);
              $this->_tpl_info[$cur_key]['name'] = $cur_key;
            }
            break;

          case 'tdata':
            if( $__get_in() != 'template' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_tpl_info[$cur_key]['data'] = $this->_xml->value;
            break;

          case 'mmtemplate_name':
            if( $__get_in() != 'template' ) {
              // validity error.
            }
            $this->_xml->read();
            $cur_key = $this->_xml->value;
            if( !isset($this->_tpl_info[$cur_key]) ) $this->_tpl_info[$cur_key] = array();
            if( isset($this->_tpl_info[$cur_key]) ) {
              // error, duplicate template name in XML file
            }
            $this->_tpl_info[$cur_key]['name'] = $cur_key;
            $this->_tpl_info[$cur_key]['type'] = 'MM';
            $p = strpos($cur_key,'.');
            if( $p !== FALSE ) {
              $tmp = substr($cur_key,0,$p);
              $this->_tpl_info[$cur_key]['name'] = $tmp;
            }
            break;

          case 'mmtemplate_data':
            if( $__get_in() != 'template' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_tpl_info[$cur_key]['data'] = $this->_xml->value;
            break;

          case 'cssname':
            if( $__get_in() != 'stylesheet' ) {
              // validity error.
            }
            $this->_xml->read();
            $cur_key = $this->_xml->value;
            if( !isset($this->_css_info[$cur_key]) ) $this->_css_info[$cur_key] = array();
            if( isset($this->_css_info[$cur_key]) ) {
              // error, duplicate stylesheet name in XML file
            }
            $this->_css_info[$cur_key]['name'] = $cur_key;
            break;

          case 'cssdata':
            if( $__get_in() != 'stylesheet' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_css_info[$cur_key]['data'] = $this->_xml->value;
            break;

          case 'cssmediatype':
            if( $__get_in() != 'stylesheet' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_css_info[$cur_key]['mediatype'] = $this->_xml->value;
            break;

          case 'refname':
            if( $__get_in() != 'reference' ) {
              // validity error.
            }
            $this->_xml->read();
            $cur_key = $this->_xml->value;
            if( !isset($this->_ref_map[$cur_key]) ) $this->_ref_map[$cur_key] = array();
            if( isset($this->_ref_map[$cur_key]) ) {
              // error, duplicate reference name in XML file
            }
            $this->_ref_map[$cur_key]['name'] = $cur_key;
            break;

          case 'refdata':
            if( $__get_in() != 'reference' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_ref_map[$cur_key]['data'] = $this->_xml->value;
            break;

          case 'reflocation':
            if( $__get_in() != 'reference' ) {
              // validity error.
            }
            $this->_xml->read();
            $this->_ref_map[$cur_key]['location'] = $this->_xml->value;
            break;
          }
          break;

        case XmlReader::END_ELEMENT:
          switch( $this->_xml->localName ) {
          case 'theme':
          case 'template':
          case 'stylesheet':
          case 'assoc':
          case 'reference':
          case 'mmtemplate':
            if( count($in) ) {
              array_pop($in);
            }
            $cur_key = null;
          }
          break;
      }
    }

    $this->_scanned = TRUE;
  }

  public function validate()
  {
    $this->_scan();
    if( !isset($this->_design_info['name']) || $this->_design_info['name'] == '' ) {
      throw new CmsException('Invalid XML FILE (test1)');
    }
    if( count($this->_tpl_info) == 0 ) {
      throw new CmsException('Invalid XML FILE (test2)');
    }
    if( count($this->_css_info) == 0 ) {
      throw new CmsException('Invalid XML FILE (test3)');
    }
    // it validates.
  }

  public function get_design_info()
  {
    $this->_scan();

    $mod = cms_utils::get_module('DesignManager');
    $out = $this->_design_info;
    $out['description'] = 'TODO - set theme description';
    $out['generated'] = 0; // not known.
    $out['cmsversion'] = $mod->Lang('unknown'); // a good, early version number.
    return $out;
  }

  public function get_template_list()
  {
    $this->_scan();
    $out = array();
    foreach( $this->_tpl_info as $key => $one ) {
      $rec = array();
      $rec['name'] = $one['name'];
      $rec['desc'] = '';
      $rec['data'] = base64_decode($one['data']);
      if( isset($one['type']) && $one['type'] == 'MM' ) {
        $rec['type_originator'] = 'MenuManager';
        $rec['type_name'] = 'navigation';
      }
      else {
        $rec['type_originator'] = CmsLayoutTemplateType::CORE;
        $rec['type_name'] = 'page';
      }
      $out[$key] = $rec;
    }
    return $out;
  }

  public function get_stylesheet_list()
  {
    $this->_scan();

    $out = array();
    foreach( $this->_css_info as $key => $one ) {
      $rec = array();
      $rec['name'] = $one['name'];
      $rec['desc'] = '';
      $rec['data'] = base64_decode($one['data']);
      $rec['mediatype'] = base64_decode($one['mediatype']);
      $rec['medisaquery'] = '';
      $out[] = $rec;
    }
    return $out;
  }

  protected function get_destination_dir()
  {
    $name = $this->get_new_name();
    $config = cmsms()->GetConfig();
    $dirname = munge_string_to_url($name);
    $dir = cms_join_path($config['uploads_path'],'themes',$dirname);
    @mkdir($dir,0777,TRUE);
    if( !is_dir($dir) || !is_writable($dir) ) {
      throw new CmsException('Could not create directory, or could not write in directory '.$dir);
    }

    return $dirname;
  }

  protected function validate_template_names()
  {
    $this->_scan();

    $templates = CmsLayoutTemplate::template_query(array('as_list'=>1));
    $tpl_names = array_values($templates);

    foreach( $this->_tpl_info as $key => &$rec ) {
      // make sure that this  template doesn't already exist.
      $name = $rec['name'];
      if( in_array($name,$tpl_names) ) {
        $orig_name = $name;
        $n = 1;
        while( $n < 10 ) {
          $n++;
          $new_name = $orig_name.' '.$n;
          if( !in_array($new_name,$tpl_names) ) {
            $rec['name'] = $new_name;
            $rec['old_name'] = $orig_name;
            break;
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

    foreach( $this->_css_info as $key => &$rec ) {
      if( in_array($rec['name'],$css_names) ) {
        // gotta come up with a new name
        $orig_name = $rec['name'];
        $n = 1;
        while( $n < 10 ) {
          $n++;
          $new_name = $orig_name.' '.$n;
          if( !in_array($new_name,$css_names) ) {
            $rec['old_name'] = $rec['name'];
            $rec['name'] = $new_name;
            break;
          }
        }
      }
    }
  }

  public function import()
  {
    $this->validate();
    $this->validate_template_names();
    $this->validate_stylesheet_names();

    $config = cmsms()->GetConfig();
    $newname = $this->get_new_name();
    $destdir = $this->get_destination_dir();
    $ref_map =& $this->_ref_map;

    // part1 .. start creating design..
    $design = new CmsLayoutCollection();
    $design->set_name($newname);
    $description = $this->get_suggested_description();

    if(empty($description))
    {
      $description = $info['description'];
      if( $description ) $description .= "\n----------------------------------------\n";
      $description .= 'Generated '.\locale_ftime('%x %X',$info['generated'])."\n";
      $description .= 'By CMSMS version: '.$info['cmsversion']."\n";
      $description .= 'Imported '.\locale_ftime('%x %X');
    }

    $design->set_description($description);

    // part2 .. expand files.
    foreach( $this->_ref_map as $key => &$rec ) {
      if( !isset($rec['data']) || $rec['data'] == '' ) continue;

      $destfile = cms_join_path($config['uploads_path'],'themes',$destdir,$rec['name']);
      file_put_contents($destfile,base64_decode($rec['data']));
      $rec['tpl_url'] = "{uploads_url}/themes/$destdir/{$rec['name']}";
      $rec['css_url'] = "[[uploads_url]]/themes/$destdir/{$rec['name']}";
    }

    // part3 .. process stylesheets
    $css_info = $this->get_stylesheet_list();
    foreach( $css_info as $name => &$css_rec ) {
      $stylesheet = new CmsLayoutStylesheet();
      $stylesheet->set_name($css_rec['name']);

      $ob = &$this;
      $regex='/url\s*\(\"*(.*)\"*\)/i';
      $css_rec['data'] = preg_replace_callback($regex,
                           function($matches) use ($ob,$ref_map,$destdir) {
                             $config = cmsms()->GetConfig();
                             $url = $matches[1];
                             if( !startswith($url,'http') || startswith($url,$config['root_url']) ||
                                 startswith($url,'[[root_url]]') ) {
                               $bn = basename($url);
                               if( isset($ref_map[$bn]) ) {
                                 $out = $ref_map[$bn]['css_url'];
                                 return 'url('.$out.')';
                               }
                             }
                             return $matches[0];
                           },$css_rec['data']);
      if( isset($css_rec['media_type']) ) $stylesheet->add_media_type($css_rec['mediatype']);
      $stylesheet->set_content($css_rec['data']);
      $stylesheet->save();
      $design->add_stylesheet($stylesheet);
    }

    // part4 .. process templates
    $fn1 = function($matches) use ($ob,&$tpl_info) {
      $out = preg_replace_callback("/template\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
        function($matches) use ($ob,&$tpl_info) {
           if( isset($tpl_info[$matches[1]]) ) {
            $rec = $tpl_info[$matches[1]];
            $out = str_replace($matches[1],$rec['name'],$matches[0]);
            return $out;
          }
          // find the new name and do a substitution
         return $matches[0];
        },$matches[0]);
      return $out;
    };

    $fn2 = function($matches) use ($ob,&$type,$ref_map,$destdir) {
      $config = cmsms()->GetConfig();
      $url = $matches[2];
      if( !startswith($url,'http') || startswith($url,$config['root_url']) || startswith($url,'{root_url}') ) {
        $bn = basename($url);
        if( isset($ref_map[$bn]) ) {
          $out = $ref_map[$bn]['tpl_url'];
          $out = " $type=\"$out\"";
          return $out;
        }
      }
      return $matches[0];
    };

    $tpl_info = $this->get_template_list();
    $have_mm_template = FALSE;
    foreach( $tpl_info as $name => &$tpl_rec ) {
      if( $tpl_rec['type_originator'] == 'MenuManager' ) $have_mm_template = TRUE;

      $template = new CmsLayoutTemplate();
      $template->set_owner(get_userid(FALSE));
      $template->set_name($tpl_rec['name']);

      $types = array("href", "src", "url");
      $content = $tpl_rec['data'];
      foreach( $types as $type ) {
        $tmp_type = $type;
        $innerT = '[a-z0-9:?=&@/._-]+?';
        $content = preg_replace_callback("|$type\=([\"'`])(".$innerT.")\\1|i", $fn2,$content);
      }

      $content = preg_replace('/\{stylesheet/','{cms_stylesheet',$content);

      $regex='/\{menu.*\}/';
      $content = preg_replace_callback( $regex, $fn1, $content );

      $regex='/\{.*MenuManager.*\}/';
      $content = preg_replace_callback( $regex, $fn1, $content );

      $tpl_rec['data'] = $content;
      $template->set_content($content);
      $template->set_type($tpl_rec['type_originator'].'::'.$tpl_rec['type_name']);
      $template->save();
      $design->add_template($template);
    }

    // part5 ... save design
    $design->save();

    // part6 ... Make sure MenuManager is activated.
    if( $have_mm_template ) {
      $modops = ModuleOperations::get_instance();
      $modops->ActivateModule('MenuManager',1);
    }
  }
} // end of class

#
# EOF
#
?>
