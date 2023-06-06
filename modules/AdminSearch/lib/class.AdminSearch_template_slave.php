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
        $strposFunctionName = $this->search_casesensitive() ? 'strpos' : 'stripos';
        if( $strposFunctionName((string)$tpl->get_name(),$this->get_text()) !== FALSE ) return TRUE;
        if( $strposFunctionName((string)$tpl->get_content(),$this->get_text()) !== FALSE ) return TRUE;
        if( $this->search_descriptions() && $strposFunctionName((string)$tpl->get_description(),$this->get_text()) !== FALSE ) return TRUE;
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
        $title = $tpl->get_name();
        if( $tpl->has_content_file() ) {
            $config = \cms_config::get_instance();
            $file = $tpl->get_content_filename();
            $title = $tpl->get_name().' ('.cms_relative_path($file,$config['root_path']).')';
        }
        $resultSet = $this->get_resultset($title,AdminSearch_tools::summarize($this->get_description()),$this->get_mod()->create_url( 'm1_','admin_edit_template','', [ 'tpl'=>$one ] ));

        $content = $tpl->get_name();
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[\CmsLangOperations::lang_from_realm('admin','name')] = $this->generate_snippets($content);
        }

        $content = $tpl->get_content();
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[\CmsLangOperations::lang_from_realm('admin','content')] = $this->generate_snippets($content);
        }

        if( $this->search_descriptions()) {
            $content = $tpl->get_description();
            $resultSet->count += $count = $this->get_number_of_occurrences($content);
            if ($this->show_snippets() && $count > 0) {
                $resultSet->locations[\CmsLangOperations::lang_from_realm('admin','description')] = $this->generate_snippets($content);
            }
        }

        return $resultSet;
    }

    public function get_matches()
    {
        $db = cmsms()->GetDb();
        $mod = $this->get_mod();
        // get all of the template ids
        $sql = 'SELECT id FROM '.CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME.' ORDER BY name ASC';
        $all_ids = $db->GetCol($sql);
        $output = [];
        $resultSets = array();
        if( count($all_ids) ) {
            $chunks = array_chunk($all_ids,15);
            foreach( $chunks as $chunk ) {
                $tpl_list = CmsLayoutTemplate::load_bulk($chunk);
                foreach( $tpl_list as $tpl ) {
                    if( $this->check_tpl_match($tpl) ) $resultSets[] = $this->get_tpl_match_info($tpl);
                }
            }
        }
        #processing the results
        foreach ($resultSets as $result_object) {
            $output[] = json_encode($result_object);
        }

        return $output;
    }
} // end of class
