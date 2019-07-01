<?php
namespace CMSMS\internal;

class page_template_parser extends \Smarty_Internal_Template
{

    protected $_priority = 100;

    protected $_contentBlocks;

    private static $_allowed_static_plugins = array('global_content');

    public function __construct( $template_resource, $smarty )
    {
        parent::__construct($template_resource, $smarty );

        $this->registerDefaultPluginHandler([$this, 'defaultPluginHandler']);
        $this->merge_compiled_includes = TRUE;

        $this->registerPlugin('compiler','content', [ $this, 'smarty_compiler_contentblock' ], false );
        $this->registerPlugin('compiler','content_image', [ $this, 'smarty_compiler_imageblock' ],false);
        $this->registerPlugin('compiler','content_module', [ $this, 'smarty_compiler_moduleblock' ],false);
        $this->registerPlugin('compiler','content_text', [ $this, 'smarty_compile_contenttext' ] ,false);
    }

    public function smarty_compiler_contentblock($params,$smarty)
    {
        // todo: should be in page_template_parser
        // {content} tag encountered.
        $rec = array('type'=>'text','id'=>'','name'=>'','noedit'=>false, 'usewysiwyg'=>'true','oneline'=>'false','default'=>'','label'=>'',
                     'size'=>'50','tab'=>'','maxlength'=>'255','required'=>0,'placeholder'=>'','priority'=>'','cssname'=>'','adminonly'=>0);
        foreach( $params as $key => $value ) {
            $value = trim($value,'"\'');
            if( $key == 'type' ) continue;
            if( $key == 'block' ) $key = 'name';
            if( $key == 'wysiwyg' ) $key = 'usewysiwyg';
            if( startswith( $key, 'data-') ) {
                $rec[$key] = $value;
                continue;
            }
            if( isset($rec[$key]) ) $rec[$key] = $value;
        }

        if( !$rec['name'] ) $rec['name'] = $rec['id'] = 'content_en';
        if( strpos($rec['name'],' ') !== FALSE ) {
            if( !$rec['label'] ) $rec['label'] = $rec['name'];
            $rec['name'] = str_replace(' ','_',$rec['name']);
        }
        if( !$rec['id'] ) $rec['id'] = str_replace(' ','_',$rec['name']);

        /*
        // check for duplicate
        if( isset(self::$_contentBlocks[$rec['name']]) ) throw new CmsEditContentException('Duplicate content block: '.$rec['name']);
        */

        // set priority
        if( empty($rec['priority']) || !$rec['priority'] ) {
            if( !$this->_priority ) $this->_priority = 100;
            $rec['priority'] = $this->_priority++;
        }

        $this->_contentBlocks[$rec['name']] = $rec;
    }

    public function smarty_compiler_imageblock($params,$smarty)
    {
        // todo: should be in page_template_parser
        // {content_image} tag encountered.
        if( !isset($params['block']) || empty($params['block']) ) throw new CmsEditContentException('{content_image} tag requires block parameter');

        $rec = [ 'type'=>'image','name'=>'','label'=>'','upload'=>true,'dir'=>'','default'=>'','tab'=>'',
                 'priority'=>'','exclude'=>'','sort'=>0, 'profile'=>'' ];
        foreach( $params as $key => $value ) {
            if( $key == 'type' ) continue;
            if( $key == 'block' ) $key = 'name';
            if( isset($rec[$key]) ) $rec[$key] = trim($value,"'\"");
        }

        if( !$rec['name'] ) {
            $n = count($this->_contentBlocks)+1;
            $rec['name'] = 'image_'.$n;
        }
        if( strpos($rec['name'],' ') !== FALSE ) {
            if( !$rec['label'] ) $rec['label'] = $rec['name'];
            $rec['name'] = str_replace(' ','_',$rec['name']);
        }
        if( empty($rec['id']) ) $rec['id'] = str_replace(' ','_',$rec['name']);
        if( !$rec['priority'] ) {
            if( !$this->_priority ) $this->_priority = 100;
            $rec['priority'] = $this->_priority++;
        }

        // set priority
        if( empty($rec['priority']) || $rec['priority'] == 0 ) {
            if( !$this->_priority ) $this->_priority = 100;
            $rec['priority'] = $this->_priority++;
        }

        if( !is_array($this->_contentBlocks) ) $this->_contentBlocks = array();
        $this->_contentBlocks[$rec['name']] = $rec;
    }

