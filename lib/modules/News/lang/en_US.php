<?php
// A
$lang['addarticle'] = 'Add Article';
$lang['addcategory'] = 'Add Category';
$lang['addfielddef'] = 'Add Field Definition';
$lang['addnewsitem'] = 'Add News Item';
$lang['allcategories'] = 'All Categories';
$lang['allentries'] = 'All Entries';
$lang['allowed_upload_types'] = 'Allow only files with these extensions to be uploaded';
$lang['allow_summary_wysiwyg'] = 'Allow using a WYSIWYG editor on the summary field';
$lang['anonymous'] = 'Anonymous';
$lang['apply'] = 'Apply';
$lang['approve'] = 'Set Status to \'Published\'';
$lang['areyousure'] = 'Are you sure you want to delete?';
$lang['areyousure_deletemultiple'] = 'Are you sure you want to delete multiple articles';
$lang['areyousure_multiple'] = 'Are you sure you want to perform this action on multiple articles?';
$lang['article'] = 'Article';
$lang['articleadded'] = 'The article was successfully added.';
$lang['articledeleted'] = 'The article was successfully deleted.';
$lang['articles'] = 'Articles';
$lang['articlesubmitted'] = 'The article was successfully submitted.';
$lang['articleupdated'] = 'The article was successfully updated.';
$lang['author'] = 'Author';
$lang['author_label'] = 'Posted by:';
$lang['auto_create_thumbnails'] = 'Automatically create thumbnail files for files with these extensions';

// B
$lang['bulk_delete'] = 'Delete';
$lang['bulk_setcategory'] = 'Set Category';
$lang['bulk_setdraft'] = 'Set to Draft';
$lang['bulk_setpublished'] = 'Set to Published';
$lang['browsecattemplate'] = 'Browse Category Templates';

// C
$lang['cancel'] = 'Cancel';
$lang['categories'] = 'Categories';
$lang['category'] = 'Category';
$lang['categoryadded'] = 'The category was successfully added.';
$lang['categorydeleted'] = 'The category was successfully deleted.';
$lang['categoryupdated'] = 'The category was successfully updated.';
$lang['category_label'] = 'Category:';
$lang['checkbox'] = 'Checkbox';
$lang['close'] = 'Close';
$lang['content'] = 'Content';
$lang['customfields'] = 'Field Definitions';

// D
$lang['dateformat'] = '%s not in a valid yyyy-mm-dd hh:mm:ss format';
$lang['default_category'] = 'Default Category';
$lang['default_templates'] = 'Default Templates';
$lang['delete'] = 'Delete';
$lang['delete_article'] = 'Delete Article';
$lang['delete_selected'] = 'Delete Selected Articles';
$lang['deprecated'] = 'unsupported';
$lang['description'] = 'Add, edit and remove News entries';
$lang['desc_adminsearch'] = 'Search all news articles (regardless of status or expiry)';
$lang['desc_news_settings'] = 'Settings for the News module';
$lang['detailtemplate'] = 'Detail Templates';
$lang['detailtemplateupdated'] = 'The updated Detail Template was successfully saved to the database.';
$lang['detail_page'] = 'Detail Page';
$lang['detail_template'] = 'Detail Template';
$lang['displaytemplate'] = 'Display Template';
$lang['down'] = 'Down';
$lang['draft'] = 'Draft';
$lang['dropdown'] = 'Dropdown';

// E
$lang['edit'] = 'Edit';
$lang['editarticle'] = 'Edit Article';
$lang['editcategory'] = 'Edit Category';
$lang['editfielddef'] = 'Edit Field Definition';
$lang['email_subject'] = 'The Subject of the outgoing email';
$lang['email_template'] = 'The format of the email message';
$lang['enddate'] = 'End Date';
$lang['endrequiresstart'] = 'Entering an end date requires a start date also';
$lang['entries'] = '%s Entries';
$lang['error_categorynotfoun'] = 'The category specified was not found';
$lang['error_categoryparent'] = 'Invalid category parent';
$lang['error_duplicatename'] = 'An item with that name already exists';
$lang['error_filesize'] = 'An uploaded file exceeded the maximum allowed size';
$lang['error_insufficientparams'] = 'Insufficient (or empty) parameters';
$lang['error_invaliddates'] = 'One or more of the dates entered were invalid';
$lang['error_invalidfiletype'] = 'Cannot upload this type of file';
$lang['error_invalidurl'] = 'Invalid URL <em>(maybe it is already used, or there are invalid characters)</em>';
$lang['error_mkdir'] = 'Could not create directory: %s';
$lang['error_movefile'] = 'Could not create file: %s';
$lang['error_noarticlesselected'] = 'No Articles Were Selected';
$lang['error_nooptions'] = 'No options specified for field definition';
$lang['error_templatenamexists'] = 'A template by that name already exists';
$lang['error_upload'] = 'Problem occurred uploading a file';

