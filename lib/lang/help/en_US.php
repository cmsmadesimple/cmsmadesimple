<?php
/* H */
$lang['help_group_permissions'] = <<<EOT
<h4>CMSMS Admin Permission Model</h4>
<ul>
<li>CMSMS Uses a system of named permissions.  Access to these permissions determines a users ability to perform different functions in the CMSMS Admin console.</li>
<li>The CMSMS core creates several permissions on installation <em>(occasionally permissions are added or deleted during an upgrade process)</em>.  Third party modules may create additional permissions.</li>
<li>Permissions are associated with user groups.  An authorized user can adjust the permissions that are associated with certain member groups <em>(including the permission to change a group permissions)</em>.  The <strong>Admin</strong> group is a special group.  Members of this group will have all permissions.</li>
<li>Admin user accounts can be members of zero or more groups.  It may be possible for a user account that is not a member of any groups to still perform various functionality <em>(please read about ownership and additional-editors in the Content Manager help, and Design Manager help).</em>.  The first user account <em>(uid == 1)</em>, which is typically named &quot;Admin&quot; is a special user account and will have all permissions.</li>
</ul>
EOT;

$lang['help_cmscontentmanager_help'] = <<<EOT
<h3>Introduction</h3>
<p>This document describes the CMS Content Manager module.  It is aimed primarily at the website designer or developer and describes in broad terms how content items work in CMS Made Simple.</p>
<p>The primary interface of the Content Manager module is the content list.  It displays content items in a tabular format and provides the ability to quickly search, navigate, and manage multiple content items.  This is a dynamic list.  The display is adjusted depending on some site configuration items, some global settings, user permissions and the individual content items.   The following text describes what content items are, and how the content list behaves with them.</p>
<h3>Content Hierarchy and Navigations</h3>
<p>CMS Made Simple builds frontend navigations dynamically from the content list, the individual types of content items, the content in those content items, and the navigation template.  The organization of navigations is primarily controlled by the parent-child/hierarchy relationship of your content items.  Starting from the top <em>(root)</em> level, downwards.</p>
<p>Adding a new item to the navigation menu is as simple as creating a new content item, placing it in the desired location in the hierarchy, and <em>(depending upon the content item type)</em> specifying the different properties that that content item type provides.</p>
<h3>Content Item Types:</h3>
  <p>CMSMS is distributed with several different content item types <em>(and more are available via third party add-on modules)</em>.  These content item types serve different purposes when a navigation is generated.  Some do not contain content, but are only used for managing navigations.  For example, the separator content item type has no content of its own, and exists solely to organize content items and provide a visible separator in the generated navigation.</p>
  <p>Below is a brief description of each content item type that is distributed with CMS Made Simple</p>
<ul>
  <li>Content Page
  <p>This content item type is most similar to an HTML page, and is usually used for that purpose.  When editors create content page items they select a design and template which controls the appearance of the page, specify a title, and enter the content for the page.</p>
    <p>Content items may also contain forms, logic, display dynamic data from modules, or user defined tags (UDTs).  This flexibility allows creating specialized applications, or extremely flexible and dynamic websites.</p>
  </li>
  <li>Link
    <p>This content item type is used in navigations to generate a link to a page on an external website.</p>
  </li>
  <li>Page Link
  <p>This content item type is also used in navigations.  It generates a secondary link to an existing content page.  This content item type can be used if a content item can be accessed from multiple places in the navigation.</p>
  </li>
  <li>Separator
  <p>This content item type is also used in navigations.  It is usually used to generate a horizontal (or vertical) divider between navigation items.  Some types of navigations <em>(determined by the navigation template)</em> may not display separators at all.</p>
  </li>
  <li>Section Header
    <p>The section header is also only displayed in navigations.  It is used to organize content items.  It provides a textual header above, or between other content items.  Section headers do not have URLS and cannot usually be clicked on.  Some navigation templates may style section headers differently than other content items.</p>
  </li>
  <li>Error Page
    <p>The Error Page is a special type of content item type.  It is used when a user attempts to navigate to a content item that is either not navigable or does not exist.</p>
  </li>
</ul>
<p>Many third party modules provide more content types to serve different purposes.  Such as displaying catalogues of products, or restricting the content to authorized users.</p>
<h3>The Content Item List</h3>
<p>The content list is the primary interface to the module.  This form provides the main management interface for your content. From here you can create, edit, delete, copy, deactivate, and organize your content items.  This screen is heavily optimized for larger websites providing pagination and search mechanisms to display only a small amount of pages at a time, but to quickly find items to manage.</p>
 <h4>Columns</h4>
