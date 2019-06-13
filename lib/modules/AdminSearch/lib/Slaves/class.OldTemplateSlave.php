<?php
namespace AdminSearch\Slaves;
use AdminSearch;
use CMSMS\Database\Connection as Database;

final class OldTemplateSlave extends AbstractSlave
{
    private $mod;
    private $db;

    public function __construct(AdminSearch $mod, Database $db)
    {
        $this->mod = $mod;
        $this->db = $db;
    }

    public function get_name()
    {
        return $this->mod->Lang('lbl_oldmodtemplate_search');
    }

    public function get_description()
    {
        return $this->mod->Lang('desc_oldmodtemplate_search');
    }

    public function get_matches()
    {
        $userid = get_userid();

        $query = 'SELECT module_name,template_name,content FROM '.CMS_DB_PREFIX.'module_templates WHERE content LIKE ?';
        $dbr = $this->db->GetArray($query,array('%'.$this->get_text().'%'));
        if( is_array($dbr) && count($dbr) ) {
            $output = array();

            foreach( $dbr as $row ) {
                // here we could actually have a smarty template to build the description.
                $pos = strpos($row['content'],$this->get_text());
                if( $pos !== FALSE ) {
                       $start = max(0,$pos - 50);
                       $end = min(strlen($row['content']),$pos+50);
                       $text = substr($row['content'],$start,$end-$start);
                       $text = cms_htmlentities($text);
                       $text = str_replace($this->get_text(),'<span class="search_oneresult">'.$this->get_text().'</span>',$text);
                       $text = str_replace("\r",'',$text);
                       $text = str_replace("\n",'',$text);
                }

                $tmp = array('title'=>"{$row['module_name']} + {$row['template_name']}",'text'=>$text);

                $output[] = $tmp;
            }

            return $output;
        }

    }

    public function get_section_description()
    {
        return $this->mod->Lang('sectiondesc_oldmodtemplates');
    }
} // end of class

?>
