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
        $strposFunctionName = $this->search_casesensitive() ? 'strpos' : 'stripos';
        if( $strposFunctionName((string)$css->get_name(),$this->get_text()) !== FALSE ) return TRUE;
        if( $strposFunctionName((string)$css->get_content(),$this->get_text()) !== FALSE ) return TRUE;
        if( $this->search_descriptions() && $strposFunctionName((string)$css->get_description(),$this->get_text()) !== FALSE ) return TRUE;
        return FALSE;
    }

    private function get_mod()
    {
        static $_mod;
        if( !$_mod ) $_mod = \cms_utils::get_module('DesignManager');
        return $_mod;
    }

    private function get_css_match_info(\CmsLayoutStylesheet $css, &$mod)
    {

        $one = $css->get_id();
        $title = $css->get_name();
        if( $css->has_content_file() ) {
            $config = \cms_config::get_instance();
            $file = $css->get_content_filename();
            $title = $css->get_name().' ('.cms_relative_path($file,$config['root_path']).')';
        }
        $resultSet = $this->get_resultset($title,AdminSearch_tools::summarize($this->get_description()),$this->get_mod()->create_url( 'm1_','admin_edit_css','', [ 'css'=>$one ] ));

        $content = $css->get_name();
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[$mod->lang('name')] = $this->generate_snippets($content);
        }

        $content = $css->get_content();
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[$mod->lang('prompt_stylesheet')] = $this->generate_snippets($content);
        }

        if( $this->search_descriptions()) {
            $content = $css->get_description();
            $resultSet->count += $count = $this->get_number_of_occurrences($content);
            if ($this->show_snippets() && $count > 0) {
                $resultSet->locations[$mod->lang('prompt_description')] = $this->generate_snippets($content);
            }
        }

        return $resultSet;

    }

    public function get_matches()
    {
        $db = cmsms()->GetDb();
        $mod = $this->get_mod();
        // get all of the stylesheet ids
        $sql = 'SELECT id FROM '.CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME.' ORDER BY name ASC';
        $all_ids = $db->GetCol($sql);
        $output = [];
        $resultSets = array();

        if( count($all_ids) ) {
            $chunks = array_chunk($all_ids,15);
            foreach( $chunks as $chunk ) {
                $css_list = \CmsLayoutStylesheet::load_bulk($chunk);
                foreach( $css_list as $css ) {
                    if( $this->check_css_matches($css) ) $resultSets[] = $this->get_css_match_info($css,$mod);
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
