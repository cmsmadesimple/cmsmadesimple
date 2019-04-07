<h3>What does this do:</h3>
<p>This module provides the ability for website editors to quickly, and easily keep their visitors and users up to date with the events of the organization.  It allows for summary views, detail views, and navigation by category.  This module is also an ideal example module for development of modules within CMSMS.</p>

<h3>Features:</h3>
<ul>
   <li>Nested categories</li>
   <li>Categories can have an associated image</li>
   <li>Articles can belong to a single category</li>
   <li>The article detail page (and therefore detail template) can be determined by its category.</li>
   <li>Numerous roles and permissions for managing time related content</li>
   <li>Articles can have an optional summary field, and optional start and end dates.</li>
   <li>Articles allow attachment of numerous different types of tertiary field definitions.</li>
   <li>Extensive filtering and sorting options</li>
   <li>Extensible field types - third party modules can create field types for PressRoom</li>
   <li>The ability to relate individual articles, and display them on the website frontend.</li>
   <li>Uses file based templates</li>
   <li>Preferences are stored in the CMSMS config.php file</li>
   <li>Search module integration</li>
   <li>Adminsearch integration</li>
   <li>CMSMS Alerts</li>
   <li>More...</li>
</ul>

<h3>About Dates and Times</h3>
<p>This module keeps three different dates with each article:</p>
<ul>
     <li>The post date</li>
     <li>The display start date/time</li>
     <li>The display end date/time</li>
</ul>
<p>These dates are essentially mutually exclusive.  The start and end fields are used to control the visibility of the article, along with the status field.  The rules for displaying articles are as follows:</p>
<ul>
      <li>If the start time is specified in an article
          <p>The article cannot be displayed until that time.</p>
      </li>
      <li>If the end time is specified in an article
          <p>The article cannot be displayed after that time.</p>
      </li>
</ul>
<p>If either value is set to January 1, 1970 that is the same as turning it &quot;off&quot;</p>
<p>The end time if specified, cannot be earlier than the start time, if specified.</p>
<p>The post date is primarily only used for sorting in summary views, and for display purposes. It is NOT used to indicate whether an article can or cannot be displayed.</p>
<h3>Permissions:</h3>
<p>On installation, this module creates four new permissons:</p>
<ul>
    <li>Manage PressRoom Articles
    	<p>Users with this permission have the ability to manage any and all PressRoom articles, as well as the ability to manage categories.</p>
    </li>
    <li>Create and edit my PressRoom articles
        <p>Users with this permission have the ability to add PressRoom articles, and can only edit PressRoom articles that they created.</p>
	<p>Users with this permission cannot by default set their articles as published <em>(see the config entries section)</em>, but only as 'Needs Approval'</p>
    </li>
    <li>Delete my PressRoom articles
        <p>Users with this permission can delete PressRoom articles that they created.  If the user only has the "Create and edit my PressRoom articles" permission, then they cannot delete any articles.</p>
    </li>
    <li>Approve PressRoom articles for display
        <p>Users with only this permission have the following functionality:</p>
	<ul>
	    <li>View any article, without editing it.</li>
	    <li>Change the status of any article from "Needs approval" to "Published"</li>
	    <li>Change the status of any article from "Published" to "Needs Approval"</li>
	</ul>
    </li>
</ul>
<p>Additionally, users with the "Modify Site Preferences" permission can manage field definitions for the module.</p>