<p>Each content item is displayed as a row in a table.  There are a number of columns to quickly display various attributes of each content item, and some convenient action icons.  Some columns may be hidden from view entirely, or only for some rows depending upon a number of factors:</p>
  <ul>
    <li>Your Access Permissions and Page Ownership:
      <p>If your account is restricted to certain permissions some columns may not be displayed, or may be disabled.</p>
    </li>
    <li>System Preferences and Site Configuration
      <p>Some system preferences, and site configuration options will result in some columns from being disabled.  For example, the &quot;url&quot; column </p>
    </li>
    <li>The content item type
      <p>Depending on the content item type, certain columns may become irrelevant.  For example, it is not possible for &quot;Section Headers&quot; or &quot;Separators&quot; to become the default page, so nothing will be displayed in the &quot;default&quot; column for those content items.</p>
    </li>
    <li>Whether the content item is being edited
      <p>When other users <em>(or even yourself) are editing a content item, some columns will be hidden in the row for each content type to prevent modifying, deleting or copying the content page.</p>
    </li>
  </ul>
  <h5>Column List</h5>
      <p>The Content Manager module provides a flexible mechanism for hiding and showing different columns in the content list.  Additionally, some columns may be hidden based on the site configuration.  For example, the URL column is hidden if pretty <em>(Search Engine Friendly)</em> URLS are not configured.</p>
      <p>Each column in the content list display has a special purpose:</p>
   <ul>
     <li>Expand/Collapse Column
      <p>When a content item has children this column will be filled with an icon that allows expanding the list to show the item children, or collapsing the list to hide them.  The state of which items are expanded, and which are collapsed is saved on a per-user basis.  So that when you revisit the content manager the expanded/collapsed state of your pages will remain the same.</p>
     </li>

     <li>Hierarchy Column
      <p>The hierarchy column displays the location of each content item in the hierarchy in a numeric fashion.  The hierarchy of the first root level page begins with 1 and increases incrementally for each peer. Each child begins with the 1, and its peers increment incrementally.   Therefore, the second grandchild of the third child of the first item in the content list would have a hierarchy of 1.3.2.</p>
      <p>The hierarchy mechanism is a significant portion of what provides the ability of CMS Content Manager to organize content items, and then build navigations from them.</p>
     </li>

     <li>Page Title / Menu Text Column
      <p>This column can either display the page title, or the page menu text.  This depends on a setting in the &quot;Site Admin &raquo; Content Manager Settings&quot; page.</p>
      <p>This column will contain a link to allow editing the content item <em>(unless the content item is locked)</em>.  Hovering over the text in this column will display additional information about the content item such as the unique numeric content id, and whether the page is cachable or not.</p>
      <p>If the content item is locked, hovering over the text in the column will display information about who locked the item, and when the lock expires.</p>
     </li>

     <li>URL Column
      <p>If enabled, this column will display any alternate URL for this content item.  <em>(Note: Only certain content item types support alternate an alternate URL).</em></p>
     </li>

     <li>Page Alias Column
      <p>This column displays the unique alias associated with each page.  Aliases are text strings that uniquely identify the content item.  You use the content items alias (or numeric id) when you need to refer to a page within the system.<em>(Note: Some content types do not have aliases).</em></p>
     </li>

     <li>Template Column
      <p>This column displays the design and template that is used to display the content for the item.  See the help for the &quot;Design Manager&quot; module for an explanation of how CMSMS manages designs, including stylesheets and templates.  <em>(Note: Some content item types do not use a design, or a template.)</em></p>
     </li>

     <li>Type Column
       <p>This column indicates the content type (i.e: Content, Section header, Separator, etc.).<p>
     </li>

     <li>Owner Column
       <p>The owner column displays the username of the owner of the content item.  Hovering over the text in this column will display information as to when the content item was created and last edited.</p>
     </li>

     <li>Active Column
       <p>This column displays icons to show the current active state of the content item.  Active items can be navigated to, and will appear in navigation menus on the frontend.  If your user account has sufficient privilege to the content item you can click on the icon to toggle its active state.</p>
     </li>

     <li>Default Column
       <p>This column displays whether the content item is the default page or not.  The default content item is the home page for your website.  Only some content types allow the content type to be the default.</p>
       <p>If your user account has sufficient privilege, and the content type supports being the default content for the website you can click on the icon to change the default flag to a different page.</p>
     </li>

     <li>The &quot;Move&quot; Column
       <p>Depending upon your access privileges, you may see icons that allow changing the order of the content items with respect to its immediate peers.  This is a simple mechanism of quickly re-ordering content items amongst its peers.  The &quot;Re-order Pages&quot; option allows reorganizing pages en-mass, and when editing a content item you can quickly assign the item to a different parent.</p>
     </li>

     <li>Action Icons
       <p>Depending on your access privileges, the content type and its current lock state you may see different icons on each content row allowing different functionality:</p>
       <ul>
         <li>View - Open up a new browser window <em>(or tab)</em> and view the content item as your visitors will see it.</li>
         <li>Copy - Copy the content item to a new content item.
           <p>A new content item will be created with a new page title, and alias and you will be presented with the ability to edit the new page.</p>
         </li>
         <li>Delete - Delete the content item
           <p>Depending upon your access privileges, and whether or not the content item has children, the option to delete the content item may be hidden or disabled.</p>
         </li>
         <li>Steal Lock
           <p>For content items that are currently being edited, but for which the lock has expired <em>(the editor has not made a change to the form for a while)</em> this option will allow you to steal the lock.</p>
         </li>
         <li>Bulk operations checkbox
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
    <p>When editing a content item as an owner or as a user with &quot;Manage All Content&quot; permission, the user can select other administrative users, or Admin groups that are also allowed to edit that content item.</p>

 <h4>Relevant Permissions.</h4>
    <p>There are a few permissions that effect what columns are displayed in the content list and the ability to interact with the content list:</p>
    <ul>
      <li>Add Pages
    <p>This permission allows users to create new content items. Additionally, users with this permission are able to copy content items that they have edit ability on.</p>
      </li>
      <li>Modify Any Page
        <p>Users with this position will have the ability to edit any content item.  It is similar to being an &quot;Additional Editor&quot; on all content items.</p>
      </li>
      <li>Remove Pages
        <p>This permission allows users to remove content items that they have edit ability on.  Without this permission, the delete icon on each content item row in the content list will be hidden.</p>
      </li>
      <li>Reorder Pages
        <p>This permission allows users who have edit ability to all siblings of a content item to re-arrange content items amongst their peers.</p>
        <p>i.e: A user in a group who has edit ability to the content item with hierarchy 1.3 and all of its direct siblings <em>(1.1, 1.2, 1.3, 1.4, etc).</em> will be able to re-arrange those items in the navigation.  Users without this permission will not see the move up/down icons in listcontent.</p>
      </li>
      <li>Manage All Content
        <p>This permission provides super-user capability on all content items.  Users with this permission can add, edit, delete, and re-order any content item.  They also have the ability to set the default content item, and perform bulk actions like change ownership that may or may not be available to users with other permissions.</p>
      </li>
    </ul>
   <p>It is possible for an Admin user account to not be a member of any groups, and for that Admin user account still have the ability <em>(as an owner or additional editor)</p> to edit some content items.</p>

 <h4>Content Locking</h4>
   <p>Content locking is a mechanism that prevents two editors from editing the same item at the same time, and therefore destroying each others work.  Admin users are given exclusive access to a content item until such time as they submit the changes.</p>
   <p>If a content item is locked, you will not be able to edit it until the lock has expired.  See below for information on lock expiring.  Once a lock has expired, a user will have the option of stealing the lock from the original editor and beginning a new edit session.</p>
   <p>A special icon is displayed on the content item row to indicate that the lock can be stolen.</p>

 <h4>Configuration</h4>
   <p>Some configuration items effect the visibility of certain items in the content list:</p>

 <h4>Other functionality</h4>
   <ul>
     <li>Pagination
       <p>The content list can be paginated.  This is a performance feature for large sites with a great deal of content items.  The default limit is 500 items, however this limit can be lowered by adjusting the value in the options dialogue.</p>
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
           <p>This option allows deleting multiple content items (and their children) in few steps.  All of the selected content items and their descendants will be analysed for the their eligibility to be deleted.  Users will then be prompted with a list of the content items that passed the analysis <em>(if any)</em> and to confirm the action.</p>
	   <p>Only users with the permission to remove pages and modify any page, or Manage All Content can use this option.</p>
           <p><strong>Note:</strong> When selecting many content items, or content items with many descendants, this can be a very memory, database and time intensive operation.</p>
         </li>
         <li>Set Active
           <p>This option will ensure that the content items selected are marked as &quot;Active&quot;.  Users will be asked to confirm the operation. This operation does not work on descendent of the selected pages.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Inactive
           <p>this option analyses the selected items for eligibility, and will set all of the eligible content items to inactive.  Inactive pages cannot be navigated to, and may break a working website.  The default page cannot be set to inactive.</p>
	   <p>Only users with the &quot;Manage All Content&quot; permission can use this option.</p>
         </li>
         <li>Set Cachable
           <p>This option sets the selected content items to &quoc;cachable&quot;.  This may have different effects based on the site configuration:<p>
           <p>If enabled in &quot;Site Admin >> Global Settings&quot; then content items marked as &quot;cachable&quot; can be cached by the browser <em>(this reduces load on your webserver for users visiting the same page frequently).</em>
           <p>Also in &quot;Site Admin >> Global Settings&quot; Smarty caching effects cachable pages.  This is an advanced tool that will cache the generated html code of a for repeated use, and can dramatically reduce server load and improve performance.  However, it is an advanced topic and may negatively the dynamic nature of some content items.</p>
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

