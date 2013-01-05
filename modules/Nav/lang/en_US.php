<?php
#A
#B
#C
#D
#E
#F
$lang['friendlyname'] = 'CMSMS Navigation Builder';

#G
#H
$lang['help'] = <<<EOT
<h3>What does this do?</h3>
  <p>The &quot;Nav&quot; module is an engine for generating navigations from the CMSMS content tree and a smarty template.  This module provides flexible filtering capabilities to allow building numerous navigations based on different criteria, and a simple to use hierarchical data format for generating navigations with complete flexibility.</p>
  <p>This module has no admin interface of its own, insted it uses the DesignManager to manage menu templates.</p>
<h3>How do I use it?</h3>
<p>The simplest way to use this module is to insert the <code>{Nav}</code> tag into a template.  The module accepts numerous parameters to alter its behavior and filter the data.</p>
<h3>Why do I care about templates?</h3>
<p>This is the power of CMSMS.  Navigations can be built automatically using the data from your content hierarchy, and a smarty template.  There is no need to edit a navigation object each time a content page is added or removed from the system.  Additionally, navigation templates can easily include javascript or advanced functionality and can be shared between websites.</p>
<p>This module is distributed with a few sample templates, they are only samples.  You are free and encouraged to copy them and modify the templaes to your liking.  Styling of the navigation is accomplished by editing a CMSMS stylesheet.  Stylesheets are not included with the Nav module.</p>
<h3>The node object:</h3>
  <p>Each nav template is provided with an array of node objects that match the criteria specified on the tag.  Below is a description of the members of the node object:</p>
<ul>
  <li>\$node->id -- The content object integer ID.</li>
  <li>\$node->url -- URL to the content object.  This should be used when building links.</li>
  <li>\$node->accesskey -- Access Key, if defined.</li>
  <li>\$node->tabindex -- Tab index, if defined.</li>
  <li>\$node->titleattribute -- Description, or Title attribute (title), if defined.</li>
  <li>\$node->hierarchy -- Hierarchy position.  (i.e. 1.3.3)</li>
  <li>\$node->default -- TRUE if this node refers to the default content object.</li>
  <li>\$node->menutext -- Menu Text</li>
  <li>\$node->raw_menutext -- Menu Text without having html entities converted</li>
  <li>\$node->alias -- Page alias</li>
  <li>\$node->extra1 -- This field contains the value of the extra1 page property, unless the loadprops-parameter is set to NOT load the properties.</li>
  <li>\$node->extra2 -- This field contains the value of the extra2 page property, unless the loadprops-parameter is set to NOT load the properties.</li>
  <li>\$node->extra3 -- This field contains the value of the extra3 page property, unless the loadprops-parameter is set to NOT load the properties.</li>
  <li>\$node->image -- This field contains the value of the image page property (if non empty), unless the loadprops-parameter is set to NOT load the properties.</li>
  <li>\$node->thumbnail -- This field contains the value of the thumbnail page property (if non empty), unless the loadprops-parameter is set to NOT load the properties.</li>
  <li>\$node->target -- This field contains Target for the link (if non empty), unless the loadprops-parameter is set to NOT load the properties.</li>
  <li>\$node->created -- Item creation date</li>
  <li>\$node->modified -- Item modified date</li>
  <li>\$node->parent -- TRUE if this node is a parent of the currently selected page</li>
  <li>\$node->current -- TRUE if this node is the currently selected page</li>
  <li>\$node->children -- An array of node objects representing the displayable children of this node.</li>
</ul>
<h3>Examples:</h3>
<ul>
   <li>A simple navigation that is only 2 levels deep, using the default template:<br/>
     <pre><code>{Nav number_of_levels=2}</code></pre>
   </li>
     <li>Display a simple navigation two levels deep starting with the children of the current page.  Use the default template:</li>
     <pre><code>{Nav number_of_levels=2 start_page=\$page_lias}</code></pre>
   </li>
   <li>Display a simple navigation two levels deep starting with the children of the current page.  Use the default template:</li>
     <pre><code>{Nav number_of_levels=2 childrenof=\$page_lias}</code></pre>
   </li>
   <li>Display a simple navigation two levels deep starting with the current page, and everything below it.  Use the default template:</li>
     <pre><code>{Nav number_of_levels=2 start_page=\$page_lias}</code></pre>
   </li>
   <li>Display a navigation two levels deep starting with the current page, its peers, and everything below it.  Use the default template:</li>
     <pre><code>{Nav start_page=\$page_lias show_root_siblings=1}</code></pre>
   </li>
   <li>Display a navigation of the specified menu items and their children.  Use the templte named mymenu</li>
     <pre><code>{Nav items='alias1,alias2,alias3' number_of_levels=20 template=mymenu}</code></pre>
   </li>
</ul>
EOT;
$lang['help_action'] = 'TODO';
$lang['help_childrenof'] = 'This option will have the menu only display items that are descendants of the selected page id or alias.  i.e: <code>{menu childrenof=$page_alias}</code> will only display the children of the current page.';
$lang['help_items'] = 'Specify a comma separated list of page aliases that this menu should display.';
$lang['help_loadprops'] = 'Use this parameter when NOT using advanced properties in your menu manager template.  This will disable the loading of all of the content properties for each node (such as extra1, image, thumbnail, etc).  This will dramatically decrease the number of queries required to build a menu, and increase memory requirements, but will remove the possibility for much more advanced menus';
$lang['help_number_of_levels'] = 'This setting will limit the depth of the generated menu to the specified number of levels.  By default the value for this parameter is implied to be unlimited, except when using the items parameter, in which case the number_of_levels parameter is implied to be 1';
$lang['help_show_all'] = 'This option will cause the menu to show all nodes even if they are set to not show in the menu. It will still not display inactive pages however.';
$lang['help_show_root_siblings'] = 'This option only becomes useful if start_element or start_page are used.  It basically will display the siblings along side of the selected start_page/element.';
$lang['help_start_element'] = 'Starts the menu displaying at the given start_element and showing that element and it\'s children only.  Takes a hierarchy position (e.g. 5.1.2).';
$lang['help_start_page'] = 'Starts the menu displaying at the given start_page and showing that element and it\'s children only.  Takes a page alias.';
$lang['help_template'] = 'The template to use for displaying the menu.  The named template must exist in the DesignManager or an error will be displayed.  If this parameter is not specified the default template of type Nav::Navigation will be used';
#I
#J
#K
#L
#M
#N
#O
#P
#Q
#R
#S
#T
$lang['type_navigation'] = 'Navigation';
$lang['type_Nav'] = 'Nav';

#U
#V
#W
#X
#Y
#Z
?>