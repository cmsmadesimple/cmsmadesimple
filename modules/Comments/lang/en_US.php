<?php
$lang['addacomment'] = 'Add A Comment';
$lang['cancel'] = 'Cancel';
$lang['comment'] = 'Comment';
$lang['error'] = 'Error';
$lang['errorenterauthor'] = 'Enter An Author';
$lang['errorentercomment'] = 'Enter A Comment (isn\'t that the point?)';
$lang['submit'] = 'Submit';
$lang['yourname'] = 'Your Name';
$lang['helpnumber'] = 'Maximum number of items to display -- leaving empty will show all items';
$lang['dateformat'] = 'Date/Time format using parameters from php\'s strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.';
$lang['help'] = <<<EOD
	<h3>What does this do?</h3>
	<p>The comments module is a tag module.  It's used to add comments to individual pages which can be read by users who visit the page later.  The practical reason for this module is for documentation pages, so that users can add additional comments and information to the page.</p>
	<h3>How do I use it?</h3>
	<p>Comments is just a tag module.  It's inserted into your page or template by using the cms_module tag.  Example syntax would be: <code>{cms_module module="comments"}</code></p>
EOD;
?>
