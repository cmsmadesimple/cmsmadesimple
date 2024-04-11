<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

$lang['friendlyname'] = 'User Guide';
$lang['admindescription'] = 'For displaying and editing the a customisable CMS User Guide';
$lang['need_permission'] = 'You need permission to use this module';
$lang['title_userguide_settings'] = 'User Guide Settings';
$lang['desc_userguide_settings'] = 'Settings for the User Guide module';


# Install
$lang['ask_uninstall'] = 'Are you sure you want to uninstall the User Guide module? All User Guide
information will be permanently deleted.';


# User Guide pages
$lang['smarty_error'] = 'This page may include a smarty tag that is not displaying correctly. Please either add {literal}{/literal} tags, remove, or turn off smarty processing.';
$lang['admin_only_visible'] = 'only visible to Admin users';


# Pages Tab
$lang['tab_pages'] = 'Pages';
$lang['submit'] = 'Sumbit';
$lang['cancel'] = 'Cancel';
$lang['save_options'] = 'Save Options';
$lang['apply'] = 'Apply';
$lang['add_item'] = 'Create New User Guide Page';
$lang['edit_item'] = 'Edit User Guide Page';
$lang['item_saved'] = 'This User Guide Page is now saved';
$lang['item_notsaved'] = 'This User Guide Page did not save';
$lang['edit'] = 'Edit this User Guide Page';
$lang['delete'] = 'Delete this User Guide Page';
$lang['confirm_delete'] = 'Are you sure that you want to delete this User Guide Page';
$lang['item_deleted'] = 'This User Guide Page is now deleted';
$lang['order_error'] = 'Error found and corrected with page order - please try again';
$lang['view_user_guide'] = 'View User Guide';


# Options Tab
$lang['tab_options'] = 'Options';
$lang['title_customModuleName'] = 'Custom Module Name';
$lang['title_adminSection'] = 'Module Admin Section';

$lang['title_separate_settings'] = 'Separate menu entry for User Guide settings';
$lang['title_customCSS'] = 'Custom CSS';
$lang['text_customCSS'] = 'To add custom CSS for the user guide pages, create the file \'assets/module_custom/UserGuide/custom.css\'.
   You can use the .guide class to target the User Guide content.';
$lang['title_useSmarty'] = 'Use Smarty';
$lang['help_useSmarty'] = 'Process all User Guide pages through Smarty before displaying them?
   If enabled you will need to use {literal} tags around any curly brackets { & } in the content.';
$lang['settings_saved'] = 'Your Options have been saved.';
$lang['saved_and_imported'] = 'Options saved and imported User Guide pages.';
$lang['import_completed'] = 'User Guide pages and settings imported.';
$lang['file_error'] = 'Filename error - not selected.';
$lang['import_error'] = 'Import error - file did not import correctly.';
$lang['title_export'] = 'Export content to XML';
$lang['title_import'] = 'Import content from XML';
$lang['title_import_export'] = 'Import & Export content';
$lang['module_import_database_error'] = 'Database import failed';

$lang['xml_export'] = 'XML Export';
$lang['xml_import'] = 'Import User Guide Content';
$lang['title_exportImageFolder'] = 'Export Image Folder';
$lang['text_exportImageFolder'] = 'Location of User Guide images to include in export, e.g. "images/UserGuide". Location is relative to uploads_url. Leave empty to skip image export.';
$lang['export_completed'] = 'User Guide pages and settings exported.';
$lang['title_import_UsersGuide'] = 'Import from UsersGuide module';
$lang['title_import_UserGuide2'] = 'Import from UserGuide2 module';
$lang['import_UsersGuide'] = 'Import Content & Settings';
$lang['text_UsersGuide'] = 'from UsersGuide Module';
$lang['text_UserGuide2'] = 'from UserGuide2 Module';
$lang['none'] = 'none';


# Edit User Guide Page
$lang['error_title_empty'] = 'A title is required';
$lang['title_title'] = 'Title';
$lang['title_active'] = 'Active';
$lang['title_content'] = 'Content';
$lang['title_admin'] = 'Admin only';
$lang['prompt_admin'] = 'only visible to full Admin users';
$lang['title_embed_type'] = 'Video type';
$lang['title_embed_code'] = 'Video id or code';
$lang['title_embed_first'] = 'Show video above content';
$lang['embed_type'] = 'Video type';
$lang['embed_type_vimeo'] = 'Vimeo (ID only)';
$lang['embed_type_youtube'] = 'YouTube (ID only)';
$lang['embed_type_local'] = 'Local video (URL)';
$lang['embed_type_code'] = 'Code (HTML)';
$lang['embed_type_prompt_vimeo'] = 'Vimeo ID - full url after /video/: 929800455?badge=0&autopause=0&player_id=0&app_id=58479';
$lang['embed_type_prompt_youtube'] = 'YouTube ID - e.g. n4IhCSMkADc?si=YJ4K5qsrgl9LXW0a';
$lang['embed_type_prompt_local'] = 'Local video URL relative to uploads_url - e.g. images/UserGuide/video.mp4';
$lang['embed_type_prompt_code'] = 'Full Embed Code';










###    ###   #########   ###        #########
###    ###   #########   ###        #########
###    ###   ###         ###        ###   ###
##########   #########   ###        #########
##########   #########   ###        #########
###    ###   ###         ###        ###
###    ###   #########   #########  ###
###    ###   #########   #########  ###

