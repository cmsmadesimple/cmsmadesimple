<?php
/* H */
$lang['help_cmscontentmanager_help'] = <<<EOT
<h3>Introduction</h3>
<p>This document describes the CMS Content Manager module.  It is aimed primarily at the website designer or developer and describes in broad terms how content items work in CMS Made Simple.</p>
<p>The primary interface of the Content Manager module is the content list.  It displays content items in a tabular format and provides the ability to quickly search, navigate, and manage multiple content items.</p>
<h3>Content Items</h3>
<p>TODO: Describe content items here broadly, the different types, and how they relate to building a navigation.</p>
<ul>
  <li>Content Page
    <p>This content item type is most similar to an HTML page.  When editors create content page items they select a design and template which controls the appearance of the page, specify a title, and specify content for the page.</p>
    <p>Content items may also contain forms, logic, or display dynamic data from modules, or user defined tags (UDTs).  Thiis flexibility allows creating specialized applications, or extremely flexible and dynamic websites.</p>
  </li>
  <li>Link
    <p>This content item type is only displayed in navigations.  It generates a link to a page on an external website.</p>
  </li>
  <li>Page Link
    <p>This content item type is also only displayed in navigations.  It generates an additional link to an existing content page.</p>
  </li>
  <li>Separator
    <p>This content item type is also used in navigations.  In some types of navigations it will generate a horizontal (or vertical) devider between navigation items.  Some types of navigations may not display separators at all.</p>
  </li>
  <li>Section Header
    <p>The section header is also only displayed in navigations.  It is used to organize content items.  It provides a textual header above, or between other content items.</p>
  </li>
  <li>Error Page
    <p>The Error Page is a special type of content item type.  It is used when a user attempts to navigate to a content item that is either not navigable or does not exist.</p>
  </li>
</ul>
<p>Many third party modules provide more content types to serve different purposes.  Such as displaying catalogs of products, or restricting the content to authorized users.</p>
<h3>The Content Item List</h3>
<p>The content list is the primary interface to the module.  This form provides the main management interface for your content. From here you can create, edit, delete, copy, deactivate, and organize your content items.  This screen is heavily optimized for larger websites providing pagination and search mechanisms to display only a small amount of pages at a time, but to quickly find the item you would like to edit.</p>
 <h4>Columns</h4>
