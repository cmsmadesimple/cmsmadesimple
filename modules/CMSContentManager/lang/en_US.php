<?php
#A
$lang['addcontent'] = 'Add New Content';
$lang['apply'] = 'Apply';

#B
$lang['bulk_active'] = 'Set Active';
$lang['bulk_cachable'] = 'Set Cachable';
$lang['bulk_changeowner'] = 'Change Owner';
$lang['bulk_delete'] = 'Delete';
$lang['bulk_hidefrommenu'] = 'Hide From Menu';
$lang['bulk_inactive'] = 'Set Inactive';
$lang['bulk_insecure'] = 'Set Insecure (HTTP)';
$lang['bulk_noncachable'] = 'Set Not Cachable';
$lang['bulk_secure'] = 'Set Secure (HTTPS)';
$lang['bulk_setdesign'] = 'Set Design &amp; Template';
$lang['bulk_showinmenu'] = 'Show In Menu';

#C
$lang['cancel'] = 'Cancel';
$lang['close'] = 'Close';
$lang['colhdr_id'] = 'Id';
$lang['colhdr_active'] = 'Active';
$lang['colhdr_alias'] = 'Alias';
$lang['colhdr_copy'] = 'Copy';
$lang['colhdr_default'] = 'Default';
$lang['colhdr_delete'] = 'Delete';
$lang['colhdr_edit'] = 'Edit';
$lang['colhdr_expand'] = 'Expand';
$lang['colhdr_friendlyname'] = 'Type';
$lang['colhdr_hier'] = 'Hierarchy';
$lang['colhdr_menutext'] = 'Menu Text';
$lang['colhdr_move'] = 'Move';
$lang['colhdr_multiselect'] = 'Multiselect';
$lang['colhdr_name'] = 'Page Title';
$lang['colhdr_owner'] = 'Owner';
$lang['colhdr_page'] = 'Name';
$lang['colhdr_template'] = 'Template';
$lang['colhdr_url'] = 'URL';
$lang['colhdr_view'] = 'View';
$lang['confirm_expandall'] = 'Are you sure you want to expand all content?  On large sites this may cause problems.  Please use caution';
$lang['confirm_setdefault'] = 'Are you sure you want to change the default content page for this website?';
$lang['confirm_setinactive'] = 'Are you sure you want to set this page inactive?\nThis page will not be viewable, and may affect the behavior of a working website?';
$lang['confirm_viewpage'] = 'Are you sure you want to open a new window to view this page?';
$lang['contractall'] = 'Collapse All';

#D
$lang['desc_contentmanager_settings'] = 'Adjust settings related to listing, or editing content pages';

#E
$lang['error_bulk_permission'] = 'You do not have permission to perform this bulk action';
$lang['error_contenttype_disallowed'] = 'Default Content Type is also set to Disallowed';
$lang['error_copy_permission'] = 'You do not have permission to copy this content page';
$lang['error_delete_defaultcontent'] = 'You cannot delete the default content page';
$lang['error_delete_haschildren'] = 'You cannot delete a page that has children';
$lang['error_delete_permission'] = 'You do not have permission to delete this content page';
$lang['error_editpage_contenttype'] = 'The content type selected is invalid';
$lang['error_editpage_permission'] = 'Insufficient permission to edit content pages (or this content page)';
$lang['error_invalidbulkaction'] = 'Invalid bulk action';
$lang['error_invalidpageid'] = 'Invalid page id';
$lang['error_missingparam'] = 'A parameter required for this action was missing, or invalid';
$lang['error_movecontent'] = 'Could not move content page';
$lang['error_nobulkaction'] = 'No bulk action (or invalid bulk action) selected';
$lang['error_notconfirmed'] = 'Operation not confirmed';
$lang['error_ordercontent_nothingtodo'] = 'Nothing to do!';
$lang['error_setactive'] = 'Could not activate this page.  This could be a database problem, or a permissions issue.';
$lang['error_setdefault'] = 'Could not change the default content page.  This could be a database problem, or a permissions issue.';
$lang['error_setinactive'] = 'Could deactivate this page.  This could be a database problem, or a permissions issue.';
$lang['expandall'] = 'Expand All';

#F
$lang['find'] = 'Find';
$lang['friendlyname'] = 'Content Manager';

#G