<h3>Pretty URLS, URL Slugs, Category summary views, and Detail Views:</h3>
<p>When pretty URLS are enabled, there will be a single pretty URL for each detail view.  And there is flexibility in specifying which content page (and therefore page template, and detail template) will be used for displaying that detail view.</p>
<p>Each article can have a URL slug.  Similar to editing a content Like when editing a content page.  The URL slug, in combination with the root url will be the single pretty URL to the detail view of tthe article.</p>
<p>If a URL slug is not specified for an article, then the Pretty URL to a detail view for an article will take the form of &lt;urlprefix&gt;/&lt;articleid&gt;/&lt;date&gt;-&lt;title&gt;.</p>
<p>To determine a page id to utilize for displaying the article, the following mechanism is used:</p>
<ol>
   <li>First an attempt at finding a detailpage is made by looking at the category that the requested article is linked to, and by looking at parent categories until a detailpage is found, or there are no further parents.</li>
   <li>Next, if no detailpage can be found, and the "pressroom_detailpage" config entry is set, then that page id or alias will be used as a detailpage.</li>
   <li>Lastly, the default content page will be used.</li>
</ol>
<p>If any of the found detailpages cannot be loaded, then even if the article is valud, a 404 error will be thrown.</p>
<p>To minimize the problem with search engines indexing your content it is important that the canonical meta tag is set specifically for each detail view.  The default CMSMS page templates, and PressRoom detail templates help with this.</p>
<p>This module also supports pretty URLS for a summamry view of a specific category.  These urls take the form of &lt;urlprefix&gt;/&lt;bycategoryprefix&gt;/&lt;categoryid&gt;/&lt;category-name&gt;.</p>
<p>For a URL to a summary view of a specific category, the above mechanism is used to determine the content page.</p>

<h3>Frontend Actions:</h3>
<ul>
   <li>default
       <p>This action is used to display brief information about articles matching the specified criterium.  See the section about parameters below for the full list of possible parameters for this view.</p>
       <ul>
           <li>Calling a summary view<br/>
	       <pre><code>{PressRoom [summarytemplate=string] [limit=int]...}</code></pre>
	   </li>
	   <li>Creating a link to a summary view<br/>
	       <pre><code>{cms_action_url module=PressRoom action=default [summarytemplate=string] [limit=int] ...}</code></pre>
	   </li>
       </ul>
   </li>

   <li>detail
       <p>This action is used to display a single article in detail view.  A valid article id must be provided (or -1).  If the article id specified is invalid, or the article cannot be found or loaded for some reason, then a 404 error will be generated.</p>
       <ul>
           <li>Creating a detail view<br/>
	       <pre><code>{PressRoom action=detail article=int [detailtemplate=string]}</code></pre>
           </li>
           <li>Creating a link to a detail view<br/>
	       <pre><code>{cms_action_url module=PressRoom action=detail article=int [detailtemplate=string] ...}</code></pre>
	       <p><strong>Note:</strong> - See above for the discussion about pretty URLS and URL slugs.</p>
	   </li>
       </ul>
   </li>

   <li>showcategories
       <p>This action is used to display a nested view of PressRoom categories with links to articles within that category. Optional parameters allow specifying a starting point in the category tree, and a maximum number of levels.</p>
       <ul>
	   <li>Creating a showcategories view<br/>
	       <pre><code>{PressRoom action=showcategories [categorytemplate=string] [alias=string|from=int] [maxdepth=int]}</code></pre>
	   </li>
	   <li>Creating a link to a showcategories view<br/>
	       <pre><code>{cms_action_url module=PressRoom action=showcategories [categorytemplate=string] [alias=string|from=int] [maxdepth=int] ...}</code></pre>
	   </li>
       </ul>
   </li>

   <li>admin_user
       <p>This action is used to output HTML to display author information based on the author's admin uid.  The <code>admin_user.tpl</code> template will, by default display the users full name if it is specified in his profile, otherwise it will display the username.</p>
       <ul>
          <li>Creating an admin_user view<br/>
	      <pre><code>{PressRoom action=admin_user uid=int}</code></pre>
	      <p>Example:</p>
	      <pre><code>{PressRoom action=admin_user uid=$article->author_id}</code></pre>
	  </li>
       </ul>
   </li>

</ul>

