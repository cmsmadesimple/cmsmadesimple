<?php
namespace __appbase;
use \CmsLayoutTemplateType;
use \CmsLayoutTemplate;

class content_reader
{
    protected $filename;
    protected $contentops;
    protected $tpl_id;

    public function __construct( $filename, \ContentOperations $ops, $tpl_id = null )
    {
        $tpl_id = (int) $tpl_id;
        if( $tpl_id < 1 ) $tpl_id = $this->_getDefaultTemplateId();
        if( $tpl_id < 1 ) throw new \Exception('Could not get a valid template id');
        if( !is_file( $filename ) ) throw new \InvalidArgumentException("invalid filename passed to ".___METHOD__);
        $this->filename = $filename;
        $this->contentops = $ops;
        $this->tpl_id = (int) $tpl_id;
    }

    protected function _get_childnode_value(&$parent,$nodename)
    {
        $children = $parent->childNodes;
        foreach( $children as $childnode ) {
            if( $childnode->nodeName == $nodename ) return $childnode->nodeValue;
        }
        return NULL;
    }

    protected function _get_node_attribute(&$node,$attrname)
    {
        $attr = $node->attributes->getNamedItem($attrname);
        if( $attr ) return $attr->nodeValue;
        return NULL;
    }

    function _getTemplateNameFromId($tplid)
    {
        if( !$this->_template_cache ) {
            $tpl_type = CmsLayoutTemplateType::load('Core::Page');
            $tpl = $tpl_type->get_dflt_template();
            $parms = array();
            $parms[] = 't:'.$tpl_type->get_id();
            $parms['as_list'] = 1;
            $list = CmsLayoutTemplate::template_query($parms);
            $this->_template_cache = array();
            if( count($list) ) $this->_template_cache = $list;
        }

        if( isset($this->_template_cache[$tplid]) ) return $this->_template_cache[$tplid];
    }

    protected function _getDefaultTemplateId()
    {
        if( !$this->_default_template ) {
            $tpl_type = CmsLayoutTemplateType::load('Core::Page');
            $tpl = $tpl_type->get_dflt_template();
            $this->_default_template = $tpl->get_id();
        }
        return $this->_default_template;
    }

    protected function node_to_content_obj( $node )
    {
        // a. get the content type
        $contenttype = $this->_get_childnode_value($node,'type');

        // b. create the new content object for filling.
        $content_obj = $this->contentops->CreateNewContent($contenttype);
        if( !$content_obj ) return NULL;

        $content_obj->SetName($this->_get_node_attribute($node,'name'));
        $content_obj->SetMenuText($this->_get_childnode_value($node,'menutext'));
        $content_obj->SetActive($this->_get_childnode_value($node,'active'));
        $content_obj->SetAccessKey($this->_get_childnode_value($node,'accesskey'));
        $content_obj->SetTabIndex($this->_get_childnode_value($node,'tabindex'));
        $content_obj->SetMetaData($this->_get_childnode_value($node,'metadata'));
        $content_obj->SetCachable($this->_get_childnode_value($node,'cachable'));
        $content_obj->SetShowInMenu($this->_get_childnode_value($node,'showinmenu'));

        $alias = $this->_get_childnode_value($node,'alias');
        $tmp = $this->contentops->CheckAliasError($alias);
        if( $tmp ) $alias = '';
        $content_obj->SetAlias($alias);
        $content_obj->SetTemplateId($this->tpl_id);

        // now to get the properties.
        $children = $node->childNodes;
        foreach( $children as $childnode ) {
            if( $childnode->nodeName != 'property' ) continue;
            $propname = $this->_get_node_attribute($childnode,'name');
            $proptype = $this->_get_node_attribute($childnode,'type');
            $propval  = $childnode->nodeValue;

            $content_obj->setPropertyValue($propname,$propval);
        }

        return $content_obj;
    }

    protected function import_content( $node, $parent_id = -1 )
    {
        while( $node != NULL ) {
            if( $node->nodeName == 'cms_content' ) {
                $content_obj = $this->node_to_content_obj( $node );
                if( $content_obj ) {
                    $content_obj->SetParentId($parent_id);
                    $content_obj->SetOwner(1);
                    $content_obj->Save();
                    $new_parent_id = $content_obj->Id();
                    // see if there are more.
                    $this->import_content($node->firstChild,$new_parent_id);
                }
            }

            $node = $node->nextSibling;
        }
    }

    public function import()
    {
        $doc = new \DomDocument();
        $doc->load( $this->filename );
        $root = $doc->firstChild->firstChild;
        $this->import_content( $root );
    }
} // class