$lang['eventdesc-NewsArticleAdded'] = 'Sent when an article is added.';
$lang['eventhelp-NewsArticleAdded'] = '<h4>Parameters</h4>
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

$lang['eventdesc-NewsArticleDeleted'] = 'Sent when an article is deleted.';
$lang['eventhelp-NewsArticleDeleted'] = '<h4>Parameters</h4>
<ul>
<li>"news_id" - Id of the news article</li>
</ul>
';

$lang['eventdesc-NewsArticleEdited'] = 'Sent when an article is edited.';
$lang['eventhelp-NewsArticleEdited'] = '<h4>Parameters</h4>
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

$lang['eventdesc-NewsCategoryAdded'] = 'Sent when a category is added.';
$lang['eventhelp-NewsCategoryAdded'] = '<h4>Parameters</h4>
<ul>
<li>"category_id" - Id of the news category</li>
<li>"name" - Name of the news category</li>
</ul>
';

$lang['eventdesc-NewsCategoryDeleted'] = 'Sent when a category is deleted.';
$lang['eventhelp-NewsCategoryDeleted'] = '<h4>Parameters</h4>
<ul>
<li>"category_id" - Id of the deleted category </li>
<li>"name" - Name of the deleted category</li>
</ul>
';

$lang['eventdesc-NewsCategoryEdited'] = 'Sent when a category is edited.';
$lang['eventhelp-NewsCategoryEdited'] = '<h4>Parameters</h4>
<ul>
<li>"category_id" - Id of the news category</li>
<li>"name" - Name of the news category</li>
<li>"origname" - The original name of the news category</li>
</ul>
';

$lang['expired'] = 'Expired';
$lang['expired_searchable'] = 'Expired articles can appear in search results';
$lang['expired_viewable'] = 'Expired articles can be viewed in the detail view';
$lang['expiry'] = 'Expiry';
$lang['expiry_date_asc'] = 'Expiry Date Ascending';
$lang['expiry_date_desc'] = 'Expiry Date Descending';
$lang['expiry_interval'] = 'The number of days (by default) before an article expires (if expiry is selected)';
$lang['extra'] = 'Extra';
$lang['extra_label'] = 'Extra:';

// F
$lang['fesubmit_redirect'] = 'PageID or alias to redirect to after a news article has been submitted via the fesubmit action';
$lang['fesubmit_status'] = 'The status of news articles submitted via the frontend';
$lang['fielddef'] = 'Field Definition';
$lang['fielddefadded'] = 'Field Definition Successfully Added';
$lang['fielddefdeleted'] = 'Field Definition Deleted';
$lang['fielddefupdated'] = 'Field Definition Updated';
$lang['file'] = 'File';
$lang['filter'] = 'Filter';
$lang['firstpage'] = '&lt;&lt;';
$lang['formsubmit_emailaddress'] = 'Email address to receive notification of news submission';
$lang['formtemplate'] = 'Form Templates';

