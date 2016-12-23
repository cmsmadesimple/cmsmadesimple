<?php

final class AdminSearch_template_slave extends AdminSearch_slave
{
    public function get_name()
    {
        $mod = cms_utils::get_module('AdminSearch');
        return $mod->Lang('lbl_template_search');
    }

    public function get_description()
    {
        $mod = cms_utils::get_module('AdminSearch');
        return $mod->Lang('desc_template_search');
    }

    public function check_permission()
    {
        $userid = get_userid();
        return check_permission($userid,'Modify Templates');
    }

    private function check_tpl_match(\CmsLayoutTemplate $tpl)
    {
        if( strpos($tpl->get_name(),$this->get_text()) !== FALSE ) return TRUE;
        if( strpos($tpl->get_content(),$this->get_text()) !== FALSE ) return TRUE;
        if( $this->search_descriptions() && strpos($tpl->get_description(),$this->get_text()) !== FALSE ) return TRUE;
        return FALSE;
    }

    private function get_mod()
    {
        static $_mod;
        if( !$_mod ) $_mod = \cms_utils::get_module('DesignManager');
        return $_mod;
    }

    private function get_tpl_match_info(\CmsLayoutTemplate $tpl)
    {
        $one = $tpl->get_id();
        $intext = $this->get_text();
        $text = '';
        $content = $tpl->get_content();
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
        $url = $this->get_mod()->create_url( 'm1_','admin_edit_template','', [ 'tpl'=>$one ] );
        $url = str_replace('&amp;','&',$url);
        $title = $tpl->get_name();
        if( $tpl->has_content_file() ) {
            $config = \cms_config::get_instance();
            $file = $tpl->get_content_filename();
            $title = $tpl->get_name().' ('.cms_relative_path($file,$config['root_path']).')';
        }
        $tmp = [ 'title'=>$title,
                 'description'=>AdminSearch_tools::summarize($tpl->get_description()),
                 'edit_url'=>$url,'text'=>$text ];
        return $tmp;
    }

    public function get_matches()
    {
        $db = cmsms()->GetDb();
        $mod = $this->get_mod();
        // get all of the template ids
        $sql = 'SELECT id FROM '.CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME.' ORDER BY name ASC';
        $all_ids = $db->GetCol($sql);
        $output = [];
        if( count($all_ids) ) {
            $chunks = array_chunk($all_ids,15);
            foreach( $chunks as $chunk ) {
                $tpl_list = CmsLayoutTemplate::load_bulk($chunk);
                foreach( $tpl_list as $tpl ) {
                    if( $this->check_tpl_match($tpl) ) $output[] = $this->get_tpl_match_info($tpl);
                }
            }
        }
        return $output;
    }
} // end of class