<h3>File Templates:</h3>
<p>This module uses file based templates by default <em>(templates from other locations such as DesignManager can be specified)</em>.  This allows developers to use their favorite text editor such as Emacs or Sublime, and allows templates to be stored along with other application specific files in a version-control-system.</p>
<p>Master copies of the templates are stored in the PressRoom module's templates directory.  These files should never be modified, as they will be overwritten on upgrade.  Instead, to override templates, or to create new templates for the module, the files should be copied to <code>{cms_root}/assets/module_custom/PressRoom/templates</code>.</p>
<p>The following shell commands (assuming your current working directory is the CMSMS installation root dir will create the appropriate destination directory, and copy the default summary template twice.   Once to module_custom/PressRoom/templates/default.tpl to allow overriding the default template,  and again to module_custom/PressRoom/templates/mysummary.tpl to allow creating an alternate summary template.</p>
<pre><code>mkdir -p assets/module_custom/PressRoom/templates
cp lib/modules/PressRoom/templates/default.tpl assets/module_custom/PressRoom/templates/default.tpl
cp lib/modules/PressRoom/templates/default.tpl assets/module_custom/PressRoom/templates/mysummary.tpl</code></pre>
<p>Now, to utilize the "mysummary.tpl" file on a summary view you can call PressRoom like this <code>{PressRoom summarytemplate='mysummary.tpl'}</code></p>
<p>The following default, frontend templates exist in the PressRoom module:</p>
<ul>
    <li>default.tpl - The default template for a summary view.</li>
    <li>detail.tpl - The default template for a detail view.</li>
    <li>showcategories.tpl - The default template for a categories view.</li>
    <li>admin_user.tpl - The default template for an admin_user view.</li>
</ul>
<p><strong>Reminder:</strong> - You should never modify the files that are in the PressRoom module's templates directory.  Instead, modified templates should be placed in <code>module_custom/PressRoom/templates</code>.</p>
<p><strong>Note:</strong> - File based templates MUST end with the .tpl extension, or CMSMS may interpret the parameter on a module call as a database template.</p>

<h3>Smarty Plugins:</h3>
<p>The PressRoom module registers the following smarty plugins:</p>
<ul>
    <li><code>{pressroom_category catid=number|alias=string [long=bool] [assign=string]}</code>
        <p><strong>Description:</strong> Return a category given it's id or alias.</p>
	<p><strong>Returns:</strong> object</p>
	<p><strong>Parameters:</strong>
	<ul>
	    <li>catid=## - The category id to load.  Either this or alias must be provided.</li>
	    <li>alias=string - The category alias to load.  Either this or catid must be provided.</li>
	    <li>assign=string - Optionally assign the output from this plugin to the named smarty variable.</li>
	</ul>
    </li>
    <li><code>{pressroom_category_name catid=number|alias=string [long=bool] [assign=string]}</code>
        <p><strong>Description:</strong> Return a category name given it's id or alias.</p>
	<p><strong>Returns:</strong> string</p>
	<p><strong>Parameters:</strong>
	<ul>
	    <li>catid=## - The category id to load.  Either this or alias must be provided.</li>
	    <li>alias=string - The category alias to load.  Either this or catid must be provided.</li>
	    <li>long=bool - Optionally return the category long name (which includes the full path to the root).</li>
	    <li>assign=string - Optionally assign the output from this plugin to the named smarty variable.</li>
	</ul>
    </li>
    <li><code>{pressroom_nextarticle id=number [assign=string]}</code>
        <p><strong>Description:</strong> Return the next available, published article given an input article id.  This plugin is useful for displaying next and previous links.</p>
	<p><strong>Returns:</strong> object</p>
	<p><strong>Parameters:</strong>
	<ul>
	    <li>id=## - The current article id.</li>
	    <li>assign=string - Optionally assign the output from this plugin to the named smarty variable.</li>
	</ul>
	<p><strong>Note:</strong> Behavior of this plugin is unpredictable when there are multiple articles with the same date.</p>
	<p><strong>Note:</strong> Another way of achieving this behavior with more control is to use a call to a summary action with a different template, and specific criteria that filters out the current article.</p>
    </li>
    <li><code>{pressroom_prevarticle id=number [assign=string]}</code>
        <p><strong>Description:</strong> Return the previous available, published article given an input article id.  This plugin is useful for displaying next and previous links.</p>
	<p><strong>Returns:</strong> object</p>
	<p><strong>Parameters:</strong>
	<ul>
	    <li>id=## - The current article id.</li>
	    <li>assign=string - Optionally assign the output from this plugin to the named smarty variable.</li>
	</ul>
	<p><strong>Note:</strong> Behavior of this plugin is unpredictable when there are multiple articles with the same date.</p>
	<p><strong>Note:</strong> Another way of achieving this behavior with more control is to use a call to a summary action with a different template, and specific criteria that filters out the current article.</p>
    </li>
</ul>

<h3>Smarty Tools</h3>
<p>This module provides the $PressRoomTools object to Smarty to aide in templating. It has several methods:</p>
<ul>
    <li><code>$PressRoomTools->get_category(mixed $in)</code>
       <p><strong>Description:</strong> Given a category id or alias, get the category object.</p>
       <p><strong>Returns:</strong> Either the category object, or null.</p>
       <p><strong>Example:</strong>
       <pre><code>{$cat = $PressRoomTools->get_category($article->category_id)}</code></pre>
    </li>
    <li><code>PressRoomTools->category_childof(mixed $a, mixed $b)</code>
       <p><strong>Description:</strong> Given a category id or alias, get the category object.</p>
       <p><strong>Returns:</strong> boolean.  TRUE if $a is a child of $b.  FALSE otherwise.</p>
       <p><strong>Example:</strong>
       <pre><code>{if $PressRoomTools->category_childof($article->category_id,'events')}...{/if}</code></pre>
    </li>
</ul>

<h3>Config variables:</h3>
<p>The PressRoom module can read from the following variables in the CMSMS config file:</p>
<ul>
    <li><code>pressroom_dflt_status</code> - string
       <p>This entry determines the default status for ALL new articles.  Possible values are: draft, published, disabled and needsapproval.</p>
    </li>
    <li><code>pressroom_dflt_useendtime</code> - bool
       <p>This entry determines whether, by default, new articles will use the end time field.</p>
    </li>
    <li><code>pressroom_dflt_searchable</code> - bool
       <p>This entry determines whether, by default, new articles will be indexed in the search endgine
    </li>
    <li><code>pressroom_summary_enabled</code> - bool
       <p>This entry determines whether or not a summary field should be displayed when adding or editing an article.</p>
    </li>
    <li><code>pressroom_summary_usewysiwyg</code> - bool
       <p>This entry determines whether the summary field (if enabled) should allow a WYSIWYG text field when adding or editing an article.</p>
    </li>
    <li><code>pressroom_category_required</code> - bool
       <p>This entry determines whether a category is required for new articles.</p>
    </li>
    <li><code>pressroom_urlslug_required</code> - bool
       <p>This entry determines whether URL slugs are required when adding or editing an article.</p>
    </li>
    <li><code>pressroom_own_editpublished</code> - bool
       <p>This entry determines whether or not users with the "Create and edit my PressRoom articles" permission can edit articles that are published.</p>
    </li>
    <li><code>pressroom_own_setpublished</code> - bool
       <p>This entry determines whether or not users with the "Create and edit my PressRoom articles" permission can set their articles as published.</p>
    </li>
    <li><code>pressroom_expired_searchable</code> - bool
       <p>This entry determines whether expired articles (articles that have an end date, and that end date is in the past) can be searched by a search module.</p>
    </li>
    <li><code>pressroom_detail_show_expired</code> - bool
       <p>This entry determines whether expired articles (articles that have an end date, and that end date is in the past) can be displayed in a detail view.)
    </li>
    <li><code>pressroom_alert_on_draft</code> - bool
       <p>Determines whether CMSMS admin alerts are generated for draft articles.</p>
    </li>
    <li><code>pressroom_alert_needsapproval</code> - bool
       <p>Determines whether CMSMS admin alerts are generated for articles that have the "Needs approval" status.</p>
    </li>
    <li><code>pressroom_detailpage</code> - string|int
       <p>Used when url-rewriting is enabled, and a detailpage cannot be found by looking through an article's category hierarcy.  This entry specifies the page alias or id of the page to use for rendering the article.  If not specified, the default content page will be used.</p>
       <p><strong>Note:</strong> When url-rewriting is disabled there is the possibility that the same article may have multiple URLS.</code>
       <p><strong>Note:</strong> Changing this entry will NOT change the canonical URL for articles, but you should clear the cache each time you change it.</p>
    </li>
    <li><code>pressroom_default_pretty_category_url</code> - bool
       <p>If enabled, the system will generate pretty urls for a summary view for a specific category. The default value is true</p>
    </li>
    <li><code>pressroom_default_bycategory_withchildren</code> - bool
       <p>Determines whether by default, category summary views should include child categories.  The default value is true</p>
       <p>Also see the withchildren parameter to allow overriding this on a per-call basis.</p>
       <p>And see the {cms_module_hint} plugin for ways to inject this flag or other flags from page content or templates.</p>
    </li>
    <li><code>presroom_urlprefix</code> - string
       <p>Determines the prefix for all pretty urls issued by, and read by this module.  The default value is "PressRoom".</p>
       <p><strong>Note:</strong> Changing this config entry will change the URL for all existing news articles that do not have a URL slug.   Links to existing articles outside of this CMSMS installation will be broken.   You should also ensure you clear the cache if you change config entries</p>
    </li>
    <li><code>pressroom_categoryurlprefix</code> - string
        <p>Determins the secondary prefix for all pretty urls to summary view for a specific category.  The default value is "bycategory".</p>
	<p><strong>Note:</strong> Changing this config entry will change the URL for all existing category news summary URLS.  Existing external links to your summary views will be broken.  You should also ensure that you clear the cache after changing config entries.</p>
    </li>
    <li><code>pressroom_nopretty</code> - boolean
        <p>Disabled pretty URL generation for the whole module.</p>
    </li>
