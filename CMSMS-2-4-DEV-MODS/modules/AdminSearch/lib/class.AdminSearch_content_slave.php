<?php

final class AdminSearch_content_slave extends AdminSearch_slave
{
    public function get_name()
    {
        $mod = cms_utils::get_module('AdminSearch');
        return $mod->Lang('lbl_content_search');
    }

    public function get_description()
    {
        $mod = cms_utils::get_module('AdminSearch');
        return $mod->Lang('desc_content_search');
    }

    public function check_permission()
    {
        $userid = get_userid();
        $mod = cms_utils::get_module('CMSContentManager');
        return $mod->CanEditContent();
    }

    public function get_matches()
    {
        #key: db colums name, value: corresponding function name to get the value from the Content Object
        $content_db_fields = array(
            'content_name' => [ 'function_name' => 'Name', 'translation' => \CmsLangOperations::lang_from_realm('admin','name') ],
            'menu_text' => [ 'function_name' => 'MenuText', 'translation' => \CmsLangOperations::lang_from_realm('admin','menutext') ],
            'content_alias' => [ 'function_name' => 'Alias', 'translation' => \CmsLangOperations::lang_from_realm('admin','alias') ],
            'metadata' => [ 'function_name' => 'Metadata', 'translation' => \CmsLangOperations::lang_from_realm('admin','metadata') ],
            'titleattribute' => [ 'function_name' => 'TitleAttribute', 'translation' => \CmsLangOperations::lang_from_realm('admin','titleattribute') ],
            'page_url'  => [ 'function_name' => 'URL', 'translation' => \CmsLangOperations::lang_from_realm('admin','page_url') ]
        );

        $userid = get_userid();

        $content_manager = cms_utils::get_module('CMSContentManager');
        $db = cmsms()->GetDb();
        $where_clause = implode(' LIKE ? OR ', array_keys($content_db_fields));

        #content table
        $query = 'SELECT DISTINCT content_id FROM '.CMS_DB_PREFIX.'content WHERE ' . $where_clause . ' LIKE ?';
        #content_props table
        $query2 = 'SELECT DISTINCT content_id,prop_name,content FROM '.CMS_DB_PREFIX.'content_props WHERE content LIKE ?';
        $txt = '%'.$this->get_text().'%';
        
        $output = array();
        
        $resultSets = array();

        $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

        #checking the content table
        $this->process_query_string($query);

        $dbr = $db->GetArray($query, array_fill(0,count($content_db_fields),$txt));
        if( is_array($dbr) && count($dbr) ) {


            foreach( $dbr as $row ) {
                $content_id = $row['content_id'];
                if( !check_permission($userid,'Manage All Content') && !check_permission($userid,'Modify Any Page') &&
                    !cmsms()->GetContentOperations()->CheckPageAuthorship($userid,$content_id) ) {
                    // no access to this content page.
                    continue;
                }

                $content_obj = cmsms()->GetContentOperations()->LoadContentFromId($content_id);
                if( !is_object($content_obj) ) continue;

                if (!isset($resultSets[$content_id])) {
                    $resultSets[$content_id] = $this->get_resultset($content_obj->Name(),$content_obj->Name(),$content_manager->create_url('m1_','admin_editcontent','',array('content_id'=>$content_id)));
                }

                // we're going to check several standard attributes of the content object
                foreach ($content_db_fields as $db_field => $value) { 
                    $fname = $value['function_name'];
                    $content = $content_obj->$fname(); //get the attribute value (text)
 
                    $resultSets[$content_id]->count += $count = $this->get_number_of_occurrences($content);

                    if (!$this->show_snippets()) continue;
                    if( $count > 0 ) {
                        $snippets = $this->generate_snippets($content);
                        $resultSets[$content_id]->locations[$content_db_fields[$db_field]['translation']] = $snippets;
                    }
                }
              
            }
        }
        
        #checking the content_props table
        $this->process_query_string($query2);
        $dbr = $db->GetArray($query2, [$txt]);
        if( is_array($dbr) && count($dbr) ) {


            foreach( $dbr as $row ) {
                $content_id = $row['content_id'];
                if( !check_permission($userid,'Manage All Content') && !check_permission($userid,'Modify Any Page') &&
                    !cmsms()->GetContentOperations()->CheckPageAuthorship($userid,$content_id) ) {
                    // no access to this content page.
                    continue;
                }

                $content_obj = cmsms()->GetContentOperations()->LoadContentFromId($content_id);
                if( !is_object($content_obj) ) continue;
                //if( !$content_obj->HasSearchableContent() ) continue;
                
                if (!isset($resultSets[$content_id])) {
                    $resultSets[$content_id] = $this->get_resultset($content_obj->Name(),$content_obj->Name(),$content_manager->create_url('m1_','admin_editcontent','',array('content_id'=>$content_id)));
                }

                // here we could actually have a smarty template to build the description.
                $content = $row['content'];

               
                $resultSets[$content_id]->count += $count = $this->get_number_of_occurrences($content);

                if (!$this->show_snippets()) continue; 
                if( $count > 0 ) {
                    $snippets = $this->generate_snippets($content);
                    if ($row['prop_name'] == 'content_en') {
                        $prop_name = \CmsLangOperations::lang_from_realm('admin','content');
                    } elseif (\CmsLangOperations::key_exists($row['prop_name'],'admin')) {
                      $prop_name = \CmsLangOperations::lang_from_realm('admin',$row['prop_name']);
                    } else {
                      $prop_name = $row['prop_name'];
                    }
                    
                    $resultSets[$content_id]->locations[$prop_name] = $snippets;
                }
            }
        }

        #processing the results
        foreach ($resultSets as $cId => $result_object) {
            $output[] = json_encode($result_object);
        }

        return $output;
    }
} // end of class

?>