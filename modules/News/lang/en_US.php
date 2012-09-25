<?php
$lang['info_expired_viewable'] = 'If enabled, expired articles can be viewed in detail mode (this is reproducing older functionality).  the showall parameter can be used on the URL (when not using pretty urls) to also indicate that expired articles can be viewed';
$lang['expired_viewable'] = 'Expired articles can be viewed in the detail view';
$lang['error_insufficientparams'] = 'Insufficient (or empty) parameters';
$lang['error_duplicatename'] = 'An item with that name already exists';
$lang['detail_page'] = 'Detail Page';
$lang['detail_template'] = 'Detail Template';
$lang['warning_preview'] = 'Warning: This preview panel behaves much like a browser window allowing you to navigate away from the initially previewed page. However, if you do that, you may experience unexpected behaviour.  Navigating away from the initial page and returning will not give the expected results.<br/><strong>Note:</strong> The preview does not upload files you may have selected for upload.';
$lang['article'] = 'Article';
$lang['preview'] = 'Preview';
$lang['info_detail_returnid'] = 'This preference is used to determine a page (and therefore a template) to use to view detail pages.  Individualized News Detail URLS will not work if this parameter is not set to a valid page.  Additionally, if this preference is set, and no detailpage parameter is provided on the news tag, then this value will be used for detail links';
$lang['title_detail_returnid'] = 'Default page to use for detail views';
$lang['title_submission_settings'] = 'News Submission Settings';
$lang['title_fesubmit_settings'] = 'Frontend Submit Settings';
$lang['title_notification_settings'] = 'Notification Settings';
$lang['title_detail_settings'] = 'Detail View Settings';
$lang['error_invalidurl'] = 'Invalid URL <em>(maybe it is already used, or there are invalid characters)</em>';
$lang['url'] = 'URL';
$lang['expired_searchable'] = 'Expired articles can appear in search results';
$lang['helpshowall'] = 'Show all articles, irrespective of end date';
$lang['error_invaliddates'] = 'One or more of the dates entered were invalid';
$lang['notify_n_draft_items_sub'] = '%d News article(s)';
$lang['notify_n_draft_items'] = 'You have %s that is/are not published';
$lang['unlimited'] = 'Unlimited';
$lang['none'] = 'None';
$lang['anonymous'] = 'Anonymous';
$lang['unknown'] = 'Unknown';
$lang['fesubmit_redirect'] = 'PageID or alias to redirect to after a news article has been submitted via the fesubmit action';
$lang['allow_summary_wysiwyg'] = 'Allow using a WYSIWYG editor on the summary field';
$lang['title_browsecat_template'] = 'Browse Category Template Editor';
$lang['title_browsecat_sysdefault'] = 'Default Browse category Template';
$lang['browsecattemplate'] = 'Browse Category Templates';
$lang['error_filesize'] = 'An uploaded file exceeded the maximum allowed size';
$lang['post_date_desc'] = 'Post Date Descending';
$lang['post_date_asc'] = 'Post Date Ascending';
$lang['expiry_date_desc'] = 'Expiry Date Descending';
$lang['expiry_date_asc'] = 'Expiry Date Ascending';
$lang['title_desc'] = 'Title Descending';
$lang['title_asc'] = 'Title Ascending';
$lang['status_desc'] = 'Status Descending';
$lang['status_asc'] = 'Status Ascending';
$lang['fesubmit_status'] = 'The status of news articles submitted via the frontend';
$lang['error_invalidfiletype'] = 'Cannot upload this type of file';
$lang['error_upload'] = 'Problem occurred uploading a file';
$lang['error_movefile'] = 'Could not create file: %s';
$lang['error_mkdir'] = 'Could not create directory: %s';
$lang['expiry_interval'] = 'The number of days (by default) before an article expires (if expiry is selected)';
$lang['removed'] = 'Removed';
$lang['msg_contenttype_removed'] = <<<EOT
The news content type has been removed.  Please place {news} tags with appropriate parameters into your page template or into your page content to replace this functionality.
EOT;
$lang['delete_selected'] = 'Delete Selected Articles';
$lang['areyousure_deletemultiple'] = 'Are you sure you want to delete all of these news articles?\nThis action cannot be undone!';
$lang['error_templatenamexists'] = 'A template by that name already exists';
$lang['error_noarticlesselected'] = 'No Articles Were Selected';
$lang['reassign_category'] = 'Change Category To';
$lang['select'] = 'Select';
$lang['approve'] = 'Set Status to \'Published\'';
$lang['revert'] = 'Set Status to \'Draft\'';
$lang['hide_summary_field'] = 'Hide the summary field when adding or editing articles';
$lang['textbox'] = 'Text Input';
$lang['checkbox'] = 'Checkbox';
$lang['textarea'] = 'Text Area';
$lang['file'] = 'File';
$lang['auto_create_thumbnails'] = 'Automatically create thumbnail files for files with these extensions';
$lang['allowed_upload_types'] = 'Allow only files with these extensions to be uploaded';
$lang['fielddefupdated'] = 'Field Definition Updated';
$lang['editfielddef'] = 'Edit Field Definition';
$lang['up'] = 'Up';
$lang['down'] = 'Down';
$lang['fielddefdeleted'] = 'Field Definition Deleted';
$lang['nameexists'] = 'A field by that name already exists';
$lang['notanumber'] = 'Maximum Length is Not a Number';
$lang['fielddef'] = 'Field Definition';
$lang['fielddefadded'] = 'Field Definition Successfully Added';
$lang['public'] = 'Public';
$lang['type'] = 'Type';
$lang['info_maxlength'] = 'The maximum length only applies to text input fields.';
$lang['maxlength'] = 'Maximum Length';
$lang['addfielddef'] = 'Add Field Definition';
$lang['customfields'] = 'Field Definitions';
$lang['deprecated'] = 'unsupported';
$lang['subject_newnews'] = 'A new News article has been posted';
$lang['email_subject'] = 'The Subject of the outgoing email';
$lang['email_template'] = 'The format of the email message';
$lang['formsubmit_emailaddress'] = 'Email address to receive notification of news submission';
$lang['extra'] = 'Extra';
$lang['uploadscategory'] = 'Uploads Category';
$lang['title_available_templates'] = 'Available Templates';
$lang['resettodefault'] = 'Reset to Factory Defaults';
$lang['title_form_template'] = 'Form Template Editor';
$lang['title_detail_template'] = 'Detail Template Editor';
$lang['title_summary_template'] = 'Summary Template Editor';
$lang['prompt_templatename'] = 'Template Name';
$lang['prompt_template'] = 'Template Source';
$lang['title_form_sysdefault'] = 'Default Form Template';
$lang['title_summary_sysdefault'] = 'Default Summary Template';
$lang['title_detail_sysdefault'] = 'Default Detail Template';
$lang['info_sysdefault2'] = '<strong>Note:</strong> This tab contains text areas to allow you to edit a set of templates that are displayed when you create a \'new\' summary, detail, or form template.  Changing content in this tab, and clicking \'submit\' will <strong>not effect any current displays</strong>.';
$lang['info_sysdefault'] = '(the content used by default when a new template is created)';
$lang['template'] = 'Template';
$lang['prompt_name'] = 'Name';
$lang['prompt_default'] = 'Default';
$lang['prompt_newtemplate'] = 'Create A New Template';
$lang['help_pagelimit'] = 'Maximum number of items to display (per page).  If this parameter is not supplied all matching items will be displayed.  If it is, and there are more items available than specified in the parameter, text and links will be supplied to allow scrolling through the results';
$lang['prompt_page'] = 'Page';
$lang['firstpage'] = '&lt;&lt;';
$lang['prevpage'] = '&lt;';
$lang['nextpage'] = '&gt;';
$lang['lastpage'] = '&gt;&gt;';
$lang['prompt_of'] = 'of';
$lang['prompt_pagelimit'] = 'Page Limit';
$lang['prompt_sorting'] = 'Sort By';
$lang['title_filter'] = 'Filters';
$lang['published'] = 'Published';
$lang['draft'] = 'Draft';
$lang['expired'] = 'Expired';
$lang['author'] = 'Author';
$lang['sysdefaults'] = 'Restore to defaults';
$lang['restoretodefaultsmsg'] = 'This operation will restore the template contents to their system defaults.  Are you sure you want to proceed?';
$lang['addarticle'] = 'Add Article';
$lang['articleadded'] = 'The article was successfully added.';
$lang['articleupdated'] = 'The article was successfully updated.';
$lang['articledeleted'] = 'The article was successfully deleted.';
$lang['addcategory'] = 'Add Category';
$lang['categoryadded'] = 'The category was successfully added.';
$lang['categoryupdated'] = 'The category was successfully updated.';
$lang['categorydeleted'] = 'The category was successfully deleted.';
$lang['addnewsitem'] = 'Add News Item';
$lang['allcategories'] = 'All Categories';
$lang['allentries'] = 'All Entries';
$lang['areyousure'] = 'Are you sure you want to delete?';
$lang['articles'] = 'Articles';
$lang['cancel'] = 'Cancel';
$lang['category'] = 'Category';
$lang['categories'] = 'Categories';
$lang['default_category'] = 'Default Category';
$lang['content'] = 'Content';
$lang['dateformat'] = '%s not in a valid yyyy-mm-dd hh:mm:ss format';
$lang['delete'] = 'Delete';
$lang['description'] = 'Add, edit and remove News entries';
$lang['formtemplate'] = 'Form Templates';
$lang['detailtemplate'] = 'Detail Templates';
$lang['default_templates'] = 'Default Templates';
$lang['detailtemplateupdated'] = 'The updated Detail Template was successfully saved to the database.';
$lang['displaytemplate'] = 'Display Template';
$lang['edit'] = 'Edit';
$lang['enddate'] = 'End Date';
$lang['endrequiresstart'] = 'Entering an end date requires a start date also';
$lang['entries'] = '%s Entries';
$lang['status'] = 'Status';
$lang['expiry'] = 'Expiry';
$lang['filter'] = 'Filter';
$lang['more'] = 'More';
$lang['category_label'] = 'Category:';
$lang['author_label'] = 'Posted by:';
$lang['extra_label'] = 'Extra:';
$lang['moretext'] = 'More Text';
$lang['name'] = 'Name';
$lang['news'] = 'News';
$lang['news_return'] = 'Return';
$lang['newcategory'] = 'New Category';
$lang['needpermission'] = 'You need the \'%s\' permission to perform that function.';
$lang['nocategorygiven'] = 'No Category Given';
$lang['startdatetoolate'] = 'The Start Date is too late (after end date?)';
$lang['nocontentgiven'] = 'No Content Given';
$lang['noitemsfound'] = '<strong>No</strong> items found for category: %s';
$lang['nopostdategiven'] = 'No Post Date Given';
$lang['note'] = '<em>Note:</em> Dates must be in a \'yyyy-mm-dd hh:mm:ss\' format.';
$lang['notitlegiven'] = 'No Title Given';
$lang['nonamegiven'] = 'No Name Given';
$lang['numbertodisplay'] = 'Number to Display (empty shows all records)';
$lang['print'] = 'Print';
$lang['postdate'] = 'Post Date';
$lang['postinstall'] = 'Make sure to set the "Modify News" permission on users who will be administering News items.';
$lang['selectcategory'] = 'Select Category';
$lang['showchildcategories'] = 'Show Child Categories';
$lang['sortascending'] = 'Sort Ascending';
$lang['startdate'] = 'Start Date';
$lang['startoffset'] = 'Start displaying at the nth item';
$lang['startrequiresend'] = 'Entering a start date requires an end date also';
$lang['apply'] = 'Apply';
$lang['submit'] = 'Submit';
$lang['summary'] = 'Summary';
$lang['summarytemplate'] = 'Summary Templates';
$lang['summarytemplateupdated'] = 'The News Summary Template was successfully updated.';
$lang['title'] = 'Title';
$lang['options'] = 'Options';
$lang['optionsupdated'] = 'The options were successfully updated.';
$lang['useexpiration'] = 'Use Expiration Date';
$lang['eventdesc-NewsArticleAdded'] = 'Sent when an article is added.';
$lang['eventhelp-NewsArticleAdded'] = '<p>Sent when an article is added.</p>
<h4>Parameters</h4>
<ul>
<li>"news_id" - Id of the news article</li>
<li>"category_id" - Id of the category for this article</li>
<li>"title" - Title of the article</li>
<li>"content" - Content of the article</li>
<li>"summary" - Summary of the article</li>
<li>"status" - Status of the article ("draft" or "publish")</li>
<li>"start_time" - Date the article should start being displayed</li>
<li>"end_time" - Date the article should stop being displayed</li>
<li>"useexp" - Whether the expiration date should be ignored or not</li>
</ul>
';
$lang['eventdesc-NewsArticleEdited'] = 'Sent when an article is edited.';
$lang['eventhelp-NewsArticleEdited'] = '<p>Sent when an article is edited.</p>
<h4>Parameters</h4>
<ul>
<li>"news_id" - Id of the news article</li>
<li>"category_id" - Id of the category for this article</li>
<li>"title" - Title of the article</li>
<li>"content" - Content of the article</li>
<li>"summary" - Summary of the article</li>
<li>"status" - Status of the article ("draft" or "publish")</li>
<li>"start_time" - Date the article should start being displayed</li>
<li>"end_time" - Date the article should stop being displayed</li>
<li>"useexp" - Whether the expiration date should be ignored or not</li>
</ul>
<p><strong>Note:</strong> Not all parameters may be present when this event is sent.</p>
';
$lang['eventdesc-NewsArticleDeleted'] = 'Sent when an article is deleted.';
$lang['eventhelp-NewsArticleDeleted'] = '<p>Sent when an article is deleted.</p>
<h4>Parameters</h4>
<ul>
<li>"news_id" - Id of the news article</li>
</ul>
';
$lang['eventdesc-NewsCategoryAdded'] = 'Sent when a category is added.';
$lang['eventhelp-NewsCategoryAdded'] = '<p>Sent when a category is added.</p>
<h4>Parameters</h4>
<ul>
<li>"category_id" - Id of the news category</li>
<li>"name" - Name of the news category</li>
</ul>
';
$lang['eventdesc-NewsCategoryEdited'] = 'Sent when a category is edited.';
$lang['eventhelp-NewsCategoryEdited'] = '<p>Sent when a category is edited.</p>
<h4>Parameters</h4>
<ul>
<li>"category_id" - Id of the news category</li>
<li>"name" - Name of the news category</li>
<li>"origname" - The original name of the news category</li>
</ul>
';
$lang['eventdesc-NewsCategoryDeleted'] = 'Sent when a category is deleted.';
$lang['eventhelp-NewsCategoryDeleted'] = '<p>Sent when a category is deleted.</p>
<h4>Parameters</h4>
<ul>
<li>"category_id" - Id of the deleted category </li>
<li>"name" - Name of the deleted category</li>
</ul>
';

