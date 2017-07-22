<?php
// A
$lang['all'] = 'All';

// D
$lang['desc_css_search'] = 'Search for matching text in stylesheets';
$lang['desc_content_search'] = 'Search for matching text in content pages';
$lang['desc_filter_all'] = 'Toggle all filters';
$lang['desc_oldmodtemplate_search'] = 'Search old module templates.';
$lang['desc_template_search'] = 'Search for matching text in templates';

// E
$lang['error_nosearchtext'] = 'Please enter a search term';
$lang['error_select_slave'] = 'You must select at least one filter type';

// F
$lang['filter'] = 'Filter';
$lang['finished'] = 'Finished';
$lang['friendlyname'] = 'Admin Search';

// H
$lang['help'] = <<<EOT
<h3>What does this do?</h3>
  <p>This module provides the ability to quickly find places in templates, content pages, and other database where a text string occurs.  It is particularly useful for finding smarty tags, class names, ids or other bits of HTML code that may be hard to find in a large website.</p>
  <p>This module has no frontend interaction it is designed for use by CMSMS site developers or editors to find sub strings of text or code.  Not for use in finding text on the frontend of websites.</p>

<h3>How Do I Use It</h3>
  <p>This module is visible to most administrators of the website with at least some permissions to edit templates, stylesheets, or some content   Though the list of what can be searched may be reduced.</p>
  <p>The module provides a text field where a single string can be entered (the string is not divided into words or otherwise parsed).  It also provides the ability to only search certain subsections of the website.</p>
  <p>Searching will generate a nested, expandable list of sections where matches were found.  Under each section a description of the match is displayed.  Usually with a link that will direct you to a form to edit the item.</p>

<h3>Support</h3>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2012, calguy1000 <a href="mailto:calguy1000@hotmail.com">&lt;calguy1000@hotmail.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>
EOT;

// L
$lang['lbl_content_search'] = 'Search Content Pages';
$lang['lbl_css_search'] = 'Search Stylesheets';
$lang['lbl_gcb_search'] = 'Search Global Content Blocks';
$lang['lbl_oldmodtemplate_search'] = 'Search module templates';
$lang['lbl_search_desc'] = 'Search Descriptions <em>(where applicable)</em>';
$lang['lbl_template_search'] = 'Search Templates';

// M
$lang['moddescription'] = 'A utility to search the database tables for rows containing certain text. Useful for finding where certain styles, tags, or modules are used.';

// P
$lang['placeholder_search_text'] = 'Enter Search Text';
$lang['postinstall'] = 'Admin Search module installed';
$lang['postuninstall'] = 'Admin Search module uninstalled';

// S
$lang['search'] = 'Search';
$lang['search_text'] = 'Search Text';
$lang['search_results'] = 'Search Results';
$lang['sectiondesc_oldmodtemplates'] = 'Results in this section are not clickable, as each module provides its own Admin panel interface for editing templates';
$lang['settings'] = 'Settings';
$lang['starting'] = 'Starting';

// W
$lang['warn_clickthru'] = 'This will open another window. Cancelling from that window may not return you to this page. Your search results may be lost.';

?>