#H
$lang['help_listsettings_namecolumn'] = 'Specify what text should be displayed in the &quot;Name&quot; column of the content list view.  Choose either the page title, or menu text';
$lang['help_listsettings_visiblecolumns'] = 'Select which columns to display in the content list view.  Use caution here as some functionality may not be available if the column is hidden';
$lang['help_pagedflt_active'] = 'Indicate wether new pages will be active by default.  Site visitors cannot navigate to inactive pages';
$lang['help_pagedflt_addteditors'] = 'Specify a default list of additional editors for each new page';
$lang['help_pagedflt_cachable'] = 'Specify wether new pages should be cachable or not.';
$lang['help_pagedflt_content'] = 'Specify the default HTML or smarty code that should be placed in the primary content area of the newly created page';
$lang['help_pagedflt_contenttype'] = 'Specify the default content type for new pages.  This is useful if using other third party modules that provide optional content editing functionality';
$lang['help_pagedflt_metadata'] = 'Specify HTML tags <em>(usually meta tags)</em> or smarty tags here that are specific to each page, and should be placed by default in the head section of each page.  You will have the ability to customize this code for each page when creating or editing the page.';
$lang['help_pagedflt_design_id'] = 'Specify the default design of the new content page.  The design is used to find the stylesheets to use';
$lang['help_pagedflt_extra1'] = 'Specify the default value for the extra1 field for newly created content pages';
$lang['help_pagedflt_extra2'] = 'Specify the default value for the extra1 field for newly created content pages';
$lang['help_pagedflt_extra3'] = 'Specify the default value for the extra1 field for newly created content pages';
$lang['help_pagedflt_searchable'] = 'Specify wether new pages should be searchable, or not.';
$lang['help_pagedflt_secure'] = 'Specify wether new pages should be <em>(by default)</em> secure <em>(uses the HTTPS protocol)</em>.  For secure pages to work the site must have a valid certificate installed on the web server, and the site must be configured properly';
$lang['help_pagedflt_showinmenu'] = 'Specify wether new pages should be visible in normal navigations by default';
$lang['help_pagedflt_template_id'] = 'Specify the default template to use for the new content page.  The template controls page logic, and what content blocks are visible to the editor';

#I
$lang['info_javascript_required'] = '<strong>Warning:</strong> In order to operate correctly, this module requires a browser with javascript support enabled.';
$lang['info_ordercontent'] = 'Drag and drop elements to adjust their order in the content tree.  <strong>Note: </strong> Some content items, such as separators do not accept children';
$lang['info_pagedflt'] = 'This panel allows setting default values for various content properties when creating a new page of type &quot;Content&quot; <em>or its derived types.  These settings will have no effect on existing pages, and user settings may override some settings.</em>';
$lang['info_preview_notice'] = '<strong>Warning:</strong> This preview panel behaves much like a browser window allowing you to navigate away from the initially previewed page. However, if you do that, you may experience unexpected behaviour. If you navigate away from the initial display and return, you may not see the un-committed content until you make a change to the content in the main tab, and then reload this tab. When adding content, if you navigate away from this page, you will be unable to return, and must refresh this panel.';

#J

#K

#L

#M
$lang['moddescription'] = 'A module for managing content within CMSMS';
$lang['msg_bulk_successful'] = 'Bulk content operation successful';
$lang['msg_editpage_success'] = 'Content Updated';
$lang['msg_cancelled'] = 'Operation Cancelled';
$lang['msg_prefs_saved'] = 'Preferences saved';

#N
$lang['no'] = 'No';

#O

