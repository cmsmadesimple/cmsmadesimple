<ul>
<li>
<p>Version: 1.0</p>
<p>This module is a hacked and extended version of <em>Ted Kulp\'s</em> News module.  I simply added another field to the database, and some more code to make that field worl.... I also re-cleaned the code a bit, so it was a little easier to read, other than that, it\'s Ted\'s code.</p>
</li> 
<li> 
<p>Version: 1.1</p> 
<p>Added the ability to set an automatic expiry date from a pulldown, moved the category selection, and on the main page you now filter the entries you want to see.</p> 
</li> 
<li> 
<p>Version: 1.2</p> 
<p>Added summary, no_anchor and length parameters.  In summary mode links are made to the real articles, tags are stripped, and links are insreted to the news page and the specific news item.</p> 
</li> 
<li> 
<p>Version: 1.3</p> 
<p>Minor cosmetic changes</p> 
</li> 
<li> 
<p>Version 1.5</p> 
<p>Merged into the trunk News module</p> 
</li> 
<li> 
<p>Version 1.6</p> 
<p>Added pagination, and moved the add button to the top (calguy)</p>
</li>
<li>
<p>Version 2.0</p>
<p>Re-written to use smarty templates, and several other significant improvements</p>
</li>
<li>
<p>Version 2.0.1</p>
<p>Minor tweaks to the RSS output to allow it to work correctly on different browsers, and to support non alpha numeric characters in the description.</p> 
</li> 
<li>
<p>Version 2.0.2</p>
<p>- Add a "start" parameter to specify a start offset for news items</p>
<p>- The template tabs now have a "reset to default" button on them</p>
<p>- Start menu item is now required, but end date is optional when useexpirydate is on, </p>
<p>- Change the permissions model significantly, The "Modify News" permission is only for articles and categories. "Modify Templates" permission is required to edit the templates, and "Modify Site Preferences" is required to edit the options.</p> 
<p>- Put the rss feed titile into the lang entries</p>
</li> 
<li>
<p>Version 2.0.3</p>
<p>- Added the ability to track the original author of an article</p>
</li>
<li>
<p>Version 2.2</p>
<p>- Added browsecat parameter</p>
</li>
<li>
<p>Version 2.3</p>
<p>- Changed to use multiple database templates <em>Old file templates will not work.</em></p>
<p>- Now allow for admin approval to change news state from draft to published.</p>
<p>- Pagination is now available in the default summary templates</p>
<p>- More.</p>
</li>
<li>
<p>Version 2.3.0.2</p>
<p>- Minor fixes to the help, changelog, to the number parameter, and to add a missing CreatePermission call.</p>
<p>- Fixes to the start parameter to work differently</p>
</li>
<li>
<p>Version 2.4</p>
<p>- Added a form on the frontend to allow users to submit news articles.  This should be wrapped with customcontent to prevent spamming, etc.</p>
<p>- Fixes to allow 1.0.x compatibility.</p>
<p>- Updated the help.</p>
</li>
<li><p>Version 2.5</p>
<p>Adds an extra field that can be re-used for anything (associating with a file, an extra image, etc.</p>
</li>
<li><p>Version 2.5.1</p>
<p>Removes a small error I left in a template.</p>
<p>Moves the default templates into their own tab to remove confusion.</p>
<p>Use SelectLimit in the summary view instead of the LIMIT n,start or LIMIT n offset startelement</p>
<p>Bumped minimum version to 1.1</p>
</li>
<li><p>Version 2.6</p>
<p>Adds user defined fields, including text areas, text input, checkboxes, and \'files\' which allows you to associate a file with a particular news article.</p>
<p>Adds the ability to bulk change the category of selected news articles</p>
<p>Adds the ability to bulk delete selected news articles</p>
<p>Adds a new permission \'Delete News\'.  Users will need this permission to be able to delete news articles.</p>
<p>Fixes some frontend editing capabilities (extra fields aren\'t in there yet)</p>
<p>Adds a preference to allow hiding the summary text area for adding and editing articles.</p>
<p>Remove the news content type (was deprecated in News 2.5.1)</p>
<p>Now Requires CMS 1.2 or later</p>
<p>Fixes some hardcoded lang strings in the articles tab</p>
<p>Adds a preference that allows specifying the default number of days before an article expires (if expiry is used)</p>
<p>Fixes the browsecat mode so that extra parameters added on the tag are carried down through the links</p>
<p>Adds multiple database templates to the browsecat action.</p>
</li>
<li><p>Version 2.6.1</p>
<p>Now use cms_move_uploaded_file instead of move_uploaded_file.</p>
<p>Fixes a minor issue with the start parameter.</p>
<p>Now Require CMS 1.2.1 or later</p>
<p>Fixes an issue where I was assuming that the db prefix was cms_</p>
<p>Fixes some warnings if no custom fields were defined.</p>
<p>Fixes some calls to GetModuleInstance().</p>
</li>
<li><p>Version 2.6.2</p>
<p>Decode entities for the template name when editing a template</p>
<p>Fix an issue with warnings if no custom fields are defined</p>
</li>
<li><p>Version 2.7</p>
<p>Fixes to news fesubmit and html entities</p>
<p>Adds an optional dropdown category box for fesubmit</p>
<p>Adds a preference indicating to disable the wysiwyg for the summary view</p>
<p>Adds a preference to determine which page to redirect to after fesubmit</p>
<p>Fixes to author id and author management wrt frontend submitted articles</p>
<p>Adds a preference to allow defining a landing page for links generated in the rss feed</p>
<p>Updates the pretty urls in the rss feed</p>
<p>A slight tweak to the rssfeed.tpl to use the feed title.</p>
<p>Adds custom field support to the frontend submit form</p>
</li>
<li><p>Version 2.8</p>
<p>Now require CMS 1.3-beta1 at least</p>
<p>Now use cms_html_entity_decode to ensure php4 compatibility</p>
<p>Support the extra field in the fesubmit form</p>
<p>Add fieldsbyname values to detail and summary reports</p>
<p>Fix minor issue with missing table prefix when deleting a category</p>
<p>Fix browsecat if there are one or more child categories with the same name</p>
<p>Cleanup and some minor bug fixes</p>
</li>
<li><p>Version 2.8.1</p>
<p>Fixes a minor problem with custom fields.</p>
<p>Fixes a minor problem with sorting by news_extra.</p>
</li>
<li><p>Version 2.8.2</p>
<p>Fixes a minor problem with registered routes</p>
</li>
<li><p>Version 2.8.3</p>
<p>Fixes a minor problem with fesubmit redirect actions</p>
</li>
<li><p>Version 2.9</p>
<p>Adds notification output</p>
<p>Adds prevpage and nextpage url variables in the summary view.</p>
<p>Get rid of the dateformat parameter and preference.</p>
<p>Remove any and all RSS code</p>
<p>Add support for syntax hilighter when editing templates.</p>
<p>Bug fixes</p>
</li>
<li><p>Version 2.9.2</p>
<p>Add the showall parameter.</p>
<p>Minor cleanup to parameters.</p>
<p>Add a new preference to allow showing expired articles in search results. If you plan on using this parameter, you may need to re-index all search content.</p>
</li>
<li><p>Version 2.9.3</p>
  <p>Fix help wrt the number and pagelimit parameters.</p>
  <p>Add the articleid=-1 capability to the detail view.</p>
  <p>No longer add empty values for empty extra fields submitted via the fesubmit action.</p>
  <p>Add keys to the fieldvals table.</p>
  <p>Fix problem with draft articles being searchable.</p>
</li>
<li><p>Version 2.9.4</p>
  <p>Hide the expiry date stuff when adding/editing an article, unless use expiry date is checked.</p>
  <p>Add create date and modified date to be available in the summary template (usefull for rss feeds in cgfeedmakr).</p>
  <p>Fix problem with browsecat counting non published articles.</p>
</li>
<li><p>Version 2.10</p>
  <p>Numerous bug fixes and minor enhancements.</p>
</li>
<li><p>Version 2.10.1</p>
  <p>Fix minor warning.</p>
</li>
<li><p>Version 2.10.2</p>
  <p>Fix minor permission related template issues.</p>
</li>
<li><p>Version 2.10.3</p>
  <p>Fix minor template issue.</p>
</li>
<li><p>Version 2.10.4</p>
  <p>Minor bug fixes.</p>
</li>
<li><p>Version 2.10.5</p>
  <p>Minor bug fixes.</p>
</li>
<li><p>Version 2.11.1</p>
  <p>bug fix to #5861 ... a fix to the custom field on error.</p>
</li>
<li><p>Version 2.11.2</p>
  <p>Fix pretty urls for search results.</p>
  <p>Fix canonical url in detail view.</p>
</li>
<li>>Version 2.12
  <ul>
    <li>Major re-structuring takes place.  Involves oopifying much of the code for the summary and detail views.</li>
    <li>The objects provided in the summary and detail views are now not simple objects, but smart ones.  Read the information in system default detail template for more information.</li>
    <li>Optimization of code to minimize queries and improve performance.</li>
    <li>Removes field alieses i,e.: \$entry->fieldname in the summary and detail views as it was an incomplete, problematic solution.</li>
    <li>The \$entry->fields and \$entry->customfieldsbyname hash are now merged into \$entry->fields.  This may cause some problems with upgrading.</li>
    <li>Adds support for preview when adding or editing a news article.</li>
    <li>Adds support for a user selectable search module.</li>
    <li>Removes print actions.</li>
    <li>Many more changes.</li>
  </ul>
</li>
<li>Version 2.12.9
  <ul>
    <li>Now send event when when status is changed in admin panel.</li>
    <li>News fesubmit documented as deprecated.</li>
    <li>Added the showall preference to prevent throwing an exception when viewing expired articles in detail view and the showall param</li>
  </ul>
</li>
<li>Version 2.14
  <ul>
    <li>Enhancements to the news_field class.</li>
    <li>Adds the idlist parameter.</li>
    <li>Security fix.</li>
  </ul>
</li>
</ul>