    public function smarty_compiler_moduleblock($params,$smarty)
    {
        // todo: should be in page_template_parser
        // {content_module} tag encountered.
        if( !isset($params['block']) || empty($params['block']) ) throw new CmsEditContentException('{content_module} tag requires block parameter');

        $rec = array('type'=>'module','id'=>'','name'=>'','module'=>'','label'=>'', 'blocktype'=>'','tab'=>'','priority'=>'');
        $parms = array();
        foreach( $params as $key => $value ) {
            if( $key == 'block' ) $key = 'name';

            $value = trim(trim($value,'"\''));
            if( isset($rec[$key]) ) {
                $rec[$key] = $value;
            }
            else {
                $parms[$key] = $value;
            }
        }

        if( !$rec['name'] ) {
            $n = count($this->_contentBlocks)+1;
            $rec['id'] = $rec['name'] = 'module_'.$n;
        }
        if( strpos($rec['name'],' ') !== FALSE ) {
            if( !$rec['label'] ) $rec['label'] = $rec['name'];
            $rec['name'] = str_replace(' ','_',$rec['name']);
        }
        if( !$rec['id'] ) $rec['id'] = str_replace(' ','_',$rec['name']);
        $rec['params'] = $parms;
        if( $rec['module'] == '' ) throw new CmsEditContentException('Missing module param for content_module tag');
        if( !$rec['priority'] ) {
            if( !$this->_priority ) $this->_priority = 100;
            $rec['priority'] = $this->_priority++;
        }

        // set priority
        if( empty($rec['priority']) || !$rec['priority'] ) {
            if( !$this->_priority ) $this->_priority = 100;
            $rec['priority'] = $this->_priority++;
        }

        if( !is_array($this->_contentBlocks) ) $this->_contentBlocks = array();
        $this->_contentBlocks[$rec['name']] = $rec;
    }

    public function smarty_compile_contenttext($params,$smarty)
    {
        // todo: should be in page_template_parser
        // {content_text} tag encountered.
        //if( !isset($params['block']) || empty($params['block']) ) throw new \CmsEditContentException('{content_text} smarty block tag requires block parameter');

        $rec = [ 'type'=>'static','name'=>'','label'=>'','upload'=>true,'dir'=>'','default'=>'','tab'=>'',
                 'priority'=>'','exclude'=>'','sort'=>0, 'profile'=>'', 'text'=>'' ];
        foreach( $params as $key => $value ) {
            if( $key == 'type' ) continue;
            if( $key == 'block' ) $key = 'name';
            if( isset($rec[$key]) ) $rec[$key] = trim($value,"'\"");
        }

        if( !$rec['name'] ) {
            $n = count($this->_contentBlocks)+1;
            $rec['name'] = 'static_'.$n;
        }
        if( strpos($rec['name'],' ') !== FALSE ) {
            if( !$rec['label'] ) $rec['label'] = $rec['name'];
            $rec['name'] = str_replace(' ','_',$rec['name']);
        }
        if( empty($rec['id']) ) $rec['id'] = str_replace(' ','_',$rec['name']);

        // set priority
        if( empty($rec['priority']) || $rec['priority'] == 0 ) {
            if( !$this->_priority ) $this->_priority = 100;
            $rec['priority'] = $this->_priority++;
        }

        if( !$rec['text'] ) return; // do nothing.
        $rec['static_content'] = trim(strip_tags($rec['text']));
        if( !is_array($this->_contentBlocks) ) $this->_contentBlocks = array();
        $this->_contentBlocks[$rec['name']] = $rec;
    }

    public function get_content_blocks()
    {
        $this->compileTemplateSource();
        return $this->_contentBlocks;
    }

    /**
     * _dflt_plugin
     *
     * @internal
     */
    public static function _dflt_plugin($params,$smarty)
    {
        return '';
    }

    /**
     * Dummy default plugin handler for smarty.
     *
     * @access private
     * @internal
     */
    public function defaultPluginHandler($name, $type, $template, &$callback, &$script, &$cachable)
    {
        // this is hackish, but... if we tell smarty that a name is a 'function' plugin, it will error out
        // because the system expects a 'close' function but does not call the defaultPluginHandler
        $tmp = strtolower($name);
        if( (endswith($tmp,'block') || startswith($tmp,'block')) && $type != 'block' ) return false;

        $callback = array(__CLASS__,'_dflt_plugin');
        $cachable = false;
        return TRUE;
    }

    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        die(__FILE__.'::'.__LINE__.' CRITICAL: This method should never be called');
    }

    public static function create( $rsrc )
    {
        // we need a new smarty object because we do not want registered plugins, error handlers and cruft.
        // but we do want template directories, and resources.
        $global_smarty = \cms_utils::get_smarty();
        $smarty = new smarty_theme_template();  //
        $smarty->setCompileDir(TMP_TEMPLATES_C_LOCATION);
        $smarty->registered_resources = $global_smarty->registered_resources;
        $dirs = $global_smarty->getTemplateDir();
        foreach( $dirs as $dir ) {
            $smarty->addTemplateDir( $dir );
        }
        $parser = new self( $rsrc, $smarty );
        return $parser;
    }
} // class
