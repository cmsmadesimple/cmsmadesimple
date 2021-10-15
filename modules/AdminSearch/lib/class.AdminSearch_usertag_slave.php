<?php

final class AdminSearch_usertag_slave extends AdminSearch_slave
{
    public function get_name()
    {
        $mod = $this->get_mod();
        return $mod->Lang('lbl_usertag_search');
    }

    public function get_description()
    {
        $mod = $this->get_mod();
        return $mod->Lang('desc_usertag_search');
    }

    public function check_permission()
    {
        $userid = get_userid();
        return check_permission($userid,'Modify User-defined Tags');
    }

    private function check_matches($udtprops)
    {
        $strposFunctionName = $this->search_casesensitive() ? 'strpos' : 'stripos';
        $needle = $this->get_text();
        if( $strposFunctionName($udtprops['userplugin_name'],$needle) !== FALSE ) return TRUE;
        if( $strposFunctionName($udtprops['code'],$needle) !== FALSE ) return TRUE;
        if( $this->search_descriptions() && $strposFunctionName($udtprops['description'],$needle) !== FALSE ) return TRUE;
        return FALSE;
    }

    private function get_mod()
    {
        static $_mod;
        if( !$_mod ) $_mod = \cms_utils::get_module('AdminSearch');
        return $_mod;
    }

    private function get_match_info($udtprops, &$mod)
    {
        $title = $udtprops['userplugin_name'];
        $gCms = cmsms();
        $config = $gCms->GetConfig();
        $url = $config['root_url'].'/'.$config['admin_dir'].'/editusertag.php?userplugin_id='.$udtprops['userplugin_id'].'&amp;'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
        $resultSet = $this->get_resultset($title,AdminSearch_tools::summarize($udtprops['description']),$url);

        $content = $title;
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[$mod->lang('name')] = $this->generate_snippets($content);
        }

        $content = $udtprops['code'];
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[$mod->lang('prompt_code')] = $this->generate_snippets($content);
        }

        if( $this->search_descriptions()) {
            $content = $udtprops['description'];
            $resultSet->count += $count = $this->get_number_of_occurrences($content);
            if ($this->show_snippets() && $count > 0) {
                $resultSet->locations[$mod->lang('prompt_description')] = $this->generate_snippets($content);
            }
        }
        return $resultSet;
    }

    public function get_matches()
    {
        $mod = $this->get_mod();
        // get all UDT ids
        $gCms = cmsms();
        $usertagops = $gCms->GetUserTagOperations();
        $all_ids = $usertagops->ListUserTags();
        $output = array();
        $resultSets = array();

        if( $all_ids ) {
            foreach( $all_ids as $id=>$name ) {
                $row = $usertagops->GetUserTag($name);
                if( $this->check_matches($row) ) $resultSets[] = $this->get_match_info($row,$mod);
            }
        }

        // process the results
        foreach ($resultSets as $result_object) {
            $output[] = json_encode($result_object);
        }
        return $output;
    }
} // end of class
