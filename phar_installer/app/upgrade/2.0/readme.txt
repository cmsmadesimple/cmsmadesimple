CMS Made Simple Version 2.0
---------------------------

NOTE: This is a major upgrade and a significant amount of code has changed in this version.
NOTE: Many sites will not upgrade cleanly.  You may need to spend some time resolving various errors.
NOTE: The CMSMS Forum at https://forum.cmsmadesimple.org is an important resource to help you in determining how to solve problems prior to and after upgrading your site.


----------------
BEFORE UPGRADING
----------------
Before upgrading major versions such as this, please ensure that you:
  A:  check all of your modules for compatibility with CMSMS 2.0 before upgrading.
  B:  Ensure you have upgraded all modules to their latest available version, as this should help ensure that your modules are compatible with CMSMS 2.0
  C:  Ensure that you have a verified backup of all of your files and the database before upgrading so that you can restore in case of an error.
  D:  Completely read the announcements, release notes, and any documentation (including the README files distributed with the installation) before proceeding.


---------------
UPGRADE ISSUES:
---------------
A:  Smarty variable scope issues
--
Description: CMSMS has updated the Smarty template engine.  Smarty variables created in one template are no longer automatically available throughout the generation of the page.  You must explicitly copy those variables into another scope using the {assign} smarty plugin, it's shortcut or the new {share_data} plugin.

Symptoms:  After upgrading the site you may see one or more notices, warnings, or fatal errors about 'undefined index variablename'.  and other related messages.