<p>Each content item is displayed as a row in a table.  There are a number of columns to quickly display various attributes of each content item, and some convenient action icons.  Some columns may be hidden from view entirely, or only for some rows depending upon a number of factors:</p>
  <ul>
    <li>Your Access Permissions and Page Ownership:
      <p>If your account is restricted to certain permissions some columns may not be displayed, or may be disabled.</p>
    </li>
    <li>System Preferences and Site Configuration
      <p>Some system preferences, and site configuration options will result in some columns from being disabled.  For example, the &quot;url&quot; column </p>
    </li>
    <li>The content type
      <p>Depending on the content type, certain columns may become irrelevent.  For example, it is not possible for &quot;Section Headers&quot; or &quot;Separators&quot; to become the default page, so nothing will be displayed in the &quot;default&quot; column for those content items.</p>
    </li>
    <li>Whether the content item is being edited
      <p>When other users <em>(or even yourself) are editing a content item, some columns will be hidden in the row for each content type to prevent modifying, deleting or copying the content page.</p>
    </li>
  </ul>
  <h5>Column List</h5>
      <p>The Content Manager module provides a flexible mechanism for hiding and showing different columns in the content list.  Additionally, some columns may be hidden based on the site configuration.  For example, the URL column is hidden if pretty <em>(Search Enging Friendly)</em> URLS are not configured.</p>
      <p>Each column in the content list display has a special purpose:</p>
   <ul>
     <li>Expand/Collapse Column
      <p>When a content item has children this column will be filled with an icon that allows expanding the list to show the item children, or collapsing the list to hide them.  The state of which items are expanded, and which are collapsed is saved on a per-user basis.  So that when you revisit the content manager the expanded/collapsed state of your pages will remain the same.</p>
     </li>

     <li>Hierarchy Column
      <p>The hierarchy column displays the organization of each content page in a numeric state.  The hierarchy of the first root level page begins with 1 and increases incrementally for each peer. Each child begins with the 1, and its peers increment incrementally.   Therefore, the second grandchild of the third child of the first page in the content list would have a hierarchy of 1.3.2.</p>
      <p>The hierarchy mechanism is a significant portion of what provides the ability of CMS Content Manager to organize content items, and then build navigations from them.</p>
     </li>
     
     <li>Page Title / Menu Text Column
      <p>This column can either display the page title, or the page menutext.  This depends on a setting in the &quot;Site Admin &raquo; Content Manager Settings&quot; page.</p>
      <p>This column will contain a link to allow editing the content item <em>(unless the content item is locked)</em>.  Hovering over the text in this column will display additional information about the content item such as the unique numeric content id, and wether the page is cachable or not.</p>
      <p>If the content item is locked, hovering over the text in the column will display information about who locked the item, and when thelock expires.</p>
     </li>
     <li>URL Column
      <p>If enabled, this column will display any alternate URL for this content item.  <em>(Note: Only certain content item types support alternate an alternate URL).</em></p>
     </li>
     <li>Page Alias Column
      <p>This column displays the unique alias associated with each page.  Aliases are text strings that uniquely identify the content item.  You use the content items alias (or numeric id) when you need to refer to a page within the system.<em>(Note: Some content types do not have aliases).</em></p>
     </li>
     <li>Template Column</li>
      <p>This column displays the design and template that is used to display the content for the item.  See the help for the &quot;Design Manager&quot; module for an explanation of how CMSMS manages designs, including stylesheets and templates.  <em>(Note: Some content item types do not use a design, or a template.)</em></p>
     <li>Type Column
       <p>This column indicates the content type (i.e: Content, Section header, Separator, etc.).<p>
     </li>
     <li>Owner Column
       <p>The owner column displays the username of the owner of the content item.  Hovering over the text in this column will display information as to when the content item was created and last edited.</p>
     </li>
     <li>Active Column.
       <p>This column displays icons to show the current active state of the content item.  Active items can be navigated to, and will appear in navigation menus on the frontend.  If your user account has sufficient privilege to the content item you can click on the icon to toggle its active state.</p>
     </li>
     <li>Default Column.
       <p>This column displays whether the content item is the default page or not.  The default content item is the home page for your website.  Only some content types allow the content type to be the default.</p>
       <p>If your user account has sufficient privilege, and the content type supports being the default content for the website you can click on the icon to change the default flag to a different page.</p>
     </li>
     <li>The &quot;Move&quot; Column.
       <p>Depending upon your access privileges, you may see icons that allow changing the order of the content items with respect to its immediate peers.</p>
     </li>
     <li>Action Icons.
     <p>Depending on your access privileges, the content type and its current lock state you may see different icons on each content row allowing different functionality:</p>
       <ul>
         <li>View - Open up a new browser window <em>(or tab)</em> and view the content item as your visitors will see it.</li>
         <li>Copy - Copy the content item to a new content item.
           <p>A new content item will be created with a new page title, and alias and you will be presented with the ability to edit the new page.</p>
         </li>
         <li>Delete - Delete the content item.
           <p>Depending upon your access privileges, and wether or not the content item has children, the option to delete the content item may be hidden or disabled.</p>
         </li>
         <li>Steal Lock
           <p>For content items that are currently being edited, but for which the lock has expired <em>(the editor has not made a change to the form for a while)</em> this option will allow you to steal the lock.</p>
         </li>
         <li>Bulk operations checkbox.
           <p>The bulk operations checkbox allows selecting multiple content items to operate on en-mass.</p>
         </li>
       </ul> 
     </li>
   </ul>

 <h4>Edit Ability</h4>
   <p>The ability to edit a content item is determined either by permission <em>(see the Manage All Content, and Modify Any Page permissions below)</em>, or by being the owner, or additional editor of a content item.</p>
 <h4>Owners</h4>
   <p>By default, the owner of a content item is the user that initially created it.  Owners, or users with &quot;Manage All Content&quot; permission can give ownership of a page to another user.</p>
 <h4>Additional Editors</h4>
    <p>When editing a content item as an owner or as a user with &quot;Manage All Content&quot; permission, the user can select other administrative users, or admin groups that are also allowed to edit that content item.</p>
 <h4>Relevent Permissions.</h4>
    <p>There are a few permissions that effect what columns are displayed in the content list and the ability to interact with the content list:</p>
    <ul>
      <li>Add Pages
    <p>This permission allows users to create new content items. Additionally, users with this permission are able to copy content items that they have edit ability on.</p>
      </li>
      <li>Modify Any Page 
        <p>Users with this position will have the ability to edit any content item.  It is similar to being an &quot;Additional Editor&quot; on all content items.</p>
      </li>
      <li>Remove Pages
        <p>This permission allows users to remove pages that they have edit ability on.  Without this permission, the delete icon on each content item row in the content list will be hidden.</p>
      </li>
      <li>Reorder Pages
        <p>This permission allows users who have edit ability to all siblings of a page to re-arrange pages amongst those siblings.</p>
        <p>i.e: A user in a group who has edit ability to the page with hierarchy 1.3 and all of its direct siblings <em>(1.1, 1.2, 1.3, 1.4, etc).</em> will be able to re-arrange those pages in the navigation.</p>
        <p>Users without this permission will not see the move up/down icons in listcontent.</p>
      </li>
      <li>Manage All Content
        <p>This permission provides super-user capability on all content items.  Users with this permission can add, edit, delete, and re-order any content item.  They also have the ability to set the default content item, and perform bulk actions like change ownership that may or may not be available to users with other permissions.</p>
      </li>
    </ul>
   <p>It is possible for an admin user account to not be a member of any groups, and for that admin user account still have the ability <em>(as an owner or additional editor)</p> to edit some content items.</p>
 <h4>Content Locking</h4>
   <p>Content locking is a mechanism that prevents two editors from editing the same item at the same time, and therefore destroying each others work.  Admin users are given exclusive access to a content item until such time as they submit the changes.</p>
   <p>If a content item is locked, you will not be able to edit it until the lock has expired.  See below for information on lock expiring.  Once a lock has expired, a user will have the option of stealing the lock from the original editor and beginning a new edit session.</p>
   <p>A special icon is displayed on the content item row to indicate that the lock can be stolen.</p>
 <h4>Configuration</h4>
   <p>Some configuration items effect the visibility of certain items in the content list:</p>
 <h4>Other functionality</h4>
   <ul>
     <li>Pagination
       <p>The content list can be paginated.  This is a performance feature for large sites with a great deal of content items.  The default limit is 500 items, however this limit can be lowered by adjusting the value in the options dialog.</p>
     </li>
     <li>Expand/Collapse All
       <p>These options allow expanding all content items with children so that the children are visible.  Or, conversely collapsing all content items with children so that the children are not visible.  It is useful to easily find a content item, or to get an overview of the website structure.  Each content item with children can still be expanded, or collapsed individually.</p>
     </li>
     <li>Searching
       <p>The &quot;Find&quot; textbox in the top left corner of the content list allows users to quickly and easily find a content item by its title, or menu text.  This form uses ajax and autocomplete to display a dropdown list of all items matching the string entered (a minimum of three characters is required).</p>
     </li>
     <li>Bulk Actions
       <p>The &quot;With Selected&quot; form at the bottom right of the content list allows users with appropriate access to modify, or interact with content items en-masse.  Numerous options are available (depending both on the selected items, and the users access permission):</p>
       <ul>
         <li>Delete
           <p>This option allows deleting multiple content items (and their children) in few steps.  All of the selected content items and their descendants will be analyzed for the their eligibility to be deleted.  Users will then be prompted with a list of the content items that passed the analysis <em>(if any)</em> and to confirm the action.</p>
	   <p>Only users with the permission to remove pages and modify any page, or Manage All Content can use this option.</p>
           <p><strong>Note:</strong> When selecting many content items, or content items with many descendants, this can be a very memory, database and time intensive operation.</p>
         </li>
         <li>Set Active
           <p>This option will ensure that the content items selected are marked as &quot;Active&quot;.  Users will be asked to confirm the operation. This operation does not work on descendents of the selected pages.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Inactive
           <p>this option analyzes the selected items for eligibility, and will set all of the eligible content items to inactive.  Inactive pages cannot be navigated to, and may break a working website.  The default page cannot be set to inactive.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Cachable
           <p>This option sets the selected content items to &quoc;cachable&quot;.  This may have different effects based on the site configuration:<p>
           <p>If enabled in &quot;Site Admin >> Global Settings&quot; then content items marked as &quot;cachable&quot; can be cached by the browser <em>(this reduces load on your webserver for users visiting the same page frequently).</em>
           <p>Also in &quot;Site Admin >> Global Settings&quot; Smarty caching effects cachable pages.  This is an advanced tool that will cache the generated html code of a for repeated use, and can dramatically reduce server load and improve performace.  However, it is an advanced topic and may negatively the dynamic nature of some content items.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Not Cachable
           <p>This option ensures that the selected content items are not cachable.<p>
         </li>
         <li>Show In Menu
           <p>This option ensures that the selected content items are visible in frontend navigation menus.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Hide From Menu
           <p>This option ensures that the selected content items will not be visible (by default) in frontend navigation menus.  Various options of navigation generation modules may override the &quot;Show In Menu&quot; setting.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Secure (HTTPS)
           <p>This option will ensure that HTTPS will be used when the selected content items are displayed.</p>
           <p><strong>Note:</strong> You may need to adjust the secure URL settings in the CMSMS config.php file, and to contact your host about proper SSL configuration.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Insecure (HTTP)
           <p>This option removes the HTTPS flag from the selected content items.</p>
           <p><strong>Note:</strong> Content items without the secure <em>(HTTPS)</em> may still be accessed via the HTTPS protocol.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Design & Template
           <p>This option will display a form to set the design and template that are associated with the selected content items.  Only some content item types have a design and template association. i.e: the &quot;content&quot; item type, and those provided by other modules that provide similar functionality.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Owner
           <p>This option displays a form that allows changing the ownership of the selected content items.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
       </ul>
     </li>
     <li>Reordering
       <p>Users with the &quot;Manage All Content&quot; permission have the ability to re-organize content items en-masse by selecting the &quot;Reorder Pages&quot; item from the options menu on the content list display.   This provides a form where content items can be re-ordered with simple drag and drop operations.</p>
       <p><strong>Note:</strong> This can be a very memory and database intensive operation, and we do not suggest using this option on websites with more than a few hundred content items.</p>
     </li>
   </ul>