</ul>

<h3>Links within Templates:</h3>
<p>By default, a summary view will create links to detail views<p>

<h3>Search integration:</h3>
<ul>
<li>When and if you change the <code>pressroom_expired_searchable</code> entry in the CMSMS config.php, you must ensure that all search words are re-indexed from within the search module.</li>
<li>This module indexes words from the article summary and article content within the Search module.  It does not included words contained within custom fields.</li>
</ul>

<h3>Parameters:</h3>
<ul>
    <li>summarytemplate:
        <p><strong>View:</strong> default</p>
        <p><strong>Default:</strong> default.tpl</p>
	<p><strong>Description:</strong> Specify an alternate summary template to use.</p>
	<p>By default, this module uses file based templates so without specifying a smarty resource name, the system will look in <code>assets/module_custom/PressRoom/templates</code> for templates.   You can specify alternate smarty resources such as:  <code>cms_file:something.tpl</code> to look for something.tpl in <code>assets/templates</code> or <code>cms_template:something</code> to use a template named "something" from within DesignManager.</p>
	<p>You can use the <code>{cms_module_hint}</code> smarty plugin to specify which template to used from within the content page, or the page template.</p>
    </li>
    <li>detailtemplate:
        <p><strong>View:</strong> detail</p>
        <p><strong>Default:</strong> detail.tpl</p>
	<p><strong>Description:</strong> Specify an alternate detail template to use.</p>
	<p>See the summarytemplate paramter above for info about file based templates, or using alternate smarty resources.</p>
	<p>You can use the <code>{cms_module_hint}</code> smarty plugin to specify which template to used from within the content page, or the page template.</p>
    </li>
    <li>categorytemplate:</li>
        <p><strong>View:</strong> showcategories</p>
        <p><strong>Default:</strong> showcategories.tpl</p>
	<p><strong>Description:</strong> Specify an alternate template to use for a showcategories view.</p>
	<p>See the summarytemplate paramter above for info about file based templates, or using alternate smarty resources.</p>
	<p>You can use the <code>{cms_module_hint}</code> smarty plugin to specify which template to used from within the content page, or the page template.</p>
    </li>
    <li>article:
        <p><strong>View:</strong> detail</p>
        <p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> Specify an integer article id to display in a detail view.  If -1 is used, then the latest published, non expired article will be used.</p>
    </li>
    <li>limit:
        <p><strong>View:</strong> summary</p>
        <p><strong>Default:</strong> 50</p>
	<p><strong>Description:</strong> An integer value that represents the maximum number of articles to display per page.  A maximum value of 1000 is permitted.</p>
    </li>
    <li>category_id:
        <p><strong>View:</strong> summary</p>
        <p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> Filter display only articles belonging to this category (and optionally any child categories). The value must be a positive integer representing a valid category.</p>
    </li>
    <li>category_alias:
        <p><strong>View:</strong> summary</p>
        <p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> Filter display only articles belonging to this category (and optionally any child categories). The value must be a valid category alias.</p>
    </li>
    <li>withchildren:
        <p><strong>View:</strong> summary</p>
        <p><strong>Default:</strong> false</p>
	<p><strong>Description:</strong> A boolean value that indicates whether or not to display articles from child categories when either the category_id or category_alias parameters are also provided.</p>
    </li>
    <li>useperiod:
        <p><strong>View:</strong> summary</p>
        <p><strong>Default:</strong> true</p>
	<p><strong>Description:</strong> A boolean value that indicates whether or not to utilize the start and end time of articles in the filtering.  By default, only articles that either have no start/end times, or who's start/end time are between the current time are displayed.  specifying useperiod=false will disable all start/end-time checks.</p>
    </li>
    <li>showall:
        <p><strong>View:</strong> summary</p>
	<p><strong>Default:</strong> false</p>
	<p><strong>Description:</strong> A boolean value that indicates whether articles should be displayed regardless of status.</p>
    </li>
    <li>idlist:
        <p><strong>View:</strong> summary</p>
	<p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> An array of article ids to further filter the resulting view by.   Only articles that are within the specified idlist AND match all other conditions will be displayed.   This feature is utilized by the "Related Articles" field type.</p>
    </li>
    <li>page:
        <p><strong>View:</strong> summary</p>
	<p><strong>Default:</strong> 1</p>
	<p><strong>Description:</strong> An integer value representing the page of results to display.</p>
    </li>
    <li>sortby:
        <p><strong>View:</strong> summary</p>
	<p><strong>Default:</strong> news_date</p>
	<p><strong>Description:</strong> A string value indicating how articles in the summary view should be sorted.
	   Possible values are: "modified_date", "create_date", "news_date", "title", "status", "field"
	<p>
	<p>The default sort order is descending.  You can change this to ascending by appending the string _asc to the sortby parameter.
	   i.e:  <code>{PressRoom sortby=title_asc}</code>
	</p>
    </li>
    <li>sortdata:
        <p><strong>View:</strong> summary</p>
	<p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> Only useful when the sortby parameter is set to "field", this parameter should indicate the name of the field to use in the sorting.</p>
    </li>
    <li>args:
        <p><strong>View:</strong> summary</p>
	<p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> This is a string containing a base64 encoded and json_encoded array of parameters to merge into the request.  This is utilized by the default summary template on the pagination mechanism, and should only be used to return the parameters used in generating the summary view TO the summary view.</p>
    </li>
    <li>alias:
        <p><strong>View:</strong> showcategories</p>
	<p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> The alias (string) of a category specifying where to start the category view.  When specified only children of the specified alias will be displayed.</p>
    </li>
    <li>from:
        <p><strong>View:</strong> showcategories</p>
	<p><strong>Default:</strong> -1</p>
	<p><strong>Description:</strong> The integer id of a category specifying where to start the category view.  When specified only children of the specified category will be displayed.</p>
    </li>
    <li>maxdepth:
        <p><strong>View:</strong> showcategories</p>
	<p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> The maximum levels of categories to display.</p>
    </li>
    <li>uid:
        <p><strong>View:</strong> admin_user</p>
	<p><strong>Default:</strong> n/a</p>
	<p><strong>Description:</strong> The integer UID of the admin user to display.</p>
    </li>
    <li>noslug:
        <p><strong>View:</strong> n/a</p>
	<p><strong>Default:</strong> false</p>
	<p><strong>Description:</strong> This parameter can be used in the call to {cms_action_url} when building links to detail views to indicate that any URL slug should be ignored when building the article.</p>
    </li>
