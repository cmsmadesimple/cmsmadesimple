<?php
class dm_design_exporter
{
  private $_design;
  private $_tpl_list;
  private $_css_list;
  private $_files;
  private $_image = null;
  private $_description;
  private $_export_warnings = array();
  private $_export_report_file;
  static  $_mm_types;
  static  $_nav_types;

  private static $_dtd = <<<EOT
<!DOCTYPE design [
  <!ELEMENT design (name,description,generated,cmsversion,template+,stylesheet*,file*)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT description (#PCDATA)>
  <!ELEMENT generated (#PCDATA)>
  <!ELEMENT cmsversion (#PCDATA)>
  <!ELEMENT template (tkey,tname,tdesc,tdata,ttype_originator,ttype_name)>
  <!ELEMENT tkey (#PCDATA)>
  <!ELEMENT tname (#PCDATA)>
  <!ELEMENT tdesc (#PCDATA)>
  <!ELEMENT tdata (#PCDATA)>
  <!ELEMENT ttype_originator (#PCDATA)>
  <!ELEMENT ttype_name (#PCDATA)>
  <!ELEMENT stylesheet (csskey,cssname,cssdesc,cssmediatype,cssmediaquery,cssdata)>
  <!ELEMENT csskey (#PCDATA)>
  <!ELEMENT cssname (#PCDATA)>
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
            if( (!is_array(self::$_mm_types) || count(self::$_mm_types) == 0) && (!is_array(self::$_nav_types) || count(self::$_nav_types) == 0) ) {
                throw new CmsException('Cannot find any Navigation template types (is MenuManager or Navigator installed and enabled?');
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

  public function get_export_report_file()
  {
    return $this->_export_report_file;
  }

  private function _get_report_dir()
  {
    $dir = cms_join_path(TMP_CACHE_LOCATION,'designmanager_export');
    if( !is_dir($dir) ) @mkdir($dir,0777,TRUE);
    return $dir;
  }

  private function _record_export_warning($value,$resolved,$reason)
  {
    $resolved = trim((string) $resolved);
    $key = md5($value.'|'.$resolved.'|'.$reason);
    if( isset($this->_export_warnings[$key]) ) return;

    $entry = array(
      'value' => $value,
      'resolved' => $resolved,
      'reason' => $reason
    );
    $this->_export_warnings[$key] = $entry;

    $msg = 'Design "'.$this->_design->get_name().'" skipped asset "'.$value.'"';
    if( $resolved != '' ) $msg .= ' resolved to "'.$resolved.'"';
    $msg .= ' ('.$reason.')';
    audit('','DesignManager/export',$msg);
  }

  private function _write_export_report()
  {
    $dir = $this->_get_report_dir();
    if( !is_dir($dir) || !is_writable($dir) )
    {
      audit('','DesignManager/export','Unable to write export report to '.$dir);
      return;
    }

    $filename = sprintf(
      '%s-%s.log',
      munge_string_to_url($this->_design->get_name()),
      date('Ymd-His')
    );
    $filename = trim($filename,'-');
    if( $filename == '' ) $filename = 'design-export-'.date('Ymd-His').'.log';
    $report_file = cms_join_path($dir,$filename);

    $lines = array();
    $lines[] = 'CMS Made Simple Design Export Report';
    $lines[] = 'Design: '.$this->_design->get_name();
    $lines[] = 'Generated: '.date('c');
    $lines[] = 'Warnings: '.count($this->_export_warnings);
    $lines[] = '';

    if( count($this->_export_warnings) )
    {
      foreach( $this->_export_warnings as $entry )
      {
        $lines[] = 'Asset: '.$entry['value'];
        if( $entry['resolved'] != '' ) $lines[] = 'Resolved: '.$entry['resolved'];
        $lines[] = 'Reason: '.$entry['reason'];
        $lines[] = '';
      }
    }
    else
    {
      $lines[] = 'No missing or unreadable referenced assets were detected.';
    }

    $content = implode(PHP_EOL,$lines).PHP_EOL;
    if( @file_put_contents($report_file,$content) === FALSE )
    {
      audit('','DesignManager/export','Failed to write export report to '.$report_file);
      return;
    }

    $this->_export_report_file = $report_file;
    audit('','DesignManager/export','Export report written to '.$report_file.' with '.count($this->_export_warnings).' warning(s)');
  }

  private function _resolve_asset_path($value,&$nvalue,&$fn,&$reason = null)
  {
    $config = \cms_config::get_instance();
    $smarty = cmsms()->GetSmarty();
    $nvalue = $value;
    $fn = null;
    $reason = null;

    try
    {
      if( strpos($value,'[[') !== FALSE )
      {
        $old_left = $smarty->left_delimiter;
        $old_right = $smarty->right_delimiter;
        try
        {
          $smarty->left_delimiter = '[[';
          $smarty->right_delimiter = ']]';
          $nvalue = $smarty->fetch('string:'.$value);
        }
        finally
        {
          $smarty->left_delimiter = $old_left;
          $smarty->right_delimiter = $old_right;
        }
      }
      else if( strpos($value,'{') !== FALSE )
      {
        $nvalue = $smarty->fetch('string:'.$value);
      }
    }
    catch( \Throwable $e )
    {
      $reason = 'asset path could not be resolved from template expression';
      return FALSE;
    }

    $fn = cms_join_path($config['root_path'],$nvalue);
    if( startswith($nvalue,'/') && !startswith($nvalue,'//') )
    {
      $fn = cms_join_path($config['root_path'],$nvalue);
    }
    elseif( startswith($nvalue,$config['root_url']) )
    {
      $fn = str_replace($config['root_url'],$config['root_path'],$nvalue);
    }

    if( !is_file($fn) )
    {
      $reason = 'file does not exist';
      return FALSE;
    }

    if( !is_readable($fn) )
    {
      $reason = 'file is not readable';
      return FALSE;
    }

    return TRUE;
  }

  private function _get_url_signature($url)
  {
    $nvalue = null;
    $fn = null;
    $reason = null;
    if( !$this->_resolve_asset_path($url,$nvalue,$fn,$reason) )
    {
      $this->_record_export_warning($url,$fn,$reason);
      return FALSE;
    }

    return $this->_get_signature($url);
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
        $sig = '__'.$type.',,'.md5($fn).'__';
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
                                                 $sig = $ob->_get_url_signature($url);
                                                 if( $sig === FALSE ) return $matches[0];
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

        $temp_fix_cmsselflink = function($matches) use ($ob) {
            // GCB (required name param)
            $out = preg_replace_callback("/href\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
                                         function($matches) use ($ob) {
                                             return str_replace($matches[1],'ignore::'.$matches[1],$matches[0]);
                                         },$matches[0]);
            return $out;
        };

        $undo_fix_cmsselflink = function($matches) use ($ob) {
            // GCB (required name param)
            $out = preg_replace_callback("/href\s*=[\\\"']{0,1}(ignore\:\:[a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
                                         function($matches) use ($ob) {
                                             $rep = substr($matches[1],8);
                                             return str_replace($matches[1],$rep,$matches[0]);
                                         },$matches[0]);
            return $out;
        };

        // replace cms_selflink stuff with an ignore
        $regex='/\{cms_selflink.*\}/';
        $content = preg_replace_callback( $regex, $temp_fix_cmsselflink, $content );

        // compars root url to another url
        // handle relative paths
        // and no schema
        $is_same_host = function(cms_url $url1,cms_url $url2) {
            if( $url1->get_host() != $url2->get_host() && $url2->get_host() != '') return FALSE;
            if( $url1->get_port() != $url2->get_port() ) return FALSE;
            if( $url1->get_scheme() != $url2->get_scheme() && $url2->get_scheme() != '') return FALSE;
            $p1 = $url1->get_path();
            $p2 = $url2->get_path();
            if( $p1 != $p2 && !startswith($p2,$p1) ) return FALSE;
            return TRUE;
        };

        $ob = &$this;
        $types = array("href", "src", "url");
        foreach( $types as $type ) {
            $innerT = '[a-z0-9:?=&@/._-]+?';
            $content = preg_replace_callback("|$type\=([\"'`])(".$innerT.")\\1|i",
                                             function($matches) use ($ob,$type,&$is_same_host) {
                                                 $config = cmsms()->GetConfig();
                                                 $url = $matches[2];
                                                 $root_url = new cms_url($config['root_url']);
                                                 $the_url = new cms_url($url);
                                                 if( !startswith($url,'ignore::') && $is_same_host($root_url,$the_url) ) {
                                                     $sig = $ob->_get_url_signature($url);
                                                     if( $sig === FALSE ) return $matches[0];
                                                     //return $sig;
                                                     return " $type=\"$sig\"";
                                                 }
                                                 return $matches[0];
                                             },
                                             $content);
        }

        // remove ignore stuff on cms_selflink
        $regex='/\{cms_selflink.*\}/';
        $content = preg_replace_callback( $regex, $undo_fix_cmsselflink, $content );

        return $content;
    }

    public function parse_stylesheets()
    {
        if( is_null($this->_css_list) ) {
            $this->_css_list = array();

            $csslist = $this->_design->get_stylesheets();
            if( is_array($csslist) && count($csslist) > 0 ) {
                foreach( $csslist as $css_id ) {
                    $css_ob = CmsLayoutStylesheet::load($css_id);

                    $new_content = $this->_parse_css_for_urls($css_ob->get_content());
                    $sig = $this->_get_signature($css_ob->get_name(),'CSS');
                    $new_css_ob = clone $css_ob;
                    $new_css_ob->set_name($sig);
                    $new_css_ob->set_content($new_content);

                    if( !is_array($this->_css_list) ) $this->_css_list = array();
                    $this->_css_list[] = array('name'=>$css_ob->get_name(),'obj'=>$new_css_ob);
                }
            }
        }
    }

    public function list_stylesheets()
    {
        $this->parse_stylesheets();
        if( is_array($this->_css_list) && count($this->_css_list) ) {
            $out = array();
            foreach( $this->_css_list as $rec ) {
                $out[] = $rec['obj']->get_name();
            }
            return $out;
        }
    }

    public function _add_template($name,$type = 'TPL')
    {
        switch( $type ) {
        case 'TPL':
            $tpl_ob = null;
            if( is_object($name) ) {
                $tpl_ob = $name;
                $name = $tpl_ob->get_name();
            }
            else {
                $tpl_ob = CmsLayoutTemplate::load($name);
            }
            $sig = $this->_get_signature($tpl_ob->get_name(),$type);

            // recursion...
            $new_content = $this->_parse_tpl_urls($tpl_ob->get_content());
            $new_content = $this->_get_sub_templates($new_content);
            $sig = $this->_get_signature($tpl_ob->get_name(),'TPL');
            $new_tpl_ob = clone $tpl_ob;
            $new_tpl_ob->set_name($sig);
            $new_tpl_ob->set_content($new_content);

            if( !is_array($this->_tpl_list) ) $this->_tpl_list = array();
            $this->_tpl_list[$sig] = array('name'=>$name,'obj'=>$new_tpl_ob);
            return $sig;

        case 'MM':
            // MenuManager file template
            $mod = cms_utils::get_module('MenuManager');
            if( !$mod ) throw new \CmsException('MenuManager file template specified, but MenuManager could not be loaded.');

            $tpl = $mod->GetTemplateFromFile($name);
            if( !$tpl ) throw new \CmsException('Could not find MenuMaager template '.$name);

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
            $this->_tpl_list[$sig] = array('name'=>$name,'obj'=>$new_tpl_ob);
            return $sig;
        } // switch
    }

    private function _get_sub_templates($template)
    {
        $ob = &$this;

        $replace_mm = function($matches) use ($ob) {
            // Menu Manager (optional template param)
            $mod = \cms_utils::get_module('MenuManager');
            if( !$mod ) throw new \CmsException('MenuManager tag specified, but MenuManager could not be loaded.');

            $have_template = false;
            $out = preg_replace_callback("/template\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
                                         function($matches) use ($ob,&$have_template) {
                                             $the_tpl = $matches[1];
                                             if( ($pos = strpos($matches[1],' ')) !== FALSE )  $the_tpl = substr($matches[1],0,$pos);
                                             $type = 'TPL';
                                             if( endswith($the_tpl,'.tpl') ) $type = 'MM';
                                             $sig = $ob->_add_template($the_tpl,$type);
                                             $have_template = TRUE;
                                             $out = str_replace($the_tpl,$sig,$matches[0]);
                                             return $out;
                                         },$matches[0]);

            if( !$have_template ) {
                // MenuManager default template.
                $tpl = CmsLayoutTemplate::load_dflt_by_type('MenuManager::navigation');
                $sig = $ob->_add_template($tpl->get_name());
                $out = substr($matches[0],0,-1).' template=\''.$sig.'\'}';
            }
            return $out;
        };

        $replace_navigator = function($matches) use ($ob) {
            // Navigator (optional template param)
            $mod = \cms_utils::get_module('Navigator');
            if( !$mod ) throw new \CmsException('Navigator tag specified, but Navigator could not be loaded.');

            $have_template = false;
            $out = preg_replace_callback("/template\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
                                         function($matches) use ($ob,&$have_template) {
                                             $have_template = TRUE;
                                             $sig = $ob->_add_template($matches[1]);
                                             return str_replace($matches[1],$sig,$matches[0]);
                                         },$matches[0]);
            if( !$have_template ) {
                // Navigator default template.
                $tpl = CmsLayoutTemplate::load_dflt_by_type('Navigator::navigation');
                $sig = $ob->_add_template($tpl->get_name());
                $out = substr($matches[0],0,-1).' template=\''.$sig.'\'}';
            }
            return $out;
        };

        $replace_gcb = function($matches) use ($ob) {
            // GCB (required name param)
            $out = preg_replace_callback("/name\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
                                         function($matches) use ($ob) {
                                             $sig = $ob->_add_template($matches[1]);
                                             return str_replace($matches[1],$sig,$matches[0]);
                                         },$matches[0]);
            return $out;
        };

        $replace_include = function($matches) use ($ob) {
            // include (required file param)
            $out = preg_replace_callback("/file\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
                                         function($matches) use ($ob) {
                                             if( !startswith($matches[1],'cms_template:') ) {
                                                 throw new \CmsException('Only templates that use {include} with cms_template resources can be exported.');
                                             }
                                             $tpl = substr($matches[1],strlen('cms_template:'));
                                             $sig = $ob->_add_template($tpl);
                                             return str_replace($matches[1],'cms_template:'.$sig,$matches[0]);
                                         },$matches[0]);
            return $out;
        };

        $regex='/\{menu.*\}/';
        $template = preg_replace_callback( $regex, $replace_mm, $template );

        $regex='/\{.*MenuManager.*\}/';
        $template = preg_replace_callback( $regex, $replace_mm, $template );

        $regex='/\{.*Navigator.*\}/';
        $template = preg_replace_callback( $regex, $replace_navigator, $template );

        $regex='/\{global_content.*\}/';
        $template = preg_replace_callback( $regex, $replace_gcb, $template );

        $regex='/\{include.*\}/';
        $template = preg_replace_callback( $regex, $replace_include, $template );

        return $template;
    }

    public function parse_templates()
    {
        if( is_null($this->_tpl_list) ) {
            $this->_tpl_list = array();

            $idlist = $this->_design->get_templates();
            if( is_array($idlist) && count($idlist) > 0 ) {
                $tpllist = \CmsLayoutTemplate::load_bulk($idlist);
                if( count($idlist) != count($tpllist) ) throw new \CmsException('Internal error... could not directly load all of the templates associated with this design');
                foreach( $tpllist as $tpl ) {
                    $this->_add_template($tpl);
                }
            }
        }
    }

    public function list_templates()
    {
        $this->parse_templates();
        if( is_array($this->_tpl_list) && count($this->_tpl_list) ) {
            $out = array();
            foreach( $this->_tpl_list as $rec ) {
                $out[] = $rec['obj']->get_name();
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

    private function _xml_output_template(CmsLayoutTemplate $tpl,$name,$lvl = 0)
    {
        if( $tpl->get_content() == '' ) throw new CmsException('Cannot export empty template');
        $output = $this->_open_tag('template',$lvl);
        $output .= $this->_output('tkey',$tpl->get_name(),$lvl+1);
        $output .= $this->_output_data('tname',$name,$lvl+1);
        $output .= $this->_output_data('tdesc',$tpl->get_description(),$lvl+1);
        $output .= $this->_output_data('tdata',$tpl->get_content(),$lvl+1);
        if( !$tpl->get_type_id() ) throw new \CmsException('Cannot get template type for '.$tpl->get_name());

        $type = CmsLayoutTemplateType::load($tpl->get_type_id());
        $output .= $this->_output_data('ttype_originator',$type->get_originator(),$lvl+1);
        $output .= $this->_output_data('ttype_name',$type->get_name(),$lvl+1);
        $output .= $this->_close_tag('template',$lvl);
        return $output;
    }

    private function _xml_output_stylesheet(CmsLayoutStylesheet $css,$name,$lvl = 0)
    {
        if( $css->get_content() == '' ) throw new CmsException('Cannot export empty stylesheet');
        $output = $this->_open_tag('stylesheet',$lvl);
        $output .= $this->_output('csskey',$css->get_name(),$lvl+1);
        $output .= $this->_output_data('cssname',$name,$lvl+1);
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
        $p = strpos($key,',,');
        $nkey = substr($key,0,$p);
        $nkey = substr($nkey,2);

        $output = $this->_open_tag('file',$lvl);
        $output .= $this->_output('fkey',$key,$lvl+1);
        switch($nkey) {
        case 'URL':
            // javascript file or image or something.
            // could have smarty syntax.
            $nvalue = null;
            $fn = null;
            $reason = null;
            if( !$this->_resolve_asset_path($value,$nvalue,$fn,$reason) ) {
                $this->_record_export_warning($value,$fn,$reason);
                return '';
            }

            $data = @file_get_contents($fn);
            if( $data === FALSE ) {
                $this->_record_export_warning($value,$fn,'file could not be read');
                return '';
            }

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
            break;
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
        $output .= $this->_output_data('description',$this->_design->get_description());
        $output .= $this->_output_data('generated',time());
        $output .= $this->_output_data('cmsversion',CMS_VERSION);
        foreach( $this->_tpl_list as $rec ) {
            $output .= $this->_xml_output_template($rec['obj'],$rec['name'],1);
        }
        foreach( $this->_css_list as $rec ) {
            $output .= $this->_xml_output_stylesheet($rec['obj'],$rec['name'],1);
        }
        if( is_array($this->_files) && count($this->_files) ) {
            foreach( $this->_files as $key => $value ) {
                $output .= $this->_xml_output_file($key,$value,1);
            }
        }
        $output .= $this->_close_tag('design',0);
        $this->_write_export_report();
        return $output;
    }
} // end of class

#
# EOF
#
?>