<h3>Adding Content Pages</h3>
  <h4>Content Types</h4>
  <h4>
<h3>Editing Content Pages</h3>
EOT;
$lang['help_myaccount_admincallout'] = 'If enabled administrative bookmarks <em>(shortcuts)</em> will be enabled allowing you to manage a list of frequently used actions in the admin console.';
$lang['help_myaccount_admintheme'] = 'Select an adminstration theme to use.  Different administration themes have different menu layouts, work better for mobile displays, and have varous additional features.';
$lang['help_myaccount_ce_navdisplay'] = 'Select which content field should be displayed in content lists.  Options include the page title, or menu text.  If &quot;None&quot; is selected, then the site preference will be used';
$lang['help_myaccount_dateformat'] = 'Specify a date format string to use when dates are displayed.  This string uses <a href="http://php.net/manual/en/function.strftime.php">strftime</a> format.  <strong>Note:</strong> some third party addons may not obey this setting.</strong>';
$lang['help_myaccount_dfltparent'] = 'Specify the default parent page for creating a new content page.  The use of this setting also depends on your content editing permissions.<br/><br/>Drill down to the selected default parent page by selecting the topmost parent, and successive child pages from the provided dropdowns.<br/><br/>The text field on the right will always indicate which page is currently selected.';
$lang['help_myaccount_email'] = 'Specify an email address.  This is used for the lost password functionality, and for any notification emails sent by the system (or addon modules).';
$lang['help_myaccount_enablenotifications'] = 'If enabled, the system will display various notifications about things that need to be taken care of in the navigation';
$lang['help_myaccount_firstname'] = 'Optionally specify your given name.  This may be used in the admin theme, or to personally address emails to you';
$lang['help_myaccount_hidehelp'] = 'If enabled the system will hide module help links from the admin console.  In most circumstances the help provided with modules is targetted towards site developers and may not be useful to content editors.';
$lang['help_myaccount_homepage'] = 'You may select a page to automatically direct to when you login to the CMSMS admin console.  This may be useful when you primarily use one function.';
$lang['help_myaccount_ignoremodules'] = 'If admin notifications are enabled you can select to ignore notifications from some modules';
$lang['help_myaccount_indent'] = 'This option will indent the content list view to illustrate the parent and child page relationship';
$lang['help_myaccount_language'] = 'Select the language to display for the admin interface.  The list of available languages may vary on each CMSMS install';
$lang['help_myaccount_lastname'] = 'Optionally specify your surname.  This may be used in the admin theme, or to personally address emails to you';
$lang['help_myaccount_password'] = 'Please enter a unique, and secure password for this website.  The password should be more more than six characters long, and should use a combination of upper case, lower case, non alphanumeric, and digits.  Please leave this field blank if you do no wish to change your password.';
$lang['help_myaccount_passwordagain'] = 'To reduce errors, please enter your password again.  Leave this field empty if you do not wish to change your password.';
$lang['help_myaccount_syntax'] = 'Select which syntax highlighting module to use when editing HTML, or smarty code.  The list of available modules may change depending on what your site administrator has configured';
$lang['help_myaccount_username'] = 'Your username is your unique name for the CMSMS admin panel.  Please use only alphanumeric characters and the underscore';
$lang['help_myaccount_wysiwyg'] = 'Select which WYSIWYG <em>(What You See Is What You Get)</em> module to use when editing HTML content.  You may also select &quot;None&quot; if you are comfortable with HTML.  The list of available WYSIWYG editors may change depending on what t he site administrator has configured.';