#P
$lang['page'] = 'Page';
$lang['pages'] = 'Pages';
$lang['postinstall'] = 'Content module installed';
$lang['postuninstall'] = 'The content module has been uninstalled. Note: no content has been removed from CMSMS.  You are free to reinstall this module or install an alternate content management module';
$lang['preuninstall'] = 'Are you sure you want to uninstall this module?\nAlthough uninstalling this module will not destroy any content, it may prevent you from being able to manage the pages of your website.';
$lang['prompt_alias'] = 'Alias';
$lang['prompt_bulk_changeowner'] = 'Change Page Ownership';
$lang['prompt_bulk_delete_content'] = 'Bulk Delete Content Pages';
$lang['prompt_bulk_delete_content2'] = 'These pages are due to be deleted';
$lang['prompt_bulk_setdesign'] = 'Bulk Change Template and Design';
$lang['prompt_cachable'] = 'Cachable';
$lang['prompt_collapseall'] = 'Collapse all pages';
$lang['prompt_confirm_operation'] = 'Confirm Operation';
$lang['prompt_confirm1'] = 'Yes, I am sure I want to do this';
$lang['prompt_confirm2'] = 'Yes, I am really sure I want to do this';
$lang['prompt_content_id'] = 'Content ID';
$lang['prompt_created'] = 'Created';
$lang['prompt_design'] = 'Design';
$lang['prompt_editpage_addcontent']= 'Add Content Page';
$lang['prompt_editpage_contenttype'] = 'Content Type';
$lang['prompt_editpage_editcontent']= 'Edit Content Page';
$lang['prompt_expandall'] = 'Expand all content pages';
$lang['prompt_multiselect_toggle'] = 'Select this page for bulk content operations';
$lang['prompt_name'] = 'Name';
$lang['prompt_options'] = 'Options';
$lang['prompt_ordercontent'] = 'Reorder Content';
$lang['prompt_owner'] = 'Owner';
$lang['prompt_lastmodified'] = 'Last Modified';
$lang['prompt_lastmodifiedby'] = 'Last Modified By';
$lang['prompt_listsettings'] = 'Content List Settings';
$lang['prompt_list_namecolumn'] = 'Content Field to Display in Name Column';
$lang['prompt_list_visiblecolumns'] = 'Visible Columns';
$lang['prompt_pagedefaults'] = 'New Page Defaults';
$lang['prompt_pagedflt_active'] = 'Is this page active?';
$lang['prompt_pagedflt_addteditors'] = 'Additional Editors';
$lang['prompt_pagedflt_cachable'] = 'Is this page cachable?';
$lang['prompt_pagedflt_content'] = 'Default Content';
$lang['prompt_pagedflt_contenttype'] = 'Default Content Type';
$lang['prompt_pagedflt_design_id'] = 'Default Page Design';
$lang['prompt_pagedflt_extra1'] = 'Extra1 Field value';
$lang['prompt_pagedflt_extra2'] = 'Extra2 Field value';
$lang['prompt_pagedflt_extra3'] = 'Extra3 Field value';
$lang['prompt_pagedflt_metadata'] = 'Metadata';
$lang['prompt_pagedflt_searchable'] = 'Is the contents of this page searchable';
$lang['prompt_pagedflt_secure'] = 'Is this page secure?';
$lang['prompt_pagedflt_showinmenu'] = 'Can this page show in the menu?';
$lang['prompt_pagedflt_template_id'] = 'Default Page Template';
$lang['prompt_pagelimit'] = 'Page Limit';
$lang['prompt_page_collapse'] = 'Hide children of this page';
$lang['prompt_page_expand'] = 'Show children of this page';
$lang['prompt_page_copy'] = 'Copy this page';
$lang['prompt_page_default'] = 'This is the default page.  i.e: The home page for the website';
$lang['prompt_page_delete'] = 'Delete this page';
$lang['prompt_page_edit'] = 'Edit this page';
$lang['prompt_page_menutext'] = 'Menu Text';
$lang['prompt_page_setactive'] = 'Set this page active.';
$lang['prompt_page_setdefault'] = 'Set this page to be the default page for your website.';
$lang['prompt_page_setinactive'] = 'Set this page inactive.';
$lang['prompt_page_sortdown'] = 'Move this page down in page order amongst its peers';
$lang['prompt_page_sortup'] = 'Move this page up in page order amongst its peers';
$lang['prompt_page_template'] = 'Edit this template';
$lang['prompt_page_title'] = 'Title';
$lang['prompt_page_view'] = 'View this page in another window';
$lang['prompt_preview'] = 'Preview';
$lang['prompt_secure'] = 'Secure';
$lang['prompt_settings'] = 'Settings';
$lang['prompt_template'] = 'Template';
$lang['prompt_title'] = 'Title';
$lang['prompt_withselected'] = 'With Selected';

#Q

#R
$lang['reorderpages'] = 'Reorder Content';
$lang['revert'] = 'Revert';

#S
$lang['select_all'] = 'Select All';
$lang['submit'] = 'Submit';

#T
$lang['title_contentmanager_settings'] = 'Content Manager Settings';
$lang['title_editpage_apply'] = 'Save the changes to this content page, and continue editing';
$lang['title_editpage_cancel'] = 'Abandon all unsaved changes to this page, and return to the content list';
$lang['title_editpage_submit'] = 'Save the changes to this content page, and return to the content list';
$lang['title_editpage_view'] = 'View the current content page <em>(saved changes only)</em>.';
$lang['title_userpageoptions'] = 'Page List Options';
$lang['toggle'] = 'Toggle Selected';

#U

#V

#W

#X

#Y
$lang['yes'] = 'Yes';

#Z

?>