Solution:  You need to find the location where the 'undefined' variable was created and copy it to a global scope.   Using the AdminSearch mechanism is a good way to find instances where these variables are used and/or created.   Once you find the template that the variable was created in,  you can alter the template to copy the variable into the global scope for use by other templates.  One way to do this is via the {assign} smarty plugin (or it's short form).  i.e:  {assign var=foo value=$foo scope=global}.  Another way is to use the {share_data} plugin that was created for CMSMS 2.0.  i.e: {share_data vars='title,description,foo'}


B:  Smarty security issues
--
Description:  CMSMS has enabled the built in smarty security mechanism to prevent editors, or content submitters from entering potentially unsafe smarty code.

Symptom:   You will see the 'Oops' error page with a message like:  access to **something** not allowed by security setting

Solution:  You can enable permissive smarty by adding $config['permissive_smarty'] = 1; into your config.php file.

Warning:  We do not recommend the use of permissive smarty on websites that allow submission of content by untrusted users.  i.e:  if you are using a module such as CGFeedback or AComments, or allowing news or blog article submission, or uploads by untrusted users, you should not use this setting.


C:  Sites that use multiple templates of the same name will have difficulties.
--
Description: If for example your page template is called 'MySite' and your MenuManager template is also entitled 'MySite', then there will be difficulty with the upgrade to CMSMS 2.0.  This is because the Design manager module (new for 2.0) treats all templates alike, and therefore requires that each template has a unique name. The upgrade process will ensure that all templates will have a unique name, however it WILL NOT touch your templates.  Therefore if you did not correct this situation before the upgrade you may encounter errors.

Symptoms:  If you get a white screen immediately after upgrade, that takes quite a while (up to your PHP time limit) to complete.  And/or you get errors like stack overflow" or "timeout" or "insufficient php memory" this may be the cause.

Solution:  Go into the design manager and find the new name for your child template (i.e: News, MenuManager, or some template from a third party module) and change the appropriate call in your page template.    i.e:  If You call {menu template='MySite'} in your page template, you will need to alter that call to specify the new template name.


D:  invalid characters in the template or stylesheet names.
--
Description:  Previous versions of CMSMS did not have tight controls on the characters that could be used in template and stylesheet names, GCB's, UDT's, etc.  This presented numerous difficulties over time.   In CMSMS 2.0 we have tightened the range of characters that can be used.  Typically these are alphanumeric characters, spaces the comma, dot, dash, and colon (:).  And some UTF-8 characters, but nothing that is not URL safe.

The CMSMS installation assistant will clean item names (templates, designs, collections, stylesheets) on upgrade, and this result in errors on your site.  The errors may be something like 'template not found: cms_template:foo'.   You can resolve these errors by making the appropriate changes in your templates.  The AdminSearch utility may be useful in finding
the places where changes are needed.


E:  Database stylesheets whose name ends in .css will be renamed
--
Description:   The extension .css is reserved for future file based CSS functionality.   Therefore any database stylesheets ending in .css will be renamed.

Symptoms:  You may either see the 'Oops' error page, or you may have a styling problem.  This is because the upgrade may have renamed your stylesheets.  If you are referring to stylesheets by name you may see an error message.

Solution:  Find the new name of the stylesheet from within the Design Manager and adjust your page templates accordingly.   The AdminSearch module may be of assistance here.


F:  Database templates  whose name ends in .tpl will be renamed
--
Description:  The extension .tpl is reserved for file based template functionality (useful in modules). Therefore any database templates ending in .tpl will be renmed.

Symptoms:  You see the 'Oops' error page with a message like:  Unable to load template cms_template 'something'

Solution:  Find the new name of the stylesheet from within the Design Manager, and adjust your page templates accordingly.  The AdminSearch module may be of assistance here.


G:  Third party plugins in the lib/smarty directory
--
Description:  Third party plugins that were manually installed into the lib/smarty/libs/plugins directory will be deleted on upgrade.

Symptoms:  You see the 'Oops' error page with a message like:  Syntax error in template "tpl_body:7"  on line 67 " {invalid_plugin} unknown tag "invalid_plugin"

Solution:  Copy the plugin file from your backup to the <CMSMS ROOT>/plugins directory


H:  Old plugins may not function
--
Description:  Some plugins that were formerly part of the core, or have been deprecated, and may no longer function.  i.e: some plugins like {toggle_open} and {toggle_close}

Symptoms:  You see the 'Oops' error page with a message like: Syntax error in template "tpl_body:7"  on line 67 " {invalid_plugin} unknown tag "invalid_plugin"

Solution:  Comment out the plugin call from within your template using {* and *} ... or replace the plugin with a different one.

Note:  Plugins that ended in the name 'close' will not function in CMSMS 2.0.  i.e:  {toggle_close}.


I:  Module use of CMSMailer
--
Description:  Some modules or plugins may use the CMSMailer module to send email messages, without explicitly declaring a dependency (modules).  The functionality for sending emails has been internalized into the CMSMS API's, and the CMSMailer module is usually not installed by default on an upgrade.

Symptoms:  Various symptoms.... fatal errors or warnings related to sending emails, or accessing a property of a non object in functionality that sends mail.

Solution:  From within the ModuleManager module, install the CMSMailer module.


----------------
WHAT HAS CHANGED
----------------
Note:  This is only a brief list of the major items that changed in CMSMS 2.0.  For more information you are encouraged to view the CMSMS docs site at
https://docs.cmsmadesimple.org and the forum at https://forum.cmsmadesimple.org

1.  New Smarty
    - Introduces variable scopes
    - Introduces the smarty security policy
2.  New Template, Stylesheet and Design paradigm
    - GCB's are now Generic templates.  There is no WYSIWYG functionality on generic templates.
    - All templates must be uniquely named
    - All stylesheets must be uniquely named
    - MenuManager, Navigator, Search, and News converted to use new paradigm
3.  New Content Manager module
    - Pagination, Filtering and Find
    - Now handles many more content pages
    - Locking to prevent accidental overwrites
4.  New Design Manager module
    - Handles Designs, Templates, Stylesheets, and Categories
    - Locking to prevent accidental overwrites
    - Has import and export functionality
    - Makes module development easier as module authors do not need to manage templates.
5.  New AdminSearch module
    - Allows searching through page content, templates, and stylesheets for various strings
6.  New Navigator Module
    - Allows building navigations recursively, is faster, and supports more flexibility.
    - Templates are much easier to understand
7.  Enhanced and improved ModuleManager module
    - See stale, new, and old modules at a glance
    - Easier to use
8.  Performance enhancements
    - Throughout the core with a focus on speed improvements for frontend rendering.
9.  Removed CMSPrinting
    - A UDT is used as a stub to prevent errors from this.  But there is no printing module distributed in the core.
10. Removed old, seldom used plugins
11. Improved admin theme and API
12. Improved admin navigation
13. Internalize CMSMailer
    - CMSMailer classes are now in the core API.  CMSMailer functionality is only a stub function for compatibility for those modules that need it.
14. UTF-8 URL Slugs
    - Just like domain names can have utf-8 characters,  URL slugs in CMSMS can now contain utf-8 characters.
15. API Changes
    - API Changes will break some modules.  Check for compatibility before upgrading.
16. More config options
more....
    - Almost everything has been adjusted in some way.
