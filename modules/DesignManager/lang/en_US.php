<?php
#A
$lang['attached_stylesheets'] = 'Attached Stylesheets';
$lang['available_stylesheets'] = 'Available Stylesheets';
$lang['attached_templates'] = 'Attached Templates';
$lang['available_templates'] = 'Available Templates';

#B


#C
$lang['cancel'] = 'Cancel';
$lang['confirm_bulk_tmplop'] = 'Are you sure you want to perform this action on multiple templates?';
$lang['confirm_delete_1'] = 'Are you sure you want to delete this design?';
$lang['confirm_delete_2'] = 'Yes, I am sure I want to delete this item';
$lang['confirm_delete_category'] = 'Are you sure you want to delete this category?';
$lang['confirm_reset_type'] = 'Reset the default content of this type to the version distributed with the system (or module)?\n\nResetting this will only effect newly created templates of this type.  No existing templates will be adjusted.';
$lang['create'] = 'Create';
$lang['create_category'] = 'Create a new Category';
$lang['create_template'] = 'Create a new Template';
$lang['create_design'] = 'Create a new Design';

#D
$lang['delete_attached_stylesheets'] = 'Delete attached and orphaned stylesheets';
$lang['delete_attached_templates'] = 'Delete attached and orphaned templates';
$lang['delete_template'] = 'Delete Template';
$lang['delete_design'] = 'Delete Design';

#E
$lang['edit_category'] = 'Edit Category';
$lang['edit_template'] = 'Edit Template';
$lang['edit_design'] = 'Edit Design';
$lang['edit_type'] = 'Edit Template Type';
$lang['error_missingparam'] = 'A required parameter is missing';
$lang['error_notconfirmed'] = 'The action was not confirmed';
$lang['error_notemplates'] = 'No Editable Templates Found';
$lang['error_nothingselected'] = 'Nothing selected';

#F
$lang['friendlyname'] = 'Design Manager';

#G


#H
$lang['help_category_desc'] = 'A description for a template category is optional, but may help when organizing templates';
$lang['help_category_name'] = 'A category name is required, and must be unique';
$lang['help_create_template'] = 'This function allows creating a new template of the selected template type.  The default contents from the template type will be used';
$lang['help_dflt_template'] = 'The default template contains the contents that are used when you create a new template of this type.  It is not used during frontend processing';
$lang['help_has_dflt'] = 'If &quot;Yes&quot;, this template type has some default content that is used as a sample when creating a new template of this type.  Of course you are able to change the contents of hte template';
$lang['help_rm_tpl'] = 'Enabling this option will delete all templates that are attached to this design, but not attached to another design.  Use extreme caution when enabling this option.';
$lang['help_rm_css'] = 'Enabling this option will delete all stylesheets that are attached to this design, but not attached to another design.  Use extreme caution when enabling this option.';
$lang['help_template_addteditors'] = 'Here you can specify additional users that have permission to edit this template';
$lang['help_template_bulk'] = 'This option allows performing actions on multiple templates at once.  Use with caution';
$lang['help_template_category'] = 'The template category is used for organizing and filtering templates.  A template may only belong to one category.  Selecting a category is optional';
$lang['help_template_contents'] = 'Enter or edit the contents for the particular template.  This is a smarty template.  The data available to the smarty template depends on where in the applicaion this template is called.';
$lang['help_template_description'] = 'You may provide a text description for this template to help with searching, organization, and to describe any special features or notes that are specific to this template';
$lang['help_template_designs'] = 'A template can belong to zero or more designs.  If a template is attached to a design then that template can be chosen for a content page, or exported with the template to an XML file';
$lang['help_template_dflt'] = 'Some templates (depending on their type) can be the &quot;default&quot; template for that type.  The &quot;default&quot; template for a type is typically used when a request is made for a specific template type, but without specifying a template name.';
$lang['help_template_name'] = 'Specify a name for this template.  The name must contain only alphanumeric characters, and must be unique to the system';
$lang['help_template_owner'] = 'You may change the authorized &quot;owner&quot; of this template.  The owner of a template is allowed to edit the template at any time (even without specific template editing permissions, and can change the additional editors.';
$lang['help_template_type'] = 'The template type is used for organizational purposes, and for finding default contents when restoring the template to its default value (if any).  Templates belonging to a certain module type may be removed if the module is uninstalled.';