// H
$lang['help'] = <<<EOF
<h3>Important Notes</h3>
<p>Version 2.9 and greater of News has removed the formatpostdate member from the templates, and has also removed the dateformat parameter.  You should be using the cms_date_format modifier (as indicated in the default templates) to format dates, and should be using entry->postdate instead of entry->formatpostdate in your templates.</p>
<h3>What does this do?</h3>
<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to administration menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
<h4>Numerous display methods</h4>
<p>The parameters supported by the news module, and support for numerous templates of each time mean that your options for displaying news articles are limitless.</p>
<h4>Custom Fields</h4>
<p>The News module allows defining numerous custom fields (including files and images) that will allow you to attach PDF files or numerous images to your articles.</p>
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
<li>Create a new database template <em>(either summary or detailed as required)</em>.  Give the new template the same name as the old file template, and paste the contents.</li>
<li>Hit Submit</li>
</ul>
<p>Following these steps should solve the problem of your news templates not being found and other similar smarty errors when you upgrade to a version of CMS that has News 2.3 or greater.</p>
EOF;
$lang['helpaction'] = <<<EOT
Override the default action.  Possible values are:
<ul>
<li>&quot;detail&quot; - to display a specified articleid in detail mode.</li>
<li>&quot;default&quot; - to display the summary view</li>
<li>&quot;fesubmit&quot; - <strong>Deprecated</strong> to display the frontend form for allowing users to submit news articles on the front end. Add the <code>{cms_init_editor}</code> tag in the metadata section to initialize the selected WYSIWYG editor. (Site Admin >> Global Settings)</li>
<li>&quot;browsecat&quot; - to display a browsable category list.</li>
</ul>
EOT;
$lang['helpbrowsecat'] = 'Shows a browsable category list.';
$lang['helpbrowsecattemplate'] = 'Use a database template for displaying the category browser. This template must exist in the Design Manager, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['helpcategory'] = 'Used in the summary view to display only items for the specified categories. <b>Use * after the name to show children.</b>  Multiple categories can be used if separated with a comma. Leaving empty, will show all categories.  This parameter also works for the frontend submit action, however only a single category name is supported.';
$lang['helpdetailpage'] = 'Page to display News details in.  This can either be a page alias or an id. Used to allow details to be displayed in a different template from the summary.  This parameter will have no effect for articles with custom URLs.';
$lang['helpdetailtemplate'] = 'Use a separate database template for displaying the article detail. This template must exist in the Design Manager, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.  This parameter is not used when generating urls if custom urls are specified.';
$lang['helpformtemplate'] = 'Use a database template for displaying the article submission form. This template must exist in the Design Manager, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['helpmoretext'] = 'Text to display at the end of a news item if it goes over the summary length.  Defaults to "More"';
$lang['helpnumber'] = 'Maximum number of items to display (per page) -- leaving empty will show all items.  This is a synonym for the pagelimit parameter.';
$lang['helpshowall'] = 'Show all articles, irrespective of end date';
$lang['helpshowarchive'] = 'Show only expired news articles.';
$lang['helpsortasc'] = 'Sort news items in ascending date order rather than descending.';
$lang['helpsortby'] = 'Field to sort by.  Options are: "news_date", "summary", "news_data", "news_category", "news_title", "news_extra", "end_time", "start_time", "random".  Defaults to "news_date". If "random" is specified, the sortasc parameter is ignored.';
$lang['helpstart'] = 'Start at the nth item -- leaving empty will start at the first item.';
$lang['helpsummarytemplate'] = 'Use a separate database template for displaying the article summary.  This template must exist in the Design Manager, though it does not need to be the default.  If this parameter is not specified, then the current template marked as default will be used.';
$lang['help_articleid'] = 'This parameter is only applicable to the detail view.  It allows specifying which news article to display in detail mode.  If the special value -1 is used, the system will display the newest, published, non expired article.';
$lang['help_article_title'] = 'Enter the article title.  It should be a brief, and should not include any html tags.';
$lang['help_article_category'] = 'For organization purposes, you may select a category';
$lang['help_article_content'] = 'Enter the main article content here';
$lang['help_article_enddate'] = 'If use expiry is enabled, this date specifies when the article will be hidden from view';
$lang['help_article_extra'] = 'This is extra data to associate with the news article.  It may be used for a sorting order or for other designer intended behavior.  You should consult your site developer as to how this field is used (if at all)';
$lang['help_article_searchable'] = 'This field indicates whether this article should be indexed by the search module';
$lang['help_article_postdate'] = 'The postdate <em>(usually the current date, for new articles)</em> is the date that will be used as the publish date for the article.  It is also used in sorting';
$lang['help_article_summary'] = 'Enter a brief paragraph to describe the article.  This summary may be used when displaying views of a number of articles';
$lang['help_article_startdate'] = 'When use expiry is enabled, this date specifies the date from which the article will be visible on the website';
$lang['help_article_status'] = 'If you want the article to be immediately viewable by others then select a status of published.  If you would like to continue working on this article for a while, then select draft.';
$lang['help_article_url'] = 'The optional article url <em>(some other platforms call this a slug)</em> is a unique url suffix to access this article.  Users can navigate to &lt;site_root&gt;/&lt;your_url&gt; to view this article.';
$lang['help_article_useexpiry'] = 'This checkbox toggles the expiry date behavior.  Expiry date behavior dictates when an article becomes visible on the website, and when it subsequently becomes invisible.';
$lang['help_articles_filtercategory'] = 'Optionally filter the list of displayed articles in this list by those that belong to the selected category';
$lang['help_articles_filterchildcats'] = 'If enabled, articles in the selected category, and their child categories will be displayed.';
$lang['help_articles_pagelimit'] = 'Select the number of articles to show in one page.  For sites with a large number of articles specifying a page limit between 10 and 100 will significantly improve performance';
$lang['help_articles_sortby'] = 'Select how articles will be initially sorted.';
$lang['help_category_name'] = 'Enter a name for this category.  The name should be safe for use in URLS and have no special characters.';
$lang['help_category_parent'] = 'Optionally specify a parent category to build a hierarchy of categories.';
$lang['help_fesubmit_redirect'] = 'Page ID or alias to redirect to after a succuessful frontend submission';
$lang['help_fielddef_maxlen'] = 'For text fields you can specify the maximum length of user input (in characters)';
$lang['help_fielddef_name'] = 'Each field definition must have a name.  Though not strictly necessary, the field name should contain only alphanumeric characters and the underscore.  Refrain from using whitespace in the field name.';
$lang['help_fielddef_options'] = 'Here you may specify the valid options for dropdown fields.';
$lang['help_fielddef_public'] = 'Specify if the field definition is public or not.  Public field definitions are viewable in frontend views, and can be entered by the fesubmit action.  Custom fields that are not public can only be edited in the Admin interface by authorized administrators.';
$lang['help_fielddef_type'] = 'Each custom field can be of a different type for different purposes.  Select the field type that best matches the purpose of the field.';
$lang['help_idlist'] = 'Applicable only to the default action (summary view).  This parameter accepts a comma separated list of numeric article ids and allows further filtering articles to only the article ids specified.  The actual list of articles output is still subject to article status, expiry date, and other parameters.';
$lang['help_opt_alert_drafts'] = 'If enabled, you will receive notifications (alerts) indicating that one or more news articles needs to be reviewed and published.';
$lang['help_opt_allowed_upload_types'] = 'For custom fields of type &quot;file&quot; This setting indicates a comma separated list of file extensions that are valid for the article editor to upload.';
$lang['help_opt_dflt_category'] = 'This option allows specifying the default category for new news articles.';
$lang['help_opt_hide_summary'] = 'This option allows disabling the summary field when adding and/or editing a news article <em>(including with the fesubmit action)</em>';
$lang['help_opt_allow_summary_wysiwyg'] = 'This field indicates whether a WYSIWYG editor should be enabled for the summary field when editing an article.  In many circumstances the summary field is a simple text field, however this is optional.<br/>This setting is ignored if the summary field is disabled completely <em>(see above)</em>';
$lang['help_opt_expiry_interval'] = 'Set the default number of days (minimum 1) That articles will expire in when article expiry is enabled.   The expiry date can be adjusted when adding or editing a news article';
$lang['help_pagelimit'] = 'Maximum number of items to display (per page).  If this parameter is not supplied all matching items will be displayed.  If it is, and there are more items available than specified in the parameter, text and links will be supplied to allow scrolling through the results.  The maximum value for this parameter is 1000.';
$lang['hide_summary_field'] = 'Hide the summary field when adding or editing articles';

