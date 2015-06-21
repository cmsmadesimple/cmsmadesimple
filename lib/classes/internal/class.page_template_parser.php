<?php

namespace CMSMS\internal;

class page_template_parser extends \Smarty_Internal_Template
{
    private static $_allowed_static_plugins = array('global_content');

    public function __construct($template_resource, $smarty, $_parent = null, $_cache_id = null, $_compile_id = null, $_caching = null, $_cache_lifetime = null)
    {
        $_caching = false;
        $_compile_id = 'cmsms_parser_'.time();
        parent::__construct($template_resource, $smarty, $_parent, $_cache_id, $_compile_id, $_caching, $_cache_lifetime);

        $this->registerDefaultPluginHandler(array($this,'defaultPluginHandler'));

        $this->registerPlugin('compiler','content',array('CMS_Content_Block','smarty_compiler_contentblock'),false);
        $this->registerPlugin('compiler','content_image',array('CMS_Content_Block','smarty_compiler_imageblock'),false);
        $this->registerPlugin('compiler','content_module',array('CMS_Content_Block','smarty_compiler_moduleblock'),false);
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
		if($type == 'compiler') {
			$callback = array(__CLASS__,'_dflt_plugin');
			$cachable = false;
			return TRUE;
		}

        return FALSE;
    }

    public function fetch()
    {
        die(__METHOD__.' should never be called');
    }
}