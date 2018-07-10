<?php
global $admin_user;

//
// Types
//
verbose_msg('debug: page template type');
$page_template_type = new CmsLayoutTemplateType();
$page_template_type->set_originator(CmsLayoutTemplateType::CORE);
$page_template_type->set_name('page');
$page_template_type->set_dflt_flag(TRUE);
$page_template_type->set_lang_callback('\\CMSMS\internal\\std_layout_template_callbacks::page_type_lang_callback');
$page_template_type->set_content_callback('\\CMSMS\internal\\std_layout_template_callbacks::reset_page_type_defaults');
$page_template_type->set_help_callback('\\CMSMS\internal\\std_layout_template_callbacks::template_help_callback');
$page_template_type->reset_content_to_factory();
$page_template_type->set_content_block_flag(TRUE);
$page_template_type->save();

verbose_msg('debug: generic template type');
$gcb_template_type = new CmsLayoutTemplateType();
$gcb_template_type->set_originator(CmsLayoutTemplateType::CORE);
$gcb_template_type->set_name('generic');
$gcb_template_type->set_lang_callback('\\CMSMS\internal\\std_layout_template_callbacks::generic_type_lang_callback');
$gcb_template_type->set_help_callback('\\CMSMS\internal\\std_layout_template_callbacks::template_help_callback');
$gcb_template_type->save();