// I
$lang['info_allow_fesubmit'] = 'This option controls wether the fesubmit action will be allowed to function at all for this site.  Use caution when enabling this.';
$lang['info_categories'] = 'For organization purposes news articles can be organized into hierarchical categories';
$lang['info_detail_returnid'] = 'This preference is used to determine a page (and therefore a template) to use to view detail pages.  Custom news Detail URLS will not work if this parameter is not set to a valid page.  Additionally, if this preference is set, and no detailpage parameter is provided on the news tag, then this value will be used for detail links';
$lang['info_expired_searchable'] = 'If enabled, expired articles may continue to be indexed by the search module, and appear in search results';
$lang['info_expired_viewable'] = 'If enabled, expired articles can be viewed in detail mode (this is reproducing older functionality).  the showall parameter can be used on the URL (when not using pretty urls) to also indicate that expired articles can be viewed';
$lang['info_fesubmit_notification'] = 'You may optionally send an email to a single email address when a new article is submitted via the fesubmit action.';
$lang['info_maxlength'] = 'The maximum length only applies to text input fields.';
$lang['info_public'] = 'Only Public fields are available for frontend editing, and/or for display in summary or detail views.';
$lang['info_reorder_categories'] = 'Drag and drop each item into the correct order to change category relationships';
$lang['info_searchable'] = 'This field indicates whether this article should be indexed by the search module';
$lang['info_sysdefault'] = '(the content used by default when a new template is created)';
$lang['info_sysdefault2'] = '<strong>Note:</strong> This tab contains text areas to allow you to edit a set of templates that are displayed when you create a \'new\' summary, detail, or form template.  Changing content in this tab, and clicking \'submit\' will <strong>not effect any current displays</strong>.';

