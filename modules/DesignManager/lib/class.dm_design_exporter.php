<?php
class dm_design_exporter
{
  private $_design;
  private $_tpl_list;
  private $_css_list;
  private $_files;
  private $_image = null;
  private $_description;
  static  $_mm_types;
  static  $_nav_types;

  private static $_dtd = <<<EOT
<!DOCTYPE design [
  <!ELEMENT design (name,description,generated,cmsversion,template+,stylesheet+,file+)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT description (#PCDATA)>
  <!ELEMENT generated (#PCDATA)>
  <!ELEMENT cmsversion (#PCDATA)>
  <!ELEMENT template (tkey,tdesc,tdata,ttype_originator,ttype_name)>
  <!ELEMENT tkey (#PCDATA)>
  <!ELEMENT tdesc (#PCDATA)>
  <!ELEMENT tdata (#PCDATA)>
  <!ELEMENT ttype_originator (#PCDATA)>
  <!ELEMENT ttype_name (#PCDATA)>
  <!ELEMENT stylesheet (csskey,cssdesc,cssmediatype,cssmediaquery,cssdata)>
  <!ELEMENT csskey (#PCDATA)>
  <!ELEMENT cssdesc (#PCDATA)>
  <!ELEMENT cssmediatype (#PCDATA)>
  <!ELEMENT cssmediaquery (#PCDATA)>
  <!ELEMENT cssdata (#PCDATA)>
  <!ELEMENT file (fkey,fvalue,fdata?)>
  <!ELEMENT fkey (#PCDATA)>
  <!ELEMENT fvalue (#PCDATA)>
  <!ELEMENT fdata (#PCDATA)>
]>\n
EOT;

  public function __construct(CmsLayoutCollection &$design)
  {
    $this->_design = $design;
    if( !is_array(self::$_mm_types ) ) {
      self::$_mm_types = CmsLayoutTemplateType::load_all_by_originator('MenuManager');
      self::$_nav_types = CmsLayoutTemplateType::load_all_by_originator('Navigator');
      if( !is_array(self::$_mm_types) || count(self::$_mm_types) == 0 || !is_array(self::$_nav_types) || count(self::$_nav_types) == 0 ) {
	throw new CmsException('Cannot find any MenuManager template types (is MenuManager installed and enabled?');
      }
    }
  }

  public function get_description()
  {
    if( is_null($this->_description) ) return $this->_design->get_description();
  }

  public function set_description($text)
  {
    $this->_description = $text;
  }

  /**
   * internal
   */
  public function _get_signature($fn,$type = 'URL')
  {
    if( is_array($this->_files) ) {
      foreach( $this->_files as $key => $data ) {
	if( $fn == $data ) return $key;
      }
    }
    $sig = '__'.$type.'::'.md5($fn).'__';
    if( !is_array($this->_files) ) $this->_files = array();
    $this->_files[$sig] = $fn;
    return $sig;
  }

  private function _parse_css_for_urls($content)
  {
    $ob = &$this;
    $regex='/url\s*\(\"*(.*)\"*\)/i';
    $content = preg_replace_callback($regex,
				     function($matches) use ($ob) {
				       $config = cmsms()->GetConfig();
				       $url = $matches[1];
				       if( !startswith($url,'http') || startswith($url,$config['root_url']) || startswith($url,'[[root_url]]') ) {
					 $sig = $ob->_get_signature($url);
					 $sig = "url(".$sig.")";
					 return $sig;
				       }
				       return $matches[0];
				     },
				     $content);

    return $content;
  }

  private function _parse_tpl_urls($content)
  {
    $ob = &$this;
    $types = array("href", "src", "url");
    foreach( $types as $type ) {
      $innerT = '[a-z0-9:?=&@/._-]+?';
      $content = preg_replace_callback("|$type\=([\"'`])(".$innerT.")\\1|i",
				       function($matches) use ($ob,$type) {
					 $config = cmsms()->GetConfig();
					 $url = $matches[2];
					 if( !startswith($url,'http') || startswith($url,$config['root_url']) || startswith($url,'{root_url}') ) {
					   $sig = $ob->_get_signature($url);
					   //return $sig;
					   return " $type=\"$sig\"";
					 }
					 return $matches[0];
				       },
				       $content);
    }
    return $content;
  }

  public function parse_stylesheets()
  {
    if( is_null($this->_css_list) ) {
      $this->_css_list = array();

      $csslist = $this->_design->get_stylesheets();
      if( is_array($csslist) && count($csslist) > 0 ) {
	foreach( $csslist as $css_id ) {
	  try {
	    $css_ob = CmsLayoutStylesheet::load($css_id);

	    $new_content = $this->_parse_css_for_urls($css_ob->get_content());
	    $sig = $this->_get_signature($css_ob->get_name(),'CSS');
	    $new_css_ob = clone $css_ob;
	    $new_css_ob->set_name($sig);
	    $new_css_ob->set_content($new_content);

	    if( !is_array($this->_css_list) ) $this->_css_list = array();
	    $this->_css_list[] = $new_css_ob;
	  }
	  catch( CmsException $e ) {
	    // do nothing.
	  }
	}
      }
    }
  }

  public function list_stylesheets()
  {
    $this->parse_stylesheets();
    if( is_array($this->_css_list) && count($this->_css_list) ) {
      $out = array();
      foreach( $this->_css_list as $one ) {
	$out[] = $one->get_name();
      }
      return $out;
    }
  }

  public function _add_template($name,$type = 'TPL')
  {
    try {
      switch( $type ) {
      case 'TPL':
	$tpl_ob = CmsLayoutTemplate::load($name);
	$sig = $this->_get_signature($tpl_ob->get_name(),$type);

	// recursion...
	$new_content = $this->_parse_tpl_urls($tpl_ob->get_content());
	$new_content = $this->_get_sub_templates($new_content);
	$sig = $this->_get_signature($tpl_ob->get_name(),'TPL');
	$new_tpl_ob = clone $tpl_ob;
	$new_tpl_ob->set_name($sig);
	$new_tpl_ob->set_content($new_content);

	if( !is_array($this->_tpl_list) ) $this->_tpl_list = array();
	$this->_tpl_list[$sig] = $new_tpl_ob;
	return $sig;

      case 'MM':
	// MenuManager file template
	$mod = cms_utils::get_module('MenuManager');
	if( $mod ) {
	  $tpl = $mod->GetTemplateFromFile($name);
	  if( $tpl ) {
	    // create a new CmsLayoutTemplate object for this template
	    // and add it to the list.
	    // notice we don't recurse.
	    $tpl = $this->_parse_tpl_urls($tpl);
	    $new_tpl_ob = new CmsLayoutTemplate;
	    $new_tpl_ob->set_content($tpl);
	    $name = substr($name,0,-4);
	    $type = 'TPL';
	    $sig = $this->_get_signature($name,$type);
	    $new_tpl_ob->set_name($sig);
	    // it's a menu manager template
	    // we need to get a 'type' for this.
	    $new_tpl_ob->set_type(self::$_mm_types[0]);
	    $this->_tpl_list[$sig] = $new_tpl_ob;
	    return $sig;
	  }
	}
      } // switch
    }
    catch( CmsException $e ) {
    }
  }

  private function _get_sub_templates($template)
  {
    $ob = &$this;

    $replace_fn = function($matches) use ($ob) {
      $out = preg_replace_callback("/template\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
				   function($matches) use ($ob) {
				     $type = 'TPL';
				     if( endswith($matches[1],'.tpl') ) $type = 'MM';
				     $sig = $ob->_add_template($matches[1],$type);
				     return str_replace($matches[1],$sig,$matches[0]);
				   },$matches[0]);
      return $out;
    };

    $replace_fn2 = function($matches) use ($ob) {
      $out = preg_replace_callback("/name\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
				   function($matches) use ($ob) {
				     $sig = $ob->_add_template($matches[1]);
				     return str_replace($matches[1],$sig,$matches[0]);
				   },$matches[0]);
      return $out;
    };

    $replace_fn3 = function($matches) use ($ob) {
      $out = preg_replace_callback("/file\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
				   function($matches) use ($ob) {
				     if( !startswith($matches[1],'cms_template:') ) return $matches[0];

				     $tpl = substr($matches[1],strlen('cms_template:'));
				     $sig = $ob->_add_template($tpl);
				     return str_replace($matches[1],'cms_template:'.$sig,$matches[0]);
				   },$matches[0]);
      return $out;
    };

    $regex='/\{menu.*\}/';
    $template = preg_replace_callback( $regex, $replace_fn, $template );

    $regex='/\{.*MenuManager.*\}/';
    $template = preg_replace_callback( $regex, $replace_fn, $template );

    $regex='/\{.*Navigator.*\}/';
    $template = preg_replace_callback( $regex, $replace_fn2, $template );

    $regex='/\{global_content.*\}/';
    $template = preg_replace_callback( $regex, $replace_fn2, $template );

    $regex='/\{include.*\}/';
    $template = preg_replace_callback( $regex, $replace_fn3, $template );

    return $template;
  }

  public function parse_templates()
  {
    if( is_null($this->_tpl_list) ) {
      $this->_tpl_list = array();

      $tpllist = $this->_design->get_templates();
      if( is_array($tpllist) && count($tpllist) > 0 ) {
	foreach( $tpllist as $tpl_id ) {
	  $this->_add_template($tpl_id);
	}
      }
    }
  }

  public function list_templates()
  {
    $this->parse_templates();
    if( is_array($this->_tpl_list) && count($this->_tpl_list) ) {
      $out = array();
      foreach( $this->_tpl_list as $one ) {
	$out[] = $one->get_name();
      }
      return $out;
    }
  }

  public function list_files()
  {
    $this->parse_stylesheets();
    $this->parse_templates();
    if( is_array($this->_files) && count($this->_files) ) return $this->_files;
  }

  private function _open_tag($elem,$lvl = 1)
  {
    return str_repeat('  ',$lvl)."<{$elem}>\n";
  }

  private function _close_tag($elem,$lvl = 1)
  {
    return str_repeat('  ',$lvl)."</{$elem}>\n";
  }

  private function _output($elem,$txt,$lvl = 1)
  {
    return str_repeat('  ',$lvl).'<'.$elem.'>'.$txt.'</'.$elem.">\n";
  }

  private function _output_data($elem,$data,$lvl = 1)
  {
    $data = '<![CDATA['.base64_encode($data).']]>';
    return $this->_output($elem,$data,$lvl);
  }

  private function _xml_output_template(CmsLayoutTemplate $tpl,$lvl = 0)
  {
    if( $tpl->get_content() == '' ) throw new CmsException('Cannot export empty template');
    $output = $this->_open_tag('template',$lvl);
    $output .= $this->_output('tkey',$tpl->get_name(),$lvl+1);
    $output .= $this->_output_data('tdesc',$tpl->get_description(),$lvl+1);
    $output .= $this->_output_data('tdata',$tpl->get_content(),$lvl+1);
    if( !$tpl->get_type_id() ) throw new \CmsException('Cannot get template type for '.$tpl->get_name());

    $type = CmsLayoutTemplateType::load($tpl->get_type_id());
    $output .= $this->_output_data('ttype_originator',$type->get_originator(),$lvl+1);
    $output .= $this->_output_data('ttype_name',$type->get_name(),$lvl+1);
    $output .= $this->_close_tag('template',$lvl);
    return $output;
  }

  private function _xml_output_stylesheet(CmsLayoutStylesheet $css,$lvl = 0)
  {
    if( $css->get_content() == '' ) throw new CmsException('Cannot export empty stylesheet');
    $output = $this->_open_tag('stylesheet',$lvl);
    $output .= $this->_output('csskey',$css->get_name(),$lvl+1);
    $output .= $this->_output_data('cssdesc',$css->get_description(),$lvl+1);
    $output .= $this->_output_data('cssmediatype',implode(',',$css->get_media_types()),$lvl+1);
    $output .= $this->_output_data('cssmediaquery',$css->get_media_query(),$lvl+1);
    $output .= $this->_output_data('cssdata',$css->get_content(),$lvl+1);
    $output .= $this->_close_tag('stylesheet',$lvl);
    return $output;
  }

  private function _xml_output_file($key,$value,$lvl = 0)
  {
      if( !startswith($key,'__') || !endswith($key,'__') ) return; // invalid
      $p = strpos($key,':');
      $nkey = substr($key,0,$p);
      $nkey = substr($nkey,2);

      $mod = cms_utils::get_module('DesignManager');
      $smarty = cmsms()->GetSmarty();
      $output = $this->_open_tag('file',$lvl);
      $output .= $this->_output('fkey',$key,$lvl+1);
      switch($nkey) {
      case 'URL':
          // javascript file or image or something.
          // could have smarty syntax.
          $nvalue = $value;
          if( strpos($value,'[[') !== FALSE ) {
              // smarty syntax with [[ and ]] as delimiters
              $smarty->left_delimiter = '[[';
              $smarty->right_delimiter = ']]';
              $nvalue = $smarty->fetch('string:'.$value);
              $smarty->left_delimiter = '{';
              $smarty->right_delimiter = '}';
          }
          else if( strpos('{',$value) !== FALSE ) {
              // smarty syntax with { and } as delimiters
              $nvalue = $smarty->fetch('string:'.$value);
          }

          // now, it should be a full URL, or start at /
          // gotta convert it to a file.
          $config = cmsms()->GetConfig();
          // assumes it's a filename relative to root.
          $fn = cms_join_path($config['root_path'],$nvalue);
          if( startswith($nvalue,'/') ) {
              $fn = cms_join_path($config['root_path'],$nvalue);
          } elseif( startswith($nvalue,$config['root_url']) ) {
              $fn = str_replace($config['root_url'],$config['root_path'],$nvalue);
          }

          if( !file_exists($fn) ) throw new CmsException($mod->Lang('error_nophysicalfile',$value));

          $data = file_get_contents($fn);
          if( strlen($data) == 0 ) throw new CmsException('No data found for '.$value);

          $nvalue = basename($nvalue);
          $output .= $this->_output('fvalue',$nvalue,$lvl+1);
          $output .= $this->_output_data('fdata',$data,$lvl+1);
          break;

      case 'TPL':
          // template signature...
          // just need the key and value.
          $output .= $this->_output('fvalue',$value,$lvl+1);
          break;

      case 'CSS':
          // stylesheet signature
          // just need the key and value.
          $output .= $this->_output('fvalue',$value,$lvl+1);
          break;

      case 'MM':
          // menu manager file template
          // just need the key and value.
          $output .= $this->_output('fvalue',$value,$lvl+1);
          break;

      default:
          return;
      }
      $output .= $this->_close_tag('file',$lvl);
      return $output;
  }

  public function get_xml()
  {
    $this->parse_stylesheets();
    $this->parse_templates();

    $output = '<?xml version="1.0" encoding="ISO-8859-1"?>';
    $output .= self::$_dtd;
    $output .= $this->_open_tag('design',0);
    $output .= $this->_output('name',$this->_design->get_name());
    $output .= $this->_output('description',htmlentities($this->_design->get_description())); // todo: encode stuff here
    $output .= $this->_output('generated',time());
    $output .= $this->_output('cmsversion',CMS_VERSION);
    foreach( $this->_tpl_list as $one ) {
      $output .= $this->_xml_output_template($one,1);
    }
    foreach( $this->_css_list as $one ) {
      $output .= $this->_xml_output_stylesheet($one,1);
    }
    if( count($this->_files) ) {
        foreach( $this->_files as $key => $value ) {
            $output .= $this->_xml_output_file($key,$value,1);
        }
    }
    $output .= $this->_close_tag('design',0);
    return $output;
  }
} // end of class

#
# EOF
#
?>
