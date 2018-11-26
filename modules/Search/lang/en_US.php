<?php
// C
$lang['clear'] = 'Clear';
$lang['confirm_clearstats'] = 'Are you sure you want to permanently clear all statistics?';
$lang['confirm_reindex'] = 'This operation could take an extended amount of time, and/or require an extensive amount of PHP memory.  Are you sure you want to re-index all content?';
$lang['count'] = 'Count';

// D
$lang['default_stopwords'] = 'i, me, my, myself, we, our, ours, ourselves, you, your, yours, yourself, yourselves, he, him, his, himself, she, her, hers, herself, it, its, itself, they, them, their, theirs, themselves, what, which, who, whom, this, that, these, those, am, is, are, was, were, be, been, being, have, has, had, having, do, does, did, doing, a, an, the, and, but, if, or, because, as, until, while, of, at, by, for, with, about, against, between, into, through, during, before, after, above, below, to, from, up, down, in, out, on, off, over, under, again, further, then, once, here, there, when, where, why, how, all, any, both, each, few, more, most, other, some, such, no, nor, not, only, own, same, so, than, too, very';
$lang['description'] = 'Module for search site and other module\'s contents.';

// E
$lang['eventdesc-SearchAllItemsDeleted'] = 'Sent when all items are deleted from the index.';
$lang['eventhelp-SearchAllItemsDeleted'] = '<h4>Parameters</h4>
<ul>
<li>None</li>
</ul>
';

$lang['eventdesc-SearchCompleted'] = 'Sent when a search is completed.';
$lang['eventhelp-SearchCompleted'] = '<h4>Parameters</h4>
<ol>
<li>Text that was searched for.</li>
<li>Array of the completed results.</li>
</ol>
';

$lang['eventdesc-SearchInitiated'] = 'Sent when a search is started.';
$lang['eventhelp-SearchInitiated'] = '<h4>Parameters</h4>
<ol>
<li>Text that was searched for.</li>
</ol>
';

$lang['eventdesc-SearchItemAdded'] = 'Sent when a new item is indexed.';
$lang['eventhelp-SearchItemAdded'] = '<h4>Parameters</h4>
<ol>
<li>Module name.</li>
<li>Id of the item.</li>
<li>Additional Attribute.</li>
<li>Content to index and add.</li>
</ol>
';

$lang['eventdesc-SearchItemDeleted'] = 'Sent when an item is deleted from the index.';
$lang['eventhelp-SearchItemDeleted'] = '<h4>Parameters</h4>
<ol>
<li>Module name.</li>
<li>Id of the item.</li>
<li>Additional Attribute.</li>
</ol>
';
$lang['export_to_csv'] = 'Export to CSV';

// H
$lang['help'] = '<h3>What does this do?</h3>
<p>Search is a module for searching "core" content along with certain registered modules.  You put in a word or two and it gives you back matching, relevant results.</p>
<h3>How do I use it?</h3>
<p>The easiest way to use it is with the {Search} wrapper tag (wraps the module in a tag, to simplify the syntax). This will insert the module into your template or page anywhere you wish, and display the search form.  The code would look something like: <code>{Search}</code></p>
<h4>How do i prevent certain content from being indexed</h4>
<p>The search module will not search any "inactive" pages. However on occasion, when you are using the CustomContent module, or other smarty logic to show different content to different groups of users, it may be advisable to prevent the entire page from being indexed even when it is live.  To do this include the following tag anywhere on the page <em>&lt;!-- pageAttribute: NotSearchable --&gt;</em> When the search module sees this tag in the page it will not index any content for that page.</p>
<p>The <em>&lt;!-- pageAttribute: NotSearchable --&gt;</em> tag can be placed in the template as well.  if this is done, none of the pages attached to that template will be indexed.  Those pages will be re-indexed if the tag is removed</p>
';

// I
$lang['input_resetstopwords'] = 'Load';

// N
$lang['noresultsfound'] = 'No results found!';
$lang['nostatistics'] = 'No statistics found!';

// O
$lang['options'] = 'Options';

// P
$lang['param_action'] = 'Specify the mode of operation for the module.  Acceptable values are \'default\', and \'keywords\'.  The keywords action can be used to generate a comma seperated list of words suitable for use in a keywords meta tag.';
$lang['param_count'] = 'Used with the keywords action, this parameter will limit the output to the specified number of words';
$lang['param_detailpage'] = 'Used only for matching results from modules, this parameter allows specifying a different detail page for the results.  This is useful if, for example, you always display your detail views in a page with a different template.  <em>(<strong>Note:</strong> modules have the ability to override this parameter.)</em>';
$lang['param_formtemplate'] = 'Used only for the default action, this parameter allows specyfing the name of a non default template.';
$lang['param_inline'] = 'If true, the output from the search form will replace the original content of the \'search\' tag in the originating content block.  Use this parameter if your template has multiple content blocks, and you do not want the output of the search to replace the default content block';
$lang['param_modules'] = 'Limit search results to values indexed from the specified (comma separated) list of modules';
$lang['param_pageid'] = 'Applicable only with the keywords action, this parameter can be used to specify a different pageid to return results for';
$lang['param_passthru'] = 'Pass named parameters down to specified modules.  The format of each of these parameters is: "passtru_MODULENAME_PARAMNAME=\'value\'" i.e.: passthru_News_detailpage=\'newsdetails\'"';
$lang['param_resultpage'] = 'Page to display search results in.  This can either be a page alias or an id.  Used to allow search results to be displayed in a different template from the search form';
$lang['param_resulttemplate'] = 'This parameter allows specifying the name of a non default template to use for displaying search results.';
$lang['param_searchtext'] = 'Text to place into the search box';
$lang['param_submit'] = 'Text to place into the submit button';
$lang['param_useor'] = 'Change the default relationship from an OR relationship to an AND relationship';
$lang['prompt_alpharesults'] = 'Sort results alphabetically instead of by weight';
$lang['prompt_resetstopwords'] = 'Load default Stop Words from language';
$lang['prompt_resultpage'] = 'Page for individual module results <em>(Note modules may optionally override this)</em>';
$lang['prompt_savephrases'] = 'Track Search Phrases, not Individual Words';
$lang['prompt_searchtext'] = 'Default Search Text';

// R
$lang['reindexallcontent'] = 'Re-index All Content';
$lang['reindexcomplete'] = 'Re-index Complete!';
$lang['restoretodefaultsmsg'] = 'This operation will restore the template contents to their system defaults.  Are you sure you want to proceed?';
$lang['resulttemplate'] = 'Result Template';
$lang['resulttemplateupdated'] = 'Result Template Updated';

// S
$lang['search'] = 'Search';
$lang['searchresultsfor'] = 'Search Results For';
$lang['searchsubmit'] = 'Submit';
$lang['searchtemplate'] = 'Search Template';
$lang['searchtemplateupdated'] = 'Search Template Updated';
$lang['search_method'] = 'Pretty URLs Compatibility via Method POST, default value is always GET, to make this work just put {Search search_method="post"} ';
$lang['statistics'] = 'Statistics';
$lang['stopwords'] = 'Stop Words';
$lang['submit'] = 'Submit';
$lang['sysdefaults'] = 'Restore To Defaults';

// T
$lang['timetaken'] = 'Time Taken';
$lang['type_Search'] = 'Search';
$lang['type_searchform'] = 'Search Form';
$lang['type_searchresults'] = 'Search Results';

// U
$lang['usestemming'] = 'Use Word Stemming (English Only)';
$lang['use_or'] = 'Find results that match ANY word';

// W
$lang['word'] = 'Word';

?>
