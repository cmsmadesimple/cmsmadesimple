<?php
//global $admin_user;

//
// Stylesheets
//
// no stylesheets when no sample content

//
// Designs
//
$design = new CmsLayoutCollection();
$design->set_name('Default');
$design->set_description('Default design with just the default template.');
$design->set_default(TRUE);
$design->save();
$design->save();

//
// Types
//
$page_template_type = new CmsLayoutTemplateType();
$page_template_type->set_originator(CmsLayoutTemplateType::CORE);
$page_template_type->set_name('page');
$page_template_type->set_dflt_flag(TRUE);
$page_template_type->set_lang_callback('CmsTemplateResource::page_type_lang_callback');
$page_template_type->set_content_callback('CmsTemplateResource::reset_page_type_defaults');
$page_template_type->set_help_callback('CmsTemplateResource::template_help_callback');
$page_template_type->reset_content_to_factory();
$page_template_type->set_content_block_flag(TRUE);
$page_template_type->save();

$gcb_template_type = new CmsLayoutTemplateType();
$gcb_template_type->set_originator(CmsLayoutTemplateType::CORE);
$gcb_template_type->set_name('generic');
$gcb_template_type->set_lang_callback('CmsTemplateResource::generic_type_lang_callback');
$gcb_template_type->set_help_callback('CmsTemplateResource::template_help_callback');
$gcb_template_type->save();

//
// Template Categories
//

//
// Templates
//
$app = \__appbase\get_app();

$fn = $app->get_destdir()
    . DIRECTORY_SEPARATOR . 'admin'
    . DIRECTORY_SEPARATOR . 'templates'
    . DIRECTORY_SEPARATOR . 'orig_page_template.tpl';

$txt = file_get_contents($fn);
$template = new CmsLayoutTemplate();
$template->set_name('Default');
$template->set_description('This is the default minimal template. A simple starting point to build templates from.');
$template->set_type($page_template_type);
$template->set_content($txt);
$template->set_type($page_template_type);
$template->set_type_dflt(TRUE);
$template->add_design($design);
$template->set_owner(1);
$template->save();

//
// Extra global templates
//

// no templates when no sample content

//
// Default Content Object
//
ContentOperations::get_instance()->LoadContentType('content');
$content = new Content;
$content->SetName('Home');
$content->SetAlias();
$content->SetOwner(1);
$content->SetMenuText('Home Page');
$content->SetTemplateId($template->get_id());
$content->SetParentId(-1);
$content->SetActive(TRUE);
$content->SetShowInMenu(TRUE);
$content->SetCachable(TRUE);
$content->SetDefaultContent(TRUE);
$content->SetPropertyValue('searchable',1);
$content->SetPropertyValue('design_id',$design->get_id());
$content->SetPropertyValue('content_en',
			   '<p>Congratulations! The installation worked. You now have a fully functional installation of CMS Made Simple and you are <em>almost</em> ready to start building your site.</p><p>If you chose to install the default content, you will see numerous pages available to read.  You should read them thoroughly  as these default pages are devoted to showing you the basics of how to begin working with CMS Made Simple.  On these example pages, templates, and stylesheets many of the features of the default installation of CMS Made Simple are described and demonstrated. You can learn much about the power of CMS Made Simple by absorbing this information.</p><p>To get to the Administration Console you have to login as the administrator (with the username/password you specified during the installation process) on your site at http://yourwebsite.com/cmsmspath/admin.  If this is your site click <a title="CMSMS Demo Admin Panel" href="admin">here</a> to login.</p><p>Read about how to use CMS Made Simple in the <a class="external" href="http://docs.cmsmadesimple.org/" title="CMS Made Simple Documentation" target="_blank">documentation</a>. In case you need any help the community is always at your service, in the  <a class="external" href="http://forum.cmsmadesimple.org" title="CMS Made Simple Forum" target="_blank">forum</a> or the <a class="external" href="http://www.cmsmadesimple.org/support/irc" title="Information about the CMS Made Simple IRC channel" target="_blank">IRC</a>.</p><h3>License</h3><p>CMS Made Simple is released under the <a class="external" href="http://www.gnu.org/licenses/licenses.html#GPL" title="General Public License" target="_blank">GPL</a> license and as such you don\'t have to leave a link back to us in these templates or on your site as much as we would like it.</p><p>Some third party addon modules may include additional license restrictions.</p>');
$content->Save();
?>