// L
$lang['lastpage'] = '&gt;&gt;';
$lang['lbl_adminsearch'] = 'Search News Articles';
$lang['linkedfile'] = 'Linked file';

// M
$lang['maxlength'] = 'Maximum Length';
$lang['msg_cancelled'] = 'Operation Cancelled';
$lang['msg_categoriesreordered'] = 'Category order updated';
$lang['msg_contenttype_removed'] = <<<EOT
The news content type has been removed.  Please place {news} tags with appropriate parameters into your page template or into your page content to replace this functionality.
EOT;
$lang['msg_success'] = 'Operation Successful';
$lang['more'] = 'More';
$lang['moretext'] = 'More Text';

// N
$lang['name'] = 'Name';
$lang['nameexists'] = 'A field by that name already exists';
$lang['needpermission'] = 'You need the \'%s\' permission to perform that function.';
$lang['newcategory'] = 'New Category';
$lang['news'] = 'News';
$lang['news_return'] = 'Return';
$lang['nextpage'] = '&gt;';
$lang['noarticles'] = 'There are currently no news articles created';
$lang['noarticlesinfilter'] = 'There are no news articles to show using this filter';
$lang['nocategorygiven'] = 'No Category Given';
$lang['nocontentgiven'] = 'No Content Given';
$lang['noitemsfound'] = '<strong>No</strong> items found for category: %s';
$lang['nonamegiven'] = 'No Name Given';
$lang['none'] = 'None';
$lang['nopostdategiven'] = 'No Post Date Given';
$lang['notanumber'] = 'Maximum Length is Not a Number';
$lang['note'] = '<em>Note:</em> Dates must be in a \'yyyy-mm-dd hh:mm:ss\' format.';
$lang['notify_n_draft_items'] = 'You have %s that is/are not published';
$lang['notify_n_draft_items_sub'] = '%d News article(s)';
$lang['notitlegiven'] = 'No Title Given';
$lang['numbertodisplay'] = 'Number to Display (empty shows all records)';

// O
$lang['options'] = 'Options';
$lang['optionsupdated'] = 'The options were successfully updated.';

// P
$lang['parent'] = 'Parent';
$lang['postdate'] = 'Post Date';
$lang['postinstall'] = 'Make sure to set the "Modify News" permission on users who will be administering News items.';
$lang['post_date_asc'] = 'Post Date Ascending';
$lang['post_date_desc'] = 'Post Date Descending';
$lang['preview'] = 'Preview';
$lang['prevpage'] = '&lt;';
$lang['print'] = 'Print';
$lang['prompt_alert_drafts'] = 'Alert on Unapproved Articles';
$lang['prompt_allow_fesubmit'] = 'Allow news articles to be submitted by the frontend';
$lang['prompt_default'] = 'Default';
$lang['prompt_go'] = 'Go';
$lang['prompt_name'] = 'Name';
$lang['prompt_newtemplate'] = 'Create A New Template';
$lang['prompt_of'] = 'of';
$lang['prompt_page'] = 'Page';
$lang['prompt_pagelimit'] = 'Page Limit';
$lang['prompt_redirecttocontent'] = 'Return to page';
$lang['prompt_sorting'] = 'Sort By';
$lang['prompt_template'] = 'Template Source';
$lang['prompt_templatename'] = 'Template Name';
$lang['public'] = 'Public';
$lang['published'] = 'Published';

