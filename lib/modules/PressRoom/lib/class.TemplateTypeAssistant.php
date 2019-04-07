<?php
namespace PressRoom;
use CmsLayoutTemplateType;
use CmsLogicException;
use cms_utils;

final class TemplateTypeAssistant
{
    private function __construct()
    {
        // nothing here
    }

    protected static function get_mod()
    {
        static $_mod;
        if( !$_mod ) $_mod = cms_utils::get_module('PressRoom');
        return $_mod;
    }

    public static function page_type_lang_callback(string $str)
    {
        $mod = self::get_mod();
        if( $mod ) return $mod->Lang('tpltype_'.$str);
    }

    public static function template_help_callback(string $str)
    {
        $str = trim(Str);
        $mod = self::get_mod();
        if( $mod ) {
            $file = $mod->GetModulePath().'/doc/tpltype_'.$str.'.inc';
            if( is_file($file) ) return file_get_contents($file);
        }
    }

    public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
    {
        $mod = self::get_mod();
        if( !$mod ) return;

        if( $type->get_originator() != 'PressRoom' ) throw new CmsLogicException('Cannot reset contents for this template type');

        $fn = null;
        switch( $type->get_name() ) {
        case 'summary':
            $fn = 'default.tpl';
            break;
        case 'detail':
            $fn = 'detail.tpl';
            break;
        case 'showcategories':
            $fn = 'showcategories.tpl';
            break;
        }
        if( !$fn ) throw new CmsLogicException('Unknown type name '.$type->get_name());

        $fn = cms_join_path( $mod->GetModulePath().'/templates/'.$fn);
        if( is_file($fn) ) return file_get_contents($fn);
    }

    public static function create_dm_types()
    {
        // will throw exceptions if any of the types exist already
        $mod = self::get_mod();
        if( !$mod ) return;

        $summary_template_type = new CmsLayoutTemplateType();
        $summary_template_type->set_originator($mod->GetName());
        $summary_template_type->set_name('summary');
        $summary_template_type->set_dflt_flag(TRUE);
        $summary_template_type->set_lang_callback('PressRoom\\TemplateTypeAssistant::page_type_lang_callback');
        $summary_template_type->set_content_callback('PressRoom\\TemplateTypeAssistant::reset_page_type_defaults');
        $summary_template_type->set_help_callback('PressRoom\\TemplateTypeAssistant::template_help_callback');
        $summary_template_type->reset_content_to_factory();
        $summary_template_type->save();

        $detail_template_type = new CmsLayoutTemplateType();
        $detail_template_type->set_originator($mod->GetName());
        $detail_template_type->set_name('detail');
        $detail_template_type->set_dflt_flag(TRUE);
        $detail_template_type->set_lang_callback('PressRoom\\TemplateTypeAssistant::page_type_lang_callback');
        $detail_template_type->set_content_callback('PressRoom\\TemplateTypeAssistant::reset_page_type_defaults');
        $detail_template_type->set_help_callback('PressRoom\\TemplateTypeAssistant::template_help_callback');
        $detail_template_type->reset_content_to_factory();
        $detail_template_type->save();

        $showcategories_template_type = new CmsLayoutTemplateType();
        $showcategories_template_type->set_originator($mod->GetName());
        $showcategories_template_type->set_name('showcategories');
        $showcategories_template_type->set_dflt_flag(TRUE);
        $showcategories_template_type->set_lang_callback('PressRoom\\TemplateTypeAssistant::page_type_lang_callback');
        $showcategories_template_type->set_content_callback('PressRoom\\TemplateTypeAssistant::reset_page_type_defaults');
        $showcategories_template_type->set_help_callback('PressRoom\\TemplateTypeAssistant::template_help_callback');
        $showcategories_template_type->reset_content_to_factory();
        $showcategories_template_type->save();
    }

    public static function remove_dm_templates()
    {
        $mod = self::get_mod();
        if( !$mod ) return;

        $types = CmsLayoutTemplateType::load_all_by_originator($mod->GetName());
        if( is_array($types) && count($types) ) {
            foreach( $types as $type ) {
                $templates = $type->get_template_list();
                if( is_array($templates) && count($templates) ) {
                    foreach( $templates as $template ) {
                        $template->delete();
                    }
                }
                $type->delete();
            }
        }
    }
} // class
