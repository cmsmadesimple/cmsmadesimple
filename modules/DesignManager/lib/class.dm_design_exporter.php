<?php
class dm_design_exporter
{
  private $_design;
  private $_tpl_list;
  private $_css_list;
  private $_files;
  private $_image = null;
  private $_description;

  public function __construct(CmsLayoutCollection &$design)
  {
    $this->_design = $design;
  }

  public function get_description()
  {
    if( is_null($this->_description) ) {
      return $this->_design->get_description();
    }
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
	if( $fn == $data ) {
	  return $key;
	}
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
				       if( !startswith($url,'http') || startswith($url,$config['root_url']) || 
					   startswith($url,'[[root_url]]') ) {
					 $sig = $ob->_get_signature($url);
					 $sig = "url(".$sig.")";
					 return $sig;
				       }
				       return $matches[0];
				     },
				     $content);
  
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
	    $new_css_ob = clone $css_ob;
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
	$new_content = $this->_get_sub_templates($tpl_ob->get_content());
	$new_tpl_ob = clone $tpl_ob;
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
	    $new_tpl_ob = new CmsLayoutTemplate;
	    $new_tpl_ob->set_content($tpl);
	    $new_tpl_ob->set_name(substr($name,0,-4));
	    $sig = $this->_get_signature($name,$type);
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
} // end of class

#
# EOF
#
?>