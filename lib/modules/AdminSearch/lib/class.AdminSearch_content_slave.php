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
        return TRUE;
    }

    public function get_matches()
    {
        $userid = get_userid();

        $content_manager = cms_utils::get_module('CMSContentManager');
        $db = cmsms()->GetDb();
        $query = 'SELECT C.content_id, P.content FROM '.CMS_DB_PREFIX.'content C LEFT JOIN '.CMS_DB_PREFIX.'content_props P ON C.content_id = P.content_id WHERE P.content LIKE ? OR C.metadata LIKE ? GROUP BY C.content_id';
        //$query = 'SELECT DISTINCT C.content_id, P.content FROM '.CMS_DB_PREFIX.'content C LEFT JOIN '.CMS_DB_PREFIX.'content_props P ON C.content_id = P.content_id WHERE P.content LIKE ? OR C.metadata LIKE ?';
        //$query = 'SELECT DISTINCT content_id,prop_name,content FROM '.CMS_DB_PREFIX.'content_props WHERE content LIKE ?';
        $txt = '%'.$this->get_text().'%';
        $dbr = $db->GetArray($query, [ $txt, $txt ] );
        if( is_array($dbr) && count($dbr) ) {
            $output = array();
            $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

            foreach( $dbr as $row ) {
                $content_id = $row['content_id'];
                if( !check_permission($userid,'Manage All Content') && !check_permission($userid,'Modify Any Page') &&
                    !cmsms()->GetContentOperations()->CheckPageAuthorship($userid,$content_id) ) {
                    // no access to this content page.
                    continue;
                }

                $content_obj = cmsms()->GetContentOperations()->LoadContentFromId($content_id);
                if( !is_object($content_obj) ) continue;
                if( !$content_obj->HasSearchableContent() ) continue;

                // here we could actually have a smarty template to build the description.
                $pos = strpos($row['content'],$this->get_text());
                $text = null;
                if( $pos !== FALSE ) {
                    $start = max(0,$pos - 50);
                    $end = min(strlen($row['content']),$pos+50);
                    $text = substr($row['content'],$start,$end-$start);
                    $text = cms_htmlentities($text);
                    $text = str_replace($this->get_text(),'<span class="search_oneresult">'.$this->get_text().'</span>',$text);
                    $text = str_replace("\r",'',$text);
                    $text = str_replace("\n",'',$text);
                }

                $tmp = array('title'=>$content_obj->Name(),
                             'description'=>$content_obj->Name(),
                             'edit_url'=>$content_manager->create_url('m1_','admin_editcontent','',array('content_id'=>$content_id)),
                             'text'=>$text);

                $output[] = $tmp;
            }

            return $output;
        }

    }
} // end of class

?>