/* S */
$lang['settings_searchmodule'] = 'Select the module that should be used to index words for searching, and will provide the site search capabilities';
$lang['settings_autocreate_url'] = 'When editing content pages, should SEF/pretty URLS be auto-created?  Auto creating URLS will have no effect if pretty urls are not enabled in the CMSMS config.php file.';
$lang['settings_nosefurl'] = 'To configure <strong>S</strong>each <strong>E</strong>ngine <strong>F</strong>riendly <em>(pretty)</em> URLs you need to edit a few lines in your config.php file and possibly to edit a .htaccess file or your web servers configuration.   You can read more about configuring pretty URLS <a href="http://docs.cmsmadesimple.org/configuration/pretty-url">here</a>';
$lang['settings_autocreate_flaturls'] = 'If SEF/pretty URLs are enabled, and the option to auto-create urls is enabled, this option indicatest hat those auto-created URLS should be flat <em>(i.e: identical to the page alias)</em>.  <strong>Note:</strong> The two values do not need to remain identical, the URL value can be changed to be different than the page alias in subsequent page edits';
$lang['settings_mandatory_urls'] = 'If SEF/pretty URLs are enabled, this option indicates wether page URLS are a required field in the content editor.';
$lang['settings_badtypes'] = 'Select which content types to remove from the content type dropdown when editing or adding content.  This feature is useful if you do not want editors to be able to create certain types of content.  Use CTRL+Click to select, unselect items.  Having no selected items will indicate that all content types are allowed. <em>(applies to all users)</em>';
$lang['settings_basicattribs'] = 'This field allows you to specify which content properties that users without the &quot;Manage All Content&quot; permission are allowed to edit. The selected properties will appear in the &quot;Main Tab&quot; on the edit content page.<br/><br/>This feature us useful when you have content editors with restricted permission and want to control what content properties that they are allowed to edit.';
$lang['settings_imagefield_path'] = 'This setting is used when editing content.  The directory specified here is used to provide a list of images from which to associate an image with the content page.<br/></br/>Relative to the image uploads path, specify a directory name that contains the paths containing files for the image field';
$lang['settings_thumbfield_path'] = 'This setting is used when editing content.  The directory specified here is used to provide a list of images from which to associate a thumbnail with the content page.<br/><br/>Relative to the image uploads path, specify a directory name that contains the paths containing files for the image field.  Usually this will be the same as the path above.';
$lang['settings_contentimage_path'] = 'This setting is used when a page template contains the {content_image} tag.  The directory specified here is used to provide a selection of images to associate with the tag.<br/><br/>Relative to the uploads path, specify a directory name that contains the paths containing files for the {content_image} tag.  This value is used as a default for the dir parameter';
$lang['settings_mailtest_testaddress'] = 'Specify a valid email address that will receive the test email';
$lang['settings_mailprefs_mailer'] = 'This choice controls how CMSMS will send mail.  Using PHPs <a href="http://www.cmsmadesimple.org/downloads/">mail</a> function, sendmail, or by communicating directly with an SMTP server.<br/><br/>The &quot;mail&quot; option should work on most shared hosts, however it almost certainly will not work on most self hosted windows installations.<br/><br/>The &quot;sendmail&quot; option should work on most properly configured self hosted linux servers.  However it may not work on shared hosts.<br/><br/>The SMTP Option requires configuration information from your host.';
$lang['settings_mailprefs_from'] = 'This option controls the <em>default<em> address that CMSMS will use to send email messages.  This cannot just be any email address.  It must match the domain that CMSMS is providing.  Specifying a personal email address from a different domain is known as &quot;<a href="https://en.wikipedia.org/wiki/Open_mail_relay">relaying</a>&quot; and will most probably result in emails not being sent, or not being accepted by the receipient email server.  A typical good example for this field is noreply@mydomain.com';
$lang['settings_mailprefs_fromuser'] = 'Here you can specify a name to be associated with the email address specified above.  This name can be anything but should reasonably correspond to the email address.  i.e: &quot;Do Not Reply&quot;';
$lang['settings_mailprefs_smtphost'] = 'When using the SMTP mailer this option specifies the hostname <em>(or IP address)</em> of the SMTP server to use when sending email.  You may need to contact your host for the proper value.';
$lang['settings_mailprefs_smtpport'] = 'When using the SMTP mailer this option specifies the integer port number for the SMTP server.  In most cases this value is 25, though you may need to contact your host for the proper value.';
$lang['settings_mailprefs_smtptimeout'] = 'When using the SMTP mailer, this option specifies the number of seconds before an attempted connection to the SMTP server will fail.  A typical value for this setting is 60.<br/><br/><strong>Note:</strong> If you need a longer value here it probably indicates an underlying DNS, routing or firewall problem, and you may need to contact your host.';
$lang['settings_mailprefs_smtpauth'] = 'When using the SMTP mailer, this option indicates that the SMTP server requires authentication to send emails.  You then must specify <em>(at a minimum)</em> a username, and password.  Your host should indicate wether SMTP authentication is required, and if so provide you with a username and password, and optionally an encryption method.<br/><br/><strong>Note:</strong> SMTP authentication is required if your domain is using google apps for email.';
$lang['settings_mailprefs_smtpsecure'] = 'This option, when using SMTP authentication specifies an encryption mechanism to use when communicating with the SMTP server.  Your host should provide this information if SMTP authentication is required.';
$lang['settings_mailprefs_smtpusername'] = 'This is the username for connecting to the SMTP server if SMTP authentication is enabled.';
$lang['settings_mailprefs_smtppassword'] = 'This is the password for connecting to the SMTP server if SMTP authentication is enabled.';
$lang['settings_mailprefs_sendmail'] = 'If using the &quot;sendmail&quot; mailer method, you must specify the complete path to the sendmail binary program.  A typical value for this field is &quot;/usr/sbin/sendmail&quot;.  This option is typically not used on windows hosts.<br/><br/><strong>Note:</strong> If using this option your host must allow the popen and pclose PHP functions which are often disabled on shared hosts.';
$lang['settings_browsercache'] = 'Applicable only to cachable pages, this setting indicates that browsers should be allowed to cache the pages for an amount of time.  If enabled repeat visitors to your site may not immediately see changes to the content of the pages, however enabling this option can seriously improve the performance of your website.';
$lang['settings_browsercache_expiry'] = 'Specify the amount of time (in minutes) that browsers should cache pages for.  Setting this value to 0 disables the functionality.  In most circumstances you should specify a value greater than 30';
$lang['settings_autoclearcache'] = 'This option allows you to specify the maximum age <em>(in days)</em> before files in the cache directory will be deleted.<br/><br/>This option is useful to ensure that cached files are regenerated periodically, and that the filesystem does not become polluted with old and unnecessary files.  An ideal value for this field is 14 or 30 days.<br/><br/><strong>Note:</strong> Cached files are cleared at most once per day.';
$lang['settings_umask'] = 'The &quot;umask&quot; is an octal value that is used to specify the default permission for newly created files (this is used for files in the cache directory, and uploaded files.  For more information see the appropriate <a href="http://en.wikipedia.org/wiki/Umask">wikipedia article.</a>';
$lang['settings_disablesafemodewarn'] = 'This option will disable a warning notice if CMSMS detects that <a href="http://php.net/manual/en/features.safe-mode.php">PHP Safe Mode</a> has been detected.<br/><br/><strong>Note:</strong> Safe mode has been deprecated as of PHP 5.3.0 and removed for PHP 5.4.0.  CMSMS Does not support operation under safe mode, and our support team will not render any technical assistance for installs where safe mode is active';
$lang['settings_enablenotifications'] = 'This option will enable notifications being shown at the top of the page in each admin request.  This is useful for important notifications about the system that may require user action.  It is possible for each admin user to turn off notifications in their preferences.';
$lang['settings_pseudocron_granularity'] = 'This setting indicates how often (in minutes) the system will attempt to handle regularly scheduled tasks.';
$lang['settings_adminlog_lifetime'] = 'This setting indicates the maximum amount of time <em>(in days)</em> That entires in the admin log should be retained.';
$lang['settings_checkversion'] = 'If enabled, the system will perform a daily check for a new release of CMSMS';
$lang['settings_smartycaching'] = 'When enabled, the output from various plugins will be cached to increase performance. Additionally, most portions of compiled templates will be cached. This only applies to output on content pages marked as cachable, and only for non-admin users.  Note, this functionality may interfere with the behavior of some modules or plugins, or plugins that use non-inline forms.<br/><br/><strong>Note:</strong> When smarty caching is enabled, global content blocks <em>(GCBs)</em> are always cached by smarty, and user defined tags <em>(UDTs)</em> are never cached.  Additionally, content blocks are never cached.';
$lang['settings_smartycompilecheck'] = 'If disabled, smarty will not check the modification dates of templates to see if they have been modified.  This can significantly improve performance.  However performing any template change (or even some content changes) may require a cache clearing';