$lang['help'] = "
<h3>What does this do?</h3>
<p>'UserGuide' provides an online CMS User Guide. It is easy to access for Editors and easy to update and customise for Admins. The completed User Guide can easily be printed directly from the browser.</p><br>
<p>User Guide pages can now easily include responsive video content, from Vimeo, YouTube, a local video file, or full embed code for external content.</p><br>
<p>'UserGuide' module is a replacement for the 'UserGuide2' module and can import all contact from UserGuide2 and UsersGuide modules.</p>
<br>

<h3>Getting Started</h3>
<p>1. Install the module</p>
<p>2. Check module permissions. All 'editors' have default USE permission to view the User Guide.</p>
<br>

<h3>Printing a User Guide</h3>
<p>Simply select Print in the browser menu or press 'Ctrl + P'. </p>
<p>Tip: Select the option to print 'Headers & Footers' (if available)</p>
<br>

<h3>Options</h3>
<p>The following Admin options are available:</p>
<ul>
   <li>Add, edit, delete, rename</li>
   <li>Inculde a video on the User Guide page, from Vimeo, YouTube, a local video file, or full embed code for external content.</li>
   <li>Set a page for 'Admin only' visibility or to be 'Active' or not</li>
   <li>Drag-n-drop reordering of the User Guide pages/tabs</li>
   <li>Custom module name</li>
   <li>Choose admin menu section</li>
   <li>Custom CSS - see below</li>
   <li>Use Smarty processing on the User Guide pages. If enabled you may need to use {literal}{/literal} or {ldelim}{rdelim} tags in the page content.</li>
   <li>Import/Export - see below</li>
   <li>Import from UserGuide2 or UsersGuide module - see below</li>
</ul>
<br>

<h3>Custom CSS</h3>
<p>To add custom CSS for the user guide pages, create the file 'assets/module_custom/UserGuide/custom.css'. This will be in addition to the Admin & User Guide CSS.</p>
<p>You can use the .guide class to target the User Guide content.</p>
<br>

<h3>Import/Export User Guide</h3>
<p>All User Guide pages can be easily be exported to an XML file.</p>
<p>A full User Guide can be imported to a fresh installation of the User Guide module. Any imported pages will be added after any existing pages. Options will not be changed.</p>
<p>If an 'Export Image Folder' is set then the export will include all images from this folder. When imported any images will be imported to this same folder.</p>
<br>

<h3>Import from UserGuide2 or UsersGuide module</h3>
<p>If the UserGuide2 or UsersGuide modules are installed, an option will be shown to import all content and settings from the the older modules. This includes the custom module name (with '*' added), and admin section.</p>
<br>


<h3>Permissions</h3>
<ul>
   <li>'UserGuide - Use' - can view the user guide, except any 'Admin only' pages</li>
   <li>'UserGuide - Manage' - can do everything, and view all pages.</li>
</ul><br>


<h3>Support</h3>
<p>As per the GPL licence, this software is provided as is. Please read the text of the license for the full disclaimer.
The module author is not obligated to provide support for this code. However you might get support through the following:</p>
<ul>
   <li>For support, first <strong>search</strong> the <a href=\"//forum.cmsmadesimple.org\" target=\"_blank\">CMS Made Simple Forum</a>, for issues with the module similar to those you are finding.</li>
   <li>Then, if necessary, open a <strong>new forum topic</strong> to request help, with a thorough description of your issue, and steps to reproduce it.</li>
   <li>If you find a bug you can <a href=\"http://dev.cmsmadesimple.org/bug/list/1429\"  target=\"_blank\">submit a Bug Report</a>.</li>
   <li>For any good ideas you can <a href=\"http://dev.cmsmadesimple.org/feature_request/list/1429\"  target\"_blank\">submit a Feature Request</a>.</li>
   <li>If you found the Module useful - shout out to me on Twitter <a href=\"//twitter.com/KiwiChrisBT\">@KiwiChrisBT</a></li>
</ul><br>

<h3>Copyright &amp; Licence</h3>
<p>Copyright © 2016, Chris Taylor <chris at binnovative dot co dot uk>. All Rights Are Reserved.</p><br>
<p>This module has been released under the GNU Public License v3. However, as a special exception to the GPL, this software is distributed as an addon module to CMS Made Simple. You may only use this software when there is a clear and obvious indication in the admin section that the site was built with CMS Made Simple!</p><br>
<p>Inspired by: UsersGuide module by jissey.</p><br>";


#########  ###    ###  ##########  ###    ###  #########  ########  ###       #########  #########
#########  ###    ###  ##########  ####   ###  #########  ########  ###       #########  #########
###        ###    ###  ###    ###  #####  ###  ###        ###       ###       ###   ###  ###
###        ##########  ##########  ### ## ###  ###        ########  ###       ###   ###  ###
###        ##########  ##########  ###  #####  ###   ###  ########  ###       ###   ###  ###   ###
###        ###    ###  ###    ###  ###   ####  ###   ###  ###       ###       ###   ###  ###   ###
#########  ###    ###  ###    ###  ###    ###  #########  ########  ######### #########  #########
#########  ###    ###  ###    ###  ###    ###  #########  ########  ######### #########  #########

$lang['changelog'] = '

<h3>Version 1.0 - 04Apr24</h3>
<ul>
   <li>Initial release of the UserGuide module. A fork and replacement for UserGuide2 module.</li>
   <li>This module can import all content and settings from the UserGuide2 and UsersGuide modules if they are already installed.</li>
   <li>Can export and import content and settings and files to/from xml file</li>
   <li>Can include video content from Vimeo, YouTube, local files and also embedded code.</li>
   <li>Includes default video User Guides for CMS Made Simple.</li>
   <li>By default all Editors have view access with options to set permissions for all user groups.</li>
</ul>
<br>
';