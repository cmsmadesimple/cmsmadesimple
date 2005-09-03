<?php
$lang['addarticle'] = 'Add Article';
$lang['addcategory'] = 'Add Category';
$lang['addnewsitem'] = 'Add News Item';
$lang['allcategories'] = 'All Categories';
$lang['allentries'] = 'All Entries';
$lang['areyousure'] = 'Are you sure you want to delete?';
$lang['articles'] = 'Articles';
$lang['cancel'] = 'Cancel';
$lang['category'] = 'Category';
$lang['categories'] = 'Categories';
$lang['content'] = 'Content';
$lang['dateformat'] = '%s not in a valid yyyy-mm-dd hh:mm:ss format';
$lang['delete'] = 'Delete';
$lang['description'] = 'Add, edit and remove News entries';
$lang['detailtemplate'] = 'Detail Template';
$lang['displaytemplate'] = 'Display Template';
$lang['edit'] = 'Edit';
$lang['enddate'] = 'End Date';
$lang['endrequiresstart'] = 'Entering an end date requires a start date also';
$lang['entries'] = '%s Entries';
$lang['expiry'] = 'Expriry';
$lang['filter'] = 'Filter';
$lang['more'] = 'More';
$lang['moretext'] = 'More Text';
$lang['name'] = 'Name';
$lang['news'] = 'News';
$lang['newcategory'] = 'New Category';
$lang['needpermission'] = 'You need the \'%s\' permission to perform that function.';
$lang['nocategorygiven'] = 'No Category Given';
$lang['nocontentgiven'] = 'No Content Given';
$lang['noitemsfound'] = '<strong>No</strong> items found for category: %s';
$lang['nopostdategiven'] = 'No Post Date Given';
$lang['note'] = '<em>Note:</em> Dates must be in a \'yyyy-mm-dd hh:mm:ss\' format.';
$lang['notitlegiven'] = 'No Title Given';
$lang['numbertodisplay'] = 'Number to Display (empty shows all records)';
$lang['print'] = 'Print';
$lang['postdate'] = 'Post Date';
$lang['postinstall'] = 'Make sure to set the "Modify News" permission on users who will be administering News items.';
$lang['rsstemplate'] = 'RSS Template';
$lang['selectcategory'] = 'Select Category';
$lang['sortascending'] = 'Sort Ascending';
$lang['startdate'] = 'Start Date';
$lang['startrequiresend'] = 'Entering a start date requires an end date also';
$lang['submit'] = 'Submit';
$lang['summary'] = 'Summary';
$lang['summarytemplate'] = 'Summary Template';
$lang['title'] = 'Title';
$lang['useexpiration'] = 'Use Expiration Date';
$lang['helpnumber'] = 'Maximum number of items to display =- leaving empty will show all items.';
$lang['helpmakerssbutton'] = 'Make a button to link to an RSS feed of the News items.';
$lang['helpcategory'] = 'Only display items for that category and it\'s children.  leaving empty, will show all categories.';
$lang['helpmoretext'] = 'Text to display at the end of a news item if it goes over the summary length.  Defaults to "more..."';
$lang['helpsummarytemplate'] = 'Use a separate template for displaying the article summary.  It have to live in modules/News/templates.';
$lang['helpdetailtemplate'] = 'Use a separate template for displaying the article detail.  It have to live in modules/News/templates.';
$lang['helpsortby'] = 'Field to sort by.  Options are: "news_date", "summary", "news_data", "news_category", "news_title".  Defaults to "news_date".';
$lang['helpsortasc'] = 'Sort news items in ascending date order rather than descending.';
$lang['helpdateformat'] = 'Format to display the article\'s post date with.  This is based on the <a href="http://php.net/strftime" target="_blank">strftime</a> function and can be used in your template with $entry->formatpostdate.';
$lang['help'] = <<<EOF
	<h3>What does this do?</h3>
	<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to administration menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
	<h3>Security</h3>
	<p>The user must belong to a group with the 'Modify News' permission in order to add, edit, or delete News entries.</p>
	<h3>How do I use it?</h3>
	<p>The easiest way to use it is in conjunction with the cms_module tag.  This will insert the module into your template or page anywhere you wish, and display news items.  The code would look something like: <code>{cms_module module="news" number="5" category="beer"}</code></p>
EOF;
?>
