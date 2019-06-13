<?php
namespace AdminSearch\Slaves;
use AdminSearch;
use AdminSearch_tools;
use DesignManager;
use CMSMS\Database\Connection as Database;
use CmsLayoutStylesheet;

final class StylesheetSlave extends AbstractSlave
{
    private $mod;
    private $dm;
    private $db;

    public function __construct(AdminSearch $mod, DesignManager $dm, Database $db)
    {
        $this->mod = $mod;
        $this->dm = $dm;
        $this->db = $db;
    }

    public function get_name()
    {
        return $this->mod->Lang('lbl_css_search');
    }

    public function get_description()
    {
        return $this->mod->Lang('desc_css_search');
    }

    private function check_css_matches(CmsLayoutStylesheet $css)
    {
        if( strpos($css->get_name(),$this->get_text()) !== FALSE ) return TRUE;
        if( strpos($css->get_content(),$this->get_text()) !== FALSE ) return TRUE;
        if( $this->search_descriptions() && strpos($css->get_description(),$this->get_text()) !== FALSE ) return TRUE;
        return FALSE;
    }

    private function get_mod()
    {
        static $_mod;
        if( !$_mod ) $_mod = \cms_utils::get_module('DesignManager');
        return $_mod;
    }

    private function get_css_match_info(CmsLayoutStylesheet $css)
    {
        $one = $css->get_id();
        $intext = $this->get_text();
        $text = '';
        $content = $css->get_content();
        $pos = strpos($content,$intext);
        if( $pos !== FALSE ) {
            $start = max(0,$pos - 50);
            $end = min(strlen($content),$pos+50);
            $text = substr($content,$start,$end-$start);
            $text = htmlentities($text);
            $text = str_replace($intext,'<span class="search_oneresult">'.$intext.'</span>',$text);
            $text = str_replace("\r",'',$text);
            $text = str_replace("\n",'',$text);
        }
        $url = $this->dm->create_url( 'm1_','admin_edit_css','', [ 'css'=>$one ] );
        $url = str_replace('&amp;','&',$url);
        $title = $css->get_name();
        if( $css->has_content_file() ) {
            $config = \cms_config::get_instance();
            $file = $css->get_content_filename();
            $title = $css->get_name().' ('.cms_relative_path($file,$config['root_path']).')';
        }
        $tmp = [ 'title'=>$title,
                 'description'=>AdminSearch_tools::summarize($css->get_description()),
                 'edit_url'=>$url,'text'=>$text ];
        return $tmp;
    }

    public function get_matches()
    {
        // get all of the stylesheet ids
        $sql = 'SELECT id FROM '.CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME.' ORDER BY name ASC';
        $all_ids = $this->db->GetCol($sql);
        $output = [];
        if( count($all_ids) ) {
            $chunks = array_chunk($all_ids,15);
            foreach( $chunks as $chunk ) {
                $css_list = CmsLayoutStylesheet::load_bulk($chunk);
                foreach( $css_list as $css ) {
                    if( $this->check_css_matches($css) ) $output[] = $this->get_css_match_info($css);
                }
            }
        }
        return $output;
    }
} // end of class