$lang['help_articleid'] = 'This parameter is only applicable to the detail view.  It allows specifying which news article to display in detail mode.  If the special value -1 is used, the system will display the newest, published, non expired article.';
$lang['helpnumber'] = 'Maximum number of items to display (per page) -- leaving empty will show all items.  This is a synonym for the pagelimit parameter.';
$lang['helpstart'] = 'Start at the nth item -- leaving empty will start at the first item.';
$lang['helpcategory'] = 'Used in the summary view to display only items for the specified categories. <b>Use * after the name to show children.</b>  Multiple categories can be used if separated with a comma. Leaving empty, will show all categories.  This parameter also works for the frontend submit action, however only a single category name is supported.';
$lang['helpmoretext'] = 'Text to display at the end of a news item if it goes over the summary length.  Defaults to "More"';
$lang['helpsummarytemplate'] = 'Use a separate database template for displaying the article summary.  This template must exist and be visible in the summary template tab of the News admin, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['helpformtemplate'] = 'Use a database template for displaying the article submission form. This template must exist and be visible in the form templates tab of the News admin, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['helpbrowsecattemplate'] = 'Use a database template for displaying the category browser. This template must exist and be visible in the Browse Category Templates tab of the News admin, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['helpdetailtemplate'] = 'Use a separate database template for displaying the article detail. This template must exist and be visible in the detail template tab of the News admin, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['helpsortby'] = 'Field to sort by.  Options are: "news_date", "summary", "news_data", "news_category", "news_title", "news_extra", "end_time", "start_time", "random".  Defaults to "news_date". If "random" is specified, the sortasc param is ignored.';
$lang['helpsortasc'] = 'Sort news items in ascending date order rather than descending.';
$lang['helpdetailpage'] = 'Page to display News details in.  This can either be a page alias or an id. Used to allow details to be displayed in a different template from the summary.';
$lang['helpshowarchive'] = 'Show only expired news articles.';
$lang['helpbrowsecat'] = 'Shows a browseable category list.';
$lang['helpaction'] = <<<EOT
'Override the default action.  Possible values are:
<ul>
<li>&quot;detail&quot; - to display a specified articleid in detail mode.</li>
<li>&quot;default&quot; - to display the summary view</li>
<li>&quot;fesubmit&quot; - <strong>Deprecated</strong> to display the frontend form for allowing users to submit news articles on the front end. Add the <code>{cms_init_editor}</code> tag in the metadata section to initialize the selected wysiwyg editor. (Site Admin >> Global Settings)</li>
<li>&quot;browsecat&quot; - to display a browseable category list.</li>
</ul>
EOT;
$lang['help'] = <<<EOF
<h3>Important Notes</h3>
<p>Version 2.9 and greater of News has removed the formatpostdate member from the templates, and has also removed the dateformat parameter.  You should be using the cms_date_format modifier (as indicated in the default templates) to format dates, and should be using entry->postdate instead of entry->formatpostdate in your templates.</p>
<h3>What does this do?</h3>
<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to administration menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
<h4>Numerous display methods</h4>
<p>The parameters supported by the news module, and support for numerous templates of each time mean that your options for displaying news articles are limitless.</p>
<h4>Custom Fields</h4>
<p>The News module allows defining numerous custom fields (including files and images) that will allow you to attach pdf files or numerous images to your articles.</p>
        <h4>Categories</h4>
	<p>News supplies a hierarchical category mechanism for organizing your articles.  A news article can only be in one place in the hierarchy.</p>
	<h4>Expiry and Status</h4>
	<p>Each news article can have an optional expiry date, after which it will not be shown on your web page.  As well, articles can be marked as <em>draft</em> to remove them permanently from your web page.</p>
	<h3>Security</h3>
	<p>The user must belong to a group with the 'Modify News' permission in order to add or edit News entries.</p>
        <p>As well, In order to delete news entries, the user must belong to a group with the 'Delete News Articles' permission.</p>
	<p>In order to edit the layout templates, the user must belong to a group with the 'Modify Templates' permission.</p>
	<p>In order to edit the global news preferences, the user must belong to a group with the 'Modify Site Preferences' permission.</p>
	<p>Additionally, to approve news for frontend display the user must belong to a group with the 'Approve News' permission.</p>
	<h3>How do I use it?</h3>
	<p>The easiest way to use it is with the {news} wrapper tag (wraps the module in a tag, to simplify the syntax).  This will insert the module into your template or page anywhere you wish, and display news items.  The code would look something like: <code>{news number='5'}</code></p>
<h3>Templates</h3>
<p>Since version 2.3 News supports multiple database templates, and no longer supports additional file templates.  Users who used the old file template system should follow these steps (for each file template):</p>
<ul>
<li>Copy the file template into the clipboard</li>
<li>Create a new database template <em>(either summary or detailed as required)</em>.  Give the new template the same name (including the .tpl extension) as the old file template, and paste the contents.</li>
<li>Hit Submit</li>
</ul>
<p>Following these steps should solve the problem of your news templates not being found and other similar smarty errors when you upgrade to a version of CMS that has News 2.3 or greater.</p>
EOF;
?>