<h3>Adding and Editing Content Items</h3>
 <p>The ability to add content items depends on having either the &quot;Manage All Content&quot; permission, or the &quot;Add Pages&quot; permission.  Users with &quot;Manage All Content&quot;  permission will be able to manage all aspects  of the content item.  Users without this permission will have considerably less abilities.</p>
 <p>The add (or edit) content page form is divided into numerous tabs; numerous properties of the content item will appear on different tabs.  The list of tabs that are visible, and the &quot;properties&quot; on those tabs is influenced by numerous factors:</p>
   <ul>
     <li>The content item type
 <p>Some content item types (such as separators, and section headers) do not require much information, therefore very few tabs and properties will be displayed.</p>
     </li>
     <li>Your permission level
       <p>If your user account does not have the &quot;Manage All Content&quot; permission level, then you are only permitted to manage <em>(by default)</em> the basic properties of the content item.  Enough to edit content, and pick a page in its navigation.  You may also be restricted as to where new content items can be placed in the content hierarchy.</p>
     </li>
     <li>Site settings <em>(i.e.: the &quot;Basic Properties&quot; field in the &quot;Global Settings&quot; window, and others)</em>.
       <p>Some site settings <em>(and even config settings)</em> can influence what properties are displayed on what tab.  The &quot;Basic Properties&quot; setting in the &quot;Site Admin >> Global Settings&quot; page extends the list of content item properties that users with restricted permissions can edit.</p>
     </li>
     <li>The template that has been selected.
       <p>Tags in templates define additional properties <em>(called content blocks)</em> that authorized users can edit when editing a content item that uses that templates.   These content blocks can be plain text areas, WYSIWYG test areas, image selectors, or other items.  Template developers can specify the tab that the edit field for each content block appears on.</p>
     </li>
   </ul>
  <h4>Properties</h4>
    <p>Here we will briefly describe the common properties for the &quot;Content&quot; content item type.  Some content item types use significantly fewer properties, and some content item types supplied by third party modules may behave completely differently.</p>
  <ul>
    <li>Title
      <p>This field describes the title for the content item (if applicable).  The title is usually displayed in the &lt;title&gt; tag in the HTML page head, and somewhere in a prominent place in the HTML page content.  The site developer has complete control over how this data is used or displayed.</p>
    </li>

    <li>Alias
      <p>The page alias is a string that uniquely identifies this content item, and is usually easier to remember than the integer page id.  The alias is used in numerous locations when building CMSMS website.  It can be used to create links to content items, to build specialized navigations, or as behavioural hints to other modules indicating on what content item they should display data.</p>
      <p>By default the page alias is uniquely generated from the title when adding a new content item, however users can specify their own page alias when adding or editing the content item so long as it is unique amongst all other content items.  Some content item types do not require a page alias.</p>
      <p>Users with restricted permissions may not have the ability to specify the alias when adding or editing a content item.</p>
    </li>

    <li>Parent
      <p>The parent property specifies the content item that is the immediate parent of the content item being edited in the content hierarchy.  Users with restricted permissions may not have the ability to edit this property, or may have a restricted list of options for this property.</p>
    </li>

    <li>Content
      <p>Each page template is required to include at least the default content property <em>(a.k.a block)</em>.  However they can define many more, and different types of content blocks.  The default block usually appears in the edit content form as a wWYSIWYG enabled text area allowing the editor to specify some default content for the page.</p>
      <p>Site developers have significant control over the tab that this is displayed in, the label, maxlength, required, and other attributes to control the behaviour of this property in the edit form, and when it is displayed.</p>
      <p>If the WYSIWYG editor is enabled for this content block and content item <em>(see below)</em>, and one or more WYSIWYG editor modules are enabled, and the user has selected a WYSIWYG editor in his preferences then a WYSIWYG editor will be displayed.  Different WYSIWYG editors have different abilities, but most provide the ability to format text in different ways.  Additionally, most WYSIWYG editors allow inserting images and creating links to other content items in your website.</p>
    </li>

    <li>Menu Text
      <p>This property is used when building navigations.  The contents of this field are used as the text to display for this content item in the navigation.</p>
    </li>

    <li>Show in Menu
      <p>Often, it is useful to have content items for special purposes (such as to display sitemaps, search results, login forms, etc.) that are not displayed <em>(by default)</em> in navigation menus.  This property allows each content item to be hidden from navigation items unless overridden elsewhere.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Title Attribute
      <p>This property defines an optional text string that can be used to display additional information for the content item in the navigation.  It is typically used in the &quot;title&quot; attribute for the link that is generated in navigation menus.</p>
      <p>The site developer has the ability to display this data differently, or ignore it completely by modifying the appropriate navigation menu template.  Additionally, this data can be displayed in the page content by modifying the appropriate page template.  This property may not be important for content items in your website.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Access Key
      <p>This property defines an optional access key character <em>usually only one or two characters</em> that can be used in navigation menus to quickly access this content item in the navigation.  This is a useful feature when building accessible navigations.</p>
      <p>The site developer has complete ability to include, or exclude the use of this property in his navigation templates.  And it may not be required for your website.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Tab Index
      <p>This property is used to specify an integer index to aide in navigation to this content item in menus.  It is useful when creating accessible websites.</p>
      <p>The site developer has complete ability to include, or exclude the use of this property in his navigation templates.  And it may not be required for your website.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Target
      <p>This property is used to specify the &quot;target&quot; attribute in links to content items.  It allows creating navigations that can open content pages in different browser windows or tabs.</p>
      <p>The site developer has complete ability to include, or exclude the use of this property in his navigation templates.  And it may not be required for your website.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Url
      <p>This property is used to specify a primary URL to this content item. Users can specify a complete path or a simple flat string.  <em>(i.e.: path/to/mypage or keywordstuffedpageurl)</em>.  This property (if specified) is used when building navigations and other links to this content item, when &quot;pretty urls&quot; are enabled in the config.php.  If not specified, then the page alias and other settings control the primary route to the content item.</p>
      <p>It is important to note for SEO purposes that this is only a primary URL <em>(route)</em> to the content items.  Site visitors can still navigate to this content item via other means, i.e:  mysite.com/index.php?page=alias or mysite.com/random/random/alias or mysite.com/alias.  Sites that are concerned with search engine rankings should ensure that the &lt;link rel=&quot;canonical&quot;&gt; tag is properly configured in their page templates.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Active <em>(i.e: disabled)</em>
      <p>This property is used to indicate whether this content item can be navigated to at all.</p>
      <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Secure (HTTPS)
	<p>This property is used to indicate whether this content item should be accessed using the HTTPS protocol.  In a site configured properly for HTTPS, if this attribute is set for a content item, and an attempt is made to access this page via the insecure HTTP protocol, the user will be redirected to the same page using the more secure HTTPS protocol.  Additionally, if this flag is set then any links to this content item will specify the HTTPS protocol.</p>
        <p>It is important to know that content items without the secure flag set can still be navigated to using the HTTPS protocol, and no redirection will take place.  Therefore, for search engine ranking purposes the canonical link should be configured properly in each page template.</p>
    </li>

    <li>Cachable
	<p>This property specifies whether the compiled form of this content item can be cached on the server to reduce server load <em>(if smarty caching is enabled in global settings)</em> AND whether the browser can cache this page <em>(if browser caching is enabled in global settings)</em>.  For largely static websites enabling smarty caching and browser caching can significantly reduce server load and improve overall website performance.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Image
	<p>This property allows associating a previously uploaded image with your this content item.  Editors can select an image file from the uploads/images directory.  This image may be displayed on the generated HTML page (if applicable), or used when building the navigation.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Thumbnail
        <p>This property allows associating a previously created thumbnail image with this content item.  Editors can select a thumbnail file from the uploads/images directory.  This thumbnail may be displayed on the generated HTMLO page, or used when building the navigation.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Owner
        <p>The owner property is a dropdown field that indicates which admin user account has primary responsibility for the content item.  By default the owner of the content item is the user who first created it.  Users with significant permission can assign the ownership of an item to another user.</p>
    </li>

    <li>Additional Editors
        <p>This property specifies a list of other admin users or admin groups that are allowed to edit this content item.  It is implemented as a multi-select field.  Again, users with restricted permissions may not have the ability to adjust this property.</p>
    </li>

    <li>Design
        <p>The property allows associating a design with the content item.  A design is used to determine the stylesheets and other items that contribute to the appearance of content items.  The design is associated with different templates.  Changing the design property may result in the template property automatically changing.  By default the &quot;default design&quot; selected in the Design Manager is selected here.  Some restricted editors may not have the ability to adjust this property.</p>
    </li>

    <li>Template
        <p>The page template property is used to determine the overall layout of the content item (for those content items that generate HTML).  It also determines the use of meta tags and content blocks.  Changing this template will refresh the page and display the appropriate content properties (blocks) that are specified  in the newly selected template.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Searchable
        <p>This property controls whether the content properties on this content item can be indexed by the search module.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Disable WYSIWYG
        <p>This property will disable the WYSIWYG editor for all content blocks on this content item.  This overrides all settings on the content blocks, and any user setting.  This is useful for content items that contain pure logic in the content blocks, or strictly call other modules.  This prevents the logic or output from the modules from being effected by the styling injected by the WYSIWYG.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Page Metadata
        <p>The primary purpose of this property is for injecting meta properties into the &lt;head&gt; section of the rendered HTML page.  Typically it is useful for injecting a meta description tag.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>Page Data
        <p>This property is primarily used for inserting data, or logic into the smarty process for use by the page template.  It is an advanced field for usage in flexible layouts that change their behaviour dynamically.</p>
        <p>Users with restricted permissions may not have the ability to adjust or specify this property.</p>
    </li>

    <li>extra1, extra2, and extra3</li>
        <p>Additional properties for use in either displaying data, or influencing the behaviour of the page template.</p>
    </li>
  </ul>