// R
$lang['reassign_category'] = 'Change Category To';
$lang['removed'] = 'Removed';
$lang['reorder'] = 'Reorder';
$lang['reorder_categories'] = 'Reorder Categories';
$lang['reset'] = 'Reset';
$lang['resettodefault'] = 'Reset to Factory Defaults';
$lang['restoretodefaultsmsg'] = 'This operation will restore the template contents to their system defaults.  Are you sure you want to proceed?';
$lang['revert'] = 'Set Status to \'Draft\'';

// S
$lang['searchable'] = 'Searchable';
$lang['select'] = 'Select';
$lang['select_option'] = 'Select Option';
$lang['selectall'] = 'Select All';
$lang['selectcategory'] = 'Select Category';
$lang['showchildcategories'] = 'Show Child Categories';
$lang['sortascending'] = 'Sort Ascending';
$lang['startdate'] = 'Start Date';
$lang['startdatetoolate'] = 'The Start Date is too late (after end date?)';
$lang['startoffset'] = 'Start displaying at the nth item';
$lang['startrequiresend'] = 'Entering a start date requires an end date also';
$lang['status'] = 'Status';
$lang['status_asc'] = 'Status Ascending';
$lang['status_desc'] = 'Status Descending';
$lang['subject_newnews'] = 'A new News article has been posted';
$lang['submit'] = 'Submit';
$lang['summary'] = 'Summary';
$lang['summarytemplate'] = 'Summary Templates';
$lang['summarytemplateupdated'] = 'The News Summary Template was successfully updated.';
$lang['sysdefaults'] = 'Restore to defaults';

// T
$lang['template'] = 'Template';
$lang['textarea'] = 'Text Area';
$lang['textbox'] = 'Text Input';
$lang['title'] = 'Title';
$lang['title_asc'] = 'Title Ascending';
$lang['title_available_templates'] = 'Available Templates';
$lang['title_browsecat_sysdefault'] = 'Default Browse category Template';
$lang['title_browsecat_template'] = 'Browse Category Template Editor';
$lang['title_desc'] = 'Title Descending';
$lang['title_detail_returnid'] = 'Default page to use for detail views';
$lang['title_detail_settings'] = 'Detail View Settings';
$lang['title_detail_sysdefault'] = 'Default Detail Template';
$lang['title_detail_template'] = 'Detail Template Editor';
$lang['title_draft_entries'] = 'Unapproved News articles';
$lang['title_fesubmit_form'] = 'Submit news article';
$lang['title_fesubmit_settings'] = 'Frontend Submit Settings';
$lang['title_filter'] = 'Filters';
$lang['title_form_sysdefault'] = 'Default Form Template';
$lang['title_form_template'] = 'Form Template Editor';
$lang['title_news_settings'] = 'Settings - News module';
$lang['title_notification_settings'] = 'Notification Settings';
$lang['title_submission_settings'] = 'News Submission Settings';
$lang['title_summary_sysdefault'] = 'Default Summary Template';
$lang['title_summary_template'] = 'Summary Template Editor';
$lang['toggle_bulk'] = 'Select this article for bulk processing';
$lang['type'] = 'Type';
$lang['type_browsecat'] = 'Browse Category';
$lang['type_form'] = 'Frontend Form';
$lang['type_detail'] = 'Detail';
$lang['type_News'] = 'News';
$lang['type_summary'] = 'Summary';

// U
$lang['unknown'] = 'Unknown';
$lang['unlimited'] = 'Unlimited';
$lang['up'] = 'Up';
$lang['uploadscategory'] = 'Uploads Category';
$lang['url'] = 'URL (slug)';
$lang['useexpiration'] = 'Use Expiration Date';

// V
$lang['viewfilter'] = 'View Filter';

// W
$lang['warning_preview'] = 'Warning: This preview panel behaves much like a browser window allowing you to navigate away from the initially previewed page. However, if you do that, you may experience unexpected behaviour.  Navigating away from the initial page and returning will not give the expected results.<br/><strong>Note:</strong> The preview does not upload files you may have selected for upload.';
$lang['with_selected'] = 'With Selected';

?>
