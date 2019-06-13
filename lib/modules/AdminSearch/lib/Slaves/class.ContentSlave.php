<?php
namespace AdminSearch\Slaves;
use AdminSearch;
use CMSContentManager;
use ContentOperations;
use CMSMS\Database\Connection as Database;

final class ContentSlave extends AbstractSlave
{
    private $mod;
    private $cmgr;
    private $db;
    private $co;

    public function __construct(AdminSearch $mod, CMSContentManager $cmgr, Database $db, ContentOperations $co)
    {
        $this->mod = $mod;
        $this->cmgr = $cmgr;
        $this->db = $db;
        $this->co = $co;
    }

    public function get_name()
    {
        return $this->mod->Lang('lbl_content_search');
    }

    public function get_description()
    {
        return $this->mod->Lang('desc_content_search');
    }

    public function get_matches()
    {
        $userid = get_userid();

        $dbr = null;
        $term = $this->get_searchterm();
        $sql1 = 'SELECT P.content_id, P.content FROM '.CMS_DB_PREFIX.'content_props P WHERE P.content LIKE ?';
        $arr = $this->db->GetArray($sql1, [ $term ] );
        foreach( $arr as $row ) {
            $content_id = (int) $row['content_id'];
            if( array_key_exists($content_id, $dbr) ) continue;
            $dbr[$content_id] = $row;
        }

        $sql2 = 'SELECT C.content_id, C.metadata as content FROM '.CMS_DB_PREFIX.'content C WHERE C.metadata LIKE ?';
        $arr = $this->db->GetArray($sql2, [ $term ] );
        foreach( $arr as $row ) {
            $content_id = (int) $row['content_id'];
            if( array_key_exists($content_id, $dbr) ) continue;
            $dbr[$content_id] = $row;
        }

        if( is_array($dbr) && count($dbr) ) {
            $output = array();
            $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
            $have_perm = check_permission($userid, 'Manage All Content') || check_permission($userid, 'Modify Any Page');

            foreach( $dbr as $row ) {
                $content_id = (int) $row['content_id'];
                if( !$have_perm && !$this->co->CheckPageAuthorship($userid,$content_id) ) {
                    // no access to this content page.
                    continue;
                }

                $content_obj = $this->co->LoadContentFromId($content_id);
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
                             'edit_url'=>$this->cmgr->create_url('m1_','admin_editcontent','',array('content_id'=>$content_id)),
                             'text'=>$text);

                $output[] = $tmp;
            }

            return $output;
        }

    }
} // end of class