</ul>

<h3>Properties of a Article Object:</h3>
<p><strong>Note:</strong> - In a view, article objects cannot be modified.</p>
<ul>
   <li>id <em>(int)</em> - The unique article id number.</li>
   <li>category_id <em>(int)</em> - The id of the associated category (may be null)</li>
   <li>title <em>(string)</em> - The article title</li>
   <li>summary <em>(text/html)</em> - The article summary.  May contain HTML formatting from a WYSIWYG editor, may be null</li>
   <li>content <em>(text/html)</em> - The article content.  May contain HTML formatting from a WYSIWYG editor.</li>
   <li>status <em>(string)</em> - The status of the article.  i.e: published, draft, etc.</li>
   <li>news_date <em>(int)</em> - The unix timestamp of the post-date for the article.</li>
   <li>start_time <em>(int)</em> - The minimum time that the article can be visible for frontend display.  May be null</li>
   <li>end_time <em>(int)</em> - The maximum time that the article can be visible for display.  May be null</li>
   <li>create_date <em>(int)</em> - The unix timestamp that the article was created.</li>
   <li>modified_date <em>(int)</em> - The unix timestamp that the article was last modified.</li>
   <li>author_id <em>(int)</em> - The userid of the admin user that created the article.  See the "admin_user" action</li>
   <li>author_name <em>(string)</em> - The username of the admin user that created the article.  May be null.  See the "admin_user" action.</li>
   <li>url_slug <em>(slug)</em> - The unique URL slug for this article.</li>
   <li>searchable <em>(boolean)</em> - Whether this article can appear in the search results.</li>
   <li>fields <em>(hash)</em> - An associative array of field values.  The keys are field names.</li>
   <li>extra <em>(string)</em> - User settable extra data.  may be null.</li>