#I
$lang['import_design'] = 'Import Design';
$lang['info_edittemplate_templates_tab'] = 'Select the templates that should be attached to the design.  Attaching a template to a design is used only for organizational and export purposes.  Order is not important.';
$lang['info_edittemplate_stylesheets_tab'] = 'Select the stylesheets that should be attached to the design.  Attaching a template to a design is used only for organizational and export purposes.  The order is used for determining the order in which stylesheets are output in the resulting html.';

#J


#K


#L


#M
$lang['moddescription'] = 'A module for managing layout templates';
$lang['msg_cancelled'] = 'Operation Cancelled';
$lang['msg_category_deleted'] = 'Category Deleted';
$lang['msg_category_saved'] = 'Category Saved';
$lang['msg_template_saved'] = 'Template Saved';
$lang['msg_design_deleted'] = 'Design Deleted';
$lang['msg_design_saved'] = 'Design Saved';
$lang['msg_type_saved'] = 'Template Type Saved';

#N
$lang['no'] = 'No';

#O


#P
$lang['postinstall'] = 'Design Manager Module Installed';
$lang['postuninstall'] = 'Design Manager Module Uninstalled';
$lang['prompt_add'] = 'Add';
$lang['prompt_any'] = 'Any';
$lang['prompt_category'] = 'Category';
$lang['prompt_categories'] = 'Categories';
$lang['prompt_copy'] = 'Copy';
$lang['prompt_copy_template'] = 'Copy this Template';
$lang['prompt_created'] = 'Create Date';
$lang['prompt_default'] = 'Default';
$lang['prompt_delete'] = 'Delete';
$lang['prompt_description'] = 'Description';
$lang['prompt_descriptive_name'] = 'Descriptive Name';
$lang['prompt_dflt'] = 'Default';
$lang['prompt_dflt_template'] = 'Default Template';
$lang['prompt_edit'] = 'Edit';
$lang['prompt_filter'] = 'Filter';
$lang['prompt_has_dflt'] = 'Has Default Template Contents';
$lang['prompt_help'] = 'Help';
$lang['prompt_group'] = 'Group';
$lang['prompt_id'] = 'Id';
$lang['prompt_limit'] = 'Limit';
$lang['prompt_modified'] = 'Modified Date';
$lang['prompt_na'] = 'N/A';
$lang['prompt_name'] = 'Name';
$lang['prompt_none'] = 'None';
$lang['prompt_notdflt'] = 'This is not a default template';
$lang['prompt_notdflt_tpl'] = 'This is not a default template for this template type.';
$lang['prompt_of'] = 'Of';
$lang['prompt_originator'] = 'Originator';
$lang['prompt_owner'] = 'Owner';
$lang['prompt_page'] = 'Page';
$lang['prompt_permissions'] = 'Permissions';
$lang['prompt_resource'] = 'Resource';
$lang['prompt_return'] = 'Return';
$lang['prompt_select_all'] = 'Select all';
$lang['prompt_stylesheets'] = 'Stylesheets';
$lang['prompt_template'] = 'Template';
$lang['prompt_templates'] = 'Templates';
$lang['prompt_templatetypes'] = 'Template Types';
$lang['prompt_title_na'] = 'This template type does not have a default';
$lang['prompt_design'] = 'Design';
$lang['prompt_designs'] = 'Designs';
$lang['prompt_type'] = 'Type';
$lang['prompt_unknown'] = 'Unknown';
$lang['prompt_user'] = 'User';
$lang['prompt_view'] = 'View';
$lang['prompt_with_selected'] = 'With Selected';

#Q


#R
$lang['reset_factory'] = 'Reset to Factory Defaults';

#S
$lang['submit'] = 'Submit';

#T
$lang['tpl_types'] = 'Template Types';

#U


#V

#W
$lang['warning_category_dragdrop'] = 'To change the display order of categories you can drag the rows into their correct order';
$lang['warning_deletetemplate_attachments'] = 'This design has templates attached to it.  These templates may be in use on some page or by a module. Deleting these templates, even if they are attached to no other design, may break an otherwise working site.   Please exercise extreme caution!';
$lang['warning_deletestylesheet_attachments'] = 'This design has stylesheets attached to it.  Some of the templates in use by the system but not necessarily attached to this design may rely on these stylesheets.  Deleting these styleesheets, even if they are attached to no other design may break an otherwise working site.   Please exercise extreme caution!';
$lang['warning_no_templates_available'] = 'We could find no templates that math the selected criteria or that you have permission to edit';
$lang['whats_this'] = 'What\'s this?';

#X


#Y
$lang['yes'] = 'Yes';

#Z


?>