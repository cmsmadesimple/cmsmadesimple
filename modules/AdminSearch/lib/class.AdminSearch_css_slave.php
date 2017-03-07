<?php

final class AdminSearch_css_slave extends AdminSearch_slave
{
    public function get_name()
    {
        $mod = cms_utils::get_module('AdminSearch');
        return $mod->Lang('lbl_css_search');
    }

    public function get_description()
    {
        $mod = cms_utils::get_module('AdminSearch');
        return $mod->Lang('desc_css_search');
    }

    public function check_permission()
    {
        $userid = get_userid();
        return check_permission($userid,'Manage Stylesheets');
    }

    private function check_css_matches(\CmsLayoutStylesheet $css)
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

    private function get_css_match_info(\CmsLayoutStylesheet $css)
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
        $url = $this->get_mod()->create_url( 'm1_','admin_edit_css','', [ 'css'=>$one ] );
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
        $db = cmsms()->GetDb();
        $mod = $this->get_mod();
        // get all of the stylesheet ids
        $sql = 'SELECT id FROM '.CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME.' ORDER BY name ASC';
        $all_ids = $db->GetCol($sql);
        $output = [];
        if( count($all_ids) ) {
            $chunks = array_chunk($all_ids,15);
            foreach( $chunks as $chunk ) {
                $css_list = \CmsLayoutStylesheet::load_bulk($chunk);
                foreach( $css_list as $css ) {
                    if( $this->check_css_matches($css) ) $output[] = $this->get_css_match_info($css);
                }
            }
        }
        return $output;
    }
} // end of class