</ul>

<h3>Properties of a Category Object:</h3>
<p><strong>Note:</strong> - In a view, category objects cannot be modified.</p>
<ul>
   <li>id <em>(int)</em> - The unique article id.</li>
   <li>alias <em>(string)</em> - A unique alias for this category.  May be null.</li>
   <li>name <em>(string)</em> - The category name (may not be unique across all categories)</li>
   <li>parent_id <em>(int)</em> - The id of the parent category to form a hierarchy.  May be null or -1 to indicate no parent.</li>
   <li>item_order <em>(int)</em> - An integer describing the order to display this item amongst its direct peers.</li>
   <li>hierarchy <em>(string)</em> - Represents the display order of this item.</li>
   <li>long_name <em>(string)</em> - A string representing the complete and unique path to this category using its name and the name of the parent categories.</li>
   <li>image_url <em>(string)</em> - An optional URL slug to an image, relative to the installation root url.</li>
</ul>
<h3>Methods of a Category Object:</h3>
<ul>
   <li><code>isDescendedFromName( string $long_name ) : bool</code>
     <p>Test whether the current category object is decended from the long name of another cateogyr object.</lp>
     <p>i.e:</p>
     <pre>{if $category->isDecensedFromName( 'foo | bar')}{* use a different detail template *}{/if}</pre>
   </li>