$lang['siteprefs_lockrefresh'] = 'This field specifies the minimum frequency (in minutes) the ajax based locking mechanism should &quot;touch&quot; a lock.  An ideal value for this field is 5.';
$lang['siteprefs_locktimeout'] = 'This field specifies the number of minutes of inactivity before a lock times out.  After a lock times out other users may steal the lock.  In order for a lock to not timeout it must be &quot;touched&quot; before its expiry time.  This resets the expiry time of the lock.  Under most circumstances a 60 minute lock should be suitable.';
$lang['siteprefs_sitename'] = 'This is a human readable name for your website, i.e: the business, club, or organization name';
$lang['siteprefs_frontendlang'] = 'The default language that your webiste displays on the frontend.  This can be changed on a per-page basis using different smarty tags. i.e: <code>{cms_set_language}</code>';
$lang['siteprefs_frontendwysiwyg'] = 'When WYSIWYG editors are provided on frontend forms, what WYSIWYG module should be used?  Or none.';
$lang['siteprefs_nogcbwysiwyg'] = 'This option will disable the WYSIWYG editor on all global content blocks indepentent of user settings, or of the individual global content blocks';
$lang['siteprefs_globalmetadata'] = 'This textarea provides the ability to enter meta information that is relavent to all content pages.  This is an ideal location for meta tags such as Generator, and Author, etc';
$lang['siteprefs_logintheme'] = 'Select the admin theme (from installed admin themes) that will be used to generate the administrator login form, and as the default login theme for new admin user accounts.  Admin users will be able to select their preferred admin theme from within the user preferences panel.';
$lang['siteprefs_backendwysiwyg'] = 'Select the WYSIWYG editor for newly created admin user accounts.  Admin users will be able to select their preferred WYSIWYG editor from within the user preference panel.';
$lang['siteprefs_dateformat'] = '<p>Specify the date format string in <a href="http://ca2.php.net/manual/en/function.strftime.php">PHP strftime</a> format that will be used <em>(by default)</em> to display dates and times in your website.</p><p>Admin users can adjust these settings in the user preferences panel.</p><p><strong>Note:</strong> Some modules may choose to display times and dates differently</p>';
$lang['siteprefs_thumbwidth'] = 'Specify a width <em>(in pixels)</em> to be used by default when generating thumbnails from uploaded image files.  Thumbnails are typically displayed in the admin panel in the FileManager module or when selecting an image to insert into page content.  However, some modules may use the thumbnails on the website frontend.<br/><br/><strong>Note:</strong> Some modules may have additional preferences for how to generate thumbnails, and ignore this setting.';
$lang['siteprefs_thumbheight'] = 'Specify a height <em>(in pixels)</em> to be used by default when generating thumbnails from uploaded image files.  Thumbnails are typically displayed in the admin panel in the FileManager module or when selecting an image to insert into page content.  However, some modules may use the thumbnails on the website frontend.<br/><br/><strong>Note:</strong> Some modules may have additional preferences for how to generate thumbnails, and ignore this setting.';

?>