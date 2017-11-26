<?php
namespace CMSMS\internal;
use \CmsLayoutTemplateType;

final class std_layout_template_callbacks
{
    protected function __construct() {}

	public static function page_type_lang_callback($key)
	{
		if( $key == CmsLayoutTemplateType::CORE ) return 'Core';
		return lang($key);
	}

	public static function generic_type_lang_callback($key)
	{
		if( $key == CmsLayoutTemplateType::CORE ) return 'Core';
		return lang($key);
	}

	public static function reset_page_type_defaults()
	{
		$config = \cms_config::get_instance();
		$file = cms_join_path($config['admin_path'],'templates','orig_page_template.tpl');
		$contents = '';
		if( is_file($file) ) $contents = @file_get_contents($file);
		return $contents;
	}

    public static function template_help_callback($typename)
    {
        $typename = trim($typename);
        if( $typename == 'generic' ) return;
        $key = 'tplhelp_'.$typename;
        return lang($key);
    }

} // class