</ul>

<h3>Properties of a FieldDef object:</h3>
<ul>
   <li>id <em>(int)</em> - The unique fielddef id.</li>
   <li>name <em>(string)</em> - The unique fielddef name.</li>
   <li>type <em>(string)</em> - The class/type name of the fielddef.</li>
   <li>item_order <em>(int)</em> - The integer display order of this fielddef.</li>
</ul>

<h3>Variables available in the summary view:</h3>
<ul>
   <li>$articles - Array of matching article objects</li>
   <li>$fielddefs - An array of fielddefs objects representing existing field definitions.</li>
   <li>$fieldtypes - An array of known Field Types.</li>
</ul>

<h3>Variables available in the detail view:</h3>
<ul>
  <li>$article - An article object</li>
  <li>$category - A category object representing the category of the current article (if any).</li>
  <li>$fielddefs = An array of fielddef objects representing existing field definitions.</li>
  <li>$fieldtypes - An array of known Field Types.</li>
</ul>

<h3>Variables available in the showcategories view:</h3>
<ul>
  <li>$categories - A nested array of categories matching the criteria specified in the tag.  Note, each element is a category hash, not a category object.  The "children" element, if exists refers to child categories.</li>
  <li>$maxdepth - An integer representing the maximum depth of categories to display.</li>