EOT;

$lang['help_designmanager_help'] = <<<EOT
<h3>What is this?</h3>
<p>Design Manager is a core module <em>(a module distributed with CMSMS)</em> that unifies the functionality for managing the layout of CMSMS web pages.  It provides a complete solution for managing and editing any type of smarty template, and for organizing those templates.  Also you can create, edit, delete and manage stylesheets.  Templates, and stylesheets can then be organized into &quot;Designs&quot;.</p>

<h3>What is a &quot;Design&quot; ?</h3>
<p>A design is a container of sorts.  It contains a loose association of stylesheets and templates.  It allows managing all of the stylesheets and templates required to implement a look and feel.  Designs can be exported to a single file be shared, and imported from a single file.</p>
<p>Each content page that renders HTML is associated with a design to determine which stylesheets are to be used, and their order.  Additionally, each content page that renders HTML is associated with a template; though the template does not need to be associated with the specified design.</p>
<p>Templates and stylesheets do not need to be associated with a design, however, only the templates and stylesheets that are associated with a design are exported with the design.</p>

<h3>What are Template Types ?</h3>
  <p>Template types are one way of loosely organizing templates.  Some functionality may display lists of templates that are of a certain type to make selection easier.  For example, the edit content page displays a list of &quot;Page&quot; templates.</p>
  <p>The core creates a few standard template types on installation.  Third party modules will probably create more.  Template type definitions normally have two parts, the Originator <em>(the module or functionality that created them)</em>, and the type name itself.  i.e:  Core::Page indicates a page template type created by the core.  News::Summary is the name of the template type for news summary templates.  Although site developers cannot create new template types they can specify the type for a template when adding or editing a template.</p>
  <p>Most template types have a &quot;prototype&quot; template that is used to provide a skeleton layout creating a new template of that type.</p>
  <p>Some template types (such as the &quot;Core::Page&quot; template type support the notion of a &quot;default template&quot;.  The default template of a type is typically used by modules so that a template of a certain type can be used in the event that a specific template is not named.  Additionally, the default template of type &quot;Core::Page&quot; is used as the default template when creating a new content item of type &quot;Content&quot;</p>
  <p>The Design Manager Admin interface allows filtering templates by originator, or their type to easily find templates to edit or manage.</p>

  <h4>Generic Templates <em>(formerly Global Content Blocks)</em></h4>
    <p>A standard template type is called &quot;Core::generic&quot;.  This is a generic template that could be used for anything.  It replaces &quot;Global Content Blocks&quot; <em>(GCB)</em> from prior versions of CMSMS.</p>

<h3>What are &quot;Categories&quot; for ?</h3>
  <p>Categories are a method site developers can use to further organize templates.  Site developers can create, rename and delete categories and associate different templates with them.   When managing templates site developers can filter templates by their category.</p>

<h3>The Primary Interface</h3>
  <p>The Design Manager Admin panel is located in the CMSMS Admin navigation as &quot;Design Manager&quot; under the &quot;Layout&quot; section.</p>
  <p>The Design Manager Admin panel has numerous tabs.  Each tab serves a particular purpose, and may require special privileges.  Only a user with one of the required permissions <em>(or ownership/additional editor status on one or more templates)</em> will be able to see the design manager in the CMSMS Admin console.</p>
  <ul>
    <li>The Templates Tab
      <p>The templates tab provides all of the functionality to conveniently and easily create and manage templates.  It is visible to Admin users with the &quot;Modify Templates&quot; permission, or who are owners or additional editors of one or more templates.</p>
      <p>Some of the features of this tab include:</p>
      <ul>
        <li>A tabular format which displays summary information about each template, and provides convenient actions for working on templates individually, or en-masse.</li>
        <li>The ability to steal a locked template</li>
        <li>Advanced filtering</li>
        <li>Pagination</li>
      </ul>
    </li>

    <li>The Categories Tab
      <p>The categories tab is visible to all Admin users with the &quot;Modify Templates&quot; permission.  It provides the ability to add, edit, delete, rename and re-order categories.</p>
      <p>When adding or editing a category it is possible to provide a description of the use of the category for reference purposes.</p>
    </li>

    <li>The Template Types Tab
      <p>This tab is visible to all Admin users with the &quot;Modify Templates&quot; permission.  It provides the ability to edit information about the template type including the prototype template.  And to create a new template of each type.</p>
    </li>

    <li>The Stylesheets Tab
      <p>This tab is visible to all Admin users with the &quot;Manage Stylesheets&quot; permission.  It provides the ability to create, delete, edit, and manage stylesheets.<p>
    </li>

    <li>The Designs Tab
      <p>This admin panel tab is visible to all Admin users with the &quot;Manage Designs&quot; permission.  <em>(Note: users with that permission and no others may not have access to the full functionality of this tab)</em>.
      <p>This tab provides the ability to import, export, create, edit, and delete designs.</p>
    </li>
  </ul>
<h3>Managing Templates</h3>
 <p>The templates tab displays a list of templates matching the current filter <em>(if applied)</em> in a tabular format, with pagination.  Each row of the table represents a single template.  The columns of the table displays summary information about the template, and provides some ability to interact with it.</p>
 <p>A dropdown providing the ability to switch between pages of templates that match the current filter will appear if more than one page of templates match the current filter.</p>
 <p>An options menu exists providing the ability to adjust the current filter, or to create a new template <em>(depending upon permissions)</em>.  The filter dialogue allows filtering the displayed templates by a number of criteria, as well as changing the page limit, and sorting of displayed templates.</p>
   <h4>Table Columns:</h4>
   <ul>
     <li>Id:
       <p>This displays the unique numeric id for the template.  Clicking on the link in this column will bring up the edit template form.  Hovering over the link will display a tooltip with further information about the template.</p>
     </li>
     <li>Name:
       <p>This displays a unique textual name for the template.  Clicking on the link in this column will bring up the edit template form.  Hovering over the link will display a tooltip with further information about the template.</p>
     </li>
     <li>Type:
       <p>This displays the template type.  Hovering over the type name will display a tooltip with further information about the template type.</p>
     </li>
     <li>Design:
       <p>This column displays the design(s) that this template is associated with (if any).  If the template is associated with multiple designs a tooltip will display a list of the first few designs that this template is associated with.</p>
     </li>
     <li>Default:
       <p>This column displays an icon indicating if the template is the default for its type.</p>
     </li>
     <li>Actions:
       <p>Depending upon user privileges there will be one or more icons displayed in this column to perform various actions on, or with the template:</p>
       <ul>
          <li>Edit - Display a form to edit the contents and attributes of the template.</li>
          <li>Copy - Display a form to allow copying the selected template to a new name.  For convenience a default new name will be provided.</li>
          <li>Delete - Display a form to allow deleting the selected template.  Extra confirmation is required for this action as no checks are possible to see if the template is in use by any page, or recursively by any other template.</li>
       </ul>

     </li>
     <li>Multiselect:
	  <p>This column (depending upon permissions) will display a checkbox allowing the selection of multiple templates to perform actions on all of them simultaneously.</p>
     </li>
   </ul>
   <h4>Bulk Actions:</h4>
    <p>This is a dropdown with options <em>(currently only delete)</em> to perform on multiple templates at one time.  Use extreme caution when performing bulk actions as doing so could severely effect a working website.</p>
  <h4>Editing Templates</h4>
    <p>The edit template form is a complex form that allows management of all of the attributes of a template.  For convenience the form is divided into numerous tabs.</p>
    <p>This form supports the &quot;dirtyform&quot; functionality to reduce the chances of accidentally losing unsaved changes.  Users will be notified if attempting to navigate away from this page if the template has not been saved.</p>
    <p>This form locks the selected template so that other authorized editors will not have the ability to edit the template at the same time.  This prevents somebody else from accidentally overwriting changes of another editor.</p>
    <ul>
      <li>Name:
         <p>This text string uniquely identifies the template.  The system will generate an error when saving the template if the name is already used on another template.</p>
      </li>
      <li>Template Content:
        <p>This text area displays the actual smarty template.  If a syntax highlighter module is installed, and enabled, and the user has enabled it in his settings, then it will be enabled in this area to provide advanced editing capabilities.</p>
      </li>
      <li>Description:
        <p>This text area provides the ability to describe the purpose of the templates, and notes that may be useful to editors in the future.</p>
      </li>
      <li>Designs:
        <p>Depending upon permission levels, this tab will allow associating the template with zero or more designs.</p>
      </li>
      <li>Advanced:
        <p>This tab displays fields that allow specifying the template category, its type, and whether it is the default template for the type.  This tab is only available with the appropriate permissions.</p>
      </li>
      <li>Permissions:
        <p>If the user account is the owner of the template, or has the &quot;Modify Templates&quot; permissions this tab will allow changing the ownership of the template, and/or specifying additional editors.</p>
      </li>
      <li>Set All Pages:
        <p>Users with the &quot;Modify Templates&quot; permission will see a button which will allow setting all content pages to use this template.</p>
      </li>
    </ul>
<h3>Managing Categories</h3>
  <p>The &quot;Modify Templates&quot; permission is required to see this tab, and its associated actions.</p>
  <p>The categories tab is a simple interface that allows creating, editing, removing and re-ordering categories.  Categories can be re-ordered by dragging and dropping them into the desired order.</p>
  <p>Editing a category allows specifying a description for the category.  The description is useful for keeping a note as to the purpose of the category.</p>
<h3>Managing Template Types</h3>
  <p>The &quot;Modify Templates&quot; permission is required to see this tab, and its associated actions.</p>
  <p>Users with sufficient privilege can adjust the prototype template, and description for each template type.  The prototype template will be used as the default contents for the template when creating a new template of that type.</p>
<h3>Managing Stylesheets</h3>
    <p>The stylesheets tab is available to users with the &quot;Manage Stylesheets&quot; permission. It displays a paginated list of all stylesheets matching the current filter <em>(if applied)</em> in a tabular format.  Each row of the table represents a single stylesheet.  The columns of the table displays summary information about the stylesheet and provides some ability to interact with it.</p>
  <p>A dropdown providing the ability to switch between pages of stylesheets that match the current filter will appear if more than one page of stylesheets match the current filter.</p>
  <p>An options menu exists providing the ability to adjust the current filter, or to create a new stylesheet <em>(depending upon permissions).</em>  The filter dialogue allows filtering, sorting, and paginating the displayed stylesheets by a number of criteria.</p>
  <h4>Table Columns:</h4>
  <ul>
    <li>Id:
     <p>This displays a link containing unique numeric id for the stylesheet.   Clicking on this link will display the edit stylesheet form.  Hovering over the link will display a tooltip with further information about the stylesheet.</p>
    </li>
    <li>Name:
     <p>This displays the unique textual name for the stylesheet as a link.  Clicking on this link will display the edit stylesheet form.  Hovering over the link will display a tooltip with further information about the stylesheet.</p>
    </li>
    <li>Design:
      <p>This column displays the design(s) that this stylesheet is associated with (if any).  If the stylesheet is associated with multiple designs a tooltip will display a list of the first few designs.</p>
    </li>
    <li>Modified Date:
      <p>This column displays the date that the stylesheet was last modified.</p>
    </li>
    <li>Actions:
      <ul>
        <li>Edit - Clicking on this icon will display the edit stylesheet form.</li>
        <li>Delete - Clicking on this icon will display a form to allow deleting the stylesheet.  Extra confirmation is required for this action.</li>
      </ul>
    </li>
    <li>Multiselect:
      <p>This column displays a checkbox allowing the selection of multiple stylesheets to perform bulk actions on all of them simultaneously.</p>
    </li>
  </ul>
  <h4>Bulk Actions:</h4>
    <p>This mini form contains a dropdown with options <em>(currently only delete)</em> to perform on the selected stylesheets.  Use extreme caution when performing bulk actions as doing so could severely effect a working website.</p>
  <h4>Editing Stylesheets</h4>
    <p>The edit stylesheet form is a complex form that allows management of all of the attributes of a stylesheet.  for convenience the form is divided into numerous tabs.  It supports the &quot;dirtyform&quot; functionality to reduce the chances of accidentally losing unsaved changes, and supports locking to prevent other authorized editors from accidentally overwriting changes.</p>
    <p>Here are some of the attributes of a stylesheet that can be edited:</p>
    <ul>
      <li>Name:
        <p>This text string uniquely identifies the stylesheet.  The system will generate an error when saving the stylesheet if the name is already used by another stylesheet.</p>
      </li>
      <li>Stylesheet Content:
        <p>This text area displays the actual CSS code.  If a syntax highlighter module is installed, supports hi-lighting CSS code, is enabled, and the user has enabled it in his settings, then it will be enabled in this area to provide advanced editing capabilities.</p>
      </li>
      <li>Media Types <em style="color: red;">(deprecated)</em>:
        <p>This tab provides numerous checkbox allowing you to select media types to associate with the stylesheet.  It is preferred to use media queries instead, and this functionality may be removed at a later date.</p>
      </li>
      <li>Media Query:
        <p>This tab provides a text area where a media query can be associated with the stylesheet.</p>
      </li>
      <li>Description:
        <p>The text area in this tab provides the ability to describe the purpose of the stylesheets, and any notes that may be useful to editors in the future.</p>
      </li>
      <li>Designs:
        <p>This tab provides the ability to associate the stylesheet with one or more designs.  If any new design associations are detected this stylesheet will be placed at the end of the stylesheet list for that design.</p>
      </li>
    </ul>
<h3>Managing Designs</h3>
  <p>The designs tab is available to users with the &quot;Manage Designs&quot; permission.  It displays a list of all of the known designs in a tabular format.  Each row of the table represents a single design.  The columns of the table displays summary information about the design and provides some ability to interact with it.</p>
  <p>This tab does not provide filtering, pagination, or bulk actions as it is intended that the number of designs associated with a website should normally be kept small and manageable.</p>
  <p>An options menu exists providing the ability to create a new design, or to import a design from XML format.</p>
  <h4>Table Columns</h4>
  <ul>
    <li>Id:
      <p>This column displays a link containing the unique numeric id for the design.  Clicking on this link will display the edit design form.</p>
    </li>
    <li>Name:
      <p>This column displays a link containing the name for the design.  Clicking on this link will display the edit design form.</p>
    </li>
    <li>Default:
      <p>This column an icon represent whether or not this design is the &quot;default&quot; design.  The default design is selected first when creating a new content item of type &quot;Content Page&quot; and may be used for other purposes.  Only one design can be the default.</p>
    </li>
    <li>Actions:
      <p>This column displays various links and icons representing actions that can be performed with designs:</p>
      <ul>
        <li>Edit - Display a form to allow editing the design.</li>
        <li>Export - Export the design to an XML file that can be imported into other websites.</li>
        <li>Delete - Display a form that asks for confirmation about deleting the design.</li>
      </ul>
    </li>
  </ul>
  <h4>Editing Designs:</h4>
    <p>The edit design form is a complex form that allows management of all of the attributes of a design.  The form is divided into numerous tabs.  Unlike editing stylesheets and templates, this form does not support &quot;dirtyform&quot; or locking functionality.</p>
    <p>Some of the attributes of a design that can be edited are:</p>
    <ul>
      <li>Name:
      </li>
      <li>Templates:
        <p>This tab allows selecting different templates to associate with the design. You can drag and drop templates between the &quot;Available Templates&quot; list and the &quot;Attached Templates&quot; list and to order templates within the attached list. At this time, ordering of templates within the attached template list is not significant.</p>
      </li>
      <li>Stylesheets:
	  <p>This tab allows selecting different stylesheets to associate with the design. You can drag and drop stylesheets between the &quot;Available Stylesheets&quot; list and the &quot;Attached Stylesheets&quot; list and to order stylesheets within the attached list.  The order of stylesheets within the attached list determines the order that they will be included in the rendered page content for content items of type &quot;Content Page&quot;.</p>
      </li>
      <li>Description:
        <p>This tab provides a free form text area where a description of the design, and additional notes can be entered.  the description is also useful to other users when deciding to share a design.</p>
      </li>
    </ul>
  <h4>Importing Designs</h4>
     <p>The Design Manager module is capable of importing XML themes that were exported from CMSMS Design Manager, or from the older CMSMS theme manager.  It expands the uploaded XML file, and extracts templates, stylesheets, and other useful information from the file.  It also performs some minor transformation on the extracted data to try to adjust for overlapping names, etc.</p>
     <p>The import process is divided into a few steps:</p>
     <ul>
      <li>Step 1: Upload the file:
        <p>This step manages uploading the user selected XML file and validating its contents.  This step is vulnerable to PHP limits for file size, memory limits, and time limits for form processing.  You may need to increase those limits on overly restricted sites when uploading larger theme files.</p>
        <p>Once the XML file has passed the validation process, it is copied to a temporary location for processing in step 2.</p>
      </li>
      <li>Step 2: Verification:
        <p>The second step is for verifying and previewing the new design that will be created from the XML file.  From here you can display, and edit various aspects of the design or theme.</p>
     </ul>
  <h4>Deleting Designs</h4>
<h3>Using Templates</h3>
<h3>Options and Preferences</h3>
<h3>Upgrade Notes</h3>
EOT;

$lang['help_myaccount_admincallout'] = 'If enabled administrative bookmarks <em>(shortcuts)</em> will be enabled allowing you to manage a list of frequently used actions in the Admin console.';
$lang['help_myaccount_admintheme'] = 'Select an administration theme to use.  Different administration themes have different menu layouts, work better for mobile displays, and have various additional features.';
$lang['help_myaccount_ce_navdisplay'] = 'Select which content field should be displayed in content lists.  Options include the page title, or menu text.  If &quot;None&quot; is selected, then the site preference will be used';
$lang['help_myaccount_dateformat'] = 'Specify a date format string to use when dates are displayed.  This string uses <a href="http://php.net/manual/en/function.strftime.php" class="external" target="_blank">strftime</a> format.  <strong>Note:</strong> some third party add-ons may not obey this setting.</strong>';
$lang['help_myaccount_dfltparent'] = 'Specify the default parent page for creating a new content page.  The use of this setting also depends on your content editing permissions.<br/><br/>Drill down to the selected default parent page by selecting the topmost parent, and successive child pages from the provided dropdowns.<br/><br/>The text field on the right will always indicate which page is currently selected.';
$lang['help_myaccount_email'] = 'Specify an email address.  This is used for the lost password functionality, and for any notification emails sent by the system (or add-on modules).';
$lang['help_myaccount_enablenotifications'] = 'If enabled, the system will display various notifications about things that need to be taken care of in the navigation';
$lang['help_myaccount_firstname'] = 'Optionally specify your given name.  This may be used in the Admin theme, or to personally address emails to you';
$lang['help_myaccount_hidehelp'] = 'If enabled the system will hide module help links from the Admin console.  In most circumstances the help provided with modules is targeted towards site developers and may not be useful to content editors.';
$lang['help_myaccount_homepage'] = 'You may select a page to automatically direct to when you login to the CMSMS Admin console.  This may be useful when you primarily use one function.';
$lang['help_myaccount_ignoremodules'] = 'If Admin notifications are enabled you can select to ignore notifications from some modules';
$lang['help_myaccount_indent'] = 'This option will indent the content list view to illustrate the parent and child page relationship';
$lang['help_myaccount_language'] = 'Select the language to display for the Admin interface.  The list of available languages may vary on each CMSMS install';
$lang['help_myaccount_lastname'] = 'Optionally specify your surname.  This may be used in the Admin theme, or to personally address emails to you';
$lang['help_myaccount_password'] = 'Please enter a unique, and secure password for this website.  The password should be more more than six characters long, and should use a combination of upper case, lower case, non alphanumeric, and digits.  Please leave this field blank if you do no wish to change your password.';
$lang['help_myaccount_passwordagain'] = 'To reduce errors, please enter your password again.  Leave this field empty if you do not wish to change your password.';
$lang['help_myaccount_syntax'] = 'Select which syntax highlighting module to use when editing HTML, or smarty code.  The list of available modules may change depending on what your site administrator has configured';
$lang['help_myaccount_username'] = 'Your username is your unique name for the CMSMS Admin panel.  Please use only alphanumeric characters and the underscore';
$lang['help_myaccount_wysiwyg'] = 'Select which WYSIWYG <em>(What You See Is What You Get)</em> module to use when editing HTML content.  You may also select &quot;None&quot; if you are comfortable with HTML.  The list of available WYSIWYG editors may change depending on what the site administrator has configured.';

/* S */
$lang['settings_adminlog_lifetime'] = 'This setting indicates the maximum amount of time that entries in the Admin log should be retained.';
$lang['settings_autoclearcache'] = 'This option allows you to specify the maximum age <em>(in days)</em> before files in the cache directory will be deleted.<br/><br/>This option is useful to ensure that cached files are regenerated periodically, and that the file system does not become polluted with old and unnecessary files.  An ideal value for this field is 14 or 30 days.<br /><br /><strong>Note:</strong> Cached files are cleared at most once per day.';
$lang['settings_autocreate_flaturls'] = 'If SEF/pretty URLs are enabled, and the option to auto-create URLs is enabled, this option indicates hat those auto-created URLS should be flat <em>(i.e: identical to the page alias)</em>.  <strong>Note:</strong> The two values do not need to remain identical, the URL value can be changed to be different than the page alias in subsequent page edits';
$lang['settings_autocreate_url'] = 'When editing content pages, should SEF/pretty URLS be auto-created?  Auto creating URLS will have no effect if pretty urls are not enabled in the CMSMS config.php file.';
$lang['settings_badtypes'] = 'Select which content types to remove from the content type dropdown when editing or adding content.  This feature is useful if you do not want editors to be able to create certain types of content.  Use CTRL+Click to select, unselect items.  Having no selected items will indicate that all content types are allowed. <em>(applies to all users)</em>';
$lang['settings_basicattribs2'] = 'This field allows you to specify which content properties that users without the &quot;Manage All Content&quot; permission are allowed to edit.<br />This feature is useful when you have content editors with restricted permission and want to permit editing of additional content properties.';
$lang['settings_browsercache'] = 'Applicable only to cachable pages, this setting indicates that browsers should be allowed to cache the pages for an amount of time.  If enabled repeat visitors to your site may not immediately see changes to the content of the pages, however enabling this option can seriously improve the performance of your website.';
$lang['settings_browsercache_expiry'] = 'Specify the amount of time (in minutes) that browsers should cache pages for.  Setting this value to 0 disables the functionality.  In most circumstances you should specify a value greater than 30';
$lang['settings_checkversion'] = 'If enabled, the system will perform a daily check for a new release of CMSMS';
$lang['settings_contentimage_path'] = 'This setting is used when a page template contains the {content_image} tag.  The directory specified here is used to provide a selection of images to associate with the tag.<br /><br />Relative to the uploads path, specify a directory name that contains the paths containing files for the {content_image} tag.  This value is used as a default for the dir parameter';
$lang['settings_cssnameisblockname'] = 'If enabled, the content block name <em>(id)</em> will be used as a default value for the cssname parameter for each content block.<br/><br/>This is useful for WYSIWYG editors.  The stylesheet (block name) can be loaded by the WYSIWYG editor and provide an appearance that is closer to that of the front web page.<br/><br/><strong>Note:</strong> WYSIWYG Editors may not read information from the supplied stylesheets (if they exist) depending upon their settings and capabilities.';
$lang['settings_disablesafemodewarn'] = 'This option will disable a warning notice if CMSMS detects that <a href="http://php.net/manual/en/features.safe-mode.php" class="external" target="_blank">PHP Safe Mode</a> has been detected.<br /><br /><strong>Note:</strong> Safe mode has been deprecated as of PHP 5.3.0 and removed for PHP 5.4.0.  CMSMS Does not support operation under safe mode, and our support team will not render any technical assistance for installs where safe mode is active';
$lang['settings_enablenotifications'] = 'This option will enable notifications being shown at the top of the page in each Admin request.  This is useful for important notifications about the system that may require user action.  It is possible for each Admin user to turn off notifications in their preferences.';
$lang['settings_enablesitedown'] = 'This option allow you to toggle the website as "down for maintenance" for website visitor';
$lang['settings_enablewysiwyg'] = 'Enable WYSIWYG editor in the text area below';
$lang['settings_imagefield_path'] = 'This setting is used when editing content.  The directory specified here is used to provide a list of images from which to associate an image with the content page.<br/></br/>Relative to the image uploads path, specify a directory name that contains the paths containing files for the image field';
$lang['settings_lock_timeout'] = 'Enter a default value (in minutes) for locks to timeout.  This is used if a piece of functionality does not provide a custom lock timeout value';
$lang['settings_mailprefs_from'] = 'This option controls the <em>default<em> address that CMSMS will use to send email messages.  This cannot just be any email address.  It must match the domain that CMSMS is providing.  Specifying a personal email address from a different domain is known as &quot;<a href="https://en.wikipedia.org/wiki/Open_mail_relay" class="external" target="_blank">relaying</a>&quot; and will most probably result in emails not being sent, or not being accepted by the recipient email server.  A typical good example for this field is noreply@mydomain.com';
$lang['settings_mailprefs_fromuser'] = 'Here you can specify a name to be associated with the email address specified above.  This name can be anything but should reasonably correspond to the email address.  i.e: &quot;Do Not Reply&quot;';
$lang['settings_mailprefs_mailer'] = 'This choice controls how CMSMS will send mail.  Using PHPs mail function, sendmail, or by communicating directly with an SMTP server.<br/><br/>The &quot;mail&quot; option should work on most shared hosts, however it almost certainly will not work on most self hosted windows installations.<br/><br/>The &quot;sendmail&quot; option should work on most properly configured self hosted Linux servers.  However it may not work on shared hosts.<br/><br/>The SMTP Option requires configuration information from your host.';
$lang['settings_mailprefs_sendmail'] = 'If using the &quot;sendmail&quot; mailer method, you must specify the complete path to the sendmail binary program.  A typical value for this field is &quot;/usr/sbin/sendmail&quot;.  This option is typically not used on windows hosts.<br/><br/><strong>Note:</strong> If using this option your host must allow the popen and pclose PHP functions which are often disabled on shared hosts.';
$lang['settings_mailprefs_smtpauth'] = 'When using the SMTP mailer, this option indicates that the SMTP server requires authentication to send emails.  You then must specify <em>(at a minimum)</em> a username, and password.  Your host should indicate whether SMTP authentication is required, and if so provide you with a username and password, and optionally an encryption method.<br/><br/><strong>Note:</strong> SMTP authentication is required if your domain is using Google apps for email.';
$lang['settings_mailprefs_smtp_smtpautotls'] = 'When using the SMTP mailer, this option indicates whether to enable TLS encryption automatically, if a server supports it. This setting is valid even if the <em>Encryption Method</em> is not set to <em>TLS</em>.It also requires that the server\'s certificates are valid.<br> Some rare hosting providers impose very strict (to the point of becoming almost useless) constraints on sending an email. They block outbound SMTP to ports 25, 465 and 587 to all servers except their own. If you find your script works on your local machine, but not when you upload it to a server, this may be what is happening. The solution is poorly documented in most cases: sending through their servers, while disabling all security features, such as username, password and auto TLS encryption, which is <strong>on</strong> by default.';
$lang['settings_mailprefs_smtphost'] = 'When using the SMTP mailer this option specifies the hostname <em>(or IP address)</em> of the SMTP server to use when sending email.  You may need to contact your host for the proper value.';
$lang['settings_mailprefs_smtppassword'] = 'This is the password for connecting to the SMTP server if SMTP authentication is enabled.';
$lang['settings_mailprefs_smtpport'] = 'When using the SMTP mailer this option specifies the integer port number for the SMTP server.  In most cases this value is 25, though you may need to contact your host for the proper value.';
$lang['settings_mailprefs_smtpsecure'] = 'This option, when using SMTP authentication specifies an encryption mechanism to use when communicating with the SMTP server.  Your host should provide this information if SMTP authentication is required.';
$lang['settings_mailprefs_smtptimeout'] = 'When using the SMTP mailer, this option specifies the number of seconds before an attempted connection to the SMTP server will fail.  A typical value for this setting is 60.<br/><br/><strong>Note:</strong> If you need a longer value here it probably indicates an underlying DNS, routing or firewall problem, and you may need to contact your host.';
$lang['settings_mailprefs_smtpusername'] = 'This is the username for connecting to the SMTP server if SMTP authentication is enabled.';
$lang['settings_mailtest_testaddress'] = 'Specify a valid email address that will receive the test email';
$lang['settings_mandatory_urls'] = 'If SEF/pretty URLs are enabled, this option indicates whether page URLS are a required field in the content editor.';
$lang['settings_nosefurl'] = 'To configure <strong>S</strong>each <strong>E</strong>ngine <strong>F</strong>riendly <em>(pretty)</em> URLs you need to edit a few lines in your config.php file and possibly to edit a .htaccess file or your web servers configuration.   You can read more about configuring pretty URLS <a href="https://docs.cmsmadesimple.org/configuration/pretty-url" class="external" target="blank"><u>here</u></a> &raquo;';
$lang['settings_pseudocron_granularity'] = 'This setting indicates how often the system will attempt to handle regularly scheduled tasks.';
$lang['settings_searchmodule'] = 'Select the module that should be used to index words for searching, and will provide the site search capabilities';
$lang['settings_sitedownexcludeadmins'] = 'Do show the website to Admin users logged in to the CMSMS Admin console';
$lang['settings_sitedownexcludes'] = 'Do show the website to these IP addresses';
$lang['settings_sitedownmessage'] = 'The message shown to your website visitors when the site is down for maintenance';
$lang['settings_smartycaching'] = 'When enabled, the output from various plugins will be cached to increase performance. Additionally, most portions of compiled templates will be cached. This only applies to output on content pages marked as cachable, and only for non-Admin users.  Note, this functionality may interfere with the behaviour of some modules or plugins, or plugins that use non-inline forms.<br/><br/><strong>Note:</strong> When smarty caching is enabled, global content blocks <em>(GCBs)</em> are always cached by smarty, and user defined tags <em>(UDTs)</em> are never cached.  Additionally, content blocks are never cached.';
$lang['settings_smartycompilecheck'] = 'If disabled, smarty will not check the modification dates of templates to see if they have been modified.  This can significantly improve performance.  However performing any template change (or even some content changes) may require a cache clearing';
$lang['settings_thumbfield_path'] = 'This setting is used when editing content.  The directory specified here is used to provide a list of images from which to associate a thumbnail with the content page.<br/><br/>Relative to the image uploads path, specify a directory name that contains the paths containing files for the image field.  Usually this will be the same as the path above.';
$lang['settings_umask'] = 'The &quot;umask&quot; is an octal value that is used to specify the default permission for newly created files (this is used for files in the cache directory, and uploaded files.  For more information see the appropriate <a href="http://en.wikipedia.org/wiki/Umask" class="external" target="_blank">Wikipedia article.</a>';

$lang['siteprefs_lockrefresh'] = 'This field specifies the minimum frequency (in minutes) the Ajax based locking mechanism should &quot;touch&quot; a lock.  An ideal value for this field is 5.';
$lang['siteprefs_locktimeout'] = 'This field specifies the number of minutes of inactivity before a lock times out.  After a lock times out other users may steal the lock.  In order for a lock to not time-out it must be &quot;touched&quot; before its expiry time.  This resets the expiry time of the lock.  Under most circumstances a 60 minute lock should be suitable.';
$lang['siteprefs_sitename'] = 'This is a human readable name for your website, i.e: the business, club, or organization name';
$lang['siteprefs_frontendlang'] = 'The default language that your website displays on the frontend.  This can be changed on a per-page basis using different smarty tags. i.e: <code>{cms_set_language}</code>';
$lang['siteprefs_frontendwysiwyg'] = 'When WYSIWYG editors are provided on frontend forms, what WYSIWYG module should be used?  Or none.';
$lang['siteprefs_nogcbwysiwyg'] = 'This option will disable the WYSIWYG editor on all global content blocks independent of user settings, or of the individual global content blocks';
$lang['siteprefs_globalmetadata'] = 'This text area provides the ability to enter meta information that is relevant to all content pages.  This is an ideal location for meta tags such as Generator, and Author, etc.';
$lang['siteprefs_logintheme'] = 'Select the Admin theme (from installed Admin themes) that will be used to generate the administrator login form, and as the default login theme for new Admin user accounts.  Admin users will be able to select their preferred Admin theme from within the user preferences panel.';
$lang['siteprefs_backendwysiwyg'] = 'Select the WYSIWYG editor for newly created Admin user accounts.  Admin users will be able to select their preferred WYSIWYG editor from within the user preference panel.';
$lang['siteprefs_dateformat'] = '<p>Specify the date format string in <a href="https://www.php.net/manual/en/function.strftime.php" class="external" rel="noopener noreferrer" target="_blank"><u>PHP strftime</u></a> format that will be used <em>(by default)</em> to display dates and times in your website.</p><p>Admin users can adjust these settings in the user preferences panel.</p><p><strong>Note:</strong> Some modules may choose to display times and dates differently</p>';
$lang['siteprefs_thumbwidth'] = 'Specify a width <em>(in pixels)</em> to be used by default when generating thumbnails from uploaded image files.  Thumbnails are typically displayed in the Admin panel in the FileManager module or when selecting an image to insert into page content.  However, some modules may use the thumbnails on the website frontend.<br/><br/><strong>Note:</strong> Some modules may have additional preferences for how to generate thumbnails, and ignore this setting.';
$lang['siteprefs_thumbheight'] = 'Specify a height <em>(in pixels)</em> to be used by default when generating thumbnails from uploaded image files.  Thumbnails are typically displayed in the Admin panel in the FileManager module or when selecting an image to insert into page content.  However, some modules may use the thumbnails on the website frontend.<br/><br/><strong>Note:</strong> Some modules may have additional preferences for how to generate thumbnails, and ignore this setting.';

?>