</ul>

<h3>Variables available in the admin_user view:</h3>

<h3>FAQ:</h3>
<ul>
   <li>How do I use a non-default summary template
       <p>First, using the instructions above, create a non-default summary template in <code>assets/module_custom/PressRoom/templates</code>.</p>
       <ul>
          <li>Method 1:
	      <p>Add this code into your page content, or page template.</p>
	      <pre><code>{PressRoom action=default summarytemplate='othertemplate.tpl'}</code></pre>
	  </li>
	  <li>Method 2:
	      <p>Add the following code to the top portion of your page template, OR to the "Smarty logic that is specific to this page" field in the content editor.</p>
	      <p>This basically says:  When displaying a PressRoom summary view on this page (or on pages using this template), use the specified summary template.</p>
	      <pre><code>{cms_module_hint modules=PressRoom action=default summarytemplate='othertemplate.tpl'}</code></pre>
	  </li>
       </ul>
   </li>

   <li>How do I use a non default detail template
      <ul>
      <li>First, using the instructions above, create a non-default detail template in <code>assets/module_custom/PressRoom/templates</code>.</li>
      <li>Second, add the following code to the page template or to the "Smarty logic that is specific to this page" field in the content editor:
        <pre><code>{cms_module_hint module=PressRoom action=detail detailtemplate='othertemplate.tpl'}</code></pre>
      </li>
      </ul>
   </li>

   <li>How do I use a different detail page and template for articles in different categories:
      <ol>
         <li>Edit the desired PressRoom category, and set the detailpage to the desired detailpage.</li>
	 <li>See the instructions above about using a non default detail template.</li>
      </ol>
   </li>

   <li>How do I make further changes to the edit article form:
       <p>Many behavioral changes can be done to the form provided when adding or editing an article, beyond what is available in config files. This can be accomplished by overriding the admin_edit_article.tpl template.</p>
   </ii>
</ul>

<h3>Overriding admin templates</h3>
<p>The steps for this are identical to the steps for creating a new summary or detail template.</p>
<ol>
    <li>Copy the <code>lib/modules/PressRoom/templates/admin_edit_article.tpl</code> file to <code>assets/module_custom/PressRoom/templates/admin_edit_article.tpl</code></li>
    <li>Adjust the smarty template as you see fit.</li>
</